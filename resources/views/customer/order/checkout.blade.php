@extends('layouts.app')
@section('title','Checkout')

@push('styles')
<style>
.page-top{padding:110px 0 40px;background:linear-gradient(135deg,#1a2e22,#2c3e35)}
.checkout-grid{display:grid;grid-template-columns:1fr 360px;gap:24px;align-items:start}
.section-card{background:var(--warm-white);border-radius:20px;border:1px solid var(--border);padding:22px;margin-bottom:16px}
.section-title{font-weight:700;font-size:15px;color:var(--text-main);margin-bottom:16px;padding-bottom:12px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:8px}
.pay-method{display:flex;align-items:center;gap:12px;padding:13px 16px;border-radius:12px;border:2px solid var(--border);cursor:pointer;transition:all .2s;margin-bottom:8px}
.pay-method:hover{border-color:var(--sage);background:rgba(90,124,101,.03)}
.pay-method.selected{border-color:var(--sage);background:rgba(90,124,101,.06)}
.pay-method input[type=radio]{display:none}
.pay-icon{width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0}
.pay-name{font-weight:700;font-size:14px;color:var(--text-main)}
.pay-desc{font-size:11px;color:var(--text-muted);margin-top:2px}
.bank-dropdown{background:var(--beige);border-radius:12px;padding:14px;margin-top:8px;display:none}
.bank-option{display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:10px;border:1.5px solid var(--border);cursor:pointer;transition:all .2s;margin-bottom:7px;background:var(--warm-white)}
.bank-option:hover{border-color:var(--sage)}
.bank-option.selected{border-color:var(--sage);background:rgba(90,124,101,.06)}
.bank-option input{display:none}
.bank-logo{width:44px;height:28px;border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:800;flex-shrink:0}
.va-box{background:linear-gradient(135deg,var(--sage-dark),var(--sage));border-radius:14px;padding:18px;margin-top:12px;display:none}
.va-number{font-size:24px;font-weight:700;color:#fff;letter-spacing:.1em;font-variant-numeric:tabular-nums}
.qris-box{background:var(--beige);border-radius:14px;padding:20px;text-align:center;margin-top:12px;display:none}
.qris-img{width:200px;height:200px;border-radius:12px;border:3px solid var(--sage);margin:0 auto 12px;background:#fff;display:flex;align-items:center;justify-content:center;overflow:hidden}
.order-type-btn{flex:1;padding:11px;border-radius:10px;border:2px solid var(--border);background:transparent;cursor:pointer;font-weight:600;font-size:13px;display:flex;align-items:center;justify-content:center;gap:7px;transition:all .2s;color:var(--text-muted)}
.order-type-btn.selected{border-color:var(--sage);color:var(--sage-dark);background:rgba(90,124,101,.06)}
.summary-card{background:var(--warm-white);border-radius:20px;border:1px solid var(--border);padding:22px;position:sticky;top:90px}
.item-row{display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:1px solid var(--border);font-size:13px}
.item-row:last-child{border:none}
@media(max-width:900px){.checkout-grid{grid-template-columns:1fr}.summary-card{position:static}}
</style>
@endpush

@section('content')
<div class="page-top">
    <div class="container">
        <a href="{{ route('cart.index') }}" style="color:rgba(255,255,255,.6);font-size:13px;display:inline-flex;align-items:center;gap:6px;margin-bottom:12px">
            <i class="ti ti-arrow-left"></i> Kembali ke Keranjang
        </a>
        <h1 style="font-family:'INeedCoffee',serif;font-size:clamp(22px,4vw,32px);font-weight:600;color:#fff">Checkout</h1>
    </div>
</div>

<div class="container" style="padding-top:28px;padding-bottom:80px">
    <form action="{{ route('orders.store') }}" method="POST" id="checkout-form">
        @csrf
        {{-- Pass selected items dari cart --}}
        <input type="hidden" name="selected_items" value="{{ request('selected') }}">

        <div class="checkout-grid">
            {{-- Left --}}
            <div>
                {{-- Order type --}}
                <div class="section-card">
                    <div class="section-title"><i class="ti ti-tools-kitchen" style="color:var(--sage)"></i> Tipe Pesanan</div>
                    <div style="display:flex;gap:10px">
                        <button type="button" class="order-type-btn selected" id="btn-dine" onclick="setOrderType('dine_in',this)">
                            <i class="ti ti-armchair" style="font-size:18px"></i> Makan di Tempat
                        </button>
                        <button type="button" class="order-type-btn" id="btn-take" onclick="setOrderType('takeaway',this)">
                            <i class="ti ti-shopping-bag" style="font-size:18px"></i> Takeaway / Bawa Pulang
                        </button>
                    </div>
                    <input type="hidden" name="order_type" id="order_type" value="dine_in">

                    {{-- Table selection --}}
                    <div id="table-section" style="margin-top:14px">
                        <label class="form-label" style="font-size:13px;font-weight:600;color:var(--text-main);display:block;margin-bottom:8px">Pilih Meja</label>
                        <select name="table_number" class="form-select">
                            <option value="">— Pilih meja —</option>
                            @foreach($tables as $table)
                            <option value="{{ $table->table_number }}">Meja {{ $table->table_number }} ({{ $table->location }}, max {{ $table->capacity }} org)</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Payment Method --}}
                <div class="section-card">
                    <div class="section-title"><i class="ti ti-credit-card" style="color:var(--sage)"></i> Metode Pembayaran</div>

                    {{-- Wallet --}}
                    <label class="pay-method {{ $wallet && $wallet->balance >= 0 ? '' : 'opacity-50' }}" onclick="selectPayment('wallet',this)">
                        <input type="radio" name="payment_method" value="wallet">
                        <div class="pay-icon" style="background:rgba(90,124,101,.1)">💳</div>
                        <div style="flex:1">
                            <div class="pay-name">SOHIBA Wallet</div>
                            <div class="pay-desc">Saldo: Rp {{ number_format($wallet->balance ?? 0,0,',','.') }}</div>
                        </div>
                        @if($wallet && $wallet->balance > 0)
                        <span class="badge badge-green" style="font-size:10px">Tersedia</span>
                        @endif
                    </label>

                    {{-- QRIS --}}
                    <label class="pay-method" onclick="selectPayment('qris',this)">
                        <input type="radio" name="payment_method" value="qris">
                        <div class="pay-icon" style="background:rgba(168,85,247,.08)">📱</div>
                        <div style="flex:1">
                            <div class="pay-name">QRIS</div>
                            <div class="pay-desc">Scan QR code — semua e-wallet & m-banking</div>
                        </div>
                        <span class="badge badge-sage" style="font-size:10px">Instan</span>
                    </label>

                    {{-- QRIS Box --}}
                    <div class="qris-box" id="qris-box">
                        <div style="font-weight:700;font-size:14px;margin-bottom:12px;color:var(--text-main)">Scan QR Code ini untuk membayar</div>
                        <div class="qris-img" id="qris-img">
                            {{-- QR akan di-generate via JS --}}
                            <div style="text-align:center;padding:20px">
                                <i class="ti ti-qrcode" style="font-size:64px;color:var(--sage)"></i>
                                <div style="font-size:12px;color:var(--text-muted);margin-top:8px">QR Code akan muncul setelah total dikonfirmasi</div>
                            </div>
                        </div>
                        <div style="font-size:13px;color:var(--text-muted);margin-bottom:4px">Total Pembayaran</div>
                        <div style="font-size:22px;font-weight:700;color:var(--sage-dark)" id="qris-total">Rp 0</div>
                        <div style="font-size:11px;color:var(--text-muted);margin-top:6px">
                            <i class="ti ti-info-circle" style="font-size:13px"></i> QR berlaku 15 menit
                        </div>
                    </div>

                    {{-- Transfer Bank --}}
                    <label class="pay-method" onclick="selectPayment('transfer',this)">
                        <input type="radio" name="payment_method" value="transfer">
                        <div class="pay-icon" style="background:rgba(59,130,246,.08)">🏦</div>
                        <div style="flex:1">
                            <div class="pay-name">Transfer Bank</div>
                            <div class="pay-desc">BCA, Mandiri, BNI, BRI — Virtual Account</div>
                        </div>
                    </label>

                    {{-- Bank Dropdown --}}
                    <div class="bank-dropdown" id="bank-dropdown">
                        <div style="font-size:12px;font-weight:700;color:var(--text-muted);margin-bottom:10px;text-transform:uppercase;letter-spacing:.05em">Pilih Bank</div>
                        @php
                        $banks = [
                            'bca'     => ['BCA',   '#005BAC','#fff','1234567890'],
                            'mandiri' => ['MDR',   '#003D82','#F5A623','9876543210'],
                            'bni'     => ['BNI',   '#F05023','#fff','1122334455'],
                            'bri'     => ['BRI',   '#00529C','#F5A623','5544332211'],
                        ];
                        @endphp
                        @foreach($banks as $code => $bank)
                        <label class="bank-option" onclick="selectBank('{{ $code }}','{{ $bank[0] }}','{{ $bank[3] }}',this)">
                            <input type="radio" name="bank_code" value="{{ $code }}">
                            <div class="bank-logo" style="background:{{ $bank[1] }};color:{{ $bank[2] }}">{{ $bank[0] }}</div>
                            <div>
                                <div style="font-weight:700;font-size:13px">Bank {{ $bank[0] === 'MDR' ? 'Mandiri' : $bank[0] }}</div>
                                <div style="font-size:11px;color:var(--text-muted)">Virtual Account tersedia</div>
                            </div>
                        </label>
                        @endforeach

                        {{-- VA Box --}}
                        <div class="va-box" id="va-box">
                            <div style="font-size:12px;color:rgba(255,255,255,.7);margin-bottom:4px" id="va-bank-name">Nomor Virtual Account BCA</div>
                            <div class="va-number" id="va-number">0000 0000 0000</div>
                            <div style="font-size:11px;color:rgba(255,255,255,.7);margin-top:6px">a.n. Rumahnya Anak Sekolah</div>
                            <div style="background:rgba(255,255,255,.15);border-radius:10px;padding:10px 14px;margin-top:12px;display:flex;align-items:center;justify-content:space-between">
                                <div style="font-size:12px;color:rgba(255,255,255,.8)">Total Transfer</div>
                                <div style="font-weight:700;color:#fff;font-size:16px" id="va-total">Rp 0</div>
                            </div>
                        </div>

                        {{-- Cara transfer --}}
                        <div id="transfer-steps" style="display:none;margin-top:12px;background:var(--warm-white);border-radius:10px;padding:14px">
                            <div style="font-weight:700;font-size:13px;margin-bottom:10px;color:var(--text-main)">📋 Cara Transfer:</div>
                            <div id="steps-content" style="font-size:12px;color:var(--text-muted);line-height:2"></div>
                        </div>
                    </div>

                    {{-- Cash --}}
                    <label class="pay-method" onclick="selectPayment('cash',this)">
                        <input type="radio" name="payment_method" value="cash">
                        <div class="pay-icon" style="background:rgba(16,185,129,.08)">💵</div>
                        <div style="flex:1">
                            <div class="pay-name">Tunai / Cash</div>
                            <div class="pay-desc">Bayar langsung di kasir</div>
                        </div>
                    </label>
                </div>

                {{-- Voucher --}}
                <div class="section-card">
                    <div class="section-title"><i class="ti ti-ticket" style="color:var(--sage)"></i> Voucher / Promo</div>
                    <div style="display:flex;gap:8px">
                        <input type="text" id="voucher-input" placeholder="Masukkan kode voucher" style="flex:1;padding:10px 14px;border-radius:10px;border:1.5px solid var(--border);background:var(--beige);font-size:14px;font-family:inherit;outline:none;text-transform:uppercase;transition:border-color .2s" oninput="this.value=this.value.toUpperCase()">
                        <button type="button" onclick="applyVoucher()" class="btn-primary" style="padding:10px 18px;font-size:13px">Pakai</button>
                    </div>
                    <div id="voucher-result" style="margin-top:8px;font-size:13px;display:none"></div>
                    <input type="hidden" name="voucher_code" id="voucher-code">
                </div>

                {{-- Notes --}}
                <div class="section-card">
                    <div class="section-title"><i class="ti ti-notes" style="color:var(--sage)"></i> Catatan (opsional)</div>
                    <textarea name="notes" placeholder="Catatan untuk dapur atau kasir..." rows="2"
                        style="width:100%;padding:10px 14px;border-radius:10px;border:1.5px solid var(--border);background:var(--beige);font-size:14px;font-family:inherit;outline:none;resize:none"></textarea>
                </div>
            </div>

            {{-- Right: Summary --}}
            <div>
                <div class="summary-card">
                    <div style="font-weight:700;font-size:15px;margin-bottom:16px;color:var(--text-main)">
                        <i class="ti ti-receipt" style="color:var(--sage)"></i> Ringkasan
                    </div>

                    {{-- Selected items --}}
                    <div style="max-height:220px;overflow-y:auto;margin-bottom:14px" id="items-list">
                        @foreach($cart->items as $item)
                        <div class="item-row">
                            <div style="flex:1;font-size:13px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $item->menu->name }}</div>
                            <div style="font-size:12px;color:var(--text-muted);flex-shrink:0">×{{ $item->quantity }}</div>
                            <div style="font-weight:600;font-size:13px;min-width:80px;text-align:right;flex-shrink:0">Rp {{ number_format($item->subtotal,0,',','.') }}</div>
                        </div>
                        @endforeach
                    </div>

                    <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:12px">
                        <div style="display:flex;justify-content:space-between;font-size:13px">
                            <span style="color:var(--text-muted)">Subtotal</span>
                            <span id="sum-subtotal" style="font-weight:600">Rp {{ number_format($cart->total,0,',','.') }}</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;font-size:13px" id="discount-row" style="display:none">
                            <span style="color:#16a34a">Diskon Voucher</span>
                            <span id="sum-discount" style="font-weight:600;color:#16a34a">-Rp 0</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;font-size:13px">
                            <span style="color:var(--text-muted)">Pajak (10%)</span>
                            <span id="sum-tax">Rp {{ number_format($cart->total*0.1,0,',','.') }}</span>
                        </div>
                    </div>
                    <div style="height:1px;background:var(--border);margin-bottom:12px"></div>
                    <div style="display:flex;justify-content:space-between;font-size:18px;font-weight:700;margin-bottom:20px">
                        <span>Total</span>
                        <span id="sum-total" style="color:var(--sage-dark)">Rp {{ number_format($cart->total*1.1,0,',','.') }}</span>
                    </div>

                    <button type="submit" class="btn-primary" style="width:100%;justify-content:center;padding:14px;font-size:15px" id="submit-btn">
                        <i class="ti ti-check"></i> Konfirmasi Pesanan
                    </button>

                    <div style="margin-top:12px;font-size:11px;color:var(--text-muted);text-align:center">
                        Dengan memesan, kamu menyetujui syarat & ketentuan kami
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/qrious@4.0.2/dist/qrious.min.js"></script>
<script>
const BASE_TOTAL = {{ $cart->total * 1.1 }};
let currentTotal = BASE_TOTAL;
let discount = 0;
let selectedPayment = '';
let selectedBank = '';

const BANK_VA = {
    bca:     {name:'BCA',     va:'1234567890', steps:['Buka aplikasi BCA Mobile / ATM BCA','Pilih Transfer → Virtual Account','Masukkan nomor VA: <strong>1234567890</strong>','Masukkan nominal sesuai tagihan','Konfirmasi dan selesai']},
    mandiri: {name:'Mandiri', va:'9876543210', steps:['Buka Livin by Mandiri / ATM Mandiri','Pilih Pembayaran → Multi Payment','Masukkan kode perusahaan: 88908','Masukkan nomor VA: <strong>9876543210</strong>','Konfirmasi dan selesai']},
    bni:     {name:'BNI',     va:'1122334455', steps:['Buka BNI Mobile Banking / ATM BNI','Pilih Transfer → Virtual Account','Masukkan nomor VA: <strong>1122334455</strong>','Masukkan jumlah transfer sesuai tagihan','Konfirmasi dan selesai']},
    bri:     {name:'BRI',     va:'5544332211', steps:['Buka BRImo / ATM BRI','Pilih Pembayaran → BRIVA','Masukkan nomor BRIVA: <strong>5544332211</strong>','Masukkan jumlah sesuai tagihan','Konfirmasi dan selesai']},
};

function setOrderType(type, btn) {
    document.querySelectorAll('.order-type-btn').forEach(b=>b.classList.remove('selected'));
    btn.classList.add('selected');
    document.getElementById('order_type').value = type;
    document.getElementById('table-section').style.display = type==='dine_in' ? 'block' : 'none';
}

function selectPayment(method, el) {
    document.querySelectorAll('.pay-method').forEach(m=>m.classList.remove('selected'));
    el.classList.add('selected');
    el.querySelector('input').checked = true;
    selectedPayment = method;
    document.getElementById('bank-dropdown').style.display = method==='transfer' ? 'block' : 'none';
    document.getElementById('qris-box').style.display = method==='qris' ? 'block' : 'none';
    if(method==='qris') generateQR();
}

function selectBank(code, name, va, el) {
    event.stopPropagation();
    document.querySelectorAll('.bank-option').forEach(b=>b.classList.remove('selected'));
    el.classList.add('selected');
    el.querySelector('input').checked = true;
    selectedBank = code;
    const info = BANK_VA[code];
    document.getElementById('va-box').style.display = 'block';
    document.getElementById('va-bank-name').textContent = 'Nomor Virtual Account ' + info.name;
    document.getElementById('va-number').textContent = info.va.replace(/(.{4})/g,'$1 ').trim();
    document.getElementById('va-total').textContent = 'Rp ' + Math.round(currentTotal).toLocaleString('id-ID');
    const steps = document.getElementById('transfer-steps');
    steps.style.display = 'block';
    document.getElementById('steps-content').innerHTML = info.steps.map((s,i)=>`<div style="display:flex;gap:8px;margin-bottom:4px"><span style="background:var(--sage);color:#fff;width:18px;height:18px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;flex-shrink:0">${i+1}</span>${s}</div>`).join('');
}

function generateQR() {
    const container = document.getElementById('qris-img');
    container.innerHTML = '';
    const canvas = document.createElement('canvas');
    container.appendChild(canvas);
    new QRious({
        element: canvas,
        value: 'QRIS:RAS:' + Math.round(currentTotal) + ':' + Date.now(),
        size: 180,
        backgroundAlpha: 1,
        foreground: '#2c3e35',
        background: '#ffffff',
    });
    document.getElementById('qris-total').textContent = 'Rp ' + Math.round(currentTotal).toLocaleString('id-ID');
}

function applyVoucher() {
    const code = document.getElementById('voucher-input').value.trim();
    if(!code){ showToast('Masukkan kode voucher dulu','error'); return; }
    fetch('/orders/check-voucher',{
        method:'POST',
        headers:{'X-CSRF-TOKEN':CSRF,'Content-Type':'application/json','Accept':'application/json'},
        body: JSON.stringify({code, subtotal: {{ $cart->total }} })
    }).then(r=>r.json()).then(d=>{
        const res = document.getElementById('voucher-result');
        res.style.display = 'block';
        if(d.valid){
            discount = d.discount;
            document.getElementById('voucher-code').value = code;
            res.innerHTML = '<span style="color:#16a34a;font-weight:600"><i class="ti ti-circle-check"></i> '+d.message+'</span>';
            showToast(d.message,'success');
        } else {
            discount = 0;
            document.getElementById('voucher-code').value = '';
            res.innerHTML = '<span style="color:#c0392b"><i class="ti ti-alert-circle"></i> '+d.message+'</span>';
        }
        recalcTotal();
    });
}

function recalcTotal() {
    const sub = {{ $cart->total }};
    const afterDisc = sub - discount;
    const tax = afterDisc * 0.1;
    currentTotal = afterDisc + tax;
    document.getElementById('sum-subtotal').textContent = 'Rp ' + sub.toLocaleString('id-ID');
    document.getElementById('sum-tax').textContent = 'Rp ' + Math.round(tax).toLocaleString('id-ID');
    document.getElementById('sum-total').textContent = 'Rp ' + Math.round(currentTotal).toLocaleString('id-ID');
    const dr = document.getElementById('discount-row');
    if(discount>0){ dr.style.display='flex'; document.getElementById('sum-discount').textContent='-Rp '+discount.toLocaleString('id-ID'); }
    else dr.style.display='none';
    if(selectedBank) document.getElementById('va-total').textContent = 'Rp ' + Math.round(currentTotal).toLocaleString('id-ID');
    if(selectedPayment==='qris') { document.getElementById('qris-total').textContent = 'Rp ' + Math.round(currentTotal).toLocaleString('id-ID'); generateQR(); }
}

// Form submit validation
document.getElementById('checkout-form').addEventListener('submit', function(e) {
    if(!selectedPayment){ e.preventDefault(); Swal.fire({title:'Pilih metode pembayaran',icon:'warning',confirmButtonColor:'#3d5c47'}); return; }
    if(selectedPayment==='transfer' && !selectedBank){ e.preventDefault(); Swal.fire({title:'Pilih bank tujuan',icon:'warning',confirmButtonColor:'#3d5c47'}); return; }
    if(selectedPayment==='wallet'){
        const bal = {{ $wallet->balance ?? 0 }};
        if(bal < currentTotal){ e.preventDefault(); Swal.fire({title:'Saldo tidak cukup',text:'Saldo wallet kamu Rp '+bal.toLocaleString('id-ID')+', perlu Rp '+Math.round(currentTotal).toLocaleString('id-ID'),icon:'error',confirmButtonColor:'#3d5c47'}); return; }
    }
    const btn = document.getElementById('submit-btn');
    btn.disabled = true;
    btn.innerHTML = '<i class="ti ti-loader" style="animation:spin 1s linear infinite"></i> Memproses...';
});
</script>
<style>@keyframes spin{to{transform:rotate(360deg)}}</style>
@endpush

