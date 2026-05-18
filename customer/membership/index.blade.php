@extends('layouts.app')
@section('title','Membership')

@push('styles')
<style>
.page-top{padding:110px 0 40px;background:linear-gradient(135deg,#1a2e22,#2c3e35)}
.tier-card{border-radius:20px;border:2px solid var(--border);padding:24px;transition:all .3s;position:relative;overflow:hidden}
.tier-card.active-tier{border-color:var(--sage);box-shadow:0 0 0 4px rgba(90,124,101,.1)}
.tier-card.current{background:linear-gradient(135deg,var(--sage-dark),var(--sage));color:#fff}
.tier-icon{width:56px;height:56px;border-radius:16px;display:flex;align-items:center;justify-content:center;font-size:26px;margin-bottom:14px}
.cashback-log-item{display:flex;align-items:center;gap:12px;padding:12px;background:var(--beige);border-radius:12px}
</style>
@endpush

@section('content')
<div class="page-top">
    <div class="container">
        <h1 style="font-family:'Playfair Display',serif;font-size:clamp(22px,4vw,32px);font-weight:600;color:#fff">Membership & Rewards</h1>
        <p style="color:rgba(255,255,255,.65);font-size:14px;margin-top:6px">Makin banyak order, makin besar keuntunganmu!</p>
    </div>
</div>

<div class="container" style="padding-top:32px;padding-bottom:80px">

    {{-- Current Status Card --}}
    <div style="background:linear-gradient(135deg,var(--sage-dark),var(--sage));border-radius:20px;padding:28px;margin-bottom:32px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:20px" data-aos="fade-up">
        <div>
            <div style="font-size:12px;font-weight:700;color:rgba(255,255,255,.65);text-transform:uppercase;letter-spacing:.08em;margin-bottom:8px">Status Kamu</div>
            <div style="font-family:'Playfair Display',serif;font-size:28px;font-weight:600;color:#fff;margin-bottom:6px">
                {{ $membership ? ucfirst($membership->tier) : 'Non-Member' }}
                @if($membership && $membership->tier !== 'none')
                <span style="font-size:14px;background:rgba(255,255,255,.2);padding:4px 12px;border-radius:20px;margin-left:8px;font-family:'Plus Jakarta Sans',sans-serif;font-weight:600">Member</span>
                @endif
            </div>
            @if($membership && $membership->tier !== 'none')
            <div style="font-size:14px;color:rgba(255,255,255,.8)">
                Cashback {{ $membership->cashback_rate }}% per order · {{ $membership->completed_orders }} order selesai
            </div>
            @else
            <div style="font-size:14px;color:rgba(255,255,255,.8)">Selesaikan 10 order untuk jadi Silver Member!</div>
            @endif
        </div>
        <div style="text-align:right">
            @if($user->loyaltyPoint)
            <div style="font-size:12px;color:rgba(255,255,255,.65);margin-bottom:4px">Loyalty Points</div>
            <div style="font-family:'Playfair Display',serif;font-size:36px;font-weight:600;color:#fff">{{ number_format($user->loyaltyPoint->available_points) }}</div>
            <div style="font-size:12px;color:rgba(255,255,255,.65)">poin tersedia</div>
            @endif
        </div>
    </div>

    {{-- Progress to next tier --}}
    @if($ordersToNextTier !== null && $membership && $membership->tier !== 'platinum')
    <div style="background:var(--warm-white);border-radius:16px;border:1px solid var(--border);padding:20px;margin-bottom:32px" data-aos="fade-up">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px">
            <div style="font-weight:700;font-size:14px">Progress ke Tier Berikutnya</div>
            <div style="font-size:13px;color:var(--text-muted)">{{ $ordersToNextTier }} order lagi</div>
        </div>
        @php
            $nextTier = $membership->next_tier;
            $currentMin = $tiers[$membership->tier]['min_orders'];
            $nextMin = $nextTier['min_orders'];
            $progress = $nextMin > 0 ? min(100, round((($membership->completed_orders - $currentMin) / ($nextMin - $currentMin)) * 100)) : 100;
        @endphp
        <div style="height:8px;background:var(--beige);border-radius:4px;overflow:hidden">
            <div style="width:{{ $progress }}%;height:100%;background:var(--sage);border-radius:4px;transition:width 1s"></div>
        </div>
        <div style="display:flex;justify-content:space-between;font-size:12px;color:var(--text-muted);margin-top:6px">
            <span>{{ ucfirst($membership->tier) }} ({{ $membership->completed_orders }} order)</span>
            <span>{{ ucfirst(array_keys($tiers)[array_search($membership->tier, array_keys($tiers)) + 1] ?? '') }} ({{ $nextMin }} order)</span>
        </div>
    </div>
    @endif

    {{-- Tier Cards --}}
    <div style="font-family:'Playfair Display',serif;font-size:22px;font-weight:600;margin-bottom:20px" data-aos="fade-up">Tingkatan Membership</div>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px;margin-bottom:40px">
        @foreach($tiers as $tierKey => $tierData)
        @if($tierKey === 'none') @continue @endif
        @php $isCurrent = $membership && $membership->tier === $tierKey; @endphp
        <div class="tier-card {{ $isCurrent ? 'current' : '' }}" data-aos="fade-up" data-aos-delay="{{ $loop->index * 80 }}">
            @if($isCurrent)
            <div style="position:absolute;top:14px;right:14px;background:rgba(255,255,255,.25);border-radius:20px;padding:3px 10px;font-size:11px;font-weight:700;color:#fff">
                ✓ KAMU DISINI
            </div>
            @endif
            <div class="tier-icon" style="background:{{ $isCurrent ? 'rgba(255,255,255,.2)' : 'var(--beige)' }}">
                @switch($tierKey)
                    @case('silver') 🥈 @break
                    @case('gold') 🥇 @break
                    @case('platinum') 💎 @break
                @endswitch
            </div>
            <div style="font-weight:700;font-size:18px;margin-bottom:6px;color:{{ $isCurrent ? '#fff' : 'var(--text-main)' }}">
                {{ $tierData['label'] }}
            </div>
            <div style="font-size:13px;color:{{ $isCurrent ? 'rgba(255,255,255,.8)' : 'var(--text-muted)' }};margin-bottom:14px">
                Min. {{ $tierData['min_orders'] }} order selesai
                @if($tierKey === 'platinum')
                + Pembelian member premium
                @endif
            </div>
            <div style="display:flex;flex-direction:column;gap:7px">
                <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:{{ $isCurrent ? 'rgba(255,255,255,.9)' : 'var(--text-muted)' }}">
                    <i class="ti ti-circle-check" style="font-size:15px;color:{{ $isCurrent ? '#fff' : 'var(--sage)' }}"></i>
                    Cashback {{ $tierData['cashback'] }}% per order
                </div>
                @if($tierKey === 'gold' || $tierKey === 'platinum')
                <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:{{ $isCurrent ? 'rgba(255,255,255,.9)' : 'var(--text-muted)' }}">
                    <i class="ti ti-circle-check" style="font-size:15px;color:{{ $isCurrent ? '#fff' : 'var(--sage)' }}"></i>
                    Promo eksklusif member
                </div>
                @endif
                @if($tierKey === 'platinum')
                <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:{{ $isCurrent ? 'rgba(255,255,255,.9)' : 'var(--text-muted)' }}">
                    <i class="ti ti-circle-check" style="font-size:15px;color:{{ $isCurrent ? '#fff' : 'var(--sage)' }}"></i>
                    Priority customer service
                </div>
                <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:{{ $isCurrent ? 'rgba(255,255,255,.9)' : 'var(--text-muted)' }}">
                    <i class="ti ti-circle-check" style="font-size:15px;color:{{ $isCurrent ? '#fff' : 'var(--sage)' }}"></i>
                    Bonus wallet bulanan
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    {{-- Cashback History --}}
    @if($user->cashbackLogs->count())
    <div data-aos="fade-up">
        <div style="font-family:'Playfair Display',serif;font-size:22px;font-weight:600;margin-bottom:16px">Riwayat Cashback</div>
        <div style="display:flex;flex-direction:column;gap:10px">
            @foreach($user->cashbackLogs as $log)
            <div class="cashback-log-item">
                <div style="width:40px;height:40px;background:rgba(90,124,101,.1);border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <i class="ti ti-coin" style="font-size:18px;color:var(--sage)"></i>
                </div>
                <div style="flex:1">
                    <div style="font-weight:600;font-size:13px">{{ $log->description }}</div>
                    <div style="font-size:12px;color:var(--text-muted)">{{ $log->created_at->format('d M Y H:i') }} · {{ ucfirst($log->membership_tier) }}</div>
                </div>
                <div style="font-weight:700;font-size:15px;color:var(--sage-dark)">+Rp {{ number_format($log->amount,0,',','.') }}</div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection