<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PushSubscriptionController extends Controller
{
    public function store(Request $request)
    {
        $user = $request->user(); // Pelanggan yang login
        $user->updatePushSubscription($request->all());

        return response()->json(['success' => true]);
    }
}
