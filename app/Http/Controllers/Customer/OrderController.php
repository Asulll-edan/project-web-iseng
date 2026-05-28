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
use Illuminate\Support\Facades\DB;


class OrderController extends Controller
{
  
 private $orderService;
        private $cartService;
        private $membershipService;
    public function __construct(OrderService $orderService, CartService $cartService, MembershipService $membershipService)
    {
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
            'payment_method' => 'required|in:wallet,cash,transfer,qris',
            'order_type'     => 'required|in:dine_in,takeaway',
            'table_number'   => 'nullable|string',
            'notes'          => 'nullable|string|max:500',
            'voucher_code'   => 'nullable|string',
        ]);

        try {
            $data = $request->only('payment_method','order_type','table_number','notes','voucher_code');

            // Handle selected items from cart
            if ($request->filled('selected_items')) {
                $data['selected_items'] = array_filter(explode(',', $request->selected_items));
            }

            $order = $this->orderService->createFromCart(Auth::user(), $data);

            // Redirect ke instruksi pembayaran untuk transfer & qris
            if (in_array($order->payment_method, ['transfer','qris'])) {
                $bank = $request->get('bank_code','');
                return redirect()->route('orders.payment-instruction', $order->id)
                    ->with('success', "Order #{$order->order_number} dibuat! Selesaikan pembayaran.");
            }

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
    try {
        $order = Order::where('user_id', Auth::id())
            ->whereIn('status', [Order::STATUS_SELESAI, Order::STATUS_COMPLETED])
            ->findOrFail($id);

        DB::transaction(function () use ($order) {
            if ($order->status !== Order::STATUS_COMPLETED) {
                $this->orderService->updateStatus(
                    $order,
                    Order::STATUS_COMPLETED,
                    Auth::user(),
                    'Diselesaikan oleh customer'
                );
            }

             if ($order->payment) {
        $order->payment->update([
            'status'  => 'success',
            'paid_at' => now(),
        ]);
    }
            $this->membershipService->processOrderCompletion($order);
        });

        return response()->json([
            'success'  => true,
            'message'  => 'Pesanan selesai! Poin loyalty & cashback sudah ditambahkan. 🎉',
            'points'   => $order->fresh()->loyalty_points_earned,
            'cashback' => $order->fresh()->cashback_earned,
        ]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
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

    public function paymentInstruction(int $id)
    {
        $order = Order::where('user_id', Auth::id())
            ->with(['items.menu','payment'])
            ->findOrFail($id);
        return view('customer.order.payment-instruction', compact('order'));
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