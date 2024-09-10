<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewInvoiceNotification extends Notification
{
    use Queueable;

    public $data;

    public function __construct(array $data)
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
        return (new MailMessage)
            ->subject(__('New Invoice'))
            ->line(__('Hello') . ' ' . $notifiable->name . ',')
            ->line(__('You have a new invoice with the following details:'))
            ->line(__('Invoice ID:') .' '. $this->data['invoice_id'])
            ->line(__('Invoice Amount:') .' '. $this->data['invoice_amount'])
            ->line(__('Invoice Description: Rent payment'))
            ->action(__('View Invoice'), route('tenant.invoices.index'))
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
            'title' => __('New Invoice'),
            'message' => __('You have a new invoice with ID:') . ' ' . $this->data['invoice_id'] . ' ' . __('and amount:') . ' ' . $this->data['invoice_amount'] . ' ' . __('due on receipt of this notification.'),
        ];
    }
}
