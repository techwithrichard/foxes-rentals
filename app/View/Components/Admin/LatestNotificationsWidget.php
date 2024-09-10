<?php

namespace App\View\Components\Admin;

use Illuminate\View\Component;

class LatestNotificationsWidget extends Component
{

    public function __construct()
    {
        //
    }


    public function render()
    {
        //get latest 5 notifications from database
        $notifications = auth()->user()->notifications()->latest()->take(5)->get();
        return view('components.admin.latest-notifications-widget', compact('notifications'));
    }
}
