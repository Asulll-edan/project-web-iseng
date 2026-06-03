@extends('layouts.app')
@section('title','Topup Wallet')

@section('content')
<div style="padding:110px 0 40px;background:linear-gradient(135deg,#1a2e22,#2c3e35)">
    <div class="container">
        <h1 style="font-family:'INeedCoffee',serif;font-size:28px;font-weight:600;color:#fff">Topup SOHIBA Wallet</h1>
        <p style="font-size:13px;color:rgba(255,255,255,.6);margin-top:6px">Saldo saat ini: <strong style="color:#fff">Rp {{ number_format($wallet ? $wallet->balance : 0,0,',','.') }}</strong></p>
    </div>
</div>

<div class="container" style="padding:40px 0 80px;max-width:580px">
    @if($errors->any())
    <div style="background:rgba(220,80,60,.08);border:1px solid rgba(220,80,60,.2);border-radius:12px;padding:14px 16px;margin-bottom:20px">
        @foreach($errors->all() as $e)<div style="font-size:13px;color:#c0392b">• {{ $e }}</div>@endforeach
    </div>
    @endif

    <form action="{{ route('wallet.topup.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div style="background:var(--warm-white);border-radius:20px;border:1px solid var(--border);padding:28px;margin-bottom:20px">
            <h3 style="font-weight:700;font-size:15px;margin-bottom:20px"><i class="ti ti-cash" style="color:var(--sage)"></i> Pilih Nominal</h3>
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-bottom:16px">
                @foreach([10000,25000,50000,100000,200000,500000] as $nom)
                <button type="button" onclick="setAmount({{ $nom }})"
                    style="padding:12px 8px;border-radius:12px;border:1.5px solid var(--border);background:var(--beige);font-weight:600;font-size:13px;cursor:pointer;transition:all .2s"
                    onmouseover="this.style.borderColor='var(--sage)';this.style.color='var(--sage-dark)'"
                    onmouseout="this.style.borderColor='var(--border)';this.style.color='inherit'">
                    Rp {{ number_format($nom,0,',','.') }}
                </button>
                @endforeach
            </div>
            <div>
                <label style="font-size:13px;font-weight:600;display:block;margin-bottom:8px">Atau masukkan nominal lain</label>
                <div style="position:relative">
                    <span style="position:absolute;left:14px;top:50%;transform:translateY(-50%);font-weight:600;color:var(--text-muted)">Rp</span>
                    <input type="number" name="amount" id="amount-input" placeholder="10000" min="10000" max="10000000" required
                        style="width:100%;padding:12px 14px 12px 42px;border-radius:12px;border:1.5px solid var(--border);background:var(--beige);color:var(--text-main);font-size:15px;font-family:inherit;outline:none;transition:all .2s"
                        onfocus="this.style.borderColor='var(--sage)'" onblur="this.style.borderColor='var(--border)'">
                </div>
                <div style="font-size:11px;color:var(--text-muted);margin-top:6px">Min. Rp 10.000 · Max. Rp 10.000.000</div>
            </div>
        </div>

        <div style="background:var(--warm-white);border-radius:20px;border:1px solid var(--border);padding:28px;margin-bottom:20px">
            <h3 style="font-weight:700;font-size:15px;margin-bottom:18px"><i class="ti ti-building-bank" style="color:var(--sage)"></i> Metode Transfer</h3>
            @foreach([['transfer_bca','BCA','7623xxxx','ti-brand-mastercard'],['transfer_mandiri','Mandiri','1234xxxx','ti-credit-card'],['transfer_bni','BNI','0812xxxx','ti-building-bank'],['transfer_bri','BRI','0096xxxx','ti-coin']] as $m)
            <label style="display:flex;align-items:center;gap:14px;padding:12px 14px;border-radius:12px;border:1.5px solid var(--border);cursor:pointer;margin-bottom:10px;transition:all .2s">
                <input type="radio" name="payment_method" value="{{ $m[0] }}" style="accent-color:var(--sage);width:18px;height:18px" {{ $loop->first ? 'checked' : '' }}>
                <div style="width:36px;height:36px;background:rgba(90,124,101,.1);border-radius:8px;display:flex;align-items:center;justify-content:center">
                    <i class="ti {{ $m[3] }}" style="color:var(--sage);font-size:18px"></i>
                </div>
                <div>
                    <div style="font-weight:700;font-size:14px">Bank {{ $m[1] }}</div>
                    <div style="font-size:12px;color:var(--text-muted)">No. Rek: {{ $m[2] }} a/n RAS</div>
                </div>
            </label>
            @endforeach
        </div>

        <div style="background:var(--warm-white);border-radius:20px;border:1px solid var(--border);padding:28px;margin-bottom:20px">
            <h3 style="font-weight:700;font-size:15px;margin-bottom:14px"><i class="ti ti-photo" style="color:var(--sage)"></i> Upload Bukti Transfer</h3>
            <label id="upload-label" style="display:flex;flex-direction:column;align-items:center;justify-content:center;gap:10px;padding:32px;border:2px dashed var(--border);border-radius:14px;cursor:pointer;transition:all .2s"
                onmouseover="this.style.borderColor='var(--sage)'" onmouseout="this.style.borderColor='var(--border)'">
                <input type="file" name="proof_image" accept="image/*" style="display:none" onchange="previewProof(event)" required>
                <div id="upload-placeholder">
                    <i class="ti ti-upload" style="font-size:32px;color:var(--text-muted);display:block;text-align:center;margin-bottom:8px"></i>
                    <div style="font-size:13px;font-weight:600;text-align:center">Klik atau drag foto bukti transfer</div>
                    <div style="font-size:11px;color:var(--text-muted);text-align:center;margin-top:4px">JPG, PNG · Max 2MB</div>
                </div>
                <img id="proof-preview" src="" style="display:none;max-height:180px;border-radius:10px;object-fit:contain">
            </label>
        </div>

        <button type="submit" class="btn-primary" style="width:100%;justify-content:center;padding:14px">
            <i class="ti ti-send"></i> Kirim Permintaan Topup
        </button>
        <div style="font-size:12px;color:var(--text-muted);text-align:center;margin-top:10px">
            Admin akan memverifikasi dalam 1×24 jam
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function setAmount(val) {
    document.getElementById('amount-input').value = val;
}
function previewProof(e) {
    const file = e.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = ev => {
        document.getElementById('upload-placeholder').style.display = 'none';
        const prev = document.getElementById('proof-preview');
        prev.src = ev.target.result;
        prev.style.display = 'block';
    };
    reader.readAsDataURL(file);
}
</script>
@endpush


