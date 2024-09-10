<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DepositRefundedNotification extends Notification
{
    use Queueable;

    public array $details;


    public function __construct($details)
    {
        $this->details = $details;
    }


    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        //return mail message with the deposit refunded notification details
        //body should capture the amount refunded and the date refunded

        return (new MailMessage)
            ->subject(__('Deposit Refunded'))
            ->greeting(__('Hello!, ') . $notifiable->name)
            ->line(__('Your deposit has been refunded.'))
            ->line(__('Amount refunded: $') . $this->details['amount'])
            ->line(__('Date refunded: ') . $this->details['date'])
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
            'title' => __('Deposit Refunded'),
            'message' => __('Your deposit has been refunded.'),
        ];
    }
}
