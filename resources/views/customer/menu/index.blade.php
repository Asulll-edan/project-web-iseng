@extends('layouts.app')
@section('title','Menu — Rumahnya Anak Sekolah')

@push('styles')
<style>
.page-hero{padding:120px 0 48px;background:linear-gradient(135deg,#1a2e22,#2c3e35)}
.filter-bar{background:var(--warm-white);border-radius:var(--radius-lg);border:1px solid var(--border);padding:16px 20px;display:flex;gap:12px;align-items:center;flex-wrap:wrap}
.search-input{flex:1;min-width:220px;padding:10px 16px 10px 42px;border-radius:20px;border:1.5px solid var(--border);background:var(--beige);color:var(--text-main);font-size:14px;font-family:inherit;outline:none;transition:all .2s}
.search-input:focus{border-color:var(--sage);background:#fff}
.search-wrap{position:relative;flex:1;min-width:220px}
.search-wrap i{position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:17px;pointer-events:none}
.filter-select{padding:10px 16px;border-radius:20px;border:1.5px solid var(--border);background:var(--beige);color:var(--text-main);font-size:14px;font-family:inherit;outline:none;cursor:pointer}
.cat-chip{padding:8px 16px;border-radius:20px;border:1.5px solid var(--border);font-size:13px;font-weight:600;color:var(--text-muted);cursor:pointer;transition:all .2s;white-space:nowrap;background:transparent}
.cat-chip:hover,.cat-chip.active{background:var(--sage);color:#fff;border-color:var(--sage)}
.menu-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(230px,1fr));gap:20px}
.menu-card{background:var(--warm-white);border-radius:20px;border:1px solid var(--border);overflow:hidden;transition:all .3s;position:relative}
.menu-card:hover{transform:translateY(-6px);box-shadow:0 20px 50px rgba(90,124,101,.15)}
.menu-img{width:100%;height:180px;object-fit:cover;transition:transform .4s;background:var(--beige);display:flex;align-items:center;justify-content:center}
.menu-card:hover .menu-img{transform:scale(1.05)}
.fav-btn{position:absolute;top:10px;right:10px;width:32px;height:32px;border-radius:50%;background:rgba(255,255,255,.9);backdrop-filter:blur(8px);border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all .2s}
.fav-btn.active i{color:#e07a5f}
.skeleton{background:linear-gradient(90deg,var(--beige) 25%,var(--border) 50%,var(--beige) 75%);background-size:200% 100%;animation:shimmer 1.5s infinite}
@keyframes shimmer{0%{background-position:200% 0}100%{background-position:-200% 0}}
</style>
@endpush

@section('content')
<div class="page-hero">
    <div class="container" style="text-align:center">
        <div style="font-size:12px;font-weight:700;color:rgba(255,255,255,.5);text-transform:uppercase;letter-spacing:.1em;margin-bottom:8px">Pilih Menu</div>
        <h1 style="font-family:'INeedCoffee',serif;font-size:clamp(28px,5vw,42px);font-weight:600;color:#fff;margin-bottom:10px">Semua Menu</h1>
        <p style="font-size:14px;color:rgba(255,255,255,.65)">Menu lezat pilihan pelajar dengan harga yang bersahabat</p>
    </div>
</div>

<div class="container" style="padding-top:32px;padding-bottom:80px">
    {{-- Filter Bar --}}
    <div class="filter-bar" style="margin-bottom:24px">
        <div class="search-wrap">
            <i class="ti ti-search"></i>
            <input type="text" id="search-input" class="search-input" placeholder="Cari menu..." value="{{ request('search') }}">
        </div>
        <select id="sort-select" class="filter-select">
            <option value="">Urutkan</option>
            <option value="popular" {{ request('sort')=='popular'?'selected':'' }}>Terpopuler</option>
            <option value="rating" {{ request('sort')=='rating'?'selected':'' }}>Rating Tertinggi</option>
            <option value="price_asc" {{ request('sort')=='price_asc'?'selected':'' }}>Harga Terendah</option>
            <option value="price_desc" {{ request('sort')=='price_desc'?'selected':'' }}>Harga Tertinggi</option>
        </select>
        <span style="font-size:13px;color:var(--text-muted)" id="result-count">{{ $menus->total() }} menu</span>
    </div>

    {{-- Category Chips --}}
    <div style="display:flex;gap:8px;overflow-x:auto;padding-bottom:8px;scrollbar-width:none;margin-bottom:28px">
        <a href="{{ route('menu.index') }}" class="cat-chip {{ !request('category') ? 'active' : '' }}">🍽️ Semua</a>
        @foreach($categories as $cat)
        <a href="{{ route('menu.index', array_merge(request()->query(), ['category' => $cat->slug])) }}"
           class="cat-chip {{ request('category') == $cat->slug ? 'active' : '' }}">
            {{ $cat->icon }} {{ $cat->name }}
        </a>
        @endforeach
    </div>

    {{-- Menu Grid --}}
    <div class="menu-grid" id="menu-grid">
        @forelse($menus as $menu)
        <div class="menu-card" data-aos="fade-up" data-aos-delay="{{ ($loop->index % 4) * 60 }}">
            <div style="position:relative;overflow:hidden;height:180px;background:var(--beige);display:flex;align-items:center;justify-content:center">
                @if($menu->image)
                <img src="{{ $menu->image_url }}" class="menu-img" alt="{{ $menu->name }}" loading="lazy">
                @else
                <span style="font-size:52px">🍱</span>
                @endif
                @auth
                <button class="fav-btn {{ in_array($menu->id, $favoriteIds) ? 'active' : '' }}" onclick="toggleFav({{ $menu->id }}, this)">
                    <i class="ti {{ in_array($menu->id, $favoriteIds) ? 'ti-heart-filled' : 'ti-heart' }}" style="font-size:16px;color:{{ in_array($menu->id, $favoriteIds) ? '#e07a5f' : 'var(--text-muted)' }}"></i>
                </button>
                @endauth
                <div style="position:absolute;top:10px;left:10px;display:flex;gap:5px;flex-wrap:wrap">
                    @if($menu->is_best_seller)
                    <span class="badge badge-gold"><i class="ti ti-flame" style="font-size:11px"></i>Best Seller</span>
                    @endif
                    @if($menu->discount_price)
                    <span style="background:#e07a5f;color:#fff;border-radius:8px;padding:3px 8px;font-size:11px;font-weight:700">DISKON</span>
                    @endif
                    @if($menu->stock <= 5 && $menu->stock > 0)
                    <span style="background:#f59e0b;color:#fff;border-radius:8px;padding:3px 8px;font-size:11px;font-weight:700">Hampir Habis</span>
                    @endif
                </div>
            </div>
            <div style="padding:14px 16px">
                <a href="{{ route('menu.show', $menu->slug) }}" style="font-weight:700;font-size:14px;display:block;margin-bottom:4px;color:var(--text-main);display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden">{{ $menu->name }}</a>
                <div style="display:flex;align-items:center;gap:4px;font-size:12px;color:var(--text-muted);margin-bottom:10px">
                    <i class="ti ti-star-filled" style="color:#f59e0b;font-size:13px"></i>
                    {{ number_format($menu->rating,1) }}
                    <span>({{ $menu->review_count }})</span>
                    <span style="margin-left:auto"><i class="ti ti-clock" style="font-size:12px"></i> {{ $menu->preparation_time }}</span>
                </div>
                <div style="display:flex;align-items:center;justify-content:space-between">
                    <div>
                        @if($menu->discount_price)
                        <div style="font-size:11px;text-decoration:line-through;color:var(--text-muted)">Rp {{ number_format($menu->price,0,',','.') }}</div>
                        @endif
                        <div style="font-weight:700;font-size:16px;color:var(--sage-dark)">Rp {{ number_format($menu->effective_price,0,',','.') }}</div>
                    </div>
                    @auth
                    <button onclick="quickAdd({{ $menu->id }},this)" style="width:34px;height:34px;background:var(--sage);border:none;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all .2s" @if($menu->stock<=0) disabled style="opacity:.5" @endif>
                        <i class="ti ti-{{ $menu->stock > 0 ? 'plus' : 'x' }}" style="color:#fff;font-size:17px"></i>
                    </button>
                    @else
                    <a href="{{ route('login') }}" style="width:34px;height:34px;background:var(--sage);border-radius:50%;display:flex;align-items:center;justify-content:center">
                        <i class="ti ti-plus" style="color:#fff;font-size:17px"></i>
                    </a>
                    @endauth
                </div>
            </div>
        </div>
        @empty
        <div style="grid-column:1/-1;text-align:center;padding:60px 20px">
            <div style="font-size:48px;margin-bottom:16px">🔍</div>
            <div style="font-weight:600;font-size:18px;margin-bottom:8px">Menu tidak ditemukan</div>
            <div style="font-size:14px;color:var(--text-muted)">Coba kata kunci lain atau pilih kategori berbeda</div>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($menus->hasPages())
    <div style="margin-top:36px;display:flex;justify-content:center">
        {{ $menus->links() }}
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
let searchTimeout;
document.getElementById('search-input').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        const params = new URLSearchParams(window.location.search);
        params.set('search', this.value);
        params.delete('page');
        window.location.href = '?' + params.toString();
    }, 500);
});

document.getElementById('sort-select').addEventListener('change', function() {
    const params = new URLSearchParams(window.location.search);
    if (this.value) params.set('sort', this.value); else params.delete('sort');
    params.delete('page');
    window.location.href = '?' + params.toString();
});

function quickAdd(menuId, btn) {
    btn.disabled = true;
    const orig = btn.innerHTML;
    btn.innerHTML = '<i class="ti ti-loader" style="color:#fff;font-size:17px;animation:spin 1s linear infinite"></i>';
    fetch('/cart/add', {
        method: 'POST',
        headers: {'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json'},
        body: JSON.stringify({menu_id: menuId, quantity: 1})
    })
    .then(r => r.json())
    .then(d => {
        btn.innerHTML = '<i class="ti ti-check" style="color:#fff;font-size:17px"></i>';
        showToast(d.message || 'Ditambah ke keranjang!', 'success');
        const b = document.getElementById('cart-count');
        if (b && d.cart_count) { b.textContent = d.cart_count; b.style.display = 'flex'; }
        setTimeout(() => { btn.disabled = false; btn.innerHTML = orig; }, 1500);
    })
    .catch(() => { btn.disabled = false; btn.innerHTML = orig; });
}

function toggleFav(menuId, btn) {
    fetch('/menu/' + menuId + '/favorite', {
        method: 'POST',
        headers: {'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json'}
    })
    .then(r => r.json())
    .then(d => {
        const icon = btn.querySelector('i');
        if (d.favorited) {
            btn.classList.add('active');
            icon.className = 'ti ti-heart-filled';
            icon.style.color = '#e07a5f';
            showToast('Ditambahkan ke favorit!', 'success');
        } else {
            btn.classList.remove('active');
            icon.className = 'ti ti-heart';
            icon.style.color = 'var(--text-muted)';
        }
    });
}
</script>
@endpush


