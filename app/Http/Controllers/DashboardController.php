<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\Menu;
use App\Models\Wallet;
use App\Models\Membership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_orders_today'   => Order::whereDate('created_at', today())->count(),
            'revenue_today'        => Order::where('status', 'completed')->whereDate('completed_at', today())->sum('total_amount'),
            'total_customers'      => User::where('role', 'customer')->count(),
            'active_orders'        => Order::whereIn('status', ['menunggu', 'cooking', 'selesai'])->count(),
            'total_revenue_month'  => Order::where('status', 'completed')->whereMonth('completed_at', now()->month)->sum('total_amount'),
            'total_orders_month'   => Order::whereMonth('created_at', now()->month)->count(),
            'pending_topups'       => \App\Models\TopupRequest::where('status', 'pending')->count(),
            'pending_reservations' => \App\Models\Reservation::where('status', 'pending')->count(),
            'total_menus'          => Menu::where('is_available', true)->count(),
            'low_stock_menus'      => Menu::where('stock', '<', 10)->where('is_available', true)->count(),
        ];

        $recentOrders = Order::with('user')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $topMenus = Menu::orderBy('order_count', 'desc')->take(5)->get();
        $recentUsers = User::where('role', 'customer')->orderBy('created_at', 'desc')->take(5)->get();

        // Chart data: last 7 days revenue
        $revenueChart = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $revenueChart[] = [
                'date'    => $date->format('d M'),
                'revenue' => Order::where('status', 'completed')->whereDate('completed_at', $date)->sum('total_amount'),
                'orders'  => Order::whereDate('created_at', $date)->count(),
            ];
        }

        return view('admin.dashboard', compact('stats', 'recentOrders', 'topMenus', 'recentUsers', 'revenueChart'));
    }

    public function analytics()
    {
        return view('admin.analytics');
    }

    public function stats()
    {
        $revenueByMonth = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $revenueByMonth[] = [
                'month'   => $date->format('M Y'),
                'revenue' => Order::where('status', 'completed')->whereYear('completed_at', $date->year)->whereMonth('completed_at', $date->month)->sum('total_amount'),
                'orders'  => Order::whereYear('created_at', $date->year)->whereMonth('created_at', $date->month)->count(),
            ];
        }

        $membershipDist = [
            'none'     => Membership::where('tier', 'none')->count(),
            'silver'   => Membership::where('tier', 'silver')->count(),
            'gold'     => Membership::where('tier', 'gold')->count(),
            'platinum' => Membership::where('tier', 'platinum')->count(),
        ];

        $topMenus = Menu::select('name', 'order_count', 'rating')
            ->orderBy('order_count', 'desc')
            ->take(10)
            ->get();

        return response()->json([
            'revenue_by_month' => $revenueByMonth,
            'membership_dist'  => $membershipDist,
            'top_menus'        => $topMenus,
            'live_stats'       => [
                'active_orders'    => Order::whereIn('status', ['menunggu', 'cooking', 'selesai'])->count(),
                'revenue_today'    => Order::where('status', 'completed')->whereDate('completed_at', today())->sum('total_amount'),
                'new_customers'    => User::where('role', 'customer')->whereDate('created_at', today())->count(),
                'pending_topups'   => \App\Models\TopupRequest::where('status', 'pending')->count(),
            ],
        ]);
    }
}