<!DOCTYPE html>
<html lang="id" class="{{ auth()->check() && auth()->user()->dark_mode ? 'dark' : '' }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Rumahnya Anak Sekolah') — Premium Student Culinary</title>

<!-- Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600&display=swap" rel="stylesheet">

<!-- Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.44.0/tabler-icons.min.css">

<!-- SweetAlert2 -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

<!-- AOS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css">

<!-- Swiper -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">

<style>
:root {
  --sage:       #5a7c65;
  --sage-light: #8aaa92;
  --sage-dark:  #3d5c47;
  --cream:      #faf7f2;
  --beige:      #f0ebe0;
  --brown:      #8b6f5e;
  --charcoal:   #2c3e35;
  --warm-white: #fffdf9;
  --text-main:  #2c3e35;
  --text-muted: #6b7c72;
  --border:     rgba(90,124,101,.15);
  --shadow-sm:  0 2px 12px rgba(90,124,101,.08);
  --shadow-md:  0 8px 32px rgba(90,124,101,.12);
  --shadow-lg:  0 20px 60px rgba(90,124,101,.15);
  --radius-sm:  8px;
  --radius-md:  14px;
  --radius-lg:  20px;
  --radius-xl:  28px;
  --transition: all .3s cubic-bezier(.4,0,.2,1);
}
.dark {
  --cream:      #1a2420;
  --beige:      #1e2b25;
  --warm-white: #1a2420;
  --text-main:  #e8f0eb;
  --text-muted: #8fa897;
  --border:     rgba(138,170,146,.12);
  --shadow-sm:  0 2px 12px rgba(0,0,0,.3);
  --shadow-md:  0 8px 32px rgba(0,0,0,.4);
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{scroll-behavior:smooth}
body{font-family:'Plus Jakarta Sans',sans-serif;background:var(--cream);color:var(--text-main);line-height:1.6;overflow-x:hidden}
a{text-decoration:none;color:inherit}
img{max-width:100%}

/* Glassmorphism */
.glass{background:rgba(255,255,255,.7);backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px);border:1px solid var(--border)}
.dark .glass{background:rgba(30,43,37,.7)}

/* Premium button */
.btn-primary{display:inline-flex;align-items:center;gap:8px;padding:12px 28px;background:var(--sage);color:#fff;border-radius:var(--radius-xl);font-weight:600;font-size:14px;border:none;cursor:pointer;transition:var(--transition);box-shadow:0 4px 20px rgba(90,124,101,.3)}
.btn-primary:hover{background:var(--sage-dark);transform:translateY(-2px);box-shadow:0 8px 30px rgba(90,124,101,.4)}
.btn-outline{display:inline-flex;align-items:center;gap:8px;padding:11px 28px;background:transparent;color:var(--sage);border-radius:var(--radius-xl);font-weight:600;font-size:14px;border:2px solid var(--sage);cursor:pointer;transition:var(--transition)}
.btn-outline:hover{background:var(--sage);color:#fff;transform:translateY(-2px)}

/* Card */
.card{background:var(--warm-white);border-radius:var(--radius-lg);border:1px solid var(--border);box-shadow:var(--shadow-sm);overflow:hidden}
.card-hover{transition:var(--transition)}
.card-hover:hover{transform:translateY(-6px);box-shadow:var(--shadow-lg)}

/* Toast container */
#toast-container{position:fixed;top:24px;right:24px;z-index:9999;display:flex;flex-direction:column;gap:10px}
.toast{padding:14px 20px;border-radius:var(--radius-md);font-size:14px;font-weight:500;color:#fff;min-width:260px;max-width:380px;display:flex;align-items:center;gap:10px;box-shadow:var(--shadow-md);animation:toastIn .3s ease;pointer-events:auto}
.toast.success{background:var(--sage-dark)}
.toast.error{background:#c0392b}
.toast.info{background:var(--brown)}
@keyframes toastIn{from{opacity:0;transform:translateX(100px)}to{opacity:1;transform:none}}
@keyframes toastOut{to{opacity:0;transform:translateX(100px)}}

/* Scrollbar */
::-webkit-scrollbar{width:6px}::-webkit-scrollbar-track{background:var(--beige)}::-webkit-scrollbar-thumb{background:var(--sage-light);border-radius:3px}

/* Badge */
.badge{display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:20px;font-size:11px;font-weight:600}
.badge-sage{background:rgba(90,124,101,.12);color:var(--sage-dark)}
.badge-gold{background:rgba(212,175,55,.15);color:#9a6f00}

/* Container */
.container{max-width:1200px;margin:0 auto;padding:0 20px}

@media(max-width:768px){.container{padding:0 16px}.btn-primary,.btn-outline{padding:10px 20px;font-size:13px}}
</style>

@stack('styles')
</head>
<body>

{{-- Loading Screen --}}
@include('components.loading-screen')

{{-- Navbar --}}
@include('components.navbar')

{{-- Toast --}}
<div id="toast-container"></div>

{{-- Flash Messages --}}
@if(session('success'))
<script>window.addEventListener('DOMContentLoaded',()=>showToast('{{ session('success') }}','success'))</script>
@endif
@if(session('error'))
<script>window.addEventListener('DOMContentLoaded',()=>showToast('{{ session('error') }}','error'))</script>
@endif

{{-- Main Content --}}
<main>
    @yield('content')
</main>

{{-- Footer --}}
@include('components.footer')

{{-- Chat Bubble --}}
@include('components.chat-bubble')

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

// ── SweetAlert helpers ──────────────────────────────────
function confirmDanger(title, text, cb, btnText='Ya, Lanjutkan') {
    Swal.fire({
        title, text, icon:'warning',
        showCancelButton:true,
        confirmButtonColor:'#c0392b',
        cancelButtonColor:'#6b7c72',
        confirmButtonText: btnText,
        cancelButtonText:'Batal',
        borderRadius:'16px',
    }).then(r=>{ if(r.isConfirmed) cb(); });
}
function confirmAction(title, text, cb, btnText='Ya') {
    Swal.fire({
        title, text, icon:'question',
        showCancelButton:true,
        confirmButtonColor:'#3d5c47',
        cancelButtonColor:'#6b7c72',
        confirmButtonText: btnText,
        cancelButtonText:'Batal',
    }).then(r=>{ if(r.isConfirmed) cb(); });
}

// AOS init
AOS.init({ duration: 700, once: true, offset: 60 });

// Toast
function showToast(msg, type='info', duration=3500) {
    const icons = { success: 'ti-circle-check', error: 'ti-alert-circle', info: 'ti-info-circle' };
    const t = document.createElement('div');
    t.className = `toast ${type}`;
    t.innerHTML = `<i class="ti ${icons[type]||'ti-bell'}" style="font-size:18px;flex-shrink:0"></i><span>${msg}</span>`;
    document.getElementById('toast-container').appendChild(t);
    setTimeout(() => { t.style.animation='toastOut .3s ease forwards'; setTimeout(()=>t.remove(),300); }, duration);
}

// AJAX setup
function ajax(url, method='GET', data=null) {
    const opts = { method, headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' } };
    if (data) opts.body = JSON.stringify(data);
    return fetch(url, opts).then(r => r.json());
}

// Cart count update
function updateCartCount() {
    fetch('/cart/count').then(r=>r.json()).then(d=>{
        const badge = document.getElementById('cart-count');
        if (badge) { badge.textContent = d.count; badge.style.display = d.count > 0 ? 'flex' : 'none'; }
    });
}

// Notification count
function updateNotifCount() {
    fetch('/notifications/count').then(r=>r.json()).then(d=>{
        const badge = document.getElementById('notif-count');
        if (badge) { badge.textContent = d.count; badge.style.display = d.count > 0 ? 'flex' : 'none'; }
    });
}

@auth
setInterval(updateNotifCount, 30000);
updateCartCount();
updateNotifCount();
@endauth
</script>

@stack('scripts')
</body>
</html>