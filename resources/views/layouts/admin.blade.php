<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title','Admin') — RAS Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.44.0/tabler-icons.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<style>
:root{
  --sage:#5a7c65;--sage-light:#8aaa92;--sage-dark:#3d5c47;
  --cream:#faf7f2;--beige:#f0ebe0;--warm:#fffdf9;
  --text:#2c3e35;--muted:#6b7c72;--border:rgba(90,124,101,.15);
  --shadow:0 2px 12px rgba(90,124,101,.08);--shadow-md:0 8px 32px rgba(90,124,101,.12);
  --r:14px;
  --sidebar-w:240px;
}
*{box-sizing:border-box;margin:0;padding:0}
html,body{height:100%}
body{font-family:'Plus Jakarta Sans',sans-serif;background:var(--cream);color:var(--text);display:flex;min-height:100vh}

/* ── Sidebar ── */
.sidebar{width:var(--sidebar-w);background:var(--sage-dark);flex-shrink:0;display:flex;flex-direction:column;position:fixed;top:0;left:0;height:100vh;z-index:200;overflow-y:auto;overflow-x:hidden}
.sidebar::-webkit-scrollbar{width:4px}.sidebar::-webkit-scrollbar-thumb{background:rgba(255,255,255,.1);border-radius:2px}
.sidebar-brand{padding:22px 20px 18px;border-bottom:1px solid rgba(255,255,255,.08)}
.sidebar-brand-icon{width:36px;height:36px;background:rgba(255,255,255,.15);border-radius:10px;display:flex;align-items:center;justify-content:center;margin-bottom:10px}
.sidebar-brand-name{font-weight:700;font-size:14px;color:#fff;line-height:1.2}
.sidebar-brand-sub{font-size:11px;color:rgba(255,255,255,.5);margin-top:2px}
.sidebar-section{padding:12px 12px 4px;font-size:10px;font-weight:700;color:rgba(255,255,255,.35);text-transform:uppercase;letter-spacing:.08em}
.sidebar-link{display:flex;align-items:center;gap:10px;padding:9px 14px;margin:2px 8px;border-radius:10px;font-size:13px;font-weight:500;color:rgba(255,255,255,.65);transition:all .2s;text-decoration:none}
.sidebar-link:hover{background:rgba(255,255,255,.08);color:#fff}
.sidebar-link.active{background:rgba(255,255,255,.14);color:#fff;font-weight:600}
.sidebar-link i{font-size:17px;flex-shrink:0}
.sidebar-badge{margin-left:auto;background:rgba(255,255,255,.15);color:#fff;font-size:10px;font-weight:700;padding:2px 7px;border-radius:20px}
.sidebar-badge.alert{background:rgba(224,122,95,.3);color:#e07a5f}
.sidebar-footer{margin-top:auto;padding:16px;border-top:1px solid rgba(255,255,255,.08)}
.sidebar-user{display:flex;align-items:center;gap:10px}
.sidebar-user-avatar{width:34px;height:34px;border-radius:50%;object-fit:cover;border:2px solid rgba(255,255,255,.2)}
.sidebar-user-name{font-size:13px;font-weight:600;color:#fff;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.sidebar-user-role{font-size:11px;color:rgba(255,255,255,.5)}

/* ── Main ── */
.main-wrap{margin-left:var(--sidebar-w);flex:1;display:flex;flex-direction:column;min-height:100vh}
.topbar{background:rgba(250,247,242,.92);backdrop-filter:blur(16px);border-bottom:1px solid var(--border);padding:12px 28px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:100}
.topbar-title{font-weight:700;font-size:17px;color:var(--text)}
.topbar-right{display:flex;align-items:center;gap:12px}
.topbar-btn{width:36px;height:36px;border-radius:50%;background:var(--beige);border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--muted);transition:all .2s;position:relative}
.topbar-btn:hover{background:rgba(90,124,101,.1);color:var(--sage-dark)}
.notif-dot{position:absolute;top:3px;right:3px;width:8px;height:8px;border-radius:50%;background:#e07a5f;border:2px solid var(--cream)}
.main-content{flex:1;padding:28px;max-width:1400px;width:100%}

/* ── Cards ── */
.stat-card{background:var(--warm);border-radius:var(--r);border:1px solid var(--border);box-shadow:var(--shadow);padding:20px;display:flex;align-items:flex-start;gap:14px}
.stat-icon{width:46px;height:46px;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.stat-val{font-size:24px;font-weight:700;color:var(--text);line-height:1}
.stat-lbl{font-size:12px;color:var(--muted);margin-top:3px}
.stat-change{font-size:11px;font-weight:600;display:flex;align-items:center;gap:3px;margin-top:6px}
.stat-change.up{color:#16a34a}.stat-change.down{color:#c0392b}
.content-card{background:var(--warm);border-radius:var(--r);border:1px solid var(--border);box-shadow:var(--shadow);overflow:hidden}
.card-head{padding:16px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between}
.card-title{font-weight:700;font-size:14px;color:var(--text);display:flex;align-items:center;gap:8px}
.card-body{padding:20px}

/* ── Table ── */
.admin-table{width:100%;border-collapse:collapse;font-size:13px}
.admin-table th{padding:10px 14px;text-align:left;font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.05em;background:var(--beige);border-bottom:1px solid var(--border)}
.admin-table td{padding:12px 14px;border-bottom:1px solid rgba(90,124,101,.06);vertical-align:middle}
.admin-table tr:last-child td{border:none}
.admin-table tr:hover td{background:rgba(90,124,101,.02)}

/* ── Badges ── */
.badge{display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600}
.badge-green{background:rgba(16,185,129,.1);color:#065f46}
.badge-amber{background:rgba(245,158,11,.1);color:#92400e}
.badge-red{background:rgba(220,80,60,.08);color:#991b1b}
.badge-blue{background:rgba(59,130,246,.08);color:#1e40af}
.badge-gray{background:rgba(107,114,128,.08);color:#374151}
.badge-sage{background:rgba(90,124,101,.1);color:var(--sage-dark)}

/* ── Buttons ── */
.btn-primary{display:inline-flex;align-items:center;gap:7px;padding:9px 20px;background:var(--sage);color:#fff;border-radius:10px;font-weight:600;font-size:13px;border:none;cursor:pointer;transition:all .2s}
.btn-primary:hover{background:var(--sage-dark);transform:translateY(-1px)}
.btn-secondary{display:inline-flex;align-items:center;gap:7px;padding:8px 18px;background:var(--beige);color:var(--text);border-radius:10px;font-weight:600;font-size:13px;border:1px solid var(--border);cursor:pointer;transition:all .2s;text-decoration:none}
.btn-secondary:hover{background:rgba(90,124,101,.08)}
.btn-danger{display:inline-flex;align-items:center;gap:7px;padding:7px 16px;background:rgba(220,80,60,.08);color:#c0392b;border-radius:10px;font-weight:600;font-size:12px;border:1px solid rgba(220,80,60,.15);cursor:pointer;transition:all .2s}
.btn-danger:hover{background:#c0392b;color:#fff}
.btn-icon{width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;border:none;cursor:pointer;transition:all .2s;font-size:16px}
.btn-icon-sage{background:rgba(90,124,101,.1);color:var(--sage-dark)}.btn-icon-sage:hover{background:var(--sage);color:#fff}
.btn-icon-red{background:rgba(220,80,60,.08);color:#c0392b}.btn-icon-red:hover{background:#c0392b;color:#fff}

/* ── Form ── */
.form-group{margin-bottom:18px}
.form-label{display:block;font-size:13px;font-weight:600;color:var(--text);margin-bottom:7px}
.form-input,.form-select,.form-textarea{width:100%;padding:10px 14px;border-radius:10px;border:1.5px solid var(--border);background:var(--beige);color:var(--text);font-size:14px;font-family:inherit;outline:none;transition:border-color .2s}
.form-input:focus,.form-select:focus,.form-textarea:focus{border-color:var(--sage);background:#fff}
.form-hint{font-size:11px;color:var(--muted);margin-top:5px}

/* ── Toast ── */
#toast-container{position:fixed;top:20px;right:20px;z-index:9999;display:flex;flex-direction:column;gap:8px}
.toast{padding:12px 18px;border-radius:12px;font-size:13px;font-weight:500;color:#fff;min-width:240px;display:flex;align-items:center;gap:10px;box-shadow:var(--shadow-md);animation:tin .25s ease}
.toast.success{background:var(--sage-dark)}.toast.error{background:#c0392b}.toast.info{background:var(--muted)}
@keyframes tin{from{opacity:0;transform:translateY(-8px)}to{opacity:1;transform:none}}

/* ── Misc ── */
.page-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px}
.page-title{font-family:'Playfair Display',serif;font-size:24px;font-weight:600;color:var(--text)}
.page-sub{font-size:13px;color:var(--muted);margin-top:3px}
.divider{height:1px;background:var(--border);margin:20px 0}
@media(max-width:900px){.sidebar{transform:translateX(-100%)}.main-wrap{margin-left:0}}
</style>
@stack('styles')
</head>
<body>

{{-- ── Sidebar ───────────────────────────────────── --}}
<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="sidebar-brand-icon">
            <i class="ti ti-building-store" style="color:#fff;font-size:18px"></i>
        </div>
        <div class="sidebar-brand-name">Rumahnya Anak Sekolah</div>
        <div class="sidebar-brand-sub">Admin Panel</div>
    </div>

    <div style="flex:1;padding:8px 0">
        <div class="sidebar-section">Utama</div>
        <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="ti ti-layout-dashboard"></i> Dashboard
        </a>
        <a href="{{ route('admin.analytics.index') }}" class="sidebar-link {{ request()->routeIs('admin.analytics.*') ? 'active' : '' }}">
            <i class="ti ti-chart-bar"></i> Analytics
        </a>

        <div class="sidebar-section">Operasional</div>
        <a href="{{ route('admin.orders.index') }}" class="sidebar-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
            <i class="ti ti-shopping-bag"></i> Pesanan
            @php $activeOrders = \App\Models\Order::whereIn('status',['menunggu','cooking'])->count(); @endphp
            @if($activeOrders > 0)
            <span class="sidebar-badge alert">{{ $activeOrders }}</span>
            @endif
        </a>
        <a href="{{ route('kasir.dashboard') }}" class="sidebar-link">
            <i class="ti ti-cash-register"></i> Kasir POS
        </a>
        <a href="{{ route('kitchen.display') }}" class="sidebar-link">
            <i class="ti ti-chef-hat"></i> Kitchen Display
        </a>
        <a href="{{ route('admin.reservations.index') }}" class="sidebar-link {{ request()->routeIs('admin.reservations.*') ? 'active' : '' }}">
            <i class="ti ti-calendar-event"></i> Reservasi
        </a>

        <div class="sidebar-section">Katalog</div>
        <a href="{{ route('admin.menus.index') }}" class="sidebar-link {{ request()->routeIs('admin.menus.*') ? 'active' : '' }}">
            <i class="ti ti-bowl-chopsticks"></i> Menu
        </a>
        <a href="{{ route('admin.banners.index') }}" class="sidebar-link {{ request()->routeIs('admin.banners.*') ? 'active' : '' }}">
            <i class="ti ti-photo"></i> Banner
        </a>
        <a href="{{ route('admin.vouchers.index') }}" class="sidebar-link {{ request()->routeIs('admin.vouchers.*') ? 'active' : '' }}">
            <i class="ti ti-ticket"></i> Voucher
        </a>

        <div class="sidebar-section">Keuangan</div>
        <a href="{{ route('admin.wallet.index') }}" class="sidebar-link {{ request()->routeIs('admin.wallet.*') ? 'active' : '' }}">
            <i class="ti ti-wallet"></i> Wallet
            @php $pending = \App\Models\TopupRequest::where('status','pending')->count(); @endphp
            @if($pending > 0)
            <span class="sidebar-badge alert">{{ $pending }}</span>
            @endif
        </a>
        <a href="{{ route('admin.membership.index') }}" class="sidebar-link {{ request()->routeIs('admin.membership.*') ? 'active' : '' }}">
            <i class="ti ti-award"></i> Membership
        </a>

        <div class="sidebar-section">Pengguna</div>
        <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <i class="ti ti-users"></i> Users
        </a>

        <div class="sidebar-section">Sistem</div>
        <a href="{{ route('admin.settings.index') }}" class="sidebar-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
            <i class="ti ti-settings"></i> Pengaturan
        </a>
    </div>

    <div class="sidebar-footer">
        <div class="sidebar-user">
            <img src="{{ auth()->user()->avatar_url }}" class="sidebar-user-avatar" alt="">
            <div style="flex:1;min-width:0">
                <div class="sidebar-user-name">{{ auth()->user()->name }}</div>
                <div class="sidebar-user-role">Superadmin</div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" style="background:none;border:none;cursor:pointer;color:rgba(255,255,255,.4);font-size:18px;padding:4px" title="Logout">
                    <i class="ti ti-logout"></i>
                </button>
            </form>
        </div>
    </div>
</aside>

{{-- ── Main ───────────────────────────────────────── --}}
<div class="main-wrap">
    <div class="topbar">
        <div class="topbar-title">@yield('page-title','Dashboard')</div>
        <div class="topbar-right">
            {{-- Notif --}}
            <a href="#" class="topbar-btn" style="text-decoration:none;color:var(--muted)">
                <i class="ti ti-bell" style="font-size:18px"></i>
                @php $unread = \App\Models\Notification::where('user_id',auth()->id())->where('is_read',false)->count(); @endphp
                @if($unread > 0)<span class="notif-dot"></span>@endif
            </a>
            {{-- View site --}}
            <a href="{{ route('home') }}" target="_blank" class="topbar-btn" style="text-decoration:none;color:var(--muted)" title="Lihat website">
                <i class="ti ti-external-link" style="font-size:18px"></i>
            </a>
        </div>
    </div>

    <div class="main-content">
        {{-- Breadcrumb --}}
        @hasSection('breadcrumb')
        <div style="display:flex;align-items:center;gap:6px;font-size:12px;color:var(--muted);margin-bottom:16px">
            <a href="{{ route('admin.dashboard') }}" style="color:var(--muted);text-decoration:none;display:flex;align-items:center;gap:4px">
                <i class="ti ti-home" style="font-size:14px"></i> Dashboard
            </a>
            @yield('breadcrumb')
        </div>
        @endif

        @yield('content')
    </div>
</div>

<div id="toast-container"></div>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
function showToast(msg, type='info', dur=3500) {
    const icons = {success:'ti-circle-check',error:'ti-alert-circle',info:'ti-info-circle'};
    const el = document.createElement('div');
    el.className = 'toast ' + type;
    el.innerHTML = `<i class="ti ${icons[type]||'ti-bell'}" style="font-size:17px;flex-shrink:0"></i><span>${msg}</span>`;
    document.getElementById('toast-container').appendChild(el);
    setTimeout(() => el.remove(), dur);
}
function ajax(url, method='GET', data=null) {
    const opts = {method, headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json','Content-Type':'application/json'}};
    if (data) opts.body = JSON.stringify(data);
    return fetch(url, opts).then(r => r.json());
}
@if(session('success'))window.addEventListener('DOMContentLoaded',()=>showToast(@json(session('success')),'success'));@endif
@if(session('error'))window.addEventListener('DOMContentLoaded',()=>showToast(@json(session('error')),'error'));@endif
</script>
@stack('scripts')
</body>
</html>