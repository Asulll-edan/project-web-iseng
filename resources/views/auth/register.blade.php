<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Daftar — Rumahnya Anak Sekolah</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.44.0/tabler-icons.min.css">
<style>
:root{--sage:#5a7c65;--sage-dark:#3d5c47;--cream:#faf7f2;--beige:#f0ebe0;--text:#2c3e35;--muted:#6b7c72;--border:rgba(90,124,101,.2)}
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Plus Jakarta Sans',sans-serif;background:var(--cream);color:var(--text);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px}
.auth-card{background:#fff;border-radius:24px;border:1px solid var(--border);box-shadow:0 20px 60px rgba(90,124,101,.12);padding:40px 44px;width:100%;max-width:440px}
.auth-title{font-family:'Playfair Display',serif;font-size:22px;font-weight:600;text-align:center;margin-bottom:6px}
.auth-sub{font-size:13px;color:var(--muted);text-align:center;margin-bottom:28px}
.form-group{margin-bottom:16px}
.form-label{display:block;font-size:13px;font-weight:600;margin-bottom:7px}
.form-input{width:100%;padding:11px 14px;border-radius:10px;border:1.5px solid var(--border);background:var(--cream);color:var(--text);font-size:14px;font-family:inherit;outline:none;transition:all .2s}
.form-input:focus{border-color:var(--sage);background:#fff;box-shadow:0 0 0 3px rgba(90,124,101,.08)}
.input-wrap{position:relative}
.input-icon{position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--muted);font-size:17px;pointer-events:none}
.input-wrap .form-input{padding-left:40px}
.btn-primary{width:100%;padding:13px;background:var(--sage);color:#fff;border-radius:12px;font-weight:700;font-size:15px;border:none;cursor:pointer;transition:all .3s;margin-top:6px}
.btn-primary:hover{background:var(--sage-dark);transform:translateY(-1px)}
.alert-error{background:rgba(220,80,60,.08);border:1px solid rgba(220,80,60,.2);border-radius:10px;padding:12px 14px;font-size:13px;color:#c0392b;margin-bottom:16px}
.perks{display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:24px}
.perk{background:var(--beige);border-radius:10px;padding:10px 12px;font-size:12px;display:flex;align-items:center;gap:8px;color:var(--text)}
.perk i{font-size:16px;color:var(--sage);flex-shrink:0}
</style>
</head>
<body>
<div class="auth-card">
    <div style="text-align:center;margin-bottom:20px">
        <div style="width:52px;height:52px;background:var(--sage);border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 12px">
            <i class="ti ti-user-plus" style="color:#fff;font-size:24px"></i>
        </div>
        <div class="auth-title">Buat Akun Gratis</div>
        <div class="auth-sub">Bergabung dengan ribuan pelajar di Rumahnya Anak Sekolah</div>
    </div>

    <div class="perks">
        <div class="perk"><i class="ti ti-star"></i>Loyalty Points</div>
        <div class="perk"><i class="ti ti-wallet"></i>SOHIBA Wallet</div>
        <div class="perk"><i class="ti ti-award"></i>Membership</div>
        <div class="perk"><i class="ti ti-tag"></i>Promo Eksklusif</div>
    </div>

    @if($errors->any())
    <div class="alert-error">
        @foreach($errors->all() as $err)
        <div>• {{ $err }}</div>
        @endforeach
    </div>
    @endif

    <form action="{{ route('register') }}" method="POST">
        @csrf
        <div class="form-group">
            <label class="form-label">Nama Lengkap</label>
            <div class="input-wrap">
                <i class="ti ti-user input-icon"></i>
                <input type="text" name="name" class="form-input" placeholder="Nama kamu" value="{{ old('name') }}" required>
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Email</label>
            <div class="input-wrap">
                <i class="ti ti-mail input-icon"></i>
                <input type="email" name="email" class="form-input" placeholder="email@contoh.com" value="{{ old('email') }}" required>
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Nomor HP</label>
            <div class="input-wrap">
                <i class="ti ti-phone input-icon"></i>
                <input type="text" name="phone" class="form-input" placeholder="08xxxxxxxxxx" value="{{ old('phone') }}" required>
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Password</label>
            <div class="input-wrap">
                <i class="ti ti-lock input-icon"></i>
                <input type="password" name="password" class="form-input" placeholder="Min. 8 karakter" required>
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Konfirmasi Password</label>
            <div class="input-wrap">
                <i class="ti ti-lock-check input-icon"></i>
                <input type="password" name="password_confirmation" class="form-input" placeholder="Ulangi password" required>
            </div>
        </div>
        <button type="submit" class="btn-primary">
            <i class="ti ti-user-plus" style="margin-right:6px"></i> Daftar Sekarang
        </button>
    </form>

    <div style="text-align:center;margin-top:16px;font-size:13px;color:var(--muted)">
        Sudah punya akun? <a href="{{ route('login') }}" style="color:var(--sage);font-weight:600">Masuk</a>
    </div>
</div>
</body>
</html>