<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title','Kasir') — RAS POS</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.44.0/tabler-icons.min.css">
<style>
:root{--sage:#5a7c65;--sage-dark:#3d5c47;--cream:#faf7f2;--beige:#f0ebe0;--text:#2c3e35;--muted:#6b7c72;--border:rgba(90,124,101,.15);--shadow:0 2px 12px rgba(90,124,101,.08);--r:12px}
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Plus Jakarta Sans',sans-serif;background:#1a2420;color:#e8f0eb;min-height:100vh;display:flex;flex-direction:column}
.topbar{background:#1e2b25;border-bottom:1px solid rgba(138,170,146,.12);padding:12px 24px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:100}
.topbar-brand{display:flex;align-items:center;gap:10px;font-weight:700;font-size:16px;color:#e8f0eb}
.topbar-brand span{font-size:12px;font-weight:400;color:#8fa897;display:block;margin-top:-2px}
.topbar-right{display:flex;align-items:center;gap:16px}
.topbar-stat{background:rgba(90,124,101,.15);border-radius:8px;padding:6px 14px;font-size:13px;font-weight:600;color:#8aaa92;display:flex;align-items:center;gap:6px}
.topbar-stat.alert{background:rgba(220,100,60,.15);color:#e07a5f}
.main{flex:1;padding:24px;max-width:1400px;width:100%;margin:0 auto}
.status-dot{width:8px;height:8px;border-radius:50%;background:#4ade80;display:inline-block;box-shadow:0 0 0 3px rgba(74,222,128,.2);animation:pulse 2s infinite}
@keyframes pulse{0%,100%{box-shadow:0 0 0 3px rgba(74,222,128,.2)}50%{box-shadow:0 0 0 6px rgba(74,222,128,.05)}}
#toast-container{position:fixed;top:20px;right:20px;z-index:9999;display:flex;flex-direction:column;gap:8px}
.toast{padding:12px 18px;border-radius:10px;font-size:13px;font-weight:500;color:#fff;display:flex;align-items:center;gap:8px;min-width:240px;animation:toastIn .25s ease}
.toast.success{background:#3d5c47}.toast.error{background:#7f1d1d}.toast.info{background:#1e3a5f}
@keyframes toastIn{from{opacity:0;transform:translateY(-10px)}to{opacity:1;transform:none}}
</style>
@stack('styles')
</head>
<body>
<div class="topbar">
    <div class="topbar-brand">
        <i class="ti ti-building-store" style="font-size:22px;color:#8aaa92"></i>
        <div>
            RAS — Kasir POS
            <span>{{ auth()->user()->name }}</span>
        </div>
    </div>
    <div class="topbar-right">
        <div class="topbar-stat" id="stat-menunggu">
            <i class="ti ti-clock" style="font-size:16px"></i>
            <span id="count-menunggu">0</span> Menunggu
        </div>
        <div class="topbar-stat alert" id="stat-cooking">
            <i class="ti ti-flame" style="font-size:16px"></i>
            <span id="count-cooking">0</span> Memasak
        </div>
        <div class="topbar-stat">
            <span class="status-dot"></span> Live
        </div>
        <a href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit()"
           style="color:#8fa897;font-size:13px;display:flex;align-items:center;gap:6px">
            <i class="ti ti-logout"></i> Keluar
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none">@csrf</form>
    </div>
</div>

<div id="toast-container"></div>
<div class="main">@yield('content')</div>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
function showToast(msg,type='info',dur=3500){
    const el=document.createElement('div');
    el.className=`toast ${type}`;
    el.innerHTML=`<i class="ti ti-circle-check"></i><span>${msg}</span>`;
    document.getElementById('toast-container').appendChild(el);
    setTimeout(()=>el.remove(),dur);
}
function ajax(url,method='GET',data=null){
    const opts={method,headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json','Content-Type':'application/json'}};
    if(data)opts.body=JSON.stringify(data);
    return fetch(url,opts).then(r=>r.json());
}
</script>
@stack('scripts')
</body>
</html>