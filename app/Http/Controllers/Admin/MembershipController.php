<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Membership;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class MembershipController extends Controller
{
    private $notif;

public function __construct(NotificationService $notif)
{
    $this->notif = $notif;
}

    public function index(Request $request)
    {
        $query = Membership::with('user')->latest();

        if ($request->filled('tier')) {
            $query->where('tier', $request->tier);
        }
        if ($request->filled('search')) {
            $query->whereHas('user', fn($q) => $q->where('name','ilike','%'.$request->search.'%')
                ->orWhere('email','ilike','%'.$request->search.'%'));
        }

        $memberships = $query->paginate(20)->withQueryString();

        $tierCounts = Membership::selectRaw('tier, count(*) as total')
            ->groupBy('tier')->pluck('total','tier');

        return view('admin.membership.index', compact('memberships','tierCounts'));
    }

    public function approvePlatinum(int $id)
    {
        $membership = Membership::with('user')->findOrFail($id);

        $membership->update([
            'tier'                => 'platinum',
            'cashback_rate'       => 10,
            'tier_achieved_at'    => now(),
            'platinum_expires_at' => now()->addYear(),
            'is_active'           => true,
        ]);

        $this->notif->send(
            $membership->user_id,
            'Selamat! Kamu Platinum Member 💎',
            'Membership Platinum kamu telah diaktifkan. Nikmati cashback 10% dan berbagai keuntungan eksklusif!',
            'success',
            '/membership',
            'ti-award'
        );

        return response()->json(['success' => true, 'message' => 'Platinum membership disetujui.']);
    }
}
