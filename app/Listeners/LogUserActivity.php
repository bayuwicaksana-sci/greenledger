<?php

namespace App\Listeners;

use App\Models\UserAccessLog;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\PasswordReset;

class LogUserActivity
{
    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $user = $event->user ?? auth()->user();

        if (! $user) {
            return;
        }

        $action = match (get_class($event)) {
            Login::class => 'login',
            Logout::class => 'logout',
            Failed::class => 'failed_login',
            PasswordReset::class => 'password_reset',
            default => 'unknown',
        };

        UserAccessLog::create([
            'user_id' => $user->id,
            'action' => $action,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);

        // Update user last login on successful login
        if ($action === 'login') {
            $user->update([
                'last_login_at' => now(),
                'last_login_ip' => request()->ip(),
                'failed_login_attempts' => 0,
            ]);
        }

        // Increment failed attempts
        if ($action === 'failed_login') {
            $user->increment('failed_login_attempts');

            // Lock account after 5 failed attempts
            if ($user->failed_login_attempts >= 5) {
                $user->update([
                    'locked_until' => now()->addMinutes(30),
                ]);
            }
        }
    }
}
