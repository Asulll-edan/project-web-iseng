<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\WalletTransaction;
use App\Models\TopupRequest;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class PaymentHistoryController extends Controller
{
    public function index(Request $request)
    {
        // Orders payments
        /** @var Builder $query */

        $query = Payment::with(['user','order'])->latest();

        if ($request->filled('method'))  $query->where('method', $request->method);
        if ($request->filled('status'))  $query->where('status', $request->status);
        if ($request->filled('date'))    $query->whereDate('created_at', $request->date);
        if ($request->filled('search'))  $query->where('payment_code', 'ilike', '%'.$request->search.'%');

        $payments = $query->paginate(20)->withQueryString();

        // Summary per method
        $summary = Payment::where('status','success')
            ->selectRaw('method, count(*) as total_tx, sum(amount) as total_amount')
            ->groupBy('method')
            ->get();

        // Wallet topups
        $topups = TopupRequest::with('user')
            ->where('status','approved')
            ->when($request->filled('date'), fn($q) => $q->whereDate('approved_at', $request->date))
            ->latest('approved_at')
            ->take(20)->get();

        return view('admin.payments.history', compact('payments','summary','topups'));
    }
}