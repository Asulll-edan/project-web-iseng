@extends('layouts.app')
@section('title','Keranjang Belanja')

@push('styles')
<style>
.page-top{padding:110px 0 40px;background:linear-gradient(135deg,#1a2e22,#2c3e35)}
.cart-grid{display:grid;grid-template-columns:1fr 360px;gap:28px;align-items:start}
.cart-item{background:var(--warm-white);border-radius:16px;border:2px solid var(--border);padding:14px 16px;display:flex;gap:14px;align-items:flex-start;transition:all .2s;cursor:pointer}
.cart-item.selected{border-color:var(--sage);background:rgba(90,124,101,.03)}
.cart-item:hover{box-shadow:var(--shadow-sm)}
.cart-thumb{width:76px;height:76px;border-radius:12px;object-fit:cover;background:var(--beige);flex-shrink:0;display:flex;align-items:center;justify-content:center;overflow:hidden}
.summary-card{background:var(--warm-white);border-radius:20px;border:1px solid var(--border);padding:22px;position:sticky;top:90px}
.qty-ctrl{display:flex;align-items:center;gap:6px;background:var(--beige);border-radius:20px;padding:3px 5px}
.qty-btn{width:26px;height:26px;border-radius:50%;background:var(--warm-white);border:1px solid var(--border);cursor:pointer;font-size:14px;font-weight:600;display:flex;align-items:center;justify-content:center;transition:all .15s}
.qty-btn:hover{background:var(--sage);color:#fff;border-color:var(--sage)}
.custom-cb{width:20px;height:20px;border-radius:6px;border:2px solid var(--border);display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:all .2s;background:var(--warm-white);cursor:pointer;margin-top:2px}
.custom-cb.checked{background:var(--sage);border-color:var(--sage)}
.select-all-bar{background:var(--warm-white);border-radius:12px;border:1px solid var(--border);padding:12px 16px;display:flex;align-items:center;gap:12px;margin-bottom:12px;font-size:13px;font-weight:600}
@media(max-width:900px){.cart-grid{grid-template-columns:1fr}}
</style>
@endpush

@section('content')
<div class="page-top">
    <div class="container">
        <a href="{{ route('menu.index') }}" style="color:rgba(255,255,255,.6);font-size:13px;display:inline-flex;align-items:center;gap:6px;margin-bottom:12px">
            <i class="ti ti-arrow-left"></i> Lanjut Belanja
        </a>
        <h1 style="font-family:'Playfair Display',serif;font-size:clamp(22px,4vw,32px);font-weight:600;color:#fff">
            Keranjang Belanja
            <span style="font-size:16px;font-weight:400;color:rgba(255,255,255,.6)">({{ $cart->total_items }} item)</span>
        </h1>
    </div>
</div>

<div class="container" style="padding-top:28px;padding-bottom:80px">
    @if($cart->items->isEmpty())
    <div style="text-align:center;padding:80px 20px">
        <div style="font-size:64px;margin-bottom:20px">🛒</div>
        <div style="font-family:'Playfair Display',serif;font-size:24px;font-weight:600;margin-bottom:10px">Keranjang Kosong</div>
        <p style="color:var(--text-muted);margin-bottom:24px">Yuk tambahkan menu favoritmu!</p>
        <a href="{{ route('menu.index') }}" class="btn-primary"><i class="ti ti-bowl-chopsticks"></i> Lihat Menu</a>
    </div>
    @else
    <div class="cart-grid">
        {{-- Items --}}
        <div>
            {{-- Select all bar --}}
            <div class="select-all-bar">
                <div class="custom-cb checked" id="cb-all" onclick="toggleSelectAll()">
                    <i class="ti ti-check" style="font-size:13px;color:#fff"></i>
                </div>
                <span id="select-all-label">Pilih Semua (<span id="selected-count">{{ $cart->items->count() }}</span>/{{ $cart->items->count() }})</span>
                <button onclick="deleteSelected()" style="margin-left:auto;background:none;border:none;cursor:pointer;color:#c0392b;font-size:12px;font-weight:600;display:flex;align-items:center;gap:4px" id="delete-selected-btn">
                    <i class="ti ti-trash" style="font-size:14px"></i> Hapus dipilih
                </button>
            </div>

            <div style="display:flex;flex-direction:column;gap:10px" id="cart-items">
                @foreach($cart->items as $item)
                <div class="cart-item selected" id="item-{{ $item->id }}" onclick="toggleItem({{ $item->id }},this)" data-price="{{ $item->price }}" data-qty="{{ $item->quantity }}">
                    <div class="custom-cb checked" id="cb-{{ $item->id }}">
                        <i class="ti ti-check" style="font-size:13px;color:#fff"></i>
                    </div>
                    <div class="cart-thumb" onclick="event.stopPropagation()">
                        @if($item->menu->image)
                        <img src="{{ $item->menu->image_url }}" style="width:100%;height:100%;object-fit:cover" alt="">
                        @else <span style="font-size:28px">🍱</span> @endif
                    </div>
                    <div style="flex:1;min-width:0" onclick="event.stopPropagation()">
                        <div style="font-weight:700;font-size:14px;margin-bottom:2px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $item->menu->name }}</div>
                        <div style="font-size:12px;color:var(--text-muted);margin-bottom:6px">{{ $item->menu->category->name ?? '' }}</div>
                        @if($item->note)
                        <div style="font-size:11px;background:var(--beige);padding:4px 8px;border-radius:6px;color:var(--text-muted);margin-bottom:6px">📝 {{ $item->note }}</div>
                        @endif
                        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px">
                            <div class="qty-ctrl">
                                <button class="qty-btn" onclick="changeQty({{ $item->id }},-1)">−</button>
                                <span id="qty-{{ $item->id }}" style="font-weight:700;font-size:14px;min-width:24px;text-align:center">{{ $item->quantity }}</span>
                                <button class="qty-btn" onclick="changeQty({{ $item->id }},+1)">+</button>
                            </div>
                            <div style="display:flex;align-items:center;gap:12px">
                                <div style="font-weight:700;font-size:15px;color:var(--sage-dark)" id="sub-{{ $item->id }}">
                                    Rp {{ number_format($item->subtotal,0,',','.') }}
                                </div>
                                <button onclick="removeItem({{ $item->id }})" style="color:var(--text-muted);background:none;border:none;cursor:pointer;font-size:16px;padding:4px;transition:color .2s" onmouseover="this.style.color='#c0392b'" onmouseout="this.style.color='var(--text-muted)'">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Summary --}}
        <div class="summary-card">
            <div style="font-weight:700;font-size:15px;margin-bottom:18px;color:var(--text-main)">
                <i class="ti ti-receipt" style="color:var(--sage)"></i> Ringkasan Pesanan
            </div>

            <div style="background:rgba(90,124,101,.06);border-radius:10px;padding:10px 14px;margin-bottom:14px;font-size:12px;color:var(--sage-dark);display:flex;align-items:center;gap:6px">
                <i class="ti ti-info-circle" style="font-size:14px"></i>
                <span id="selected-info">{{ $cart->items->count() }} item dipilih untuk checkout</span>
            </div>

            <div style="display:flex;flex-direction:column;gap:9px;margin-bottom:14px">
                <div style="display:flex;justify-content:space-between;font-size:14px">
                    <span style="color:var(--text-muted)">Subtotal</span>
                    <span id="summary-subtotal" style="font-weight:600">Rp {{ number_format($cart->total,0,',','.') }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;font-size:14px">
                    <span style="color:var(--text-muted)">Pajak (10%)</span>
                    <span id="summary-tax">Rp {{ number_format($cart->total*0.1,0,',','.') }}</span>
                </div>
            </div>
            <div style="height:1px;background:var(--border);margin-bottom:12px"></div>
            <div style="display:flex;justify-content:space-between;font-size:18px;font-weight:700;margin-bottom:18px">
                <span>Total</span>
                <span id="summary-total" style="color:var(--sage-dark)">Rp {{ number_format($cart->total*1.1,0,',','.') }}</span>
            </div>

            <button onclick="goCheckout()" class="btn-primary" style="width:100%;justify-content:center;display:flex" id="checkout-btn">
                <i class="ti ti-arrow-right"></i> Checkout (<span id="checkout-count">{{ $cart->items->count() }}</span> item)
            </button>
            <a href="{{ route('menu.index') }}" style="display:block;text-align:center;margin-top:12px;font-size:13px;color:var(--text-muted)">+ Tambah menu lagi</a>
        </div>
    </div>
    @endif
</div>

{{-- Hidden form to pass selected items to checkout --}}
<form id="checkout-form" action="{{ route('orders.checkout') }}" method="GET" style="display:none">
    <input type="hidden" name="selected" id="selected-input">
</form>
@endsection

@push('scripts')
<script>
// State
const ITEM_PRICES = {
    @foreach($cart->items as $item)
    {{ $item->id }}: { price: {{ $item->price }}, qty: {{ $item->quantity }} },
    @endforeach
};
let selected = new Set([{{ $cart->items->pluck('id')->join(',') }}]);
let quantities = {
    @foreach($cart->items as $item)
    {{ $item->id }}: {{ $item->quantity }},
    @endforeach
};

function toggleItem(id, el) {
    if(selected.has(id)) { selected.delete(id); el.classList.remove('selected'); document.getElementById('cb-'+id).classList.remove('checked'); document.getElementById('cb-'+id).innerHTML=''; }
    else { selected.add(id); el.classList.add('selected'); document.getElementById('cb-'+id).classList.add('checked'); document.getElementById('cb-'+id).innerHTML='<i class="ti ti-check" style="font-size:13px;color:#fff"></i>'; }
    updateSummary();
}

function toggleSelectAll() {
    const allIds = [{{ $cart->items->pluck('id')->join(',') }}];
    if(selected.size === allIds.length) {
        // deselect all
        selected.clear();
        allIds.forEach(id => { document.getElementById('item-'+id)?.classList.remove('selected'); const cb=document.getElementById('cb-'+id); if(cb){cb.classList.remove('checked');cb.innerHTML='';} });
        document.getElementById('cb-all').classList.remove('checked');
        document.getElementById('cb-all').innerHTML='';
    } else {
        // select all
        allIds.forEach(id => { selected.add(id); document.getElementById('item-'+id)?.classList.add('selected'); const cb=document.getElementById('cb-'+id); if(cb){cb.classList.add('checked');cb.innerHTML='<i class="ti ti-check" style="font-size:13px;color:\'#fff\'"></i>';} });
        document.getElementById('cb-all').classList.add('checked');
        document.getElementById('cb-all').innerHTML='<i class="ti ti-check" style="font-size:13px;color:#fff"></i>';
    }
    updateSummary();
}

function updateSummary() {
    let sub = 0;
    selected.forEach(id => { sub += (ITEM_PRICES[id]?.price||0) * (quantities[id]||1); });
    const tax = sub * 0.1;
    const total = sub + tax;
    document.getElementById('summary-subtotal').textContent = 'Rp ' + sub.toLocaleString('id-ID');
    document.getElementById('summary-tax').textContent = 'Rp ' + Math.round(tax).toLocaleString('id-ID');
    document.getElementById('summary-total').textContent = 'Rp ' + Math.round(total).toLocaleString('id-ID');
    document.getElementById('selected-count').textContent = selected.size;
    document.getElementById('checkout-count').textContent = selected.size;
    document.getElementById('selected-info').textContent = selected.size + ' item dipilih untuk checkout';
    document.getElementById('checkout-btn').disabled = selected.size === 0;
}

function changeQty(itemId, delta) {
    const current = quantities[itemId] || 1;
    const newQty  = current + delta;

    if (newQty <= 0) {
        confirmDanger('Hapus item?', 'Item akan dihapus dari keranjang.', () => {
            updateQty(itemId, 0);
        }, 'Hapus');
        return;
    }
    updateQty(itemId, newQty);
}

function updateQty(itemId, newQty) {
    fetch('/cart/update/' + itemId, {
        method: 'PUT',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ quantity: newQty })
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            if (newQty === 0) {
                const el = document.getElementById('item-' + itemId);
                if (el) { el.style.opacity = '0'; setTimeout(() => el.remove(), 200); }
                selected.delete(itemId);
                delete quantities[itemId];
                delete ITEM_PRICES[itemId];
            } else {
                quantities[itemId] = newQty;
                document.getElementById('qty-' + itemId).textContent = newQty;
                const price = ITEM_PRICES[itemId]?.price || 0;
                document.getElementById('sub-' + itemId).textContent = 'Rp ' + (price * newQty).toLocaleString('id-ID');
            }
            updateSummary();
        } else {
            showToast(d.message || 'Gagal update', 'error');
        }
    })
    .catch(() => showToast('Terjadi kesalahan', 'error'));
}

function removeItem(itemId) {
    confirmDanger('Hapus item?','Item akan dihapus dari keranjang.',()=>{
        fetch('/cart/remove/'+itemId,{method:'DELETE',headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json'}})
        .then(r=>r.json()).then(d=>{
            const el=document.getElementById('item-'+itemId);
            if(el){el.style.opacity='0';setTimeout(()=>el.remove(),200);}
            selected.delete(itemId);
            delete quantities[itemId];
            updateSummary();
            showToast('Item dihapus','info');
        });
    },'Hapus');
}

function deleteSelected() {
    if(selected.size===0){showToast('Pilih item dulu','error');return;}
    confirmDanger('Hapus item dipilih?',selected.size+' item akan dihapus dari keranjang.',()=>{
        const ids=[...selected];
        Promise.all(ids.map(id=>fetch('/cart/remove/'+id,{method:'DELETE',headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json'}}).then(r=>r.json())))
        .then(()=>{
            ids.forEach(id=>{
                const el=document.getElementById('item-'+id);
                if(el)el.remove();
                selected.delete(id);
                delete quantities[id];
            });
            updateSummary();
            showToast(ids.length+' item dihapus','info');
        });
    },'Hapus Semua');
}

function goCheckout() {
    if(selected.size===0){showToast('Pilih minimal 1 item','error');return;}
    document.getElementById('selected-input').value=[...selected].join(',');
    document.getElementById('checkout-form').submit();
}

// Init — render checkmarks
[{{ $cart->items->pluck('id')->join(',') }}].forEach(id=>{
    const cb=document.getElementById('cb-'+id);
    if(cb){cb.classList.add('checked');cb.innerHTML='<i class="ti ti-check" style="font-size:13px;color:#fff"></i>';}
});
updateSummary();
</script>
@endpush