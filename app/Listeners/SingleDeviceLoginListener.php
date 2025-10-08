<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class SingleDeviceLoginListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event - invalidate other sessions when user logs in
     */
    public function handle(Login $event): void
    {
        $user = $event->user;
        $currentSessionId = Session::getId();
        
        // Get all active sessions for this user (excluding current session)
        $otherSessions = DB::table('sessions')
            ->where('user_id', $user->id)
            ->where('id', '!=', $currentSessionId)
            ->get();
            
        // Delete other sessions from database
        DB::table('sessions')
            ->where('user_id', $user->id)
            ->where('id', '!=', $currentSessionId)
            ->delete();
            
        // Log the action for security audit
        logger()->info('Single device login: invalidated other sessions', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'current_session' => $currentSessionId,
            'invalidated_sessions' => $otherSessions->count(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }
}
