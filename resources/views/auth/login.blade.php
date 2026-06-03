<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Masuk — Rumahnya Anak Sekolah</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.44.0/tabler-icons.min.css">
<style>
:root{--sage:#5a7c65;--sage-dark:#3d5c47;--cream:#faf7f2;--beige:#f0ebe0;--text:#2c3e35;--muted:#6b7c72;--border:rgba(90,124,101,.2)}
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Plus Jakarta Sans',sans-serif;background:var(--cream);color:var(--text);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px}
.auth-card{background:#fff;border-radius:24px;border:1px solid var(--border);box-shadow:0 20px 60px rgba(90,124,101,.12);padding:40px 44px;width:100%;max-width:420px}
.auth-brand{text-align:center;margin-bottom:32px}
.auth-brand-icon{width:56px;height:56px;background:var(--sage);border-radius:16px;display:flex;align-items:center;justify-content:center;margin:0 auto 14px}
.auth-title{font-family:'INeedCoffee',serif;font-size:24px;font-weight:600;color:var(--text);margin-bottom:6px}
.auth-sub{font-size:13px;color:var(--muted)}
.form-group{margin-bottom:18px}
.form-label{display:block;font-size:13px;font-weight:600;color:var(--text);margin-bottom:7px}
.form-input{width:100%;padding:11px 14px;border-radius:10px;border:1.5px solid var(--border);background:var(--cream);color:var(--text);font-size:14px;font-family:inherit;outline:none;transition:all .2s}
.form-input:focus{border-color:var(--sage);background:#fff;box-shadow:0 0 0 3px rgba(90,124,101,.08)}
.input-wrap{position:relative}
.input-icon{position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--muted);font-size:17px;pointer-events:none}
.input-wrap .form-input{padding-left:40px}
.btn-primary{width:100%;padding:13px;background:var(--sage);color:#fff;border-radius:12px;font-weight:700;font-size:15px;border:none;cursor:pointer;transition:all .3s;display:flex;align-items:center;justify-content:center;gap:8px;margin-top:8px}
.btn-primary:hover{background:var(--sage-dark);transform:translateY(-1px);box-shadow:0 6px 24px rgba(90,124,101,.3)}
.alert-error{background:rgba(220,80,60,.08);border:1px solid rgba(220,80,60,.2);border-radius:10px;padding:12px 14px;font-size:13px;color:#c0392b;margin-bottom:16px;display:flex;align-items:flex-start;gap:8px}
.divider{display:flex;align-items:center;gap:12px;margin:20px 0;color:var(--muted);font-size:12px}
.divider::before,.divider::after{content:'';flex:1;height:1px;background:var(--border)}
.demo-accounts{background:var(--beige);border-radius:12px;padding:14px 16px;font-size:12px}
.demo-row{display:flex;justify-content:space-between;padding:4px 0;border-bottom:1px solid rgba(90,124,101,.08)}
.demo-row:last-child{border:none}
</style>
</head>
<body>
<div class="auth-card">
    <div class="auth-brand">
        <div class="auth-brand-icon">
            <i class="ti ti-building-store" style="color:#fff;font-size:26px"></i>
        </div>
        <div class="auth-title">Selamat Datang!</div>
        <div class="auth-sub">Masuk ke Rumahnya Anak Sekolah</div>
    </div>

    @if($errors->any())
    <div class="alert-error">
        <i class="ti ti-alert-circle" style="font-size:16px;flex-shrink:0;margin-top:1px"></i>
        <span>{{ $errors->first() }}</span>
    </div>
    @endif

    <form action="{{ route('login') }}" method="POST">
        @csrf
        <div class="form-group">
            <label class="form-label">Email</label>
            <div class="input-wrap">
                <i class="ti ti-mail input-icon"></i>
                <input type="email" name="email" class="form-input" placeholder="email@contoh.com" value="{{ old('email') }}" required>
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Password</label>
            <div class="input-wrap">
                <i class="ti ti-lock input-icon"></i>
                <input type="password" name="password" id="pw" class="form-input" placeholder="••••••••" required>
                <button type="button" onclick="togglePw()" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--muted)">
                    <i class="ti ti-eye" id="pw-icon" style="font-size:17px"></i>
                </button>
            </div>
        </div>
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
            <label style="display:flex;align-items:center;gap:7px;font-size:13px;cursor:pointer">
                <input type="checkbox" name="remember" style="accent-color:var(--sage);width:15px;height:15px">
                Ingat saya
            </label>
        </div>
        <button type="submit" class="btn-primary">
            <i class="ti ti-login"></i> Masuk Sekarang
        </button>
    </form>

    <div style="text-align:center;margin-top:18px;font-size:13px;color:var(--muted)">
        Belum punya akun? <a href="{{ route('register') }}" style="color:var(--sage);font-weight:600">Daftar gratis</a>
    </div>

</div>
<script>
function togglePw(){
    const pw=document.getElementById('pw');
    const icon=document.getElementById('pw-icon');
    pw.type=pw.type==='password'?'text':'password';
    icon.className=pw.type==='password'?'ti ti-eye':'ti ti-eye-off';
}
</script>
</body>
</html>

