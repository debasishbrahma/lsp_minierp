<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class QuotationStatusUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    protected $quotation;

    /**
     * Create a new notification instance.
     */
    public function __construct($quotation)
    {
        $this->quotation = $quotation;

        Log::debug('QuotationStatusUpdated constructed', [
            'quotation_id' => $quotation->id,
            'status' => $quotation->status,
            'user_id' => $quotation->user_id,
        ]);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        /*  Log::debug('QuotationStatusUpdated via called', [
            'notifiable_id' => $notifiable->id,
            'notifiable_type' => get_class($notifiable),
        ]); */

        return ['database', 'mail'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => "Your quotation for {$this->quotation->customer_name} has been {$this->quotation->status}.",
            'quotation_id' => $this->quotation->id,
        ];

        /*  Log::debug('QuotationStatusUpdated toDatabase', [
            'notifiable_id' => $notifiable->id,
            'data' => $data,
        ]); */
        return $data;
    }

    public function toMail($notifiable)
    {
        /*  Log::debug('QuotationStatusUpdated toMail', [
            'notifiable_id' => $notifiable->id,
            'email' => $notifiable->email,
        ]); */

        return (new MailMessage)
            ->subject("Quotation Status {$this->quotation->status}")
            ->greeting("Hello {$notifiable->name},")
            ->line("Your quotation for {$this->quotation->customer_name} has been {$this->quotation->status}.")
            ->line("Total Price: {$this->quotation->total_price}")
            ->action('View Quotation', route('quotations.show', $this->quotation->id))
            ->line("This is an auto generated email. Don't reply to this.");
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
