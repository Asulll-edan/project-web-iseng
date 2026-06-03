<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\Menu;
use App\Models\TopupRequest;
use App\Models\Reservation;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_orders_today'   => Order::whereDate('created_at', today())->count(),
            'revenue_today' => Order::whereDate('created_at', today())
                ->whereIn('status', ['selesai', 'completed'])
                ->sum('total_amount'),
            'active_orders'        => Order::whereIn('status', ['menunggu', 'cooking'])->count(),
            'total_customers'      => User::where('role', 'customer')->count(),
            'pending_topup'        => TopupRequest::where('status', 'pending')->count(),
            'pending_reservation'  => Reservation::where('status', 'pending')->count(),
            'total_revenue_month' => Order::whereBetween('created_at', [
                now()->startOfMonth(),
                now()->endOfMonth()
            ])
                ->whereIn('payment_status', ['paid', 'pending'])
                ->whereIn('status', ['selesai', 'completed'])
                ->sum('total_amount'),

            'new_customers_month' => User::where('role', 'customer')
                ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->count(),
        ];

        // Revenue last 7 days
        $revenueChart = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $revenueChart[] = [
                'date'    => $date->format('d M'),
                'revenue' => (float) Order::whereDate('created_at', $date->toDateString())
->whereIn('status', ['selesai','completed'])->sum('total_amount'),
                'orders'  => Order::whereDate('created_at', $date->toDateString())->count(),
            ];
        }

        $recentOrders = Order::with(['user', 'items'])
            ->latest()->take(8)->get();

        $topMenus = Menu::orderBy('order_count', 'desc')->take(5)->get();

        $recentTopups = TopupRequest::with('user')
            ->where('status', 'pending')->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'stats',
            'revenueChart',
            'recentOrders',
            'topMenus',
            'recentTopups'
        ));
    }
}
