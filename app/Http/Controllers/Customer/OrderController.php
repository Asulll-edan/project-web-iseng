<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\RestaurantTable;
use App\Models\Voucher;
use App\Services\CartService;
use App\Services\MembershipService;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{

   private $orderService;
private $cartService;
private $membershipService;

public function __construct(
    OrderService $orderService,
    CartService $cartService,
    MembershipService $membershipService
) {
    $this->orderService = $orderService;
    $this->cartService = $cartService;
    $this->membershipService = $membershipService;
}

    public function checkout()
    {
        $cart = $this->cartService->getCartWithItems(Auth::user());

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart')->with('error', 'Keranjang belanja kosong.');
        }

        $tables  = RestaurantTable::available()->get();
        $wallet  = Auth::user()->wallet;

        return view('customer.order.checkout', compact('cart', 'tables', 'wallet'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:wallet,cash,transfer',
            'order_type'     => 'required|in:dine_in,takeaway',
            'table_number'   => 'nullable|string',
            'notes'          => 'nullable|string|max:500',
            'voucher_code'   => 'nullable|string',
        ]);

        try {
            $order = $this->orderService->createFromCart(Auth::user(), $request->only(
                'payment_method', 'order_type', 'table_number', 'notes', 'voucher_code'
            ));

            return redirect()->route('orders.show', $order->id)
                ->with('success', "Order #{$order->order_number} berhasil dibuat! 🎉");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function index()
    {
        $orders = Order::where('user_id', Auth::id())
            ->with(['items', 'payment'])
            ->latest()
            ->paginate(10);

        return view('customer.order.index', compact('orders'));
    }

    public function show(int $id)
    {
        $order = Order::where('user_id', Auth::id())
            ->with(['items.menu', 'statusLogs', 'payment'])
            ->findOrFail($id);

        return view('customer.order.show', compact('order'));
    }

    public function tracking(int $id)
    {
        $order = Order::where('user_id', Auth::id())
            ->with(['items', 'statusLogs.user'])
            ->findOrFail($id);

        return view('customer.order.tracking', compact('order'));
    }

    public function complete(int $id)
    {
        $order = Order::where('user_id', Auth::id())
            ->where('status', Order::STATUS_SELESAI)
            ->findOrFail($id);

        $this->orderService->updateStatus($order, Order::STATUS_COMPLETED, Auth::user(), 'Diselesaikan oleh customer');
        $this->membershipService->processOrderCompletion($order);

        return response()->json([
            'success' => true,
            'message' => 'Pesanan selesai! Poin loyalty & cashback sudah ditambahkan. 🎉',
            'points'  => $order->fresh()->loyalty_points_earned,
            'cashback'=> $order->fresh()->cashback_earned,
        ]);
    }

    public function checkVoucher(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        $voucher = Voucher::where('code', strtoupper($request->code))->first();

        if (!$voucher || !$voucher->isValid()) {
            return response()->json(['valid' => false, 'message' => 'Voucher tidak valid atau sudah kadaluarsa.']);
        }

        $cart     = $this->cartService->getCartWithItems(Auth::user());
        $discount = $voucher->calculateDiscount($cart->total);

        if ($discount === 0) {
            return response()->json([
                'valid'   => false,
                'message' => "Minimum order Rp " . number_format($voucher->min_order, 0, ',', '.'),
            ]);
        }

        return response()->json([
            'valid'    => true,
            'message'  => "Voucher valid! Hemat Rp " . number_format($discount, 0, ',', '.'),
            'discount' => $discount,
            'voucher'  => $voucher->only('code', 'name', 'type', 'value'),
        ]);
    }

    public function statusPoll(int $id)
    {
        $order = Order::where('user_id', Auth::id())->findOrFail($id);
        return response()->json([
            'status'       => $order->status,
            'status_label' => $order->status_label,
            'status_color' => $order->status_color,
            'updated_at'   => $order->updated_at->diffForHumans(),
        ]);
    }
}