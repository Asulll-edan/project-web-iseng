@extends('layouts.app')
@section('title','Pesanan Saya — Rumahnya Anak Sekolah')

@push('styles')
<style>
.page-top{padding:110px 0 40px;background:linear-gradient(135deg,#1a2e22,#2c3e35)}
.order-card{background:var(--warm-white);border-radius:16px;border:1px solid var(--border);padding:20px;transition:all .2s;margin-bottom:14px}
.order-card:hover{box-shadow:var(--shadow-md)}
.status-badge{display:inline-flex;align-items:center;gap:6px;padding:5px 12px;border-radius:20px;font-size:12px;font-weight:700}
.status-menunggu{background:rgba(245,158,11,.1);color:#b45309}
.status-cooking{background:rgba(239,68,68,.1);color:#b91c1c}
.status-selesai{background:rgba(20,184,166,.1);color:#0f766e}
.status-completed{background:rgba(74,222,128,.1);color:#15803d}
.status-dibatalkan{background:rgba(107,114,128,.1);color:#374151}
</style>
@endpush

@section('content')
<div class="page-top">
    <div class="container">
        <h1 style="font-family:'INeedCoffee',serif;font-size:28px;font-weight:600;color:#fff">Pesanan Saya</h1>
        <p style="font-size:13px;color:rgba(255,255,255,.6);margin-top:6px">{{ $orders->total() }} pesanan</p>
    </div>
</div>

<div class="container" style="padding:36px 0 80px">
    @forelse($orders as $order)
    <div class="order-card" data-aos="fade-up">
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:16px">
            <div>
                <div style="font-weight:700;font-size:15px">{{ $order->order_number }}</div>
                <div style="font-size:12px;color:var(--text-muted);margin-top:2px">{{ $order->created_at->format('d M Y, H:i') }}</div>
            </div>
            <span class="status-badge status-{{ $order->status }}">
                <i class="ti ti-circle" style="font-size:8px"></i>
                {{ $order->status_label }}
            </span>
        </div>

        <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:14px">
            @foreach($order->items->take(3) as $item)
            <span style="background:var(--beige);border-radius:8px;padding:5px 10px;font-size:12px;font-weight:500">{{ $item->menu_name }} x{{ $item->quantity }}</span>
            @endforeach
            @if($order->items->count() > 3)
            <span style="background:var(--beige);border-radius:8px;padding:5px 10px;font-size:12px;color:var(--text-muted)">+{{ $order->items->count() - 3 }} lainnya</span>
            @endif
        </div>

        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
            <div>
                <span style="font-size:12px;color:var(--text-muted)">Total: </span>
                <span style="font-weight:700;font-size:15px;color:var(--sage-dark)">Rp {{ number_format($order->total_amount,0,',','.') }}</span>
            </div>
            <div style="display:flex;gap:8px">
                @if($order->status === 'selesai')
                <button onclick="completeOrder({{ $order->id }},this)"
                    style="padding:9px 18px;background:var(--sage);color:#fff;border:none;border-radius:20px;font-weight:600;font-size:13px;cursor:pointer;transition:all .2s">
                    <i class="ti ti-circle-check"></i> Selesaikan Pesanan
                </button>
                @endif
                <a href="{{ route('orders.tracking',$order->id) }}"
                   style="padding:9px 18px;border:1.5px solid var(--border);border-radius:20px;font-weight:600;font-size:13px;color:var(--text-main);transition:all .2s">
                    <i class="ti ti-map-pin"></i> Tracking
                </a>
                <a href="{{ route('orders.show',$order->id) }}"
                   style="padding:9px 18px;background:var(--beige);border-radius:20px;font-weight:600;font-size:13px;color:var(--text-main);transition:all .2s">
                    Detail
                </a>
            </div>
        </div>
    </div>
    @empty
    <div style="text-align:center;padding:60px 20px">
        <div style="font-size:56px;margin-bottom:16px">📭</div>
        <div style="font-weight:700;font-size:18px;margin-bottom:8px">Belum ada pesanan</div>
        <p style="font-size:14px;color:var(--text-muted);margin-bottom:24px">Yuk, mulai pesan menu favoritmu!</p>
        <a href="{{ route('menu.index') }}" class="btn-primary" style="display:inline-flex"><i class="ti ti-bowl-chopsticks"></i> Pesan Sekarang</a>
    </div>
    @endforelse

    @if($orders->hasPages())
    <div style="margin-top:28px;display:flex;justify-content:center">{{ $orders->links() }}</div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function completeOrder(orderId, btn) {
    if (!confirm('Konfirmasi bahwa pesanan sudah kamu terima?')) return;
    btn.disabled = true;
    btn.textContent = 'Memproses...';
    fetch('/orders/' + orderId + '/complete', {
        method: 'POST',
        headers: {'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json'}
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            showToast(d.message, 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showToast('Gagal: ' + d.message, 'error');
            btn.disabled = false;
        }
    });
}
</script>
@endpush


