<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TenantExpiringLeaseNotification extends Notification
{
    use Queueable;

    public array $details;

    public function __construct($details)
    {
        $this->details = $details;
    }


    public function via($notifiable): array
    {
        return ['database','mail'];
    }


    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('Your lease is expiring soon.'))
            ->line(__('Your lease for').' ' . $this->details['property'] .' '. __('will expire on') .' '. $this->details['end_date'] . '.')
            ->line(__('Please contact the property manager to renew your lease.'))
            ->action(__('View Lease'), url('/'))
            ->line(__('Thank you for using our application!'));
    }


    public function toArray($notifiable): array
    {
        return [
            'title' => __('Your lease is expiring soon.'),
            'message' => __('Your lease will expire on') .' '. $this->details['end_date'] .' .'. __('Please contact the property manager to renew your lease.'),
        ];
    }
}
