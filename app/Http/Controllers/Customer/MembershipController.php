<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Membership;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class MembershipController extends Controller
{
    public function index()
    {
        $user       = User::with([
            'membership',
            'loyaltyPoint',
            'cashbackLogs' => function($q) { $q->latest()->take(10); }
        ])->find(Auth::id());

        $membership = $user->membership;
        $tiers      = Membership::TIERS;

        $ordersToNextTier = null;
        if ($membership && $membership->tier !== 'platinum') {
            $nextTier = $membership->next_tier;
            if ($nextTier) {
                $ordersToNextTier = max(0, $nextTier['min_orders'] - $membership->completed_orders);
            }
        }

        return view('customer.membership.index', compact('user', 'membership', 'tiers', 'ordersToNextTier'));
    }
}