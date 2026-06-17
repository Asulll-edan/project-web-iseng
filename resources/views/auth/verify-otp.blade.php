<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Verifikasi Email</title>

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.44.0/tabler-icons.min.css">

<style>
:root{
    --sage:#5a7c65;
    --sage-dark:#3d5c47;
    --cream:#faf7f2;
    --beige:#f0ebe0;
    --text:#2c3e35;
    --muted:#6b7c72;
    --border:rgba(90,124,101,.2);
}

*{
    box-sizing:border-box;
    margin:0;
    padding:0;
}

body{
    font-family:'Plus Jakarta Sans',sans-serif;
    background:var(--cream);
    color:var(--text);
    min-height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:20px;
}

.auth-card{
    background:#fff;
    border-radius:24px;
    border:1px solid var(--border);
    box-shadow:0 20px 60px rgba(90,124,101,.12);
    padding:40px 44px;
    width:100%;
    max-width:440px;
}

.icon-box{
    width:60px;
    height:60px;
    background:var(--sage);
    border-radius:16px;
    display:flex;
    align-items:center;
    justify-content:center;
    margin:0 auto 16px;
}

.icon-box i{
    color:#fff;
    font-size:28px;
}

.title{
    text-align:center;
    font-size:24px;
    font-weight:700;
    margin-bottom:8px;
}

.subtitle{
    text-align:center;
    color:var(--muted);
    font-size:14px;
    line-height:1.6;
    margin-bottom:24px;
}

.email-box{
    background:var(--beige);
    padding:12px;
    border-radius:12px;
    text-align:center;
    font-size:14px;
    margin-bottom:20px;
}

.otp-input{
    width:100%;
    padding:14px;
    border-radius:12px;
    border:1.5px solid var(--border);
    background:var(--cream);
    text-align:center;
    font-size:24px;
    letter-spacing:10px;
    font-weight:700;
    outline:none;
    margin-bottom:16px;
}

.otp-input:focus{
    border-color:var(--sage);
    background:#fff;
}

.btn-primary{
    width:100%;
    padding:14px;
    border:none;
    border-radius:12px;
    background:var(--sage);
    color:#fff;
    font-weight:700;
    cursor:pointer;
}

.btn-primary:hover{
    background:var(--sage-dark);
}

.alert-error{
    background:#fdeaea;
    color:#c0392b;
    padding:12px;
    border-radius:10px;
    margin-bottom:15px;
    font-size:13px;
}

.countdown{
    text-align:center;
    margin-top:16px;
    color:var(--muted);
    font-size:13px;
}
</style>
</head>
<body>

<div class="auth-card">

    <div class="icon-box">
        <i class="ti ti-mail-check"></i>
    </div>

    <h1 class="title">Verifikasi Email</h1>

    <p class="subtitle">
        Kami telah mengirimkan kode OTP ke email berikut:
    </p>

    <div class="email-box">
        {{ $user->email }}
    </div>

    @if($errors->any())
        <div class="alert-error">
            @foreach($errors->all() as $error)
                <div>• {{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('verify.otp') }}">
        @csrf

        <input
            type="hidden"
            name="user_id"
            value="{{ $user->id }}">

        <input
            type="text"
            name="otp"
            maxlength="6"
            class="otp-input"
            placeholder="000000"
            required>

        <button type="submit" class="btn-primary">
            <i class="ti ti-check"></i>
            Verifikasi Sekarang
        </button>
    </form>

    <div class="countdown">
        OTP berlaku selama 10 menit
    </div>

</div>

</body>
</html>