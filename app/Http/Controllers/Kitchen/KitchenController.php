<?php

namespace App\Http\Controllers\Kitchen;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatusLog;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KitchenController extends Controller
{
    public function __construct(private NotificationService $notif) {}

    public function display()
    {
        return view('kitchen.display');
    }

    public function orders()
    {
        $orders = Order::with(['items.menu', 'user'])
            ->whereIn('status', ['menunggu', 'cooking'])
            ->orderByRaw("CASE status WHEN 'cooking' THEN 0 WHEN 'menunggu' THEN 1 END")
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($order) {
                return [
                    'id'           => $order->id,
                    'order_number' => $order->order_number,
                    'status'       => $order->status,
                    'status_label' => $order->status_label,
                    'customer'     => $order->user->name,
                    'table_number' => $order->table_number,
                    'notes'        => $order->notes,
                    'time_ago'     => $order->created_at->diffForHumans(),
                    'minutes_ago'  => $order->created_at->diffInMinutes(now()),
                    'items'        => $order->items->map(fn($i) => [
                        'name'     => $i->menu_name,
                        'quantity' => $i->quantity,
                        'note'     => $i->note,
                    ]),
                ];
            });

        return response()->json(['orders' => $orders]);
    }

    public function markDone(int $id)
    {
        $order = Order::whereIn('status', ['menunggu', 'cooking'])->findOrFail($id);

        $newStatus = $order->status === 'menunggu' ? 'cooking' : 'selesai';
        $order->update(['status' => $newStatus]);

        OrderStatusLog::create([
            'order_id' => $order->id,
            'user_id'  => Auth::id(),
            'status'   => $newStatus,
            'note'     => 'Diupdate oleh kitchen ' . Auth::user()->name,
        ]);

        $this->notif->notifyOrderStatus($order);

        return response()->json([
            'success' => true,
            'status'  => $newStatus,
            'message' => "Order #{$order->order_number} → {$order->status_label}",
        ]);
    }
}