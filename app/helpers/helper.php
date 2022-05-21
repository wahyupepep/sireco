<?php

use App\Models\Log;
use Illuminate\Support\Facades\Auth;

if (!function_exists('log_activity')) {
    function log_activity($data)
    {
        Log::create([
            'ip_address' => $data[0],
            'device' => $data[1],
            'browser' => $data[2],
            'page' => $data[3],
            'action' => $data[4],
            'user_id' => Auth::user()->id
        ]);
    }
}
