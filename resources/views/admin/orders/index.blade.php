@extends('layouts.admin')
@section('title','Kelola Pesanan')
@section('page-title','Kelola Pesanan')

@push('styles')
<style>
.filter-bar{background:var(--warm);border-radius:var(--r);border:1px solid var(--border);padding:14px 18px;display:flex;gap:10px;align-items:center;flex-wrap:wrap;margin-bottom:20px}
.search-wrap{position:relative;flex:1;min-width:180px}
.search-wrap i{position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--muted);font-size:16px;pointer-events:none}
.search-inp{width:100%;padding:9px 14px 9px 38px;border-radius:10px;border:1.5px solid var(--border);background:var(--beige);color:var(--text);font-size:13px;font-family:inherit;outline:none;transition:border-color .2s}
.search-inp:focus{border-color:var(--sage);background:#fff}
.count-pill{padding:5px 14px;border-radius:20px;font-size:12px;font-weight:700;cursor:pointer;transition:all .2s;border:1.5px solid var(--border);background:transparent;color:var(--muted)}
.count-pill:hover{background:var(--beige)}
.count-pill.active{background:var(--sage);color:#fff;border-color:var(--sage)}
</style>
@endpush

@section('content')
<div class="page-header">
    <div>
        <div class="page-title">Semua Pesanan</div>
        <div class="page-sub">{{ $orders->total() }} total pesanan</div>
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap">
        @foreach(['menunggu'=>['amber',$counts['menunggu']],'cooking'=>['coral',$counts['cooking']],'selesai'=>['teal',$counts['selesai']],'completed'=>['green',$counts['completed']]] as $status=>$data)
        <a href="?status={{ $status }}" class="count-pill {{ request('status')==$status?'active':'' }}">
            {{ ucfirst($status) }} <span style="margin-left:5px;background:rgba(255,255,255,.2);padding:1px 7px;border-radius:10px">{{ $data[1] }}</span>
        </a>
        @endforeach
    </div>
</div>

<div class="filter-bar">
    <div class="search-wrap">
        <i class="ti ti-search"></i>
        <input type="text" class="search-inp" placeholder="Cari nomor order..." value="{{ request('search') }}"
            onchange="window.location='?search='+this.value+'&status={{ request('status') }}'">
    </div>
    <input type="date" class="form-input" style="width:auto" value="{{ request('date') }}"
        onchange="window.location='?date='+this.value+'&status={{ request('status') }}'">
    @if(request()->hasAny(['search','status','date']))
    <a href="{{ route('admin.orders.index') }}" class="btn-secondary" style="padding:9px 16px;font-size:12px">
        <i class="ti ti-x"></i> Reset
    </a>
    @endif
</div>

<div class="content-card">
    <div style="overflow-x:auto">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>No. Order</th>
                    <th>Customer</th>
                    <th>Item</th>
                    <th>Total</th>
                    <th>Pembayaran</th>
                    <th>Status</th>
                    <th>Waktu</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                @php
                $sc = ['menunggu'=>'badge-amber','cooking'=>'badge-amber','selesai'=>'badge-sage','completed'=>'badge-green','dibatalkan'=>'badge-red'];
                @endphp
                <tr>
                    <td>
                        <div style="font-weight:700;font-size:13px">{{ $order->order_number }}</div>
                        @if($order->table_number)
                        <div style="font-size:11px;color:var(--muted)">Meja {{ $order->table_number }}</div>
                        @endif
                    </td>
                    <td>
                        <div style="font-weight:500;font-size:13px">{{ $order->user->name }}</div>
                        <div style="font-size:11px;color:var(--muted)">{{ $order->user->email }}</div>
                    </td>
                    <td>
                        <div style="font-size:12px;color:var(--muted);max-width:200px">
                            {{ $order->items->take(2)->map(fn($i)=>$i->menu_name.' ×'.$i->quantity)->join(', ') }}
                            @if($order->items->count() > 2)
                            <span style="color:var(--sage)">+{{ $order->items->count()-2 }} lagi</span>
                            @endif
                        </div>
                    </td>
                    <td style="font-weight:700;color:var(--sage-dark)">Rp {{ number_format($order->total_amount,0,',','.') }}</td>
                    <td>
                        <div style="font-size:12px;text-transform:capitalize">{{ str_replace('_',' ',$order->payment_method) }}</div>
                        <span class="badge {{ $order->payment_status === 'paid' ? 'badge-green' : 'badge-amber' }}" style="font-size:10px">
                            {{ ucfirst($order->payment_status) }}
                        </span>
                    </td>
                    <td><span class="badge {{ $sc[$order->status] ?? 'badge-gray' }}">{{ $order->status_label }}</span></td>
                    <td style="font-size:12px;color:var(--muted)">
                        <div>{{ $order->created_at->format('d M Y') }}</div>
                        <div>{{ $order->created_at->format('H:i') }}</div>
                    </td>
                    <td>
                        <div style="display:flex;gap:6px">
                            <a href="{{ route('admin.orders.show',$order->id) }}" class="btn-icon btn-icon-sage" title="Detail">
                                <i class="ti ti-eye" style="font-size:14px"></i>
                            </a>
                            @if(!in_array($order->status,['completed','dibatalkan']))
                            <button onclick="cancelOrder({{ $order->id }},this)" class="btn-icon btn-icon-red" title="Batalkan">
                                <i class="ti ti-x" style="font-size:14px"></i>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" style="text-align:center;padding:40px;color:var(--muted)">Tidak ada pesanan ditemukan</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:16px 20px;border-top:1px solid var(--border)">{{ $orders->links() }}</div>
</div>
@endsection

@push('scripts')
<script>
function cancelOrder(id, btn) {
    if(!confirm('Batalkan order ini?')) return;
    btn.disabled = true;
    ajax('/admin/orders/'+id+'/cancel','POST')
    .then(d => { showToast(d.message,'info'); setTimeout(()=>location.reload(),700); })
    .catch(()=>btn.disabled=false);
}
</script>
@endpush