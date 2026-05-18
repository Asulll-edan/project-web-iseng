@extends('layouts.app')
@section('title','Rumahnya Anak Sekolah — Premium Student Culinary')

@push('styles')
<style>
/* ── Hero ── */
.hero{min-height:100vh;position:relative;display:flex;align-items:center;overflow:hidden;padding-top:80px}
.hero-bg{position:absolute;inset:0;background:linear-gradient(135deg,#1a2e22 0%,#2c3e35 40%,#3d5c47 100%);z-index:0}
.hero-overlay{position:absolute;inset:0;background:url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%238aaa92' fill-opacity='0.04'%3E%3Ccircle cx='30' cy='30' r='2'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");z-index:1}
.hero-content{position:relative;z-index:2;max-width:620px}
.hero-badge{display:inline-flex;align-items:center;gap:8px;background:rgba(138,170,146,.15);border:1px solid rgba(138,170,146,.25);border-radius:20px;padding:7px 16px;font-size:12px;font-weight:600;color:#8aaa92;margin-bottom:24px;backdrop-filter:blur(8px)}
.hero-title{font-family:'Playfair Display',serif;font-size:clamp(38px,6vw,62px);font-weight:600;line-height:1.15;color:#fff;margin-bottom:20px}
.hero-title span{color:#8aaa92}
.hero-desc{font-size:16px;color:rgba(255,255,255,.7);line-height:1.7;margin-bottom:32px;max-width:480px}
.hero-actions{display:flex;flex-wrap:wrap;gap:12px;align-items:center}
.hero-stats{display:flex;gap:32px;margin-top:48px;padding-top:32px;border-top:1px solid rgba(255,255,255,.08)}
.hero-stat-val{font-size:26px;font-weight:700;color:#fff}
.hero-stat-label{font-size:12px;color:rgba(255,255,255,.5);margin-top:2px}
.hero-food-grid{position:absolute;right:0;top:0;bottom:0;width:45%;display:grid;grid-template-columns:1fr 1fr;gap:12px;padding:80px 40px 40px 0;z-index:2;opacity:.85}
.food-card{border-radius:16px;overflow:hidden;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.08);backdrop-filter:blur(8px);height:160px;display:flex;align-items:center;justify-content:center;font-size:40px;flex-direction:column;gap:6px}
.food-card span{font-size:11px;color:rgba(255,255,255,.6);font-weight:500}
.food-card:nth-child(odd){margin-top:24px}

/* ── Banner Slider ── */
.banner-section{padding:40px 0;overflow:hidden}
.swiper-banner .swiper-slide{border-radius:20px;overflow:hidden;height:280px}
.banner-slide-inner{position:relative;height:100%;background:linear-gradient(135deg,var(--sage-dark),var(--sage));display:flex;align-items:center;padding:0 48px}
.banner-slide-inner .badge-promo{background:rgba(255,255,255,.2);color:#fff;border-radius:8px;padding:5px 12px;font-size:11px;font-weight:700;display:inline-block;margin-bottom:12px}
.banner-slide-inner h3{font-size:clamp(18px,3vw,26px);font-weight:700;color:#fff;margin-bottom:8px}
.banner-slide-inner p{font-size:14px;color:rgba(255,255,255,.8)}
.banner-emoji{position:absolute;right:48px;top:50%;transform:translateY(-50%);font-size:80px;opacity:.8}

/* ── Section titles ── */
.section-label{font-size:12px;font-weight:700;color:var(--sage);text-transform:uppercase;letter-spacing:.1em;display:flex;align-items:center;gap:8px;margin-bottom:8px}
.section-label::before{content:'';width:20px;height:2px;background:var(--sage);border-radius:1px}
.section-title{font-family:'Playfair Display',serif;font-size:clamp(24px,4vw,36px);font-weight:600;color:var(--text-main);margin-bottom:8px}
.section-sub{font-size:14px;color:var(--text-muted);max-width:480px}

/* ── Menu card ── */
.menu-card{background:var(--warm-white);border-radius:20px;border:1px solid var(--border);overflow:hidden;transition:all .3s cubic-bezier(.4,0,.2,1);cursor:pointer}
.menu-card:hover{transform:translateY(-8px);box-shadow:0 24px 60px rgba(90,124,101,.18)}
.menu-card-img{width:100%;height:180px;object-fit:cover;transition:transform .4s}
.menu-card:hover .menu-card-img{transform:scale(1.06)}
.menu-card-body{padding:16px}
.menu-card-name{font-weight:700;font-size:14px;margin-bottom:6px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.menu-card-price{font-weight:700;font-size:16px;color:var(--sage-dark)}
.menu-card-rating{display:flex;align-items:center;gap:4px;font-size:12px;color:var(--text-muted);margin-top:4px}

/* ── Stats counter ── */
.stats-section{background:var(--sage-dark);padding:60px 0}
.stat-counter{text-align:center}
.stat-counter-val{font-family:'Playfair Display',serif;font-size:clamp(32px,5vw,52px);font-weight:600;color:#fff}
.stat-counter-label{font-size:13px;color:rgba(255,255,255,.65);margin-top:6px}

/* ── Category pills ── */
.cat-pill{display:inline-flex;align-items:center;gap:8px;padding:10px 20px;border-radius:20px;font-size:14px;font-weight:600;border:1.5px solid var(--border);color:var(--text-muted);transition:all .2s;cursor:pointer;white-space:nowrap;text-decoration:none}
.cat-pill:hover,.cat-pill.active{background:var(--sage);color:#fff;border-color:var(--sage)}

@media(max-width:768px){
.hero-food-grid{display:none}
.hero-content{max-width:100%}
.hero-stats{gap:20px;flex-wrap:wrap}
}
</style>
@endpush

@section('content')
{{-- ── Hero ─────────────────────────────────────────────────────────────── --}}
<section class="hero">
    <div class="hero-bg"></div>
    <div class="hero-overlay"></div>
    <div class="container" style="position:relative;z-index:2;width:100%">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:40px;align-items:center">
            <div class="hero-content" data-aos="fade-right">
                <div class="hero-badge">
                    <span style="width:8px;height:8px;border-radius:50%;background:#4ade80;display:inline-block"></span>
                    Buka Sekarang · 07:00 – 21:00 WIB
                </div>
                <h1 class="hero-title">
                    Premium Kuliner<br><span>Anak Sekolah</span>
                </h1>
                <p class="hero-desc">
                    Pengalaman kuliner premium untuk pelajar. Menu lezat, harga terjangkau, suasana nyaman dan modern.
                </p>
                <div class="hero-actions">
                    <a href="{{ route('menu.index') }}" class="btn-primary" style="background:var(--sage-light)!important">
                        <i class="ti ti-bowl-chopsticks"></i> Lihat Menu
                    </a>
                    <a href="{{ route('reservation.index') }}" class="btn-outline" style="color:#fff;border-color:rgba(255,255,255,.3)">
                        <i class="ti ti-calendar-event"></i> Reservasi
                    </a>
                </div>
                <div class="hero-stats">
                    @foreach([['1000+','Pelanggan Happy'],['50+','Menu Pilihan'],['4.9','Rating Bintang'],['3th','Berdiri']] as $s)
                    <div class="stat-counter" data-aos="fade-up" data-aos-delay="{{ $loop->index * 80 }}">
                        <div class="hero-stat-val">{{ $s[0] }}</div>
                        <div class="hero-stat-label">{{ $s[1] }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div data-aos="fade-left" style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
                @foreach(['🍱 Nasi Geprek','☕ Kopi Susu','🥗 Salad Wrap','🍜 Mie Premium','🍰 Dessert Box','🥟 Gorengan'] as $item)
                <div style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);border-radius:16px;height:110px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:6px;backdrop-filter:blur(10px);{{ $loop->odd ? 'margin-top:20px' : '' }}">
                    <span style="font-size:32px">{{ explode(' ',$item)[0] }}</span>
                    <span style="font-size:11px;color:rgba(255,255,255,.65);font-weight:500">{{ implode(' ',array_slice(explode(' ',$item),1)) }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- ── Banner Slider ─────────────────────────────────────────────────────── --}}
@if($banners->count())
<section class="banner-section">
    <div class="container">
        <div class="swiper swiper-banner" data-aos="fade-up">
            <div class="swiper-wrapper">
                @foreach($banners as $banner)
                <div class="swiper-slide">
                    @if(file_exists(public_path('storage/'.$banner->image)))
                    <a href="{{ $banner->link ?? '#' }}" style="display:block;height:100%;position:relative">
                        <img src="{{ $banner->image_url }}" style="width:100%;height:100%;object-fit:cover" alt="{{ $banner->title }}">
                        <div style="position:absolute;inset:0;background:linear-gradient(90deg,rgba(0,0,0,.5),transparent);display:flex;align-items:center;padding:0 48px">
                            <div style="color:#fff"><h3 style="font-size:22px;font-weight:700;margin-bottom:6px">{{ $banner->title }}</h3><p style="font-size:14px;opacity:.85">{{ $banner->description }}</p></div>
                        </div>
                    </a>
                    @else
                    <div class="banner-slide-inner">
                        <div><div class="badge-promo">PROMO</div><h3>{{ $banner->title }}</h3><p>{{ $banner->description }}</p></div>
                        <div class="banner-emoji">🎉</div>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
            <div class="swiper-pagination"></div>
        </div>
    </div>
</section>
@endif

{{-- ── Categories ────────────────────────────────────────────────────────── --}}
<section style="padding:56px 0 32px">
    <div class="container">
        <div data-aos="fade-up" style="text-align:center;margin-bottom:28px">
            <div class="section-label" style="justify-content:center">Kategori</div>
            <div class="section-title">Temukan Menu Favoritmu</div>
        </div>
        <div style="display:flex;gap:10px;overflow-x:auto;padding-bottom:8px;scrollbar-width:none" data-aos="fade-up" data-aos-delay="100">
            <a href="{{ route('menu.index') }}" class="cat-pill active">🍽️ Semua Menu</a>
            @foreach($categories as $cat)
            <a href="{{ route('menu.index', ['category'=>$cat->slug]) }}" class="cat-pill">{{ $cat->icon }} {{ $cat->name }}</a>
            @endforeach
        </div>
    </div>
</section>

{{-- ── Best Seller ───────────────────────────────────────────────────────── --}}
@if($bestSeller->count())
<section style="padding:32px 0 60px">
    <div class="container">
        <div style="display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:28px" data-aos="fade-up">
            <div>
                <div class="section-label"><i class="ti ti-flame" style="color:var(--sage)"></i>Best Seller</div>
                <div class="section-title">Paling Banyak Dipesan</div>
                <div class="section-sub">Menu andalan yang selalu bikin balik lagi</div>
            </div>
            <a href="{{ route('menu.index') }}" style="font-size:13px;color:var(--sage);font-weight:600;display:flex;align-items:center;gap:4px;white-space:nowrap">
                Lihat semua <i class="ti ti-arrow-right"></i>
            </a>
        </div>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:20px">
            @foreach($bestSeller as $menu)
            <div class="menu-card" data-aos="fade-up" data-aos-delay="{{ $loop->index * 60 }}">
                <div style="position:relative;overflow:hidden;height:180px;background:var(--beige);display:flex;align-items:center;justify-content:center">
                    @if($menu->image)
                    <img src="{{ $menu->image_url }}" class="menu-card-img" alt="{{ $menu->name }}" loading="lazy">
                    @else
                    <span style="font-size:52px">🍱</span>
                    @endif
                    <div style="position:absolute;top:10px;left:10px;display:flex;gap:6px">
                        <span class="badge badge-gold"><i class="ti ti-flame" style="font-size:11px"></i>Best Seller</span>
                    </div>
                    @if($menu->discount_price)
                    <div style="position:absolute;top:10px;right:10px;background:#e07a5f;color:#fff;border-radius:8px;padding:3px 8px;font-size:11px;font-weight:700">DISKON</div>
                    @endif
                </div>
                <div class="menu-card-body">
                    <div class="menu-card-name">{{ $menu->name }}</div>
                    <div class="menu-card-rating">
                        <i class="ti ti-star-filled" style="color:#f59e0b;font-size:13px"></i>
                        {{ number_format($menu->rating,1) }}
                        <span>({{ $menu->review_count }})</span>
                        <span style="margin-left:auto;font-size:11px">{{ $menu->preparation_time }}</span>
                    </div>
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-top:10px">
                        <div>
                            @if($menu->discount_price)
                            <div style="font-size:11px;text-decoration:line-through;color:var(--text-muted)">Rp {{ number_format($menu->price,0,',','.') }}</div>
                            @endif
                            <div class="menu-card-price">Rp {{ number_format($menu->effective_price,0,',','.') }}</div>
                        </div>
                        @auth
                        <button onclick="quickAdd({{ $menu->id }},this)" style="width:36px;height:36px;background:var(--sage);border:none;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all .2s" title="Tambah ke keranjang">
                            <i class="ti ti-plus" style="color:#fff;font-size:18px"></i>
                        </button>
                        @else
                        <a href="{{ route('login') }}" style="width:36px;height:36px;background:var(--sage);border-radius:50%;display:flex;align-items:center;justify-content:center">
                            <i class="ti ti-plus" style="color:#fff;font-size:18px"></i>
                        </a>
                        @endauth
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ── Stats Counter ─────────────────────────────────────────────────────── --}}
<section class="stats-section">
    <div class="container">
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:32px">
            @foreach([
                ['ti-users','happy_customers','Pelanggan Happy'],
                ['ti-shopping-bag','total_orders','Pesanan Selesai'],
                ['ti-bowl-chopsticks','total_menu','Menu Tersedia'],
                ['ti-star','rating_avg','Rating Rata-rata'],
            ] as $s)
            <div class="stat-counter" data-aos="fade-up" data-aos-delay="{{ $loop->index * 80 }}">
                <i class="ti {{ $s[0] }}" style="font-size:32px;color:rgba(255,255,255,.4);margin-bottom:10px;display:block"></i>
                <div class="stat-counter-val" data-target="{{ $stats[$s[1]] }}">{{ $stats[$s[1]] }}</div>
                <div class="stat-counter-label">{{ $s[2] }}</div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ── Featured / All Menu ───────────────────────────────────────────────── --}}
@if($featured->count())
<section style="padding:70px 0">
    <div class="container">
        <div style="display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:32px" data-aos="fade-up">
            <div>
                <div class="section-label"><i class="ti ti-sparkles" style="color:var(--sage)"></i>Featured</div>
                <div class="section-title">Menu Pilihan Hari Ini</div>
            </div>
            <a href="{{ route('menu.index') }}" style="font-size:13px;color:var(--sage);font-weight:600;display:flex;align-items:center;gap:4px">
                Lihat semua <i class="ti ti-arrow-right"></i>
            </a>
        </div>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:20px">
            @foreach($featured->take(4) as $menu)
            <a href="{{ route('menu.show',$menu->slug) }}" class="menu-card card-hover" data-aos="fade-up" data-aos-delay="{{ $loop->index * 70 }}">
                <div style="height:180px;background:var(--beige);overflow:hidden;display:flex;align-items:center;justify-content:center">
                    @if($menu->image)
                    <img src="{{ $menu->image_url }}" style="width:100%;height:100%;object-fit:cover;transition:transform .4s" alt="{{ $menu->name }}">
                    @else
                    <span style="font-size:52px">🍽️</span>
                    @endif
                </div>
                <div class="menu-card-body">
                    <div class="menu-card-name">{{ $menu->name }}</div>
                    <div class="menu-card-rating"><i class="ti ti-star-filled" style="color:#f59e0b;font-size:13px"></i>{{ number_format($menu->rating,1) }}</div>
                    <div class="menu-card-price" style="margin-top:8px">Rp {{ number_format($menu->effective_price,0,',','.') }}</div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ── CTA Banner ────────────────────────────────────────────────────────── --}}
<section style="padding:0 0 80px">
    <div class="container">
        <div data-aos="fade-up" style="background:linear-gradient(135deg,var(--sage-dark),var(--sage));border-radius:24px;padding:48px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:24px">
            <div>
                <div style="font-size:12px;font-weight:700;color:rgba(255,255,255,.65);text-transform:uppercase;letter-spacing:.1em;margin-bottom:8px">Mulai Sekarang</div>
                <div style="font-family:'Playfair Display',serif;font-size:28px;font-weight:600;color:#fff;margin-bottom:8px">Pesan Sekarang,<br>Nikmati Sekarang!</div>
                <div style="font-size:14px;color:rgba(255,255,255,.75)">Gratis ongkir untuk member. Daftar sekarang dan dapatkan bonus point!</div>
            </div>
            <div style="display:flex;gap:12px;flex-wrap:wrap">
                <a href="{{ route('menu.index') }}" style="padding:13px 28px;background:#fff;color:var(--sage-dark);border-radius:20px;font-weight:700;font-size:14px;display:inline-flex;align-items:center;gap:8px;transition:all .2s">
                    <i class="ti ti-bowl-chopsticks"></i> Pesan Sekarang
                </a>
                @guest
                <a href="{{ route('register') }}" style="padding:13px 28px;background:rgba(255,255,255,.15);color:#fff;border-radius:20px;font-weight:700;font-size:14px;border:1.5px solid rgba(255,255,255,.3);display:inline-flex;align-items:center;gap:8px;transition:all .2s">
                    <i class="ti ti-user-plus"></i> Daftar Gratis
                </a>
                @endguest
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
// Banner swiper
new Swiper('.swiper-banner',{loop:true,autoplay:{delay:4000,disableOnInteraction:false},pagination:{el:'.swiper-pagination',clickable:true},spaceBetween:0,effect:'fade'});

// Quick add to cart
function quickAdd(menuId, btn) {
    btn.disabled = true;
    btn.innerHTML = '<i class="ti ti-loader" style="color:#fff;font-size:18px;animation:spin 1s linear infinite"></i>';
    fetch('/cart/add', {
        method: 'POST',
        headers: {'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json'},
        body: JSON.stringify({menu_id: menuId, quantity: 1})
    })
    .then(r => r.json())
    .then(d => {
        btn.disabled = false;
        btn.innerHTML = '<i class="ti ti-check" style="color:#fff;font-size:18px"></i>';
        showToast(d.message || 'Ditambah ke keranjang!', 'success');
        const badge = document.getElementById('cart-count');
        if (badge && d.cart_count) { badge.textContent = d.cart_count; badge.style.display = 'flex'; }
        setTimeout(() => { btn.innerHTML = '<i class="ti ti-plus" style="color:#fff;font-size:18px"></i>'; }, 1500);
    })
    .catch(() => { btn.disabled = false; btn.innerHTML = '<i class="ti ti-plus" style="color:#fff;font-size:18px"></i>'; });
}
</script>
<style>@keyframes spin{to{transform:rotate(360deg)}}</style>
@endpush
