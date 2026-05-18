@extends('layouts.app')
@section('title','Keranjang — Rumahnya Anak Sekolah')

@push('styles')
<style>
.page-top{padding:110px 0 40px;background:linear-gradient(135deg,#1a2e22,#2c3e35)}
.cart-layout{display:grid;grid-template-columns:1fr 360px;gap:28px;padding:36px 0 80px;align-items:start}
.cart-item{display:flex;gap:16px;padding:18px;background:var(--warm-white);border-radius:16px;border:1px solid var(--border);transition:all .2s}
.cart-item:hover{box-shadow:var(--shadow-sm)}
.cart-img{width:80px;height:80px;border-radius:12px;object-fit:cover;background:var(--beige);flex-shrink:0;display:flex;align-items:center;justify-content:center;overflow:hidden}
.qty-ctrl{display:flex;align-items:center;gap:8px}
.qty-btn{width:30px;height:30px;border-radius:50%;border:1.5px solid var(--border);background:transparent;cursor:pointer;font-size:16px;font-weight:600;color:var(--text-main);display:flex;align-items:center;justify-content:center;transition:all .2s}
.qty-btn:hover{background:var(--sage);color:#fff;border-color:var(--sage)}
.summary-box{background:var(--warm-white);border-radius:20px;border:1px solid var(--border);padding:24px;position:sticky;top:100px}
@media(max-width:768px){.cart-layout{grid-template-columns:1fr}.summary-box{position:static}}
</style>
@endpush

@section('content')
<div class="page-top">
    <div class="container">
        <h1 style="font-family:'Playfair Display',serif;font-size:28px;font-weight:600;color:#fff">Keranjang Belanja</h1>
        <div style="font-size:13px;color:rgba(255,255,255,.6);margin-top:6px" id="cart-item-count">{{ $cart->total_items }} item</div>
    </div>
</div>

<div class="container">
    <div class="cart-layout">
        {{-- Items --}}
        <div>
            @if($cart->items->isEmpty())
            <div style="text-align:center;padding:60px 20px;background:var(--warm-white);border-radius:20px;border:1px solid var(--border)">
                <div style="font-size:60px;margin-bottom:16px">🛒</div>
                <div style="font-weight:700;font-size:18px;margin-bottom:8px">Keranjang masih kosong</div>
                <p style="font-size:14px;color:var(--text-muted);margin-bottom:24px">Yuk, tambahkan menu favoritmu!</p>
                <a href="{{ route('menu.index') }}" class="btn-primary" style="display:inline-flex">
                    <i class="ti ti-bowl-chopsticks"></i> Lihat Menu
                </a>
            </div>
            @else
            <div style="display:flex;flex-direction:column;gap:12px" id="cart-items">
                @foreach($cart->items as $item)
                <div class="cart-item" id="item-{{ $item->id }}">
                    <div class="cart-img">
                        @if($item->menu->image)
                        <img src="{{ $item->menu->image_url }}" style="width:100%;height:100%;object-fit:cover">
                        @else
                        <span style="font-size:28px">🍱</span>
                        @endif
                    </div>
                    <div style="flex:1;min-width:0">
                        <div style="font-weight:700;font-size:14px;margin-bottom:4px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden">{{ $item->menu->name }}</div>
                        @if($item->note)
                        <div style="font-size:12px;color:var(--text-muted);margin-bottom:6px;font-style:italic">📝 {{ $item->note }}</div>
                        @endif
                        <div style="font-size:13px;color:var(--text-muted)">Rp {{ number_format($item->price,0,',','.') }} / pcs</div>
                    </div>
                    <div style="display:flex;flex-direction:column;align-items:flex-end;gap:10px;flex-shrink:0">
                        <div style="font-weight:700;font-size:15px;color:var(--sage-dark)" id="subtotal-{{ $item->id }}">
                            Rp {{ number_format($item->subtotal,0,',','.') }}
                        </div>
                        <div class="qty-ctrl">
                            <button class="qty-btn" onclick="updateQty({{ $item->id }}, {{ $item->quantity - 1 }})">−</button>
                            <span id="qty-{{ $item->id }}" style="font-weight:700;min-width:20px;text-align:center">{{ $item->quantity }}</span>
                            <button class="qty-btn" onclick="updateQty({{ $item->id }}, {{ $item->quantity + 1 }})">+</button>
                        </div>
                        <button onclick="removeItem({{ $item->id }})" style="background:none;border:none;cursor:pointer;color:var(--text-muted);font-size:12px;display:flex;align-items:center;gap:4px;transition:color .2s" onmouseover="this.style.color='#c0392b'" onmouseout="this.style.color='var(--text-muted)'">
                            <i class="ti ti-trash" style="font-size:14px"></i> Hapus
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Summary --}}
        <div class="summary-box" data-aos="fade-left">
            <h3 style="font-weight:700;font-size:16px;margin-bottom:20px">Ringkasan Pesanan</h3>
            @php $tax = $cart->total * 0.1; @endphp
            <div style="display:flex;flex-direction:column;gap:12px;margin-bottom:20px;font-size:14px">
                <div style="display:flex;justify-content:space-between">
                    <span style="color:var(--text-muted)">Subtotal</span>
                    <span id="summary-subtotal">Rp {{ number_format($cart->total,0,',','.') }}</span>
                </div>
                <div style="display:flex;justify-content:space-between">
                    <span style="color:var(--text-muted)">Pajak (10%)</span>
                    <span id="summary-tax">Rp {{ number_format($tax,0,',','.') }}</span>
                </div>
                <div style="height:1px;background:var(--border)"></div>
                <div style="display:flex;justify-content:space-between;font-weight:700;font-size:16px">
                    <span>Total</span>
                    <span style="color:var(--sage-dark)" id="summary-total">Rp {{ number_format($cart->total + $tax,0,',','.') }}</span>
                </div>
            </div>

            @if($cart->items->isNotEmpty())
            <a href="{{ route('orders.checkout') }}" class="btn-primary" style="width:100%;justify-content:center">
                <i class="ti ti-credit-card"></i> Checkout Sekarang
            </a>
            @endif

            <a href="{{ route('menu.index') }}" style="display:flex;align-items:center;justify-content:center;gap:6px;margin-top:14px;font-size:13px;color:var(--text-muted)">
                <i class="ti ti-arrow-left" style="font-size:14px"></i> Tambah menu lain
            </a>

            {{-- Wallet balance info --}}
            @auth
            @if(auth()->user()->wallet)
            <div style="margin-top:20px;padding:14px;background:var(--beige);border-radius:12px">
                <div style="font-size:12px;color:var(--text-muted);margin-bottom:4px">SOHIBA Wallet</div>
                <div style="font-weight:700;font-size:15px;color:var(--sage-dark)">
                    Rp {{ number_format(auth()->user()->wallet->balance,0,',','.') }}
                </div>
            </div>
            @endif
            @endauth
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function updateQty(itemId, qty) {
    if (qty < 0) return;
    fetch('/cart/update/' + itemId, {
        method: 'PUT',
        headers: {'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json'},
        body: JSON.stringify({quantity: qty})
    })
    .then(r => r.json())
    .then(d => {
        if (qty === 0) {
            document.getElementById('item-' + itemId).remove();
        } else {
            document.getElementById('qty-' + itemId).textContent = qty;
        }
        if (d.cart_count !== undefined) {
            const b = document.getElementById('cart-count');
            if (b) { b.textContent = d.cart_count; b.style.display = d.cart_count > 0 ? 'flex' : 'none'; }
            document.getElementById('cart-item-count').textContent = d.cart_count + ' item';
        }
        if (d.success) location.reload();
    });
}

function removeItem(itemId) {
    fetch('/cart/remove/' + itemId, {
        method: 'DELETE',
        headers: {'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json'}
    })
    .then(r => r.json())
    .then(d => {
        document.getElementById('item-' + itemId).style.opacity = '0';
        setTimeout(() => { document.getElementById('item-' + itemId).remove(); location.reload(); }, 300);
    });
}
</script>
@endpush
