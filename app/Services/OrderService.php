<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusLog;
use App\Models\Payment;
use App\Models\Voucher;
use App\Models\Menu;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Services\WalletService;
class OrderService
{
    private $walletService;
    private $notificationService;
    public function __construct(WalletService $walletService, 
    NotificationService $notificationService) {
$this->walletService=$walletService;
$this->notificationService=$notificationService;

    }

    public function createFromCart(User $user, array $data): Order
    {
        return DB::transaction(function () use ($user, $data) {
            $cart = Cart::where('user_id', $user->id)->with('items.menu')->firstOrFail();

            if ($cart->items->isEmpty()) {
                throw new \Exception('Keranjang belanja kosong.');
            }

            // Validate stock
            foreach ($cart->items as $item) {
                if ($item->menu->stock < $item->quantity) {
                    throw new \Exception("Stok {$item->menu->name} tidak mencukupi.");
                }
            }

            $taxRate    = (float) Setting::get('tax_rate', 10);
            $subtotal   = $cart->total;
            $discount   = 0;
            $voucherCode= null;

            // Apply voucher
            if (!empty($data['voucher_code'])) {
                $voucher = Voucher::where('code', $data['voucher_code'])->first();
                if ($voucher && $voucher->isValid()) {
                    $discount    = $voucher->calculateDiscount($subtotal);
                    $voucherCode = $voucher->code;
                    $voucher->increment('used_count');
                }
            }

            $taxAmount = ($subtotal - $discount) * ($taxRate / 100);
            $total     = $subtotal - $discount + $taxAmount;

            // Deduct wallet if payment method is wallet
            if ($data['payment_method'] === 'wallet') {
                $this->walletService->debit($user, $total, 'Pembayaran order');
            }

            // Create order
            $order = Order::create([
                'user_id'         => $user->id,
                'order_number'    => Order::generateOrderNumber(),
                'status'          => Order::STATUS_MENUNGGU,
                'order_type'      => $data['order_type'] ?? 'dine_in',
                'subtotal'        => $subtotal,
                'discount_amount' => $discount,
                'tax_amount'      => $taxAmount,
                'total_amount'    => $total,
                'payment_method'  => $data['payment_method'],
                'payment_status'  => $data['payment_method'] === 'wallet' ? 'paid' : 'pending',
                'voucher_code'    => $voucherCode,
                'notes'           => $data['notes'] ?? null,
                'table_number'    => $data['table_number'] ?? null,
            ]);

            // Create order items & deduct stock
            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id'  => $order->id,
                    'menu_id'   => $item->menu_id,
                    'menu_name' => $item->menu->name,
                    'price'     => $item->price,
                    'quantity'  => $item->quantity,
                    'subtotal'  => $item->subtotal,
                    'note'      => $item->note,
                ]);
                $item->menu->decrement('stock', $item->quantity);
                $item->menu->increment('order_count', $item->quantity);
            }

            // Create payment record
            Payment::create([
                'order_id'     => $order->id,
                'user_id'      => $user->id,
                'payment_code' => Payment::generatePaymentCode(),
                'amount'       => $total,
                'method'       => $data['payment_method'],
                'status'       => $data['payment_method'] === 'wallet' ? 'paid' : 'pending',
                'paid_at'      => $data['payment_method'] === 'wallet' ? now() : null,
            ]);

            // Log status
            OrderStatusLog::create([
                'order_id' => $order->id,
                'user_id'  => $user->id,
                'status'   => Order::STATUS_MENUNGGU,
                'note'     => 'Order dibuat oleh customer',
            ]);

            // Clear cart
            $cart->items()->delete();

            // Notify kasir
            $this->notificationService->notifyNewOrder($order);

            return $order;
        });
    }

    public function updateStatus(Order $order, string $newStatus, User $updatedBy, ?string $note = null): Order
    {
        $order->update(['status' => $newStatus]);

        if ($newStatus === Order::STATUS_COMPLETED) {
            $order->update(['completed_at' => now()]);
        }

        OrderStatusLog::create([
            'order_id' => $order->id,
            'user_id'  => $updatedBy->id,
            'status'   => $newStatus,
            'note'     => $note ?? "Status diubah ke {$newStatus}",
        ]);

        // Notify customer
        $this->notificationService->notifyOrderStatus($order);

        return $order;
    }
}