<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatusLog;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
private $notif;
public function __construct(NotificationService $notif)
{
    $this->notif=$notif;
}
    public function index(Request $request)
    {
        $query = Order::with(['user','items'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->where('order_number', 'ilike', '%'.$request->search.'%');
        }
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $orders = $query->paginate(20)->withQueryString();

        $counts = [
            'menunggu'  => Order::where('status','menunggu')->count(),
            'cooking'   => Order::where('status','cooking')->count(),
            'selesai'   => Order::where('status','selesai')->count(),
            'completed' => Order::where('status','completed')->count(),
        ];

        return view('admin.orders.index', compact('orders', 'counts'));
    }

    public function show(int $id)
    {
        $order = Order::with(['user','items.menu','statusLogs.user','payment'])->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    public function cancel(int $id)
    {
        $order = Order::whereNotIn('status',['completed','dibatalkan'])->findOrFail($id);
        $order->update(['status' => 'dibatalkan']);

        OrderStatusLog::create([
            'order_id' => $order->id,
            'user_id'  => Auth::id(),
            'status'   => 'dibatalkan',
            'note'     => 'Dibatalkan oleh admin',
        ]);

        $this->notif->notifyOrderStatus($order);

        return response()->json(['success' => true, 'message' => 'Order dibatalkan.']);
    }
}
