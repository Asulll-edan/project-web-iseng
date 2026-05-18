<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Order;

class KasirController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'menunggu'  => Order::where('status', 'menunggu')->count(),
            'cooking'   => Order::where('status', 'cooking')->count(),
            'selesai'   => Order::where('status', 'selesai')->count(),
            'today'     => Order::whereDate('created_at', today())->count(),
            'revenue'   => Order::whereDate('created_at', today())->where('payment_status', 'paid')->sum('total_amount'),
        ];

        $recentOrders = Order::with(['user', 'items'])
            ->whereDate('created_at', today())
            ->latest()
            ->take(10)
            ->get();

        return view('kasir.dashboard', compact('stats', 'recentOrders'));
    }
}