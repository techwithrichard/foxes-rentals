<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;

class LogSuccessfulLogout
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function handle(Logout $event)
    {
        $user = $event->user;

        $loginActivity = $user->loginActivities()
            ->where('logout_at', null)
            ->orderBy('created_at', 'desc')
            ->first();

        $loginActivity?->update([
            'logout_at' => now(),
        ]);
    }
}
