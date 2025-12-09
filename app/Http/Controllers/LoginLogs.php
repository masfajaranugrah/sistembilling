<?php

namespace App\Http\Controllers;

use App\Models\LoginLog;

class LoginLogs extends Controller
{
    public function loginLog()
    {
        $logs = LoginLog::with('user')->orderBy('created_at', 'desc')->get();

        return view('content.apps.History.historyLogin', compact('logs'));
    }
}
