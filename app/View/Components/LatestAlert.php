<?php

namespace App\View\Components;

use Illuminate\View\Component;

class LatestAlert extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        //get latest database notification for the current user
        $latestAlert = auth()->user()->unreadNotifications->first();

        return view('components.latest-alert', [
            'latestAlert' => $latestAlert
        ]);
    }
}
