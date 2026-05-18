<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TopupRequest;
use App\Models\Wallet;
use App\Services\NotificationService;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    public function __construct(
        private WalletService $walletService,
        private NotificationService $notif
    ) {}

    public function index()
    {
        $totalBalance  = Wallet::sum('balance');
        $totalTopup    = Wallet::sum('total_topup');
        $totalSpent    = Wallet::sum('total_spent');
        $pendingCount  = TopupRequest::where('status','pending')->count();
        $wallets       = Wallet::with('user')->latest()->paginate(20);

        return view('admin.wallet.index', compact(
            'totalBalance','totalTopup','totalSpent','pendingCount','wallets'
        ));
    }

    public function topupRequests(Request $request)
    {
        $query = TopupRequest::with('user')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $requests = $query->paginate(20)->withQueryString();
        return view('admin.wallet.topup-requests', compact('requests'));
    }

    public function approve(int $id)
    {
        $topup = TopupRequest::where('status','pending')->findOrFail($id);

        $this->walletService->credit(
            $topup->user,
            $topup->amount,
            'Topup wallet disetujui — ' . $topup->transaction_code,
            $topup->id
        );

        $topup->update([
            'status'      => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        $this->notif->send(
            $topup->user_id,
            'Topup Berhasil! 💰',
            'Topup Rp '.number_format($topup->amount,0,',','.').' telah dikonfirmasi.',
            'success',
            '/wallet',
            'ti-wallet'
        );

        return response()->json(['success' => true, 'message' => 'Topup disetujui, saldo masuk ke wallet user.']);
    }

    public function reject(Request $request, int $id)
    {
        $topup = TopupRequest::where('status','pending')->findOrFail($id);

        $topup->update([
            'status'      => 'rejected',
            'admin_note'  => $request->note ?? 'Ditolak oleh admin',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        $this->notif->send(
            $topup->user_id,
            'Topup Ditolak',
            'Topup Rp '.number_format($topup->amount,0,',','.').' ditolak. '.$topup->admin_note,
            'error',
            '/wallet',
            'ti-wallet'
        );

        return response()->json(['success' => true, 'message' => 'Topup ditolak.']);
    }
}
