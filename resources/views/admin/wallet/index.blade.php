@extends('layouts.admin')
@section('title','Kelola Wallet')
@section('page-title','SOHIBA Wallet')

@push('styles')
<style>
.wallet-stat{background:var(--warm);border-radius:var(--r);border:1px solid var(--border);padding:20px;display:flex;align-items:center;gap:14px}
.wallet-icon{width:48px;height:48px;border-radius:14px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
</style>
@endpush

@section('content')
<div class="page-header">
    <div>
        <div class="page-title">SOHIBA Wallet</div>
        <div class="page-sub">Monitor saldo dan transaksi wallet seluruh user</div>
    </div>
    <a href="{{ route('admin.wallet.topup-requests') }}" class="btn-primary">
        <i class="ti ti-wallet"></i> Topup Requests
        @if($pendingCount > 0)
        <span style="background:rgba(255,255,255,.25);padding:1px 8px;border-radius:10px;font-size:11px">{{ $pendingCount }}</span>
        @endif
    </a>
</div>

{{-- Stats --}}
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:24px">
    @foreach([
        ['ti-database','rgba(90,124,101,.1)','var(--sage)','Total Saldo Aktif','Rp '.number_format($totalBalance,0,',','.'),'Saldo seluruh wallet'],
        ['ti-arrow-up','rgba(59,130,246,.08)','#1d4ed8','Total Topup Masuk','Rp '.number_format($totalTopup,0,',','.'),'Semua waktu'],
        ['ti-shopping-bag','rgba(245,158,11,.1)','#b45309','Total Terpakai','Rp '.number_format($totalSpent,0,',','.'),'Pembayaran order'],
    ] as $s)
    <div class="wallet-stat">
        <div class="wallet-icon" style="background:{{ $s[1] }}">
            <i class="ti {{ $s[0] }}" style="font-size:22px;color:{{ $s[2] }}"></i>
        </div>
        <div>
            <div style="font-size:13px;color:var(--muted);margin-bottom:4px">{{ $s[3] }}</div>
            <div style="font-size:22px;font-weight:700;color:var(--text)">{{ $s[4] }}</div>
            <div style="font-size:11px;color:var(--muted);margin-top:2px">{{ $s[5] }}</div>
        </div>
    </div>
    @endforeach
</div>

{{-- Wallet list --}}
<div class="content-card">
    <div class="card-head">
        <div class="card-title"><i class="ti ti-users" style="color:var(--sage)"></i> Daftar Wallet User</div>
        <span style="font-size:12px;color:var(--muted)">{{ $wallets->total() }} wallet</span>
    </div>
    <div style="overflow-x:auto">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Saldo</th>
                    <th>Total Topup</th>
                    <th>Total Terpakai</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($wallets as $wallet)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px">
                            <img src="{{ $wallet->user->avatar_url }}" style="width:32px;height:32px;border-radius:50%;object-fit:cover" alt="">
                            <div>
                                <div style="font-weight:600;font-size:13px">{{ $wallet->user->name }}</div>
                                <div style="font-size:11px;color:var(--muted)">{{ $wallet->user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div style="font-weight:700;font-size:14px;color:var(--sage-dark)">Rp {{ number_format($wallet->balance,0,',','.') }}</div>
                    </td>
                    <td style="color:var(--muted);font-size:13px">Rp {{ number_format($wallet->total_topup,0,',','.') }}</td>
                    <td style="color:var(--muted);font-size:13px">Rp {{ number_format($wallet->total_spent,0,',','.') }}</td>
                    <td>
                        <span class="badge {{ $wallet->is_active ? 'badge-green' : 'badge-red' }}">
                            {{ $wallet->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('admin.users.show',$wallet->user_id) }}" class="btn-icon btn-icon-sage" title="Lihat User">
                            <i class="ti ti-eye" style="font-size:14px"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center;padding:30px;color:var(--muted)">Belum ada data wallet</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:16px 20px;border-top:1px solid var(--border)">{{ $wallets->links() }}</div>
</div>
@endsection
