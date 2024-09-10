<?php

namespace App\Http\Livewire\Widgets;

use Livewire\Component;

class NotificationDropdownComponent extends Component
{

    public function render()
    {
        //get latest 5 unread notifications of the user
        $notifications = auth()->user()->unreadNotifications()->latest()->take(5)->get();
        return view('livewire.widgets.notification-dropdown-component', compact('notifications'));
    }

    public function markAllNotificationsAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();

    }
}
