<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title','Kasir') — RAS POS</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.44.0/tabler-icons.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<style>
:root{--sage:#5a7c65;--sage-dark:#3d5c47;--bg:#1a2420;--bg2:#1e2b25;--bg3:#243028;--border:rgba(138,170,146,.15);--text:#e8f0eb;--muted:#8fa897;--r:12px}
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Plus Jakarta Sans',sans-serif;background:var(--bg);color:var(--text);min-height:100vh;display:flex;flex-direction:column}

/* ── Topbar ── */
.topbar{background:var(--bg2);border-bottom:1px solid var(--border);padding:0 20px;display:flex;align-items:center;height:58px;position:sticky;top:0;z-index:100;gap:0}
.topbar-brand{display:flex;align-items:center;gap:10px;padding-right:20px;border-right:1px solid var(--border);margin-right:8px}
.topbar-brand-icon{width:34px;height:34px;background:var(--sage);border-radius:9px;display:flex;align-items:center;justify-content:center}
.topbar-brand-name{font-weight:700;font-size:14px;color:var(--text)}
.topbar-brand-sub{font-size:10px;color:var(--muted)}

/* ── Nav links ── */
.nav-links{display:flex;align-items:center;gap:2px;flex:1}
.nav-link{display:flex;align-items:center;gap:7px;padding:8px 14px;border-radius:9px;font-size:13px;font-weight:500;color:var(--muted);transition:all .18s;text-decoration:none;white-space:nowrap}
.nav-link:hover{background:rgba(138,170,146,.08);color:var(--text)}
.nav-link.active{background:rgba(90,124,101,.2);color:#8aaa92}
.nav-link i{font-size:16px}

/* ── Right ── */
.topbar-right{display:flex;align-items:center;gap:8px;margin-left:auto}
.stat-pill{background:rgba(138,170,146,.1);border:1px solid var(--border);border-radius:8px;padding:5px 12px;font-size:12px;font-weight:600;color:var(--muted);display:flex;align-items:center;gap:6px;cursor:default}
.stat-pill.warn{background:rgba(224,122,95,.1);border-color:rgba(224,122,95,.2);color:#e07a5f}
.icon-btn{width:34px;height:34px;border-radius:9px;background:rgba(138,170,146,.08);border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--muted);transition:all .18s}
.icon-btn:hover{background:rgba(138,170,146,.15);color:var(--text)}
.clock-display{font-size:13px;font-weight:700;color:var(--text);font-variant-numeric:tabular-nums;padding:0 8px}

/* ── Main ── */
.main{flex:1;padding:22px 24px;max-width:1440px;width:100%;margin:0 auto}

/* ── Toast ── */
#toast-container{position:fixed;top:70px;right:20px;z-index:9999;display:flex;flex-direction:column;gap:8px}
.toast{padding:11px 16px;border-radius:10px;font-size:13px;font-weight:500;color:#fff;display:flex;align-items:center;gap:8px;min-width:220px;animation:tin .22s ease;box-shadow:0 4px 20px rgba(0,0,0,.3)}
.toast.success{background:#3d5c47}.toast.error{background:#7f1d1d}.toast.info{background:#1e3a5f}
@keyframes tin{from{opacity:0;transform:translateY(-8px)}to{opacity:1;transform:none}}

/* ── SweetAlert dark ── */
.swal2-popup{border-radius:16px!important;font-family:'Plus Jakarta Sans',sans-serif!important;background:#1e2b25!important;color:#e8f0eb!important}
.swal2-title{color:#e8f0eb!important}.swal2-html-container{color:#8fa897!important}
.swal2-confirm{background:var(--sage)!important;border-radius:9px!important}
.swal2-cancel{background:rgba(138,170,146,.15)!important;border-radius:9px!important;color:#e8f0eb!important}
</style>
@stack('styles')
</head>
<body>

<div class="topbar">
    {{-- Brand --}}
    <div class="topbar-brand">
        <div class="topbar-brand-icon"><i class="ti ti-building-store" style="color:#fff;font-size:16px"></i></div>
        <div>
            <div class="topbar-brand-name">RAS Kasir</div>
            <div class="topbar-brand-sub">{{ auth()->user()->name }}</div>
        </div>
    </div>

    {{-- Nav links --}}
    <nav class="nav-links">
        <a href="{{ route('kasir.dashboard') }}" class="nav-link {{ request()->routeIs('kasir.dashboard')?'active':'' }}">
            <i class="ti ti-layout-dashboard"></i> Dashboard
        </a>
        <a href="{{ route('kasir.orders.index') }}" class="nav-link {{ request()->routeIs('kasir.orders.*')?'active':'' }}">
            <i class="ti ti-shopping-bag"></i> Kelola Order
            @php $pending = \App\Models\Order::where('status','menunggu')->count(); @endphp
            @if($pending>0)
            <span style="background:rgba(224,122,95,.25);color:#e07a5f;font-size:10px;font-weight:700;padding:1px 7px;border-radius:10px">{{ $pending }}</span>
            @endif
        </a>
        <a href="{{ route('kitchen.display') }}" class="nav-link" target="_blank">
            <i class="ti ti-chef-hat"></i> Kitchen
        </a>
        @if(auth()->user()->isSuperadmin()||auth()->user()->isAdmin())
        <a href="{{ route('admin.dashboard') }}" class="nav-link">
            <i class="ti ti-settings"></i> Admin
        </a>
        @endif
    </nav>

    {{-- Right stats --}}
    <div class="topbar-right">
        <div class="stat-pill" id="pill-menunggu">
            <i class="ti ti-clock" style="font-size:14px"></i>
            <span id="count-menunggu">0</span> Menunggu
        </div>
        <div class="stat-pill warn" id="pill-cooking">
            <i class="ti ti-flame" style="font-size:14px"></i>
            <span id="count-cooking">0</span> Masak
        </div>
        <div class="clock-display" id="topbar-clock">--:--</div>
        <button onclick="location.reload()" class="icon-btn" title="Refresh">
            <i class="ti ti-refresh" style="font-size:16px"></i>
        </button>
        <button onclick="confirmLogout()" class="icon-btn" title="Logout">
            <i class="ti ti-logout" style="font-size:16px"></i>
        </button>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none">@csrf</form>
    </div>
</div>

<div id="toast-container"></div>
<div class="main">@yield('content')</div>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

function showToast(msg,type='info',dur=3500){
    const el=document.createElement('div');
    el.className='toast '+type;
    el.innerHTML=`<i class="ti ti-circle-check" style="font-size:16px;flex-shrink:0"></i><span>${msg}</span>`;
    document.getElementById('toast-container').appendChild(el);
    setTimeout(()=>el.remove(),dur);
}

function ajax(url,method='GET',data=null){
    const opts={method,headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json','Content-Type':'application/json'}};
    if(data)opts.body=JSON.stringify(data);
    return fetch(url,opts).then(r=>r.json());
}

function confirmLogout(){
    Swal.fire({title:'Logout?',text:'Keluar dari sesi kasir?',icon:'question',showCancelButton:true,confirmButtonText:'Ya, Logout',cancelButtonText:'Batal',confirmButtonColor:'#3d5c47'})
    .then(r=>{if(r.isConfirmed)document.getElementById('logout-form').submit();});
}

function confirmDanger(title,text,cb,btn='Lanjutkan'){
    Swal.fire({title,text,icon:'warning',showCancelButton:true,confirmButtonColor:'#c0392b',cancelButtonColor:'rgba(138,170,146,.3)',confirmButtonText:btn,cancelButtonText:'Batal'})
    .then(r=>{if(r.isConfirmed)cb();});
}

// Clock — gunakan timezone server (WIB)
function tickClock(){
    const now = new Date();
    const wib = new Date(now.toLocaleString('en-US',{timeZone:'Asia/Jakarta'}));
    const h=String(wib.getHours()).padStart(2,'0');
    const m=String(wib.getMinutes()).padStart(2,'0');
    const s=String(wib.getSeconds()).padStart(2,'0');
    const el=document.getElementById('topbar-clock');
    if(el) el.textContent=h+':'+m+':'+s;
}
tickClock(); setInterval(tickClock,1000);

// Poll order counts
function pollCounts(){
    fetch('/kasir/orders/poll',{headers:{'Accept':'application/json'}})
    .then(r=>r.json()).then(d=>{
        if(d.counts){
            const cm=document.getElementById('count-menunggu');
            const cc=document.getElementById('count-cooking');
            if(cm) cm.textContent=d.counts.menunggu||0;
            if(cc) cc.textContent=d.counts.cooking||0;
        }
    }).catch(()=>{});
}
pollCounts(); setInterval(pollCounts,15000);
</script>
@stack('scripts')
</body>
</html> 