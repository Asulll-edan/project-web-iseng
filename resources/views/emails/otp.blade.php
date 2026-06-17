<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
</head>
<body style="
    margin:0;
    padding:40px;
    background:#faf7f2;
    font-family:Arial,sans-serif;
">
<div style="text-align:center;padding:25px">
    <img
        src="https://rumah-sekolah.up.railway.app/images/logo.png"
        alt="Rumahnya Anak Sekolah"
        style="height:70px">
</div>
<div style="
    max-width:600px;
    margin:auto;
    background:white;
    border-radius:20px;
    overflow:hidden;
    box-shadow:0 10px 30px rgba(0,0,0,.08);
">

    <div style="
        background:#5a7c65;
        color:white;
        padding:25px;
        text-align:center;
    ">
        <h2 style="margin:0">
            Rumahnya Anak Sekolah
        </h2>
    </div>

    <div style="padding:35px">

        <h3>Haloo {{ $name }}</h3>

        <p>
            Terima kasih telah mendaftar.
            Gunakan kode OTP berikut untuk
            memverifikasi akun Anda.
        </p>

        <div style="
            text-align:center;
            margin:30px 0;
        ">
            <span style="
                display:inline-block;
                background:#f0ebe0;
                padding:18px 30px;
                border-radius:12px;
                font-size:32px;
                font-weight:bold;
                letter-spacing:8px;
                color:#5a7c65;
            ">
                {{ $otp }}
            </span>
        </div>

        <p>
            OTP berlaku selama <b>10 menit</b>.
        </p>

        <p>
            Jika Anda tidak melakukan pendaftaran,
            abaikan email ini.
        </p>

    </div>

    <div style="
        background:#f8f8f8;
        text-align:center;
        padding:20px;
        color:#888;
        font-size:12px;
    ">
        © {{ date('Y') }} Rumahnya Anak Sekolah
    </div>

</div>

</body>
</html>