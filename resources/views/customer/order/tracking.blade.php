@extends('layouts.app')
@section('title','Tracking Pesanan #' . $order->order_number)

@push('styles')
<style>
.tracking-step{display:flex;gap:16px;position:relative}
.tracking-step:not(:last-child)::before{content:'';position:absolute;left:19px;top:40px;bottom:-8px;width:2px;background:var(--border)}
.tracking-step.done::before{background:var(--sage)}
.step-icon{width:40px;height:40px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;border:2px solid var(--border);background:var(--warm-white);transition:all .4s}
.tracking-step.done .step-icon{background:var(--sage);border-color:var(--sage)}
.tracking-step.active .step-icon{background:rgba(90,124,101,.1);border-color:var(--sage);animation:pulseStep 2s infinite}
@keyframes pulseStep{0%,100%{box-shadow:0 0 0 0 rgba(90,124,101,.3)}50%{box-shadow:0 0 0 8px rgba(90,124,101,.0)}}
</style>
@endpush

@section('content')
<div style="padding:110px 0 40px;background:linear-gradient(135deg,#1a2e22,#2c3e35)">
    <div class="container" style="text-align:center">
        <h1 style="font-family:'Playfair Display',serif;font-size:26px;font-weight:600;color:#fff">Live Tracking</h1>
        <div style="font-size:14px;color:rgba(255,255,255,.6);margin-top:6px">{{ $order->order_number }}</div>
        <div id="status-badge" style="display:inline-flex;align-items:center;gap:6px;margin-top:12px;padding:6px 18px;border-radius:20px;background:rgba(255,255,255,.15);color:#fff;font-weight:700;font-size:14px">
            <span style="width:8px;height:8px;border-radius:50%;background:#4ade80;animation:blink 1s infinite"></span>
            <span id="status-text">{{ $order->status_label }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding:40px 0 80px;max-width:600px">
    {{-- Steps --}}
    <div style="background:var(--warm-white);border-radius:20px;border:1px solid var(--border);padding:32px;margin-bottom:20px">
        @php
        $steps = [
            ['menunggu','Pesanan Diterima','Pesanan kamu sudah masuk dan menunggu konfirmasi','ti-clock'],
            ['cooking','Sedang Dimasak','Chef kami sedang menyiapkan pesananmu dengan penuh cinta','ti-chef-hat'],
            ['selesai','Siap Diambil','Pesananmu sudah siap! Klik tombol selesai setelah menerima','ti-check'],
            ['completed','Selesai','Pesanan selesai. Terima kasih sudah makan di RAS!','ti-circle-check'],
        ];
        $statusOrder = ['menunggu'=>0,'cooking'=>1,'selesai'=>2,'completed'=>3,'dibatalkan'=>4];
        $currentIdx = $statusOrder[$order->status] ?? 0;
        @endphp
        @foreach($steps as $i => $step)
        <div class="tracking-step {{ $i < $currentIdx ? 'done' : ($i == $currentIdx ? 'active' : '') }}" style="padding-bottom:{{ $loop->last ? '0' : '24px' }}">
            <div class="step-icon">
                <i class="ti {{ $i < $currentIdx ? 'ti-check' : $step[3] }}" style="font-size:18px;color:{{ $i <= $currentIdx ? ($i < $currentIdx ? '#fff' : 'var(--sage)') : 'var(--text-muted)' }}"></i>
            </div>
            <div style="padding-top:8px">
                <div style="font-weight:700;font-size:14px;color:{{ $i <= $currentIdx ? 'var(--text-main)' : 'var(--text-muted)' }}">{{ $step[0] === $order->status ? $order->status_label : ucfirst($step[0]) }}</div>
                <div style="font-size:13px;color:var(--text-muted);margin-top:2px">{{ $step[2] }}</div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Complete button --}}
    @if($order->status === 'selesai')
    <div style="background:linear-gradient(135deg,var(--sage-dark),var(--sage));border-radius:16px;padding:24px;text-align:center;margin-bottom:20px">
        <i class="ti ti-package" style="font-size:32px;color:rgba(255,255,255,.8);display:block;margin-bottom:12px"></i>
        <div style="font-weight:700;font-size:16px;color:#fff;margin-bottom:6px">Pesananmu sudah siap!</div>
        <div style="font-size:13px;color:rgba(255,255,255,.8);margin-bottom:20px">Klik tombol di bawah setelah kamu menerima pesanan</div>
        <button onclick="completeOrder({{ $order->id }})" id="complete-btn"
            style="padding:13px 32px;background:#fff;color:var(--sage-dark);border:none;border-radius:20px;font-weight:700;font-size:15px;cursor:pointer;transition:all .2s">
            <i class="ti ti-circle-check" style="margin-right:6px"></i> Selesaikan Pesanan
        </button>
    </div>
    @endif

    {{-- Info --}}
    <div style="background:var(--warm-white);border-radius:16px;border:1px solid var(--border);padding:20px">
        <div style="display:flex;justify-content:space-between;margin-bottom:10px;font-size:13px">
            <span style="color:var(--text-muted)">Nomor Order</span>
            <span style="font-weight:700">{{ $order->order_number }}</span>
        </div>
        <div style="display:flex;justify-content:space-between;margin-bottom:10px;font-size:13px">
            <span style="color:var(--text-muted)">Total</span>
            <span style="font-weight:700;color:var(--sage-dark)">Rp {{ number_format($order->total_amount,0,',','.') }}</span>
        </div>
        <div style="display:flex;justify-content:space-between;font-size:13px">
            <span style="color:var(--text-muted)">Update terakhir</span>
            <span id="last-update">{{ $order->updated_at->diffForHumans() }}</span>
        </div>
    </div>

    <div style="text-align:center;margin-top:20px">
        <a href="{{ route('orders.show',$order->id) }}" style="font-size:13px;color:var(--text-muted);display:inline-flex;align-items:center;gap:6px">
            <i class="ti ti-receipt" style="font-size:15px"></i> Lihat Detail Pesanan
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script>
@if(!in_array($order->status, ['completed','dibatalkan']))
// Poll every 8 seconds
setInterval(function() {
    fetch('/orders/{{ $order->id }}/status-poll', {headers:{'Accept':'application/json'}})
    .then(r => r.json())
    .then(d => {
        document.getElementById('status-text').textContent = d.status_label;
        document.getElementById('last-update').textContent = d.updated_at;
        if (d.status !== '{{ $order->status }}') location.reload();
    });
}, 8000);
@endif

function completeOrder(orderId) {
    const btn = document.getElementById('complete-btn');
    if (!confirm('Konfirmasi pesanan sudah diterima?')) return;
    btn.disabled = true; btn.textContent = 'Memproses...';
    fetch('/orders/' + orderId + '/complete', {
        method: 'POST', headers: {'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json'}
    }).then(r => r.json()).then(d => {
        if (d.success) { showToast(d.message, 'success'); setTimeout(() => location.reload(), 1500); }
        else { showToast(d.message, 'error'); btn.disabled = false; }
    });
}
</script>
<style>@keyframes blink{0%,100%{opacity:1}50%{opacity:.4}}</style>
@endpush
