@extends('layouts.app')
@section('title','SOHIBA Wallet')

@push('styles')
<style>
.page-top{padding:110px 0 40px;background:linear-gradient(135deg,#1a2e22,#2c3e35)}
.wallet-card{background:linear-gradient(135deg,var(--sage-dark) 0%,var(--sage) 60%,#8aaa92 100%);border-radius:24px;padding:32px;color:#fff;position:relative;overflow:hidden;margin-bottom:28px}
.wallet-card::before{content:'';position:absolute;top:-40px;right:-40px;width:200px;height:200px;border-radius:50%;background:rgba(255,255,255,.06)}
.wallet-card::after{content:'';position:absolute;bottom:-60px;left:-30px;width:160px;height:160px;border-radius:50%;background:rgba(255,255,255,.04)}
.txn-item{display:flex;align-items:center;gap:14px;padding:14px 0;border-bottom:1px solid var(--border)}
.txn-item:last-child{border:none}
.txn-icon{width:40px;height:40px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0}
</style>
@endpush

@section('content')
<div class="page-top">
    <div class="container">
        <h1 style="font-family:'Playfair Display',serif;font-size:28px;font-weight:600;color:#fff">SOHIBA Wallet</h1>
        <p style="font-size:13px;color:rgba(255,255,255,.6);margin-top:6px">Dompet digital premium kamu</p>
    </div>
</div>

<div class="container" style="padding:36px 0 80px;max-width:800px">
    {{-- Wallet Card --}}
    <div class="wallet-card" data-aos="fade-up">
        <div style="position:relative;z-index:1">
            <div style="font-size:12px;opacity:.7;text-transform:uppercase;letter-spacing:.1em;margin-bottom:8px">Saldo Kamu</div>
            <div style="font-family:'Playfair Display',serif;font-size:clamp(28px,6vw,42px);font-weight:600;margin-bottom:20px">
                Rp {{ number_format($wallet ? $wallet->balance : 0,0,',','.') }}
            </div>
            <div style="display:flex;gap:32px;flex-wrap:wrap">
                <div><div style="font-size:11px;opacity:.65;margin-bottom:3px">Total Topup</div><div style="font-weight:700">Rp {{ number_format($wallet ? $wallet->total_topup : 0,0,',','.') }}</div></div>
                <div><div style="font-size:11px;opacity:.65;margin-bottom:3px">Total Digunakan</div><div style="font-weight:700">Rp {{ number_format($wallet ? $wallet->total_spent : 0,0,',','.') }}</div></div>
            </div>
            <div style="margin-top:24px;display:flex;gap:12px">
                <a href="{{ route('wallet.topup') }}" style="padding:10px 24px;background:rgba(255,255,255,.2);color:#fff;border-radius:20px;font-weight:600;font-size:14px;backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,.2);transition:all .2s;display:inline-flex;align-items:center;gap:8px"
                   onmouseover="this.style.background='rgba(255,255,255,.3)'" onmouseout="this.style.background='rgba(255,255,255,.2)'">
                    <i class="ti ti-plus"></i> Topup Saldo
                </a>
            </div>
        </div>
    </div>

    {{-- Pending topups --}}
    @if($topupHistory->where('status','pending')->count())
    <div style="background:rgba(245,158,11,.08);border:1px solid rgba(245,158,11,.2);border-radius:14px;padding:16px;margin-bottom:24px">
        <div style="font-weight:600;font-size:14px;color:#b45309;margin-bottom:6px"><i class="ti ti-clock" style="font-size:15px"></i> Topup Menunggu Konfirmasi</div>
        @foreach($topupHistory->where('status','pending') as $top)
        <div style="font-size:13px;color:#92400e">{{ $top->transaction_code }} · Rp {{ number_format($top->amount,0,',','.') }} · {{ $top->created_at->diffForHumans() }}</div>
        @endforeach
    </div>
    @endif

    {{-- Transactions --}}
    <div style="background:var(--warm-white);border-radius:20px;border:1px solid var(--border);padding:24px">
        <h3 style="font-weight:700;font-size:15px;margin-bottom:20px">Riwayat Transaksi</h3>
        @forelse($transactions as $txn)
        <div class="txn-item">
            <div class="txn-icon" style="background:{{ $txn->type === 'credit' ? 'rgba(74,222,128,.1)' : 'rgba(239,68,68,.1)' }}">
                <i class="ti ti-{{ $txn->type === 'credit' ? 'arrow-down' : 'arrow-up' }}" style="font-size:18px;color:{{ $txn->type === 'credit' ? '#16a34a' : '#dc2626' }}"></i>
            </div>
            <div style="flex:1;min-width:0">
                <div style="font-weight:600;font-size:13px">{{ $txn->description }}</div>
                <div style="font-size:12px;color:var(--text-muted)">{{ $txn->transaction_code }} · {{ $txn->created_at->format('d M Y H:i') }}</div>
            </div>
            <div style="font-weight:700;font-size:14px;flex-shrink:0;color:{{ $txn->type === 'credit' ? '#16a34a' : '#dc2626' }}">
                {{ $txn->type === 'credit' ? '+' : '-' }} Rp {{ number_format($txn->amount,0,',','.') }}
            </div>
        </div>
        @empty
        <div style="text-align:center;padding:32px;color:var(--text-muted)">
            <i class="ti ti-wallet" style="font-size:36px;display:block;margin-bottom:10px;opacity:.4"></i>
            Belum ada transaksi
        </div>
        @endforelse
        @if($transactions->hasPages())
        <div style="margin-top:20px">{{ $transactions->links() }}</div>
        @endif
    </div>
</div>
@endsection
