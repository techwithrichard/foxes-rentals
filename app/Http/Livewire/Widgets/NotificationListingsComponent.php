<?php

namespace App\Http\Livewire\Widgets;

use Livewire\Component;

class NotificationListingsComponent extends Component
{

    public $notifications = [];

    public function mount()
    {
        $this->notifications = auth()->user()->unreadNotifications()->get();
    }

    public function render()
    {
        //get all unread notifications for the user  with id 7


        return view('livewire.widgets.notification-listings-component');
    }

    //function to mark single notification as read
    public function markAsRead($id)
    {

        auth()->user()->unreadNotifications->where('id', $id)->markAsRead();
        //clear the notifications array and reload it
        $this->notifications = [];
        $this->notifications = auth()->user()->unreadNotifications()->get();
    }

    //function to mark all notifications as read
    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
    }
}
