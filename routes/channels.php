<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (string) $user->id === (string) $id;
});

// Chat private channel - support UUID dan multiple guards
Broadcast::channel('chat.{userId}', function ($user, $userId) {
    // $user could be from either 'web' or 'customer' guard
    // Both have 'id' property
    $authorized = (string) $user->id === (string) $userId;
    
    Log::info('Broadcasting Auth Check', [
        'user_id' => $user->id,
        'channel_user_id' => $userId,
        'authorized' => $authorized,
        'user_type' => get_class($user)
    ]);
    
    return $authorized;
});
