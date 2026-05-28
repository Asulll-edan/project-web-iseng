<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatusLog;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderManageController extends Controller
{
private $notif;

public function __construct(NotificationService $notif){
    $this->notif=$notif;

}
    public function index(Request $request)
    {
        $query = Order::with(['user', 'items.menu'])->latest();

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        } else {
            $query->whereIn('status', ['menunggu', 'cooking', 'selesai']);
        }

        if ($request->filled('search')) {
            $query->where('order_number', 'ilike', "%{$request->search}%");
        }

        if ($request->ajax() || $request->wantsJson()) {
            $orders = $query->get();
            return response()->json([
                'orders' => $orders->map(fn($o) => $this->formatOrder($o)),
                'counts' => [
                    'menunggu' => Order::where('status', 'menunggu')->count(),
                    'cooking'  => Order::where('status', 'cooking')->count(),
                    'selesai'  => Order::where('status', 'selesai')->count(),
                ],
            ]);
        }

        $orders = $query->paginate(20);
        return view('kasir.orders.index', compact('orders'));
    }

    public function show(int $id)
    {
        $order = Order::with(['user', 'items.menu', 'statusLogs.user', 'payment'])->findOrFail($id);
        return view('kasir.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, int $id)
    {
        $request->validate([
            'status' => 'required|in:cooking,selesai,dibatalkan',
        ]);

        $order = Order::findOrFail($id);

        // Validate transition
        $allowed = [
            'menunggu' => ['cooking', 'dibatalkan'],
            'cooking'  => ['selesai'],
            'selesai'  => [],
        ];

        if (!in_array($request->status, $allowed[$order->status] ?? [])) {
            return response()->json(['success' => false, 'message' => 'Transisi status tidak valid.'], 422);
        }

        $order->update(['status' => $request->status]);

        OrderStatusLog::create([
            'order_id' => $order->id,
            'user_id'  => Auth::id(),
            'status'   => $request->status,
            'note'     => "Diubah oleh kasir " . Auth::user()->name,
        ]);

        $this->notif->notifyOrderStatus($order);

        return response()->json([
            'success' => true,
            'message' => "Order #{$order->order_number} → {$order->status_label}",
            'status'  => $order->status,
            'label'   => $order->status_label,
            'color'   => $order->status_color,
        ]);
    }

    public function poll()
    {
        $orders = Order::with(['user', 'items'])
            ->whereIn('status', ['menunggu', 'cooking', 'selesai'])
            ->latest()
            ->get();

        return response()->json([
            'orders' => $orders->map(fn($o) => $this->formatOrder($o)),
            'counts' => [
                'menunggu' => $orders->where('status', 'menunggu')->count(),
                'cooking'  => $orders->where('status', 'cooking')->count(),
                'selesai'  => $orders->where('status', 'selesai')->count(),
            ],
        ]);
    }

    private function formatOrder(Order $order): array
    {
        return [
            'id'             => $order->id,
            'order_number'   => $order->order_number,
            'status'         => $order->status,
            'status_label'   => $order->status_label,
            'status_color'   => $order->status_color,
            'customer_name'  => $order->user->name,
            'total_amount'   => 'Rp ' . number_format($order->total_amount, 0, ',', '.'),
            'items_count'    => $order->items->sum('quantity'),
            'payment_method' => $order->payment_method,
            'notes'          => $order->notes,
            'table_number'   => $order->table_number,
            'time_ago'       => $order->created_at->diffForHumans(),
            'items'          => $order->items->map(fn($i) => [
                'name'     => $i->menu_name,
                'quantity' => $i->quantity,
                'note'     => $i->note,
            ]),
        ];
    }
}