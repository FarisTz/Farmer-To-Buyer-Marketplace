<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderCreatedNotification extends Notification
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
            ->subject('New Order Received - #' . $this->order->order_number)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('You have received a new order from ' . $this->order->buyer->name . '.')
            ->line('Order Details:')
            ->line('• Order Number: #' . $this->order->order_number)
            ->line('• Total Amount: TZS ' . number_format($this->order->total_amount, 2))
            ->line('• Delivery Address: ' . $this->order->delivery_address)
            ->line('• Phone: ' . $this->order->phone)
            ->action('View Order Details', route('farmer.orders.show', $this->order->id))
            ->line('Please review and confirm this order as soon as possible.')
            ->line('Thank you for using our marketplace!');
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
            'buyer_name' => $this->order->buyer->name,
            'type' => 'order_created'
        ];
    }
}
