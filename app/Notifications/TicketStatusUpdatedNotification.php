<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketStatusUpdatedNotification extends Notification
{
    use Queueable;

    public array $data;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(__('Ticket Status Updated'))
            ->line(__('Your ticket with subject') . ' ' . $this->data['subject'] . ' ' . __('has been updated to') . ' ' . $this->data['status'] . ' ' . __('by the admin.'))
            ->action(__('View Ticket'), url('/'))
            ->line(__('Thank you for using our application!'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'title' => __('Ticket Status Updated'),
            'message' => __('Your ticket with subject') . ' ' . $this->data['subject'] . ' ' . __('has been updated to') . ' ' . $this->data['status'] . ' ' . __('by the admin.'),
        ];
    }
}
