<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentProofStatusNotification extends Notification
{
    use Queueable;

    public array $data;


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

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Payment Proof Status')
            ->line('Your payment proof for amount ' . $this->data['amount'] . ' with reference number ' . $this->data['reference_number'] . ' has been ' . $this->data['status'] . ' by the admin.')
            ->line('Remarks: ' . $this->data['remarks'])
//            ->action('View Payment Proof', route('tenant.payment_proofs.index'))
            ->line('Thank you for using our application!');


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
            //
        ];
    }
}
