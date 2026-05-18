<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\TopupRequest;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class WalletController extends Controller
{
    public function index()
    {
        $user         = Auth::user();
        $wallet       = $user->wallet;
        $transactions = $wallet ? $wallet->transactions()->paginate(15) : collect();
        $topupHistory = TopupRequest::where('user_id', $user->id)->latest()->take(5)->get();

        return view('customer.wallet.index', compact('wallet', 'transactions', 'topupHistory'));
    }

    public function topupForm()
    {
        $wallet = Auth::user()->wallet;
        return view('customer.wallet.topup', compact('wallet'));
    }

    public function topup(Request $request)
    {
        $request->validate([
            'amount'         => 'required|integer|min:10000|max:10000000',
            'payment_method' => 'required|in:transfer_bca,transfer_mandiri,transfer_bni,transfer_bri',
            'proof_image'    => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $user   = Auth::user();
        $wallet = $user->wallet;

        // Check for pending topup
        $pending = TopupRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->exists();

        if ($pending) {
            return back()->with('error', 'Kamu masih punya topup yang menunggu konfirmasi.');
        }

        $path = $request->file('proof_image')->store('topup-proofs', 'public');

        TopupRequest::create([
            'user_id'          => $user->id,
            'wallet_id'        => $wallet->id,
            'transaction_code' => TopupRequest::generateCode(),
            'amount'           => $request->amount,
            'payment_method'   => $request->payment_method,
            'proof_image'      => $path,
            'status'           => 'pending',
            'expires_at'       => now()->addHours(24),
        ]);

        return redirect()->route('wallet.index')
            ->with('success', 'Bukti topup berhasil dikirim! Admin akan mengkonfirmasi dalam 1x24 jam. ✅');
    }
}