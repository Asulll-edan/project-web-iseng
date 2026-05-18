<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderTrackingController extends Controller
{
    public function track(int $id)
    {
        $order = Order::where('user_id', Auth::id())
            ->with(['statusLogs' => fn($q) => $q->latest(), 'items'])
            ->findOrFail($id);

        return response()->json([
            'order_number' => $order->order_number,
            'status'       => $order->status,
            'status_label' => $order->status_label,
            'status_color' => $order->status_color,
            'estimated'    => $order->estimated_time,
            'updated_at'   => $order->updated_at->diffForHumans(),
            'logs'         => $order->statusLogs->map(fn($l) => [
                'status'  => $l->status,
                'note'    => $l->note,
                'time'    => $l->created_at->format('H:i'),
            ]),
        ]);
    }
}