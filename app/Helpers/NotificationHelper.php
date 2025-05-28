<?php
// app/Helpers/NotificationHelper.php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class NotificationHelper
{
    public static function getUnreadCount()
    {
        if (Auth::check()) {
            return Auth::user()->notifications()->where('read', false)->count();
        }
        
        return 0;
    }
}