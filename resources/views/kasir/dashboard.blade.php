@extends('layouts.kasir')
@section('title','Kasir Dashboard')

@push('styles')
<style>
.stat-card{background:#1e2b25;border-radius:14px;border:1px solid rgba(138,170,146,.1);padding:18px 20px;display:flex;align-items:center;gap:14px}
.stat-icon{width:46px;height:46px;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.stat-val{font-size:24px;font-weight:700;color:#e8f0eb;line-height:1}
.stat-lbl{font-size:12px;color:#8fa897;margin-top:3px}
.order-card{background:#1e2b25;border-radius:14px;border:1px solid rgba(138,170,146,.1);padding:16px;transition:border-color .2s}
.order-card:hover{border-color:rgba(138,170,146,.3)}
.order-card.menunggu{border-left:3px solid #f59e0b}
.order-card.cooking{border-left:3px solid #e07a5f}
.order-card.selesai{border-left:3px solid #4ade80}
.status-badge{padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;display:inline-flex;align-items:center;gap:4px}
.badge-menunggu{background:rgba(245,158,11,.15);color:#f59e0b}
.badge-cooking{background:rgba(224,122,95,.15);color:#e07a5f}
.badge-selesai{background:rgba(74,222,128,.12);color:#4ade80}
.btn-status{padding:7px 16px;border-radius:8px;font-size:12px;font-weight:600;border:none;cursor:pointer;transition:all .2s}
.btn-cooking{background:rgba(224,122,95,.15);color:#e07a5f}.btn-cooking:hover{background:#e07a5f;color:#fff}
.btn-selesai{background:rgba(74,222,128,.12);color:#4ade80}.btn-selesai:hover{background:#16a34a;color:#fff}
</style>
@endpush

@section('content')
{{-- Stats Row --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:14px;margin-bottom:28px">
    @foreach([
        ['ti-clock','Menunggu',$stats['menunggu'],'rgba(245,158,11,.15)','#f59e0b'],
        ['ti-flame','Memasak',$stats['cooking'],'rgba(224,122,95,.15)','#e07a5f'],
        ['ti-circle-check','Siap Diambil',$stats['selesai'],'rgba(74,222,128,.12)','#4ade80'],
        ['ti-shopping-bag','Total Hari Ini',$stats['today'],'rgba(138,170,146,.12)','#8aaa92'],
    ] as $s)
    <div class="stat-card">
        <div class="stat-icon" style="background:{{ $s[3] }}">
            <i class="ti {{ $s[0] }}" style="font-size:20px;color:{{ $s[4] }}"></i>
        </div>
        <div>
            <div class="stat-val">{{ $s[2] }}</div>
            <div class="stat-lbl">{{ $s[1] }}</div>
        </div>
    </div>
    @endforeach
</div>

{{-- Revenue Today --}}
<div style="background:linear-gradient(135deg,#3d5c47,#5a7c65);border-radius:14px;padding:18px 22px;margin-bottom:28px;display:flex;align-items:center;justify-content:space-between">
    <div>
        <div style="font-size:12px;color:rgba(255,255,255,.65);text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px">Revenue Hari Ini</div>
        <div style="font-size:28px;font-weight:700;color:#fff">Rp {{ number_format($stats['revenue'],0,',','.') }}</div>
    </div>
    <i class="ti ti-trending-up" style="font-size:40px;color:rgba(255,255,255,.2)"></i>
</div>

{{-- Realtime Order List --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
    <div style="font-weight:700;font-size:16px;color:#e8f0eb;display:flex;align-items:center;gap:8px">
        <span class="status-dot"></span> Order Aktif Realtime
    </div>
    <a href="{{ route('kasir.orders.index') }}" style="font-size:13px;color:#8aaa92;display:flex;align-items:center;gap:5px">
        Lihat semua <i class="ti ti-arrow-right"></i>
    </a>
</div>

<div id="order-list" style="display:flex;flex-direction:column;gap:10px">
    @forelse($recentOrders as $order)
    <div class="order-card {{ $order->status }}">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:10px">
            <div>
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px">
                    <span style="font-weight:700;font-size:14px;color:#e8f0eb">{{ $order->order_number }}</span>
                    <span class="status-badge badge-{{ $order->status }}">
                        {{ $order->status_label }}
                    </span>
                </div>
                <div style="font-size:13px;color:#8fa897">{{ $order->user->name }} · {{ $order->items->sum('quantity') }} item · Rp {{ number_format($order->total_amount,0,',','.') }}</div>
                @if($order->table_number)
                <div style="font-size:12px;color:#8fa897;margin-top:3px"><i class="ti ti-armchair" style="font-size:13px"></i> Meja {{ $order->table_number }}</div>
                @endif
            </div>
            <div style="display:flex;align-items:center;gap:8px">
                @if($order->status === 'menunggu')
                <button onclick="updateStatus({{ $order->id }},'cooking',this)" class="btn-status btn-cooking">
                    <i class="ti ti-chef-hat"></i> Masak
                </button>
                @elseif($order->status === 'cooking')
                <button onclick="updateStatus({{ $order->id }},'selesai',this)" class="btn-status btn-selesai">
                    <i class="ti ti-check"></i> Selesai
                </button>
                @endif
                <a href="{{ route('kasir.orders.show',$order->id) }}" style="padding:7px 12px;border-radius:8px;font-size:12px;font-weight:600;background:rgba(138,170,146,.1);color:#8aaa92;display:inline-flex;align-items:center;gap:4px">
                    <i class="ti ti-eye"></i> Detail
                </a>
            </div>
        </div>
    </div>
    @empty
    <div style="text-align:center;padding:40px;background:#1e2b25;border-radius:14px;color:#8fa897">
        <i class="ti ti-inbox" style="font-size:36px;display:block;margin-bottom:10px"></i>
        Belum ada order aktif
    </div>
    @endforelse
</div>
@endsection

@push('scripts')
<script>
function updateStatus(orderId, newStatus, btn) {
    btn.disabled = true;
    ajax('/kasir/orders/' + orderId + '/status', 'POST', { status: newStatus })
    .then(d => {
        if (d.success) {
            showToast(d.message, 'success');
            setTimeout(() => location.reload(), 800);
        } else {
            showToast(d.message, 'error');
            btn.disabled = false;
        }
    });
}

// Realtime poll every 15s
setInterval(() => {
    ajax('/kasir/orders/poll').then(d => {
        if (d.counts) {
            document.getElementById('count-menunggu').textContent = d.counts.menunggu;
            document.getElementById('count-cooking').textContent = d.counts.cooking;
        }
    });
}, 15000);
</script>
@endpush

