<?php

namespace App\Listeners;

use App\Models\LoginActivity;
use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Jenssegers\Agent\Facades\Agent;


class LogSuccessfulLogin
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }


    public function handle(Login $event)
    {
        $user = $event->user;


        $loginActivity = new LoginActivity([
            'ip_address' => $this->request->ip(),
            'user_agent' => $this->request->userAgent(),
            'device' => Agent::device(),
            'browser' => Agent::browser(),
            'platform' => Agent::platform(),
            'platform_version' => Agent::version(Agent::platform()),
            'browser_version' => Agent::version(Agent::browser()),
            'login_at' => now(),
            'user_id' => $user->id,
        ]);

        $loginActivity->save();

    }


}
