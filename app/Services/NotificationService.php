<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Order;
use App\Models\User;

class NotificationService
{
    public function send(int $userId, string $title, string $message, string $type = 'info', ?string $actionUrl = null, ?string $icon = null): Notification
    {
        return Notification::create([
            'user_id'    => $userId,
            'title'      => $title,
            'message'    => $message,
            'type'       => $type,
            'icon'       => $icon ?? 'ti-bell',
            'action_url' => $actionUrl,
            'is_read'    => false,
        ]);
    }

    public function notifyNewOrder(Order $order): void
    {
        // Notify all kasir
        User::where('role', 'kasir')->where('status', 'active')->get()->each(function ($kasir) use ($order) {
            $this->send(
                $kasir->id,
                'Order Baru Masuk! 🔔',
                "Order #{$order->order_number} dari {$order->user->name}",
                'order',
                "/kasir/orders/{$order->id}",
                'ti-shopping-bag'
            );
        });

        // Notify kitchen
        User::where('role', 'kitchen')->where('status', 'active')->get()->each(function ($kitchen) use ($order) {
            $this->send(
                $kitchen->id,
                'Pesanan Baru!',
                "Order #{$order->order_number} perlu disiapkan",
                'order',
                '/kitchen',
                'ti-chef-hat'
            );
        });
    }

    public function notifyOrderStatus(Order $order): void
    {
        $messages = [
            'cooking'   => ['Pesanan Sedang Dimasak 👨‍🍳', 'Pesanan kamu sedang dimasak dengan penuh cinta!'],
            'selesai'   => ['Pesanan Siap! 🎉', 'Pesanan kamu sudah siap. Yuk, selesaikan pesananmu!'],
            'completed' => ['Terima Kasih! ⭐', 'Pesanan completed. Poin loyalty sudah ditambahkan!'],
            'dibatalkan'=> ['Pesanan Dibatalkan', 'Maaf, pesanan kamu dibatalkan.'],
        ];

        if (!isset($messages[$order->status])) return;

        [$title, $msg] = $messages[$order->status];

        $this->send(
            $order->user_id,
            $title,
            $msg,
            $order->status === 'dibatalkan' ? 'error' : 'success',
            "/orders/{$order->id}",
            'ti-shopping-bag'
        );
    }

    public function broadcast(string $title, string $message, string $type = 'info'): void
    {
        User::where('role', 'customer')->where('status', 'active')
            ->chunk(100, function ($users) use ($title, $message, $type) {
                foreach ($users as $user) {
                    Notification::create([
                        'user_id'      => $user->id,
                        'title'        => $title,
                        'message'      => $message,
                        'type'         => $type,
                        'icon'         => 'ti-speakerphone',
                        'is_broadcast' => true,
                        'is_read'      => false,
                    ]);
                }
            });
    }
}