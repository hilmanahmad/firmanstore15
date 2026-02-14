<?php

namespace App\Services;

use Exception;
use WebSocket\Client;

class OBSWebSocketService
{
    protected $client;
    protected $url;
    protected $password;
    protected $isConnected = false;

    public function __construct()
    {
        $this->url = config('obs.websocket_url', 'ws://localhost:4455');
        $this->password = config('obs.websocket_password', '');
    }

    /**
     * Connect to OBS WebSocket and perform full handshake (Hello → Identify → Identified)
     */
    public function connect($url = null, $password = null)
    {
        try {
            $this->url = $url ?? $this->url;
            $this->password = $password ?? $this->password;

            \Log::info('OBS: Attempting connection', ['url' => $this->url]);

            // Create WebSocket client
            $this->client = new Client($this->url, [
                'timeout' => 10,
            ]);

            \Log::info('OBS: WebSocket client created, waiting for Hello...');

            // Step 1: Receive Hello message (op 0)
            $helloRaw = $this->client->receive();
            $hello = json_decode($helloRaw, true);

            if (!$hello || ($hello['op'] ?? null) !== 0) {
                \Log::error('OBS: Invalid Hello message', ['received' => $helloRaw]);
                return ['status' => false, 'message' => 'Did not receive Hello from OBS. Got: ' . ($helloRaw ?: 'nothing')];
            }

            \Log::info('OBS: Hello received', ['rpcVersion' => $hello['d']['rpcVersion'] ?? null]);

            $helloData = $hello['d'];
            $rpcVersion = $helloData['rpcVersion'] ?? 1;

            // Step 2: Build Identify message (op 1)
            $identifyData = [
                'rpcVersion' => $rpcVersion,
                'eventSubscriptions' => 33, // General + Outputs
            ];

            // Handle authentication if required
            if (isset($helloData['authentication'])) {
                $challenge = $helloData['authentication']['challenge'];
                $salt = $helloData['authentication']['salt'];

                if (empty($this->password)) {
                    return ['status' => false, 'message' => 'OBS requires password but none provided'];
                }

                $authString = $this->generateAuthString($this->password, $salt, $challenge);
                $identifyData['authentication'] = $authString;
            }

            $identifyMessage = json_encode([
                'op' => 1,
                'd' => $identifyData,
            ]);

            $this->client->send($identifyMessage);

            // Step 3: Receive Identified message (op 2)
            $identifiedRaw = $this->client->receive();
            $identified = json_decode($identifiedRaw, true);

            if (!$identified || ($identified['op'] ?? null) !== 2) {
                $errorMsg = 'Authentication failed.';
                if ($identified && ($identified['op'] ?? null) === 4) {
                    // Op 4 = Close message from OBS
                    $errorMsg .= ' OBS rejected connection: ' . ($identified['d']['closeReason'] ?? 'Unknown reason');
                }
                return ['status' => false, 'message' => $errorMsg];
            }

            $this->isConnected = true;

            // Store client in session/cache for subsequent requests
            session(['obs_client_url' => $this->url]);
            session(['obs_client_password' => $this->password]);
            session(['obs_connected' => true]);

            return ['status' => true, 'message' => 'Connected and authenticated to OBS successfully'];
        } catch (Exception $e) {
            $this->isConnected = false;
            \Log::error('OBS: Connection failed', [
                'url' => $this->url,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return ['status' => false, 'message' => 'Failed to connect: ' . $e->getMessage()];
        }
    }

    /**
     * Generate OBS WebSocket v5 authentication string
     */
    protected function generateAuthString($password, $salt, $challenge)
    {
        // Step 1: SHA256(password + salt) → base64
        $secret = $password . $salt;
        $secretHash = base64_encode(hash('sha256', $secret, true));

        // Step 2: SHA256(secretHash + challenge) → base64
        $auth = $secretHash . $challenge;
        $authHash = base64_encode(hash('sha256', $auth, true));

        return $authHash;
    }

    /**
     * Reconnect using session credentials (for stateless HTTP requests)
     */
    protected function reconnectFromSession()
    {
        $url = session('obs_client_url', $this->url);
        $password = session('obs_client_password', $this->password);

        if (!$this->isConnected || !$this->client) {
            $result = $this->connect($url, $password);
            if (!$result['status']) {
                return $result;
            }
        }

        return ['status' => true];
    }

    public function sendCommand($command, $data = [])
    {
        // Reconnect if needed (each HTTP request is a new PHP process)
        $reconnect = $this->reconnectFromSession();
        if (!$reconnect['status']) {
            return $reconnect;
        }

        try {
            $requestId = uniqid('req_');
            $message = json_encode([
                'op' => 6, // Request
                'd' => [
                    'requestType' => $command,
                    'requestId' => $requestId,
                    'requestData' => (object) $data,
                ]
            ]);

            $this->client->send($message);
            $response = $this->client->receive();
            $decoded = json_decode($response, true);

            // Check if this is a RequestResponse (op 7)
            if ($decoded && ($decoded['op'] ?? null) === 7) {
                $requestStatus = $decoded['d']['requestStatus'] ?? [];
                $success = ($requestStatus['result'] ?? false) === true;

                if ($success) {
                    return [
                        'status' => true,
                        'message' => "$command executed successfully",
                        'response' => $decoded['d'],
                    ];
                } else {
                    $code = $requestStatus['code'] ?? 'unknown';
                    $comment = $requestStatus['comment'] ?? 'No details';
                    return [
                        'status' => false,
                        'message' => "$command failed: [$code] $comment",
                        'response' => $decoded['d'],
                    ];
                }
            }

            return ['status' => true, 'response' => $decoded];
        } catch (Exception $e) {
            return ['status' => false, 'message' => 'Command failed: ' . $e->getMessage()];
        } finally {
            // Close connection after each request (stateless)
            $this->disconnect();
        }
    }

    public function startRecording()
    {
        return $this->sendCommand('StartRecord');
    }

    public function stopRecording()
    {
        return $this->sendCommand('StopRecord');
    }

    public function getRecordStatus()
    {
        return $this->sendCommand('GetRecordStatus');
    }

    public function disconnect()
    {
        try {
            if ($this->client) {
                $this->client->close();
            }
        } catch (Exception $e) {
            // Ignore close errors
        }
        $this->isConnected = false;
    }
}
