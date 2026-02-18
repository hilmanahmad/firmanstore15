<?php

namespace App\Http\Controllers;

use App\Models\UserObsSetting;
use App\Services\RecordingService;
use App\Services\OBSWebSocketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RecordingController extends Controller
{
    protected $recordingService;
    protected $obsService;

    public function __construct(RecordingService $recordingService, OBSWebSocketService $obsService)
    {
        $this->recordingService = $recordingService;
        $this->obsService = $obsService;
    }

    public function index()
    {
        $user = auth()->user();
        $isSuperAdmin = $user && $user->role_code === 'SUPERADMIN';

        // Ambil OBS settings milik user
        $obsSettings = UserObsSetting::where('user_id', $user->id)
            ->where('is_active', 1)
            ->orderByDesc('is_default')
            ->orderBy('obs_name')
            ->get();

        return view('recording.index', [
            'title' => 'OBS Recording Management',
            'active' => 'recording',
            'isSuperAdmin' => $isSuperAdmin,
            'currentUserId' => $user ? $user->id : null,
            'currentUserName' => $user ? $user->name : null,
            'obsSettings' => $obsSettings,
        ]);
    }

    public function datatable(Request $request)
    {
        $row = $request->input('rows');
        $page = $request->input('page');

        $rows = $row >= 10 ? $row : 20;
        $offset = ($page - 1) * $rows;
        $searchKey = $request->input('searchKey');
        $status = $request->input('status');

        // Filter by user if not SUPERADMIN
        $user = auth()->user();
        $userId = null;
        if ($user && $user->role_code !== 'SUPERADMIN') {
            $userId = $user->id;
        }

        $query = $this->recordingService->getByArrayDT($rows, $offset, $searchKey, $status, $userId);
        $data = [];

        foreach ($query as $key => $q) {
            $data[$key]['id'] = $q->id;
            $data[$key]['code'] = $q->code;
            $data[$key]['user_id'] = $q->user_id;
            $data[$key]['user_name'] = $q->user->name ?? '-';
            $data[$key]['custom_filename'] = $q->custom_filename ?? '-';
            $data[$key]['filename'] = $q->filename ?? '-';
            $data[$key]['file_path'] = $q->file_path ?? '-';
            $data[$key]['status'] = $q->status;
            $data[$key]['status_badge'] = $q->status_badge;
            $data[$key]['started_at'] = $q->started_at ? Carbon::parse($q->started_at)->format('d M Y H:i:s') : '-';
            $data[$key]['stopped_at'] = $q->stopped_at ? Carbon::parse($q->stopped_at)->format('d M Y H:i:s') : '-';
            $data[$key]['duration'] = $q->duration ? gmdate('H:i:s', $q->duration) : '-';
            $data[$key]['notes'] = $q->notes ?? '-';
        }

        $count = $this->recordingService->getByArrayDT(0, 0, $searchKey, $status, $userId);

        return response()->json([
            "total" => $count,
            "rows" => $data
        ]);
    }

    public function startRecording(Request $request)
    {
        try {
            DB::beginTransaction();

            $recording = $this->recordingService->startRecording($request);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Recording dimulai',
                'data' => [
                    'code' => $recording->code,
                    'started_at' => $recording->started_at->format('Y-m-d H:i:s'),
                ]
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Error: ' . $th->getMessage()
            ]);
        }
    }

    public function stopRecording(Request $request)
    {
        try {
            DB::beginTransaction();

            $recording = $this->recordingService->stopRecording(
                $request->code,
                $request->filename,
                $request->file_path
            );

            if (!$recording) {
                return response()->json([
                    'status' => false,
                    'message' => 'Recording tidak ditemukan'
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Recording dihentikan',
                'data' => [
                    'code' => $recording->code,
                    'duration' => $recording->duration,
                    'filename' => $recording->filename, // Return final filename
                ]
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Error: ' . $th->getMessage()
            ]);
        }
    }

    public function completeRecording(Request $request)
    {
        try {
            DB::beginTransaction();

            $recording = $this->recordingService->completeRecording(
                $request->code,
                $request->filename,
                $request->file_path
            );

            if (!$recording) {
                return response()->json([
                    'status' => false,
                    'message' => 'Recording tidak ditemukan'
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Recording selesai dan tersimpan',
                'data' => $recording
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Error: ' . $th->getMessage()
            ]);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $this->recordingService->destroy($id);

            DB::commit();

            return response()->json(['status' => true, 'message' => 'Data berhasil dihapus']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => $th->getMessage()]);
        }
    }

    // OBS WebSocket Proxy Endpoints
    public function obsConnect(Request $request)
    {
        $result = $this->obsService->connect(
            $request->input('url', 'ws://localhost:4455'),
            $request->input('password')
        );

        // Store credentials in session for subsequent requests
        if ($result['status']) {
            session(['obs_url' => $request->input('url', 'ws://localhost:4455')]);
            session(['obs_password' => $request->input('password')]);
        }

        return response()->json($result);
    }

    public function obsStartRecord(Request $request)
    {
        // Reconnect with stored or provided credentials (PHP is stateless)
        $url = $request->input('url', session('obs_url', config('obs.websocket_url')));
        $password = $request->input('password', session('obs_password', config('obs.websocket_password')));

        $connectResult = $this->obsService->connect($url, $password);
        if (!$connectResult['status']) {
            return response()->json($connectResult);
        }

        // Pass custom_filename agar OBS simpan file dengan nama custom
        $customFilename = $request->input('custom_filename');
        $result = $this->obsService->startRecording($customFilename);
        return response()->json($result);
    }

    public function obsStopRecord(Request $request)
    {
        // Reconnect with stored or provided credentials (PHP is stateless)
        $url = $request->input('url', session('obs_url', config('obs.websocket_url')));
        $password = $request->input('password', session('obs_password', config('obs.websocket_password')));

        $connectResult = $this->obsService->connect($url, $password);
        if (!$connectResult['status']) {
            return response()->json($connectResult);
        }

        $result = $this->obsService->stopRecording();
        return response()->json($result);
    }

    public function obsDisconnect(Request $request)
    {
        $this->obsService->disconnect();
        session()->forget(['obs_url', 'obs_password', 'obs_connected']);
        return response()->json(['status' => true, 'message' => 'Disconnected from OBS']);
    }

    // ===================== OBS Settings CRUD =====================

    public function obsSettingsStore(Request $request)
    {
        try {
            $request->validate([
                'obs_name' => 'required|string|max:100',
                'obs_url' => 'required|string|max:255',
                'obs_password' => 'nullable|string|max:255',
            ]);

            $userId = auth()->id();

            // Jika is_default, unset default lain
            if ($request->is_default) {
                UserObsSetting::where('user_id', $userId)->update(['is_default' => 0]);
            }

            // Jika ini setting pertama, jadikan default
            $count = UserObsSetting::where('user_id', $userId)->where('is_active', 1)->count();

            $setting = UserObsSetting::create([
                'user_id' => $userId,
                'obs_name' => $request->obs_name,
                'obs_url' => $request->obs_url,
                'obs_password' => $request->obs_password,
                'is_default' => $request->is_default || $count === 0 ? 1 : 0,
                'is_active' => 1,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'OBS Setting berhasil disimpan',
                'data' => $setting,
            ]);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => $th->getMessage()]);
        }
    }

    public function obsSettingsUpdate(Request $request, $id)
    {
        try {
            $setting = UserObsSetting::where('id', $id)->where('user_id', auth()->id())->firstOrFail();

            $request->validate([
                'obs_name' => 'required|string|max:100',
                'obs_url' => 'required|string|max:255',
                'obs_password' => 'nullable|string|max:255',
            ]);

            if ($request->is_default) {
                UserObsSetting::where('user_id', auth()->id())->where('id', '!=', $id)->update(['is_default' => 0]);
            }

            $setting->update([
                'obs_name' => $request->obs_name,
                'obs_url' => $request->obs_url,
                'obs_password' => $request->obs_password,
                'is_default' => $request->is_default ? 1 : 0,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'OBS Setting berhasil diupdate',
                'data' => $setting,
            ]);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => $th->getMessage()]);
        }
    }

    public function obsSettingsDestroy($id)
    {
        try {
            $setting = UserObsSetting::where('id', $id)->where('user_id', auth()->id())->firstOrFail();
            $setting->delete();

            return response()->json(['status' => true, 'message' => 'OBS Setting berhasil dihapus']);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => $th->getMessage()]);
        }
    }

    public function obsSettingsTest(Request $request)
    {
        try {
            $result = $this->obsService->connect(
                $request->input('obs_url'),
                $request->input('obs_password')
            );

            return response()->json($result);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => $th->getMessage()]);
        }
    }
}
