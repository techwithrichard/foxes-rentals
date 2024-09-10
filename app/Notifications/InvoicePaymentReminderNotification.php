<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoicePaymentReminderNotification extends Notification
{
    use Queueable;

    public $data;

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
        return ['mail'];
    }


    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('Invoice Payment Reminder'))
            ->greeting(__('Hello ') . $this->data['tenant_name'])
            ->line(__('You have an outstanding balance of ') . setting('currency_symbol') . '' . number_format($this->data['balance'], 2) . ' for the month of ' . $this->data['month_year'])
            ->line(__('Please pay the outstanding balance for rent to avoid late payment charges.'))
            ->action(__('View Invoice'), $this->data['invoice_url'])
            ->line(__('Thank you.'));


    }


    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
