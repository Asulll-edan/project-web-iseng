@extends('layouts.admin')
@section('title','Riwayat Pembayaran')
@section('page-title','Riwayat Pembayaran')

@push('styles')
<style>
.method-card{background:var(--warm);border-radius:var(--r);border:1px solid var(--border);padding:18px;display:flex;align-items:center;gap:14px;transition:all .2s}
.method-card:hover{box-shadow:var(--shadow-sm);transform:translateY(-1px)}
.method-icon{width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0}
.tabs-bar{display:flex;gap:4px;border-bottom:1px solid var(--border);margin-bottom:20px}
.tab-item{padding:10px 18px;font-size:13px;font-weight:600;color:var(--muted);cursor:pointer;border-bottom:2px solid transparent;margin-bottom:-1px;transition:all .2s;background:none;border-top:none;border-left:none;border-right:none}
.tab-item.active{color:var(--sage-dark);border-bottom-color:var(--sage)}
.tab-panel{display:none}.tab-panel.active{display:block}
</style>
@endpush

@section('content')
<div class="page-header">
    <div>
        <div class="page-title">Riwayat Pembayaran</div>
        <div class="page-sub">Monitor semua transaksi pembayaran per metode</div>
    </div>
    <div style="display:flex;gap:8px;align-items:center">
        <input type="date" class="form-input" style="width:auto;padding:8px 12px" value="{{ request('date') }}"
            onchange="window.location='?date='+this.value">
    </div>
</div>

{{-- Summary per metode --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:14px;margin-bottom:24px">
    @foreach([
        ['wallet','💳','SOHIBA Wallet','rgba(90,124,101,.1)'],
        ['cash','💵','Tunai / Cash','rgba(16,185,129,.08)'],
        ['transfer','🏦','Transfer Bank','rgba(59,130,246,.08)'],
        ['qris','📱','QRIS','rgba(168,85,247,.08)'],
    ] as $m)
    @php $s = $summary->where('method',$m[0])->first(); @endphp
    <div class="method-card">
        <div class="method-icon" style="background:{{ $m[3] }}">{{ $m[1] }}</div>
        <div>
            <div style="font-size:12px;color:var(--muted);margin-bottom:3px">{{ $m[2] }}</div>
            <div style="font-size:18px;font-weight:700;color:var(--text)">
                Rp {{ number_format($s ? $s->total_amount : 0, 0,',','.') }}
            </div>
            <div style="font-size:11px;color:var(--muted);margin-top:2px">{{ $s ? $s->total_tx : 0 }} transaksi</div>
        </div>
    </div>
    @endforeach
</div>

{{-- Tabs --}}
<div class="tabs-bar">
    <button class="tab-item active" onclick="switchTab('orders',this)">💳 Pembayaran Order</button>
    <button class="tab-item" onclick="switchTab('topup',this)">⬆️ Topup Wallet</button>
</div>

{{-- Orders Payment --}}
<div class="tab-panel active" id="tab-orders">
    <div style="background:var(--warm);border-radius:var(--r);border:1px solid var(--border);padding:14px 18px;display:flex;gap:10px;margin-bottom:16px;flex-wrap:wrap">
        <div style="position:relative;flex:1;min-width:180px">
            <i class="ti ti-search" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--muted);font-size:16px;pointer-events:none"></i>
            <input type="text" class="form-input" style="padding-left:38px" placeholder="Cari kode pembayaran..." value="{{ request('search') }}"
                onchange="window.location='?search='+this.value+'&date={{ request('date') }}'">
        </div>
        <select class="form-select" style="width:auto" onchange="window.location='?method='+this.value+'&date={{ request('date') }}'">
            <option value="">Semua Metode</option>
            @foreach(['wallet'=>'Wallet','cash'=>'Cash','transfer'=>'Transfer','qris'=>'QRIS'] as $v=>$l)
            <option value="{{ $v }}" {{ request('method')===$v?'selected':'' }}>{{ $l }}</option>
            @endforeach
        </select>
        <select class="form-select" style="width:auto" onchange="window.location='?status='+this.value+'&date={{ request('date') }}'">
            <option value="">Semua Status</option>
            <option value="paid" {{ request('status')==='paid'?'selected':'' }}>Paid</option>
            <option value="pending" {{ request('status')==='pending'?'selected':'' }}>Pending</option>
        </select>
    </div>

    <div class="content-card">
        <div style="overflow-x:auto">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Kode Bayar</th>
                        <th>User</th>
                        <th>No. Order</th>
                        <th>Metode</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                        <th>Waktu</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $pay)
                    <tr>
                        <td><span style="font-family:monospace;font-size:12px;font-weight:600;color:var(--sage-dark)">{{ $pay->payment_code }}</span></td>
                        <td>
                            <div style="font-weight:600;font-size:13px">{{ $pay->user->name ?? '—' }}</div>
                            <div style="font-size:11px;color:var(--muted)">{{ $pay->user->email ?? '' }}</div>
                        </td>
                        <td>
                            @if($pay->order)
                            <a href="{{ route('admin.orders.show',$pay->order_id) }}" style="font-weight:600;font-size:12px;color:var(--sage)">
                                {{ $pay->order->order_number }}
                            </a>
                            @else <span style="color:var(--muted)">—</span> @endif
                        </td>
                        <td>
                            <span class="badge badge-{{ ['wallet'=>'sage','cash'=>'green','transfer'=>'blue','qris'=>'sage'][$pay->method] ?? 'gray' }}">
                                {{ ['wallet'=>'💳','cash'=>'💵','transfer'=>'🏦','qris'=>'📱'][$pay->method] ?? '💰' }}
                                {{ ucfirst($pay->method) }}
                            </span>
                        </td>
                        <td style="font-weight:700;font-size:14px;color:var(--sage-dark)">Rp {{ number_format($pay->amount,0,',','.') }}</td>
                        <td>
                            <span class="badge {{ $pay->status==='paid' ? 'badge-green' : 'badge-amber' }}">{{ ucfirst($pay->status) }}</span>
                            @if($pay->paid_at)
                            <div style="font-size:10px;color:var(--muted);margin-top:2px">{{ $pay->paid_at->format('H:i') }}</div>
                            @endif
                        </td>
                        <td style="font-size:12px;color:var(--muted)">{{ $pay->created_at->format('d M Y H:i') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="7" style="text-align:center;padding:30px;color:var(--muted)">Belum ada data pembayaran</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="padding:16px 20px;border-top:1px solid var(--border)">{{ $payments->links() }}</div>
    </div>
</div>

{{-- Topup --}}
<div class="tab-panel" id="tab-topup">
    <div class="content-card">
        <div style="overflow-x:auto">
            <table class="admin-table">
                <thead>
                    <tr><th>Kode</th><th>User</th><th>Nominal</th><th>Bank</th><th>Disetujui Oleh</th><th>Waktu Approve</th></tr>
                </thead>
                <tbody>
                    @forelse($topups as $topup)
                    <tr>
                        <td><span style="font-family:monospace;font-size:12px;font-weight:600;color:var(--sage-dark)">{{ $topup->transaction_code }}</span></td>
                        <td>
                            <div style="font-weight:600;font-size:13px">{{ $topup->user->name }}</div>
                            <div style="font-size:11px;color:var(--muted)">{{ $topup->user->email }}</div>
                        </td>
                        <td style="font-weight:700;font-size:14px;color:var(--sage-dark)">Rp {{ number_format($topup->amount,0,',','.') }}</td>
                        <td style="font-size:13px;text-transform:capitalize;color:var(--muted)">{{ str_replace('_',' ',$topup->payment_method) }}</td>
                        <td style="font-size:13px">{{ $topup->approver->name ?? '—' }}</td>
                        <td style="font-size:12px;color:var(--muted)">{{ $topup->approved_at ? $topup->approved_at->format('d M Y H:i') : '—' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" style="text-align:center;padding:30px;color:var(--muted)">Belum ada topup disetujui</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function switchTab(name, btn) {
    document.querySelectorAll('.tab-item').forEach(b=>b.classList.remove('active'));
    document.querySelectorAll('.tab-panel').forEach(p=>p.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('tab-'+name).classList.add('active');
}
</script>
@endpush