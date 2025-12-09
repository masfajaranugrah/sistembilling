<?php

namespace App\Http\Controllers;

use Illuminate\Broadcasting\BroadcastController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

class CustomBroadcastController extends BroadcastController
{
    /**
     * Authenticate the request for channel access.
     */
    public function authenticate(Request $request)
    {
        Log::info('🔐 CUSTOM Broadcasting Auth Called', [
            'channel' => $request->input('channel_name'),
            'socket_id' => $request->input('socket_id'),
            'has_session' => $request->hasSession(),
            'session_id' => $request->hasSession() ? $request->session()->getId() : null,
        ]);
        
        // Try multiple guards
        $guards = ['web', 'customer'];
        
        foreach ($guards as $guard) {
            $isAuth = Auth::guard($guard)->check();
            Log::info("🔍 Checking guard: {$guard}", [
                'authenticated' => $isAuth,
                'user' => $isAuth ? Auth::guard($guard)->user()->id : null,
            ]);
            
            if ($isAuth) {
                Auth::shouldUse($guard);
                Log::info("✅ Using guard: {$guard}");
                
                try {
                    $response = Broadcast::auth($request);
                    Log::info("✅ Broadcasting auth SUCCESS");
                    return $response;
                } catch (\Exception $e) {
                    Log::error("❌ Broadcasting auth FAILED: " . $e->getMessage());
                    return response()->json(['error' => $e->getMessage()], 403);
                }
            }
        }
        
        Log::warning("❌ No authenticated user found");
        return response()->json(['message' => 'Unauthenticated'], 401);
    }
}
