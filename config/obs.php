<?php

return [
    /*
    |--------------------------------------------------------------------------
    | OBS WebSocket Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for OBS WebSocket connection
    |
    */

    'websocket_url' => env('OBS_WEBSOCKET_URL', 'ws://localhost:4455'),
    'websocket_password' => env('OBS_WEBSOCKET_PASSWORD', ''),
];
