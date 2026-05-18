<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Models\Membership;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function index()
    {
        return view('admin.analytics.index');
    }

    public function data(Request $request)
    {
        $period = $request->get('period', '7'); // days

        // Revenue per day
        $revenue = [];
        for ($i = (int)$period - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $revenue[] = [
                'date'    => $date->format('d M'),
                'revenue' => (float) Order::whereDate('created_at', $date->toDateString())
                    ->where('payment_status','paid')->sum('total_amount'),
                'orders'  => Order::whereDate('created_at', $date->toDateString())->count(),
            ];
        }

        // Orders by status
        $orderStatus = Order::selectRaw('status, count(*) as total')
            ->groupBy('status')->pluck('total','status');

        // New customers per day
        $customers = [];
        for ($i = (int)$period - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $customers[] = [
                'date'  => $date->format('d M'),
                'count' => User::whereDate('created_at', $date->toDateString())->where('role','customer')->count(),
            ];
        }

        // Membership distribution
        $memberDist = Membership::selectRaw('tier, count(*) as total')
            ->groupBy('tier')->pluck('total','tier');

        // Top menus
        $topMenus = \App\Models\Menu::orderBy('order_count','desc')
            ->take(5)->get(['name','order_count','rating']);

        // Wallet stats
        $walletStats = [
            'total_topup'  => (float) WalletTransaction::where('type','credit')->sum('amount'),
            'total_spent'  => (float) WalletTransaction::where('type','debit')->sum('amount'),
            'transactions' => WalletTransaction::count(),
        ];

        return response()->json(compact(
            'revenue','orderStatus','customers','memberDist','topMenus','walletStats'
        ));
    }
}
