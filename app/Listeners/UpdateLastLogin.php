<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Carbon;

class UpdateLastLogin
{
    public function handle(Login $event)
    {
        $event->user->timestamps = false;
        $event->user->last_login_at = Carbon::now();
        $event->user->save();
        $event->user->timestamps = true;
    }
}
