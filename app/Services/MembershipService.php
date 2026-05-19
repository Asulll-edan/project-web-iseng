<?php

namespace App\Services;

use App\Models\User;
use App\Models\Membership;
use App\Models\LoyaltyPoint;
use App\Models\LoyaltyPointLog;
use App\Models\CashbackLog;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use App\Services\WalletService;


class MembershipService
{
private $walletService;

public function __construct(WalletService $walletService){
    $this->walletService=$walletService;
}
    /**
     * Called when an order is marked as completed by customer.
     */
    public function processOrderCompletion(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $user = $order->user;

            // 1. Loyalty Points
            $pointsPerOrder = (int) Setting::get('points_per_order', 20);
            $this->addLoyaltyPoints($user, $order, $pointsPerOrder);

            // 2. Cashback
            $this->processCashback($user, $order);

            // 3. Increment completed orders & check upgrade
            $membership = $user->membership;
            if ($membership) {
                $membership->increment('completed_orders');
                $membership->refresh();
                $membership->checkAndUpgradeTier();
            }
        });
    }

    private function addLoyaltyPoints(User $user, Order $order, int $points): void
    {
        $lp = LoyaltyPoint::firstOrCreate(['user_id' => $user->id], [
            'total_points' => 0, 'used_points' => 0, 'available_points' => 0,
        ]);

        $lp->increment('total_points', $points);
        $lp->increment('available_points', $points);

        LoyaltyPointLog::create([
            'user_id'     => $user->id,
            'order_id'    => $order->id,
            'points'      => $points,
            'type'        => 'earn',
            'description' => "Poin dari order #{$order->order_number}",
        ]);

        $order->update(['loyalty_points_earned' => $points]);
    }

    private function processCashback(User $user, Order $order): void
    {
        $membership = $user->membership;
        if (!$membership || $membership->tier === Membership::TIER_NONE) return;
        if ($membership->cashback_rate <= 0) return;

        $cashback = round($order->total_amount * ($membership->cashback_rate / 100));
        if ($cashback <= 0) return;

        $this->walletService->credit(
            $user,
            $cashback,
            "Cashback {$membership->cashback_rate}% dari order #{$order->order_number}",
            $order->id
        );

        CashbackLog::create([
            'user_id'        => $user->id,
            'order_id'       => $order->id,
            'amount'         => $cashback,
            'cashback_rate'  => $membership->cashback_rate,
            'membership_tier'=> $membership->tier,
            'description'    => "Cashback order #{$order->order_number}",
        ]);

        $order->update(['cashback_earned' => $cashback]);
    }
}