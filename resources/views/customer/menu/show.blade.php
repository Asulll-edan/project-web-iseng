@extends('layouts.app')
@section('title', $menu->name . ' — Rumahnya Anak Sekolah')

@push('styles')
<style>
.page-top{padding:110px 0 0;background:linear-gradient(135deg,#1a2e22,#2c3e35)}
.detail-grid{display:grid;grid-template-columns:1fr 1fr;gap:48px;align-items:start;padding:40px 0 80px}
.img-main{width:100%;border-radius:20px;overflow:hidden;height:380px;background:var(--beige);display:flex;align-items:center;justify-content:center;position:sticky;top:100px}
.img-main img{width:100%;height:100%;object-fit:cover}
.review-card{background:var(--warm-white);border:1px solid var(--border);border-radius:14px;padding:16px}
.qty-btn{width:36px;height:36px;border-radius:50%;border:1.5px solid var(--border);background:transparent;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:600;color:var(--text-main);transition:all .2s}
.qty-btn:hover{background:var(--sage);color:#fff;border-color:var(--sage)}
@media(max-width:768px){.detail-grid{grid-template-columns:1fr}.img-main{position:static;height:260px}}
</style>
@endpush

@section('content')
<div class="page-top">
    <div class="container">
        <div style="padding:20px 0 28px;display:flex;align-items:center;gap:8px;font-size:13px;color:rgba(255,255,255,.6)">
            <a href="{{ route('home') }}" style="color:rgba(255,255,255,.6)">Home</a>
            <i class="ti ti-chevron-right" style="font-size:14px"></i>
            <a href="{{ route('menu.index') }}" style="color:rgba(255,255,255,.6)">Menu</a>
            <i class="ti ti-chevron-right" style="font-size:14px"></i>
            <span style="color:rgba(255,255,255,.9)">{{ $menu->name }}</span>
        </div>
    </div>
</div>

<div class="container">
    <div class="detail-grid">
        {{-- Left: Image --}}
        <div data-aos="fade-right">
            <div class="img-main">
                @if($menu->image)
                    <img src="{{ $menu->image_url }}" alt="{{ $menu->name }}" id="main-img">
                @else
                    <span style="font-size:80px">🍱</span>
                @endif
            </div>
            @if($menu->images->count())
            <div style="display:flex;gap:8px;margin-top:12px;overflow-x:auto">
                @foreach($menu->images as $img)
                <div onclick="document.getElementById('main-img').src='{{ asset('storage/'.$img->image_path) }}'"
                     style="width:70px;height:70px;border-radius:10px;overflow:hidden;cursor:pointer;flex-shrink:0;border:2px solid transparent;transition:all .2s"
                     onmouseover="this.style.borderColor='var(--sage)'" onmouseout="this.style.borderColor='transparent'">
                    <img src="{{ asset('storage/'.$img->image_path) }}" style="width:100%;height:100%;object-fit:cover">
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Right: Info --}}
        <div data-aos="fade-left">
            <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:14px">
                @if($menu->is_best_seller)
                <span class="badge badge-gold"><i class="ti ti-flame" style="font-size:11px"></i> Best Seller</span>
                @endif
                @if($menu->is_available && $menu->stock > 0)
                <span class="badge" style="background:rgba(74,222,128,.12);color:#166534"><i class="ti ti-circle-check" style="font-size:11px"></i> Tersedia</span>
                @else
                <span class="badge" style="background:rgba(220,80,60,.1);color:#9b1c1c">Habis</span>
                @endif
                <span class="badge badge-sage">{{ $menu->category->name }}</span>
            </div>

            <h1 style="font-family:'INeedCoffee',serif;font-size:clamp(22px,4vw,30px);font-weight:600;color:var(--text-main);margin-bottom:10px">{{ $menu->name }}</h1>

            <div style="display:flex;align-items:center;gap:16px;margin-bottom:16px;flex-wrap:wrap">
                <div style="display:flex;align-items:center;gap:4px;font-size:14px;font-weight:600">
                    @for($i=1;$i<=5;$i++)
                    <i class="ti ti-star{{ $i <= round($menu->rating) ? '-filled' : '' }}" style="color:#f59e0b;font-size:16px"></i>
                    @endfor
                    <span style="color:var(--text-muted);font-weight:400;font-size:13px">({{ $menu->review_count }} ulasan)</span>
                </div>
                <span style="color:var(--text-muted);font-size:13px"><i class="ti ti-clock" style="font-size:14px"></i> {{ $menu->preparation_time }}</span>
                @if($menu->calories)
                <span style="color:var(--text-muted);font-size:13px"><i class="ti ti-flame" style="font-size:14px"></i> {{ $menu->calories }} kal</span>
                @endif
            </div>

            <div style="margin-bottom:20px">
                @if($menu->discount_price)
                <span style="font-size:14px;text-decoration:line-through;color:var(--text-muted)">Rp {{ number_format($menu->price,0,',','.') }}</span>
                @endif
                <div style="font-size:clamp(24px,4vw,32px);font-weight:700;color:var(--sage-dark)">
                    Rp {{ number_format($menu->effective_price,0,',','.') }}
                </div>
            </div>

            <p style="font-size:14px;line-height:1.8;color:var(--text-muted);margin-bottom:24px">{{ $menu->description }}</p>

            @auth
            @if($menu->is_available && $menu->stock > 0)
            <div style="background:var(--beige);border-radius:16px;padding:20px;margin-bottom:20px">
                <div style="font-size:13px;font-weight:600;margin-bottom:12px">Catatan (opsional)</div>
                <textarea id="item-note" placeholder="Contoh: tidak pedas, tanpa bawang..." rows="2"
                    style="width:100%;padding:10px 14px;border-radius:10px;border:1.5px solid var(--border);background:#fff;color:var(--text-main);font-size:13px;font-family:inherit;outline:none;resize:none"></textarea>
                <div style="display:flex;align-items:center;gap:16px;margin-top:16px">
                    <div style="display:flex;align-items:center;gap:12px">
                        <button class="qty-btn" id="qty-minus" onclick="changeQty(-1)">−</button>
                        <span id="qty-val" style="font-size:18px;font-weight:700;min-width:24px;text-align:center">1</span>
                        <button class="qty-btn" id="qty-plus" onclick="changeQty(1)">+</button>
                    </div>
                    <button onclick="addToCart()" id="add-btn"
                        style="flex:1;padding:13px;background:var(--sage);color:#fff;border:none;border-radius:12px;font-weight:700;font-size:15px;cursor:pointer;transition:all .2s;display:flex;align-items:center;justify-content:center;gap:8px">
                        <i class="ti ti-shopping-cart-plus"></i> Tambah ke Keranjang
                    </button>
                </div>
                <div style="font-size:12px;color:var(--text-muted);margin-top:8px"><i class="ti ti-package" style="font-size:13px"></i> Stok tersisa: {{ $menu->stock }}</div>
            </div>
            @else
            <div style="background:rgba(220,80,60,.06);border:1px solid rgba(220,80,60,.15);border-radius:14px;padding:16px;text-align:center;margin-bottom:20px;color:#9b1c1c;font-weight:600">
                <i class="ti ti-circle-x" style="font-size:20px;display:block;margin-bottom:6px"></i>
                Menu ini sedang tidak tersedia
            </div>
            @endif

            <div style="display:flex;gap:10px">
                <button onclick="toggleFav({{ $menu->id }},this)" id="fav-btn"
                    style="flex:1;padding:12px;border-radius:12px;border:1.5px solid var(--border);background:transparent;cursor:pointer;font-weight:600;font-size:14px;display:flex;align-items:center;justify-content:center;gap:8px;transition:all .2s;color:{{ $isFavorite ? '#e07a5f' : 'var(--text-muted)' }}">
                    <i class="ti {{ $isFavorite ? 'ti-heart-filled' : 'ti-heart' }}" style="font-size:18px"></i>
                    {{ $isFavorite ? 'Difavoritkan' : 'Favorit' }}
                </button>
                <a href="{{ route('cart.index') }}" style="flex:1;padding:12px;border-radius:12px;border:1.5px solid var(--sage);color:var(--sage);font-weight:600;font-size:14px;display:flex;align-items:center;justify-content:center;gap:8px;transition:all .2s">
                    <i class="ti ti-shopping-bag" style="font-size:18px"></i> Keranjang
                </a>
            </div>
            @else
            <a href="{{ route('login') }}" class="btn-primary" style="width:100%;justify-content:center;margin-bottom:12px">
                <i class="ti ti-login"></i> Masuk untuk memesan
            </a>
            @endauth
        </div>
    </div>

    {{-- Reviews --}}
    @if($menu->reviews->count())
    <div style="padding-bottom:80px">
        <div style="margin-bottom:24px">
            <div style="font-size:12px;font-weight:700;color:var(--sage);text-transform:uppercase;letter-spacing:.1em;margin-bottom:6px">Ulasan</div>
            <h2 style="font-family:'INeedCoffee',serif;font-size:22px;font-weight:600">Apa kata mereka?</h2>
        </div>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:16px">
            @foreach($menu->reviews->take(6) as $review)
            <div class="review-card" data-aos="fade-up" data-aos-delay="{{ $loop->index * 60 }}">
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px">
                    <img src="{{ $review->user->avatar_url }}" style="width:38px;height:38px;border-radius:50%;object-fit:cover">
                    <div>
                        <div style="font-weight:600;font-size:13px">{{ $review->user->name }}</div>
                        <div style="display:flex;gap:2px">
                            @for($i=1;$i<=5;$i++)
                            <i class="ti ti-star{{ $i <= $review->rating ? '-filled' : '' }}" style="color:#f59e0b;font-size:12px"></i>
                            @endfor
                        </div>
                    </div>
                    @if($review->is_verified)
                    <span style="margin-left:auto;font-size:10px;background:rgba(74,222,128,.1);color:#166534;padding:3px 8px;border-radius:20px;font-weight:600">Verified</span>
                    @endif
                </div>
                <p style="font-size:13px;line-height:1.7;color:var(--text-muted)">{{ $review->comment }}</p>
                <div style="font-size:11px;color:var(--text-muted);margin-top:8px">{{ $review->created_at->diffForHumans() }}</div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Related --}}
    @if($related->count())
    <div style="padding-bottom:80px">
        <h2 style="font-family:'INeedCoffee',serif;font-size:22px;font-weight:600;margin-bottom:20px">Menu Lainnya</h2>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:16px">
            @foreach($related as $r)
            <a href="{{ route('menu.show',$r->slug) }}" style="background:var(--warm-white);border-radius:16px;border:1px solid var(--border);overflow:hidden;transition:all .3s;display:block"
               onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform='none'">
                <div style="height:140px;background:var(--beige);overflow:hidden;display:flex;align-items:center;justify-content:center">
                    @if($r->image) <img src="{{ $r->image_url }}" style="width:100%;height:100%;object-fit:cover"> @else <span style="font-size:40px">🍱</span> @endif
                </div>
                <div style="padding:12px">
                    <div style="font-weight:600;font-size:13px;margin-bottom:4px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden">{{ $r->name }}</div>
                    <div style="font-weight:700;color:var(--sage-dark)">Rp {{ number_format($r->effective_price,0,',','.') }}</div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
let qty = 1;
const maxStock = {{ $menu->stock }};

function changeQty(delta) {
    qty = Math.max(1, Math.min(maxStock, qty + delta));
    document.getElementById('qty-val').textContent = qty;
    document.getElementById('qty-minus').disabled = qty <= 1;
    document.getElementById('qty-plus').disabled = qty >= maxStock;
}

function addToCart() {
    const btn = document.getElementById('add-btn');
    btn.disabled = true;
    btn.innerHTML = '<i class="ti ti-loader" style="animation:spin 1s linear infinite"></i> Menambahkan...';
    fetch('/cart/add', {
        method: 'POST',
        headers: {'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json'},
        body: JSON.stringify({menu_id: {{ $menu->id }}, quantity: qty, note: document.getElementById('item-note').value})
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            showToast(d.message, 'success');
            const b = document.getElementById('cart-count');
            if (b && d.cart_count) { b.textContent = d.cart_count; b.style.display = 'flex'; }
            btn.innerHTML = '<i class="ti ti-check"></i> Ditambahkan!';
            setTimeout(() => { btn.disabled = false; btn.innerHTML = '<i class="ti ti-shopping-cart-plus"></i> Tambah ke Keranjang'; }, 2000);
        } else {
            showToast(d.message, 'error');
            btn.disabled = false;
            btn.innerHTML = '<i class="ti ti-shopping-cart-plus"></i> Tambah ke Keranjang';
        }
    });
}

function toggleFav(menuId, btn) {
    fetch('/menu/' + menuId + '/favorite', {
        method: 'POST', headers: {'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json'}
    }).then(r => r.json()).then(d => {
        const icon = btn.querySelector('i');
        if (d.favorited) {
            icon.className = 'ti ti-heart-filled'; btn.style.color = '#e07a5f';
            btn.lastChild.textContent = ' Difavoritkan'; showToast('Ditambah ke favorit!','success');
        } else {
            icon.className = 'ti ti-heart'; btn.style.color = 'var(--text-muted)';
            btn.lastChild.textContent = ' Favorit';
        }
    });
}
</script>
@endpush


