<footer style="background:var(--charcoal);color:#a8b8b0;padding:48px 0 24px;margin-top:80px">
<div class="container">
    <div style="display:grid;grid-template-columns:2fr 1fr 1fr 1fr;gap:32px;margin-bottom:40px">
        <div>
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px">
                <div style="width:36px;height:36px;background:var(--sage);border-radius:10px;display:flex;align-items:center;justify-content:center">
                    <img src="{{ asset('images/logo.png') }}" alt="Rumahnya Anak Sekolah" style="width:100%;height:auto">
                </div>
                <div style="font-weight:700;font-size:16px;color:#e8f0eb">Rumahnya Anak Sekolah</div>
            </div>
            <p style="font-size:13px;line-height:1.8;max-width:280px">Premium student culinary experience. Tempat makan favorit pelajar dengan menu lezat dan suasana nyaman.</p>
            <div style="display:flex;gap:10px;margin-top:16px">
                @php $wa = \App\Models\Setting::get('whatsapp_number','6281234567890'); @endphp
                <a href="https://wa.me/{{ $wa }}" target="_blank" class="footer-icon-btn"><i class="ti ti-brand-whatsapp" style="font-size:18px"></i></a>
                <a href="https://www.instagram.com/msrfi_" class="footer-icon-btn"><i class="ti ti-brand-instagram" style="font-size:18px"></i></a>
                <a href="https://www.tiktok.com/@.rfii__" class="footer-icon-btn"><i class="ti ti-brand-tiktok" style="font-size:18px"></i></a>
            </div>
        </div>
        <div>
            <div style="font-weight:600;font-size:13px;color:#e8f0eb;margin-bottom:14px;text-transform:uppercase;letter-spacing:.05em">Menu</div>
            <div style="display:flex;flex-direction:column;gap:8px">
                <a href="{{ route('menu.index') }}" class="footer-link">Semua Menu</a>
                <a href="{{ route('menu.index') }}?category=menu-utama" class="footer-link">Menu Utama</a>
                <a href="{{ route('menu.index') }}?category=minuman" class="footer-link">Minuman</a>
                <a href="{{ route('menu.index') }}?category=dessert-snack" class="footer-link">Dessert</a>
            </div>
        </div>
        <div>
            <div style="font-weight:600;font-size:13px;color:#e8f0eb;margin-bottom:14px;text-transform:uppercase;letter-spacing:.05em">Layanan</div>
            <div style="display:flex;flex-direction:column;gap:8px">
                <a href="{{ route('reservation.index') }}" class="footer-link">Reservasi Meja</a>
                <a href="{{ route('wallet.index') }}" class="footer-link">SOHIBA Wallet</a>
                <a href="{{ route('membership') }}" class="footer-link">Membership</a>
                <a href="{{ route('orders.index') }}" class="footer-link">Pesanan Saya</a>
            </div>
        </div>
        <div>
            <div style="font-weight:600;font-size:13px;color:#e8f0eb;margin-bottom:14px;text-transform:uppercase;letter-spacing:.05em">Info</div>
            <div style="display:flex;flex-direction:column;gap:8px;font-size:13px">
                @php
                    $address = \App\Models\Setting::get('restaurant_address','Jl. in aja dulu');
                    $open    = \App\Models\Setting::get('open_time','07:00');
                    $close   = \App\Models\Setting::get('close_time','21:00');
                @endphp
                <span style="display:flex;gap:8px;align-items:flex-start"><i class="ti ti-map-pin" style="font-size:15px;flex-shrink:0;margin-top:2px"></i>{{ $address }}</span>
                <span style="display:flex;gap:8px;align-items:center"><i class="ti ti-clock" style="font-size:15px"></i>{{ $open }} – {{ $close }} WIB</span>
            </div>
        </div>
    </div>
    <div style="border-top:1px solid rgba(255,255,255,.08);padding-top:20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
        <span style="font-size:12px">© {{ date('Y') }} Rumahnya Anak Sekolah. All rights reserved.</span>
        <span style="font-size:12px">Made with <i class="ti ti-heart-filled" style="color:#e07a5f;font-size:12px"></i> for students</span>
    </div>
</div>
<style>
.footer-link{font-size:13px;color:#a8b8b0;transition:color .2s}
.footer-link:hover{color:#8aaa92}
.footer-icon-btn{width:34px;height:34px;background:rgba(255,255,255,.08);border-radius:8px;display:flex;align-items:center;justify-content:center;color:#a8b8b0;transition:all .2s}
.footer-icon-btn:hover{background:var(--sage);color:#fff}
@media(max-width:768px){footer>div>div:first-child{grid-template-columns:1fr 1fr!important}}
</style>
</footer>

