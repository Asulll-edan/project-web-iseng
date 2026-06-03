<nav id="navbar" style="position:fixed;top:0;left:0;right:0;z-index:500;padding:14px 0;transition:all .4s ease">
<div class="container" style="display:flex;align-items:center;justify-content:space-between;gap:16px">

    {{-- Brand --}}
    <a href="{{ route('home') }}" style="display:flex;align-items:center;gap:10px;flex-shrink:0">
        <div style="width:36px;height:36px;background:var(--sage);border-radius:10px;display:flex;align-items:center;justify-content:center">
                    <img src="{{ asset('images/logo.png') }}" alt="Rumahnya Anak Sekolah" style="width:100%;height:auto">
        </div>
        <div>
            <div style="font-family:'INeedCoffee',serif;font-size:18px;line-height:1.1">
    <div>Rumahnya</div>
    <div style="font-size:13px">Anak Sekolah</div>
</div>
        </div>
    </a>

    {{-- Nav links --}}
    <div class="nav-links" style="display:flex;align-items:center;gap:6px">
        <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
        <a href="{{ route('menu.index') }}" class="nav-link {{ request()->routeIs('menu.*') ? 'active' : '' }}">Menu</a>
        @auth
        <a href="{{ route('reservation.index') }}" class="nav-link {{ request()->routeIs('reservation.*') ? 'active' : '' }}">Reservasi</a>
        <a href="{{ route('membership') }}" class="nav-link {{ request()->routeIs('membership') ? 'active' : '' }}">Member</a>
        @endauth
    </div>

    {{-- Right actions --}}
    <div style="display:flex;align-items:center;gap:10px">
        @auth
        {{-- Cart --}}
        <a href="{{ route('cart.index') }}" style="position:relative;width:38px;height:38px;display:flex;align-items:center;justify-content:center;border-radius:50%;background:var(--beige);color:var(--text-main);transition:all .2s" class="nav-icon-btn">
            <i class="ti ti-shopping-bag" style="font-size:18px"></i>
            <span id="cart-count" style="position:absolute;top:-2px;right:-2px;background:var(--sage);color:#fff;font-size:10px;font-weight:700;width:18px;height:18px;border-radius:50%;display:none;align-items:center;justify-content:center">0</span>
        </a>

        {{-- Notifications --}}
        <a href="{{ route('notifications.index') }}" style="position:relative;width:38px;height:38px;display:flex;align-items:center;justify-content:center;border-radius:50%;background:var(--beige);color:var(--text-main);transition:all .2s" class="nav-icon-btn">
            <i class="ti ti-bell" style="font-size:18px"></i>
            <span id="notif-count" style="position:absolute;top:-2px;right:-2px;background:#e07a5f;color:#fff;font-size:10px;font-weight:700;width:18px;height:18px;border-radius:50%;display:none;align-items:center;justify-content:center">0</span>
        </a>

        {{-- User dropdown --}}
        <div style="position:relative" id="user-dropdown-wrap">
            <button onclick="toggleDropdown()" style="display:flex;align-items:center;gap:8px;padding:6px 12px 6px 6px;background:var(--beige);border-radius:20px;border:1px solid var(--border);cursor:pointer;transition:all .2s" class="nav-icon-btn">
                <img src="{{ auth()->user()->avatar_url }}" style="width:28px;height:28px;border-radius:50%;object-fit:cover" alt="">
                <span style="font-size:13px;font-weight:600;color:var(--text-main);max-width:80px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ auth()->user()->name }}</span>
                <i class="ti ti-chevron-down" style="font-size:14px;color:var(--text-muted)"></i>
            </button>
            <div id="user-dropdown" style="display:none;position:absolute;top:calc(100% + 8px);right:0;min-width:200px;background:var(--warm-white);border-radius:var(--radius-md);border:1px solid var(--border);box-shadow:var(--shadow-md);overflow:hidden;z-index:200">
                <a href="{{ route('profile.index') }}" class="dropdown-item"><i class="ti ti-user"></i> Profil Saya</a>
                <a href="{{ route('orders.index') }}" class="dropdown-item"><i class="ti ti-receipt"></i> Pesanan</a>
                <a href="{{ route('wallet.index') }}" class="dropdown-item"><i class="ti ti-wallet"></i> SOHIBA Wallet</a>
                @if(auth()->user()->isSuperadmin())
                <a href="{{ route('admin.dashboard') }}" class="dropdown-item"><i class="ti ti-layout-dashboard"></i> Admin Panel</a>
                @endif
                @if(auth()->user()->isKasir())
                <a href="{{ route('kasir.dashboard') }}" class="dropdown-item"><i class="ti ti-cash-register"></i> Kasir POS</a>
                @endif
                @if(auth()->user()->isKitchen())
                <a href="{{ route('kitchen.display') }}" class="dropdown-item"><i class="ti ti-chef-hat"></i> Kitchen Display</a>
                @endif
                <div style="height:1px;background:var(--border);margin:4px 0"></div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="dropdown-item" style="width:100%;text-align:left;background:none;border:none;cursor:pointer;color:#e07a5f">
                        <i class="ti ti-logout"></i> Keluar
                    </button>
                </form>
            </div>
        </div>
        @else
        <a href="{{ route('login') }}" class="btn-outline" style="padding:8px 20px;font-size:13px">Masuk</a>
        <a href="{{ route('register') }}" class="btn-primary" style="padding:8px 20px;font-size:13px">Daftar</a>
        @endauth
    </div>
</div>
</nav>

<style>
.nav-link{padding:8px 14px;border-radius:20px;font-size:14px;font-weight:500;color:var(--text-muted);transition:all .2s}
.nav-link:hover,.nav-link.active{background:rgba(90,124,101,.1);color:var(--sage-dark)}
.nav-icon-btn:hover{background:var(--beige)!important;transform:translateY(-1px)}
.dropdown-item{display:flex;align-items:center;gap:10px;padding:10px 16px;font-size:13px;font-weight:500;color:var(--text-main);transition:background .15s}
.dropdown-item:hover{background:var(--beige)}
.dropdown-item i{font-size:16px;color:var(--text-muted)}
#navbar.scrolled{background:rgba(250,247,242,.92);backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px);box-shadow:0 2px 20px rgba(90,124,101,.1);padding:10px 0}
.dark #navbar.scrolled{background:rgba(26,36,32,.92)}
@media(max-width:768px){.nav-links{display:none!important}}
</style>
<script>
window.addEventListener('scroll',()=>{
    document.getElementById('navbar').classList.toggle('scrolled',window.scrollY>20);
});
function toggleDropdown(){
    const d=document.getElementById('user-dropdown');
    d.style.display=d.style.display==='none'?'block':'none';
}
document.addEventListener('click',e=>{
    const wrap=document.getElementById('user-dropdown-wrap');
    if(wrap&&!wrap.contains(e.target))document.getElementById('user-dropdown').style.display='none';
});
</script>

