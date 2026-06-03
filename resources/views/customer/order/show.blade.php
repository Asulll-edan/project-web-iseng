@extends('layouts.app')
@section('title','Detail Pesanan #' . $order->order_number)

@section('content')
<div style="padding:110px 0 40px;background:linear-gradient(135deg,#1a2e22,#2c3e35)">
    <div class="container">
        <div style="font-size:13px;color:rgba(255,255,255,.6);margin-bottom:8px">
            <a href="{{ route('orders.index') }}" style="color:rgba(255,255,255,.6)">Pesanan</a>
            <i class="ti ti-chevron-right" style="font-size:13px;margin:0 6px"></i>
            {{ $order->order_number }}
        </div>
        <h1 style="font-family:'INeedCoffee',serif;font-size:26px;font-weight:600;color:#fff">{{ $order->order_number }}</h1>
        <div style="margin-top:8px">
            <span style="display:inline-flex;align-items:center;gap:6px;padding:5px 14px;border-radius:20px;font-size:13px;font-weight:700;background:rgba(255,255,255,.15);color:#fff">
                <i class="ti ti-circle" style="font-size:8px"></i> {{ $order->status_label }}
            </span>
        </div>
    </div>
</div>

<div class="container" style="padding:36px 0 80px">
    <div style="display:grid;grid-template-columns:1fr 340px;gap:28px;align-items:start">
        <div>
            {{-- Items --}}
            <div style="background:var(--warm-white);border-radius:20px;border:1px solid var(--border);padding:24px;margin-bottom:20px">
                <h3 style="font-weight:700;font-size:15px;margin-bottom:18px"><i class="ti ti-receipt" style="color:var(--sage)"></i> Item Pesanan</h3>
                @foreach($order->items as $item)
                <div style="display:flex;gap:14px;padding:12px 0;border-bottom:1px solid var(--border)">
                    <div style="width:52px;height:52px;border-radius:10px;background:var(--beige);overflow:hidden;flex-shrink:0;display:flex;align-items:center;justify-content:center">
                        @if($item->menu && $item->menu->image)<img src="{{ $item->menu->image_url }}" style="width:100%;height:100%;object-fit:cover">@else<span style="font-size:20px">🍱</span>@endif
                    </div>
                    <div style="flex:1">
                        <div style="font-weight:600;font-size:14px">{{ $item->menu_name }}</div>
                        @if($item->note)<div style="font-size:12px;color:var(--text-muted);font-style:italic">📝 {{ $item->note }}</div>@endif
                        <div style="font-size:13px;color:var(--text-muted)">Rp {{ number_format($item->price,0,',','.') }} × {{ $item->quantity }}</div>
                    </div>
                    <div style="font-weight:700;font-size:14px;flex-shrink:0">Rp {{ number_format($item->subtotal,0,',','.') }}</div>
                </div>
                @endforeach
                <div style="padding-top:14px;display:flex;flex-direction:column;gap:8px;font-size:14px">
                    <div style="display:flex;justify-content:space-between"><span style="color:var(--text-muted)">Subtotal</span><span>Rp {{ number_format($order->subtotal,0,',','.') }}</span></div>
                    @if($order->discount_amount > 0)
                    <div style="display:flex;justify-content:space-between"><span style="color:var(--sage-dark)">Diskon</span><span style="color:var(--sage-dark)">- Rp {{ number_format($order->discount_amount,0,',','.') }}</span></div>
                    @endif
                    <div style="display:flex;justify-content:space-between"><span style="color:var(--text-muted)">Pajak</span><span>Rp {{ number_format($order->tax_amount,0,',','.') }}</span></div>
                    <div style="display:flex;justify-content:space-between;font-weight:700;font-size:16px;padding-top:8px;border-top:1px solid var(--border)"><span>Total</span><span style="color:var(--sage-dark)">Rp {{ number_format($order->total_amount,0,',','.') }}</span></div>
                </div>
            </div>

            {{-- Status Timeline --}}
            <div style="background:var(--warm-white);border-radius:20px;border:1px solid var(--border);padding:24px">
                <h3 style="font-weight:700;font-size:15px;margin-bottom:20px"><i class="ti ti-timeline" style="color:var(--sage)"></i> Riwayat Status</h3>
                @foreach($order->statusLogs as $log)
                <div style="display:flex;gap:14px;padding-bottom:16px">
                    <div style="display:flex;flex-direction:column;align-items:center">
                        <div style="width:32px;height:32px;border-radius:50%;background:rgba(90,124,101,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                            <i class="ti ti-check" style="color:var(--sage);font-size:15px"></i>
                        </div>
                        @if(!$loop->last)<div style="width:2px;flex:1;background:var(--border);margin:4px 0;min-height:20px"></div>@endif
                    </div>
                    <div style="padding-top:6px;padding-bottom:{{ $loop->last ? '0' : '16px' }}">
                        <div style="font-weight:600;font-size:13px">{{ ucfirst($log->status) }}</div>
                        <div style="font-size:12px;color:var(--text-muted)">{{ $log->note }}</div>
                        <div style="font-size:11px;color:var(--text-muted);margin-top:2px">{{ $log->created_at->format('d M Y, H:i') }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div>
            {{-- Order info --}}
            <div style="background:var(--warm-white);border-radius:20px;border:1px solid var(--border);padding:22px;margin-bottom:16px">
                <div style="display:flex;flex-direction:column;gap:12px;font-size:13px">
                    @foreach([
                        ['Nomor Order',$order->order_number,'ti-hash'],
                        ['Tanggal',$order->created_at->format('d M Y, H:i'),'ti-calendar'],
                        ['Jenis',$order->order_type === 'dine_in' ? 'Makan di Tempat' : 'Takeaway','ti-tools-kitchen-2'],
                        ['Pembayaran',ucfirst(str_replace('_',' ',$order->payment_method)),'ti-credit-card'],
                        ['Meja',$order->table_number ?? '-','ti-table'],
                    ] as $info)
                    <div style="display:flex;gap:10px;align-items:flex-start">
                        <i class="ti {{ $info[2] }}" style="font-size:15px;color:var(--text-muted);flex-shrink:0;margin-top:1px"></i>
                        <div><div style="font-size:11px;color:var(--text-muted)">{{ $info[0] }}</div><div style="font-weight:600">{{ $info[1] }}</div></div>
                    </div>
                    @endforeach
                </div>
            </div>

            @if($order->status === 'selesai')
            <button onclick="completeOrder({{ $order->id }})"
                style="width:100%;padding:14px;background:var(--sage);color:#fff;border:none;border-radius:14px;font-weight:700;font-size:15px;cursor:pointer;transition:all .2s;display:flex;align-items:center;justify-content:center;gap:8px">
                <i class="ti ti-circle-check"></i> Selesaikan Pesanan
            </button>
            <div style="font-size:11px;color:var(--text-muted);text-align:center;margin-top:8px">Klik jika kamu sudah menerima pesanan</div>
            @endif

            @if($order->loyalty_points_earned)
            <div style="margin-top:14px;background:rgba(90,124,101,.08);border-radius:12px;padding:14px;text-align:center">
                <i class="ti ti-star-filled" style="color:#f59e0b;font-size:20px;display:block;margin-bottom:6px"></i>
                <div style="font-weight:700;font-size:15px;color:var(--sage-dark)">+{{ $order->loyalty_points_earned }} Poin</div>
                <div style="font-size:12px;color:var(--text-muted)">Loyalty points earned</div>
            </div>
            @endif

            @if($order->cashback_earned > 0)
            <div style="margin-top:10px;background:rgba(212,175,55,.08);border-radius:12px;padding:14px;text-align:center">
                <i class="ti ti-wallet" style="color:#d97706;font-size:20px;display:block;margin-bottom:6px"></i>
                <div style="font-weight:700;font-size:15px;color:#d97706">Rp {{ number_format($order->cashback_earned,0,',','.') }}</div>
                <div style="font-size:12px;color:var(--text-muted)">Cashback masuk wallet</div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function completeOrder(orderId) {
    if (!confirm('Konfirmasi bahwa kamu sudah menerima pesanan ini?')) return;
    fetch('/orders/' + orderId + '/complete', {
        method: 'POST', headers: {'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json'}
    }).then(r => r.json()).then(d => {
        if (d.success) { showToast(d.message, 'success'); setTimeout(() => location.reload(), 1500); }
        else showToast(d.message, 'error');
    });
}
</script>
@endpush


