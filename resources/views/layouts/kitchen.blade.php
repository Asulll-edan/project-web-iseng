<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Kitchen Display — RAS</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.44.0/tabler-icons.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<style>
:root{--sage:#5a7c65;--bg:#0f1a16;--bg2:#1a2b22;--bg3:#1e2b25;--border:rgba(90,124,101,.2);--text:#e8f0eb;--muted:#8fa897}
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Plus Jakarta Sans',sans-serif;background:var(--bg);color:var(--text);min-height:100vh;overflow-x:hidden}

/* ── Navbar ── */
.kitchen-nav{background:var(--bg2);border-bottom:1px solid var(--border);padding:0 22px;height:56px;display:flex;align-items:center;gap:0;position:sticky;top:0;z-index:100}
.kitchen-brand{display:flex;align-items:center;gap:10px;padding-right:20px;border-right:1px solid var(--border);margin-right:10px;flex-shrink:0}
.kitchen-brand-icon{width:32px;height:32px;background:rgba(90,124,101,.3);border-radius:8px;display:flex;align-items:center;justify-content:center}
.kitchen-brand-name{font-weight:700;font-size:14px;color:var(--text)}
.kitchen-brand-sub{font-size:10px;color:var(--muted)}
.nav-links{display:flex;align-items:center;gap:2px}
.nav-link{display:flex;align-items:center;gap:7px;padding:7px 13px;border-radius:8px;font-size:13px;font-weight:500;color:var(--muted);transition:all .18s;text-decoration:none}
.nav-link:hover{background:rgba(138,170,146,.08);color:var(--text)}
.nav-link.active{background:rgba(90,124,101,.2);color:#8aaa92}
.nav-right{display:flex;align-items:center;gap:8px;margin-left:auto}
.kitchen-clock{font-size:20px;font-weight:700;color:var(--text);font-variant-numeric:tabular-nums;letter-spacing:.05em}
.kitchen-date{font-size:11px;color:var(--muted);margin-top:1px;text-align:right}
.icon-btn{width:32px;height:32px;border-radius:8px;background:rgba(138,170,146,.08);border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--muted);transition:all .18s}
.icon-btn:hover{background:rgba(138,170,146,.15);color:var(--text)}
.count-badge{background:rgba(245,158,11,.12);border:1px solid rgba(245,158,11,.2);border-radius:8px;padding:5px 12px;font-size:12px;font-weight:700;color:#f59e0b;display:flex;align-items:center;gap:5px}
.count-badge.cook{background:rgba(74,222,128,.08);border-color:rgba(74,222,128,.15);color:#4ade80}

/* ── Body ── */
.kitchen-body{padding:18px 20px;min-height:calc(100vh - 56px)}

/* ── SweetAlert dark ── */
.swal2-popup{border-radius:16px!important;font-family:'Plus Jakarta Sans',sans-serif!important;background:#1e2b25!important;color:#e8f0eb!important}
.swal2-title{color:#e8f0eb!important}.swal2-html-container{color:#8fa897!important}
.swal2-confirm{background:var(--sage)!important;border-radius:9px!important}
.swal2-cancel{background:rgba(138,170,146,.15)!important;border-radius:9px!important;color:#e8f0eb!important}
</style>
@stack('styles')
</head>
<body>

<div class="kitchen-nav">
    {{-- Brand --}}
    <div class="kitchen-brand">
        <div class="kitchen-brand-icon"><i class="ti ti-chef-hat" style="color:#8aaa92;font-size:16px"></i></div>
        <div>
            <div class="kitchen-brand-name">RAS Kitchen</div>
            <div class="kitchen-brand-sub">{{ auth()->user()->name }}</div>
        </div>
    </div>

    {{-- Nav --}}
    <nav class="nav-links">
        <a href="{{ route('kitchen.display') }}" class="nav-link {{ request()->routeIs('kitchen.display')?'active':'' }}">
            <i class="ti ti-layout-grid"></i> Display
        </a>
        <a href="{{ route('kasir.dashboard') }}" class="nav-link">
            <i class="ti ti-cash-register"></i> Kasir
        </a>
        @if(auth()->user()->isSuperadmin()||auth()->user()->isAdmin())
        <a href="{{ route('admin.dashboard') }}" class="nav-link">
            <i class="ti ti-settings"></i> Admin
        </a>
        @endif
    </nav>

    {{-- Right --}}
    <div class="nav-right">
        <div class="count-badge" id="cnt-menunggu">
            <i class="ti ti-clock" style="font-size:14px"></i>
            <span id="k-menunggu">0</span> Menunggu
        </div>
        <div class="count-badge cook" id="cnt-cooking">
            <i class="ti ti-flame" style="font-size:14px"></i>
            <span id="k-cooking">0</span> Memasak
        </div>
        <div style="text-align:right">
            <div class="kitchen-clock" id="kitchen-clock">--:--:--</div>
            <div class="kitchen-date" id="kitchen-date"></div>
        </div>
        <button onclick="toggleFullscreen()" class="icon-btn" title="Fullscreen">
            <i class="ti ti-maximize" id="fs-icon" style="font-size:16px"></i>
        </button>
        <button onclick="confirmLogout()" class="icon-btn" title="Logout">
            <i class="ti ti-logout" style="font-size:16px"></i>
        </button>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none">@csrf</form>
    </div>
</div>

<div class="kitchen-body">@yield('content')</div>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

function ajax(url,method='GET',data=null){
    const opts={method,headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json','Content-Type':'application/json'}};
    if(data)opts.body=JSON.stringify(data);
    return fetch(url,opts).then(r=>r.json());
}

function confirmLogout(){
    Swal.fire({title:'Logout?',text:'Keluar dari kitchen display?',icon:'question',showCancelButton:true,confirmButtonText:'Ya, Logout',cancelButtonText:'Batal',confirmButtonColor:'#3d5c47'})
    .then(r=>{if(r.isConfirmed)document.getElementById('logout-form').submit();});
}

function toggleFullscreen(){
    if(!document.fullscreenElement){
        document.documentElement.requestFullscreen();
        document.getElementById('fs-icon').className='ti ti-minimize';
    } else {
        document.exitFullscreen();
        document.getElementById('fs-icon').className='ti ti-maximize';
    }
}

// Clock WIB
function tickClock(){
    const now=new Date();
    const wib=new Date(now.toLocaleString('en-US',{timeZone:'Asia/Jakarta'}));
    const h=String(wib.getHours()).padStart(2,'0');
    const m=String(wib.getMinutes()).padStart(2,'0');
    const s=String(wib.getSeconds()).padStart(2,'0');
    const days=['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
    const months=['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agt','Sep','Okt','Nov','Des'];
    const cl=document.getElementById('kitchen-clock');
    const dl=document.getElementById('kitchen-date');
    if(cl) cl.textContent=h+':'+m+':'+s;
    if(dl) dl.textContent=days[wib.getDay()]+', '+wib.getDate()+' '+months[wib.getMonth()]+' '+wib.getFullYear();
}
tickClock(); setInterval(tickClock,1000);
</script>
@stack('scripts')
</body>
</html>
