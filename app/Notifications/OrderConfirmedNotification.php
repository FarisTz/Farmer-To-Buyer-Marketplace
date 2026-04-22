<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderConfirmedNotification extends Notification
{
    use Queueable;

    public $order;

    /**
     * Create a new notification instance.
     */
    public function __construct($order)
    {
        $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Order Confirmed - #' . $this->order->order_number)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Great news! Your order has been confirmed by the farmer.')
            ->line('Order Details:')
            ->line('• Order Number: #' . $this->order->order_number)
            ->line('• Total Amount: TZS ' . number_format($this->order->total_amount, 2))
            ->line('• Status: ' . ucfirst($this->order->status))
            ->line('• Delivery Address: ' . $this->order->delivery_address)
            ->line('Your order is now being prepared for delivery.')
            ->action('View Order Details', route('buyer.orders.show', $this->order->id))
            ->line('Thank you for your order!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'total_amount' => $this->order->total_amount,
            'status' => $this->order->status,
            'type' => 'order_confirmed'
        ];
    }
}
