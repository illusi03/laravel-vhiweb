<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;

class LogLoginListener
{
    /**
     * Create the event listener.
     *
     * @param  Request  $request
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Handle the event.
     *
     * @param  Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        $user = $event->user;
        $user->setAttribute('last_login_at', $user->actual_login_at);
        $user->setAttribute('last_login_ip', $user->actual_login_ip);
        $user->setAttribute('actual_login_at', date('Y-m-d H:i:s'));
        $user->setAttribute('actual_login_ip', $this->request->ip());
        $user->save();
    }
}
