<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderPlacedNotification extends Notification
{
    use Queueable;

    protected $order;

    public function __construct($order)
    {
        $this->order = $order;
    }
    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'customer_name' => $this->order->user->name,
            'order_number'  => $this->order->id,
            'total_amount'  => $this->order->total_amount,
            'message'       => 'New order has been placed.'
        ];
    }
}