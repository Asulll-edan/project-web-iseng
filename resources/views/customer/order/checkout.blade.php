@extends('layouts.app')
@section('title','Checkout — Rumahnya Anak Sekolah')

@push('styles')
<style>
.page-top{padding:110px 0 40px;background:linear-gradient(135deg,#1a2e22,#2c3e35)}
.checkout-layout{display:grid;grid-template-columns:1fr 360px;gap:28px;padding:36px 0 80px;align-items:start}
.checkout-section{background:var(--warm-white);border-radius:20px;border:1px solid var(--border);padding:24px;margin-bottom:20px}
.checkout-section h3{font-weight:700;font-size:15px;margin-bottom:18px;display:flex;align-items:center;gap:8px}
.payment-option{display:flex;align-items:center;gap:14px;padding:14px 16px;border-radius:12px;border:1.5px solid var(--border);cursor:pointer;transition:all .2s;margin-bottom:10px}
.payment-option:has(input:checked){border-color:var(--sage);background:rgba(90,124,101,.05)}
.payment-option input{accent-color:var(--sage);width:18px;height:18px}
.table-option{display:flex;align-items:center;gap:10px;padding:10px 14px;border-radius:10px;border:1.5px solid var(--border);cursor:pointer;transition:all .2s;font-size:13px}
.table-option:has(input:checked){border-color:var(--sage);background:rgba(90,124,101,.05)}
.summary-box{background:var(--warm-white);border-radius:20px;border:1px solid var(--border);padding:24px;position:sticky;top:100px}
.voucher-input{display:flex;gap:8px}
.voucher-input input{flex:1;padding:10px 14px;border-radius:10px;border:1.5px solid var(--border);background:var(--beige);color:var(--text-main);font-size:14px;font-family:inherit;outline:none;transition:all .2s;text-transform:uppercase}
.voucher-input input:focus{border-color:var(--sage);background:#fff}
@media(max-width:768px){.checkout-layout{grid-template-columns:1fr}.summary-box{position:static}}
</style>
@endpush

@section('content')
<div class="page-top">
    <div class="container">
        <h1 style="font-family:'Playfair Display',serif;font-size:28px;font-weight:600;color:#fff">Checkout</h1>
        <div style="font-size:13px;color:rgba(255,255,255,.6);margin-top:6px">{{ $cart->total_items }} item siap dipesan</div>
    </div>
</div>

<div class="container">
<form action="{{ route('orders.store') }}" method="POST" id="checkout-form">
@csrf
<div class="checkout-layout">
    {{-- Left --}}
    <div>
        {{-- Order type --}}
        <div class="checkout-section">
            <h3><i class="ti ti-tools-kitchen-2" style="color:var(--sage)"></i> Jenis Pesanan</h3>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
                <label class="payment-option">
                    <input type="radio" name="order_type" value="dine_in" checked>
                    <div>
                        <div style="font-weight:600;font-size:14px">Makan di Tempat</div>
                        <div style="font-size:12px;color:var(--text-muted)">Dine In</div>
                    </div>
                    <i class="ti ti-armchair" style="font-size:20px;color:var(--text-muted);margin-left:auto"></i>
                </label>
                <label class="payment-option">
                    <input type="radio" name="order_type" value="takeaway">
                    <div>
                        <div style="font-weight:600;font-size:14px">Bawa Pulang</div>
                        <div style="font-size:12px;color:var(--text-muted)">Takeaway</div>
                    </div>
                    <i class="ti ti-package" style="font-size:20px;color:var(--text-muted);margin-left:auto"></i>
                </label>
            </div>
        </div>

        {{-- Table select --}}
        @if($tables->count())
        <div class="checkout-section" id="table-section">
            <h3><i class="ti ti-table" style="color:var(--sage)"></i> Pilih Meja</h3>
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(110px,1fr));gap:8px">
                @foreach($tables as $table)
                <label class="table-option">
                    <input type="radio" name="table_number" value="{{ $table->table_number }}" style="accent-color:var(--sage)">
                    <div>
                        <div style="font-weight:700;font-size:14px">{{ $table->table_number }}</div>
                        <div style="font-size:11px;color:var(--text-muted)">{{ $table->capacity }} org · {{ $table->location }}</div>
                    </div>
                </label>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Payment --}}
        <div class="checkout-section">
            <h3><i class="ti ti-credit-card" style="color:var(--sage)"></i> Metode Pembayaran</h3>
            <label class="payment-option">
                <input type="radio" name="payment_method" value="wallet" checked>
                <div style="width:36px;height:36px;background:rgba(90,124,101,.1);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <i class="ti ti-wallet" style="color:var(--sage);font-size:18px"></i>
                </div>
                <div style="flex:1">
                    <div style="font-weight:600;font-size:14px">SOHIBA Wallet</div>
                    <div style="font-size:12px;color:var(--sage-dark);font-weight:600">
                        Saldo: Rp {{ number_format($wallet ? $wallet->balance : 0,0,',','.') }}
                    </div>
                </div>
            </label>
            <label class="payment-option">
                <input type="radio" name="payment_method" value="cash">
                <div style="width:36px;height:36px;background:rgba(245,158,11,.1);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <i class="ti ti-cash" style="color:#d97706;font-size:18px"></i>
                </div>
                <div>
                    <div style="font-weight:600;font-size:14px">Bayar di Kasir</div>
                    <div style="font-size:12px;color:var(--text-muted)">Tunai / Cash</div>
                </div>
            </label>
            <label class="payment-option">
                <input type="radio" name="payment_method" value="transfer">
                <div style="width:36px;height:36px;background:rgba(59,130,246,.1);border-radius:8px;display:flex;align-items:center;justify-content:justify-content:center;flex-shrink:0">
                    <i class="ti ti-building-bank" style="color:#2563eb;font-size:18px"></i>
                </div>
                <div>
                    <div style="font-weight:600;font-size:14px">Transfer Bank</div>
                    <div style="font-size:12px;color:var(--text-muted)">BCA / Mandiri / BNI</div>
                </div>
            </label>
        </div>

        {{-- Notes --}}
        <div class="checkout-section">
            <h3><i class="ti ti-notes" style="color:var(--sage)"></i> Catatan (Opsional)</h3>
            <textarea name="notes" rows="3" placeholder="Contoh: tidak pedas, alergi kacang, dll."
                style="width:100%;padding:12px 14px;border-radius:12px;border:1.5px solid var(--border);background:var(--beige);color:var(--text-main);font-size:14px;font-family:inherit;outline:none;resize:none;transition:all .2s"
                onfocus="this.style.borderColor='var(--sage)'" onblur="this.style.borderColor='var(--border)'"></textarea>
        </div>
    </div>

    {{-- Right: Summary --}}
    <div class="summary-box" data-aos="fade-left">
        <h3 style="font-weight:700;font-size:16px;margin-bottom:18px">Ringkasan Pesanan</h3>

        {{-- Items --}}
        <div style="display:flex;flex-direction:column;gap:10px;margin-bottom:18px">
            @foreach($cart->items as $item)
            <div style="display:flex;gap:10px;align-items:center">
                <div style="width:40px;height:40px;border-radius:8px;background:var(--beige);overflow:hidden;flex-shrink:0;display:flex;align-items:center;justify-content:center">
                    @if($item->menu->image)<img src="{{ $item->menu->image_url }}" style="width:100%;height:100%;object-fit:cover">@else<span style="font-size:16px">🍱</span>@endif
                </div>
                <div style="flex:1;min-width:0">
                    <div style="font-size:12px;font-weight:600;display:-webkit-box;-webkit-line-clamp:1;-webkit-box-orient:vertical;overflow:hidden">{{ $item->menu->name }}</div>
                    <div style="font-size:11px;color:var(--text-muted)">x{{ $item->quantity }}</div>
                </div>
                <div style="font-weight:600;font-size:13px;flex-shrink:0">Rp {{ number_format($item->subtotal,0,',','.') }}</div>
            </div>
            @endforeach
        </div>

        {{-- Voucher --}}
        <div style="margin-bottom:18px">
            <div style="font-size:12px;font-weight:600;margin-bottom:8px;color:var(--text-muted)">KODE VOUCHER</div>
            <div class="voucher-input">
                <input type="text" id="voucher-input" name="voucher_code" placeholder="Masukkan kode" maxlength="20">
                <button type="button" onclick="applyVoucher()" style="padding:10px 16px;background:var(--sage);color:#fff;border:none;border-radius:10px;font-weight:600;font-size:13px;cursor:pointer">Pakai</button>
            </div>
            <div id="voucher-msg" style="font-size:12px;margin-top:6px;display:none"></div>
        </div>

        {{-- Totals --}}
        @php $tax = $cart->total * 0.1; @endphp
        <div style="display:flex;flex-direction:column;gap:10px;font-size:14px;margin-bottom:20px">
            <div style="display:flex;justify-content:space-between">
                <span style="color:var(--text-muted)">Subtotal</span>
                <span>Rp {{ number_format($cart->total,0,',','.') }}</span>
            </div>
            <div style="display:flex;justify-content:space-between" id="discount-row" style="display:none">
                <span style="color:var(--sage-dark)">Diskon Voucher</span>
                <span style="color:var(--sage-dark)" id="discount-val">- Rp 0</span>
            </div>
            <div style="display:flex;justify-content:space-between">
                <span style="color:var(--text-muted)">Pajak (10%)</span>
                <span>Rp {{ number_format($tax,0,',','.') }}</span>
            </div>
            <div style="height:1px;background:var(--border)"></div>
            <div style="display:flex;justify-content:space-between;font-weight:700;font-size:17px">
                <span>Total</span>
                <span style="color:var(--sage-dark)">Rp {{ number_format($cart->total + $tax,0,',','.') }}</span>
            </div>
        </div>

        <button type="submit" class="btn-primary" style="width:100%;justify-content:center;padding:14px">
            <i class="ti ti-circle-check"></i> Konfirmasi Pesanan
        </button>
        <a href="{{ route('cart.index') }}" style="display:flex;align-items:center;justify-content:center;gap:6px;margin-top:12px;font-size:13px;color:var(--text-muted)">
            <i class="ti ti-arrow-left" style="font-size:14px"></i> Kembali ke keranjang
        </a>
    </div>
</div>
</form>
</div>
@endsection

@push('scripts')
<script>
// hide table for takeaway
document.querySelectorAll('input[name="order_type"]').forEach(r => {
    r.addEventListener('change', function() {
        const ts = document.getElementById('table-section');
        if (ts) ts.style.display = this.value === 'takeaway' ? 'none' : 'block';
    });
});

function applyVoucher() {
    const code = document.getElementById('voucher-input').value.trim();
    if (!code) return;
    const msg = document.getElementById('voucher-msg');
    msg.style.display = 'block';
    msg.textContent = 'Mengecek voucher...';
    msg.style.color = 'var(--text-muted)';

    fetch('/orders/check-voucher', {
        method: 'POST',
        headers: {'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json'},
        body: JSON.stringify({code: code})
    })
    .then(r => r.json())
    .then(d => {
        if (d.valid) {
            msg.textContent = '✓ ' + d.message;
            msg.style.color = 'var(--sage-dark)';
            const dr = document.getElementById('discount-row');
            if (dr) { dr.style.display = 'flex'; }
            const dv = document.getElementById('discount-val');
            if (dv) dv.textContent = '- Rp ' + Number(d.discount).toLocaleString('id-ID');
        } else {
            msg.textContent = '✗ ' + d.message;
            msg.style.color = '#c0392b';
        }
    });
}
</script>
@endpush
