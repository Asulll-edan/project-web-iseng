@extends('layouts.app')
@section('title','Notifikasi')

@section('content')
<div style="padding:110px 0 40px;background:linear-gradient(135deg,#1a2e22,#2c3e35)">
    <div class="container">
        <h1 style="font-family:'INeedCoffee',serif;font-size:clamp(22px,4vw,32px);font-weight:600;color:#fff">Notifikasi</h1>
    </div>
</div>

<div class="container" style="padding-top:32px;padding-bottom:80px;max-width:720px">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px">
        <div style="font-weight:700;font-size:15px">Semua Notifikasi</div>
        @if($notifications->where('is_read',false)->count())
        <button onclick="markAll()" style="font-size:13px;color:var(--sage);font-weight:600;background:none;border:none;cursor:pointer;display:flex;align-items:center;gap:5px">
            <i class="ti ti-checks" style="font-size:15px"></i> Tandai semua dibaca
        </button>
        @endif
    </div>

    @if($notifications->isEmpty())
    <div style="text-align:center;padding:60px 20px;background:var(--warm-white);border-radius:20px;border:1px solid var(--border)">
        <div style="font-size:52px;margin-bottom:14px">🔔</div>
        <div style="font-weight:700;font-size:17px;margin-bottom:8px">Tidak ada notifikasi</div>
        <p style="color:var(--text-muted)">Notifikasi pesanan, promo, dan cashback akan muncul di sini</p>
    </div>
    @else
    <div style="display:flex;flex-direction:column;gap:8px">
        @foreach($notifications as $notif)
        <div id="notif-{{ $notif->id }}" onclick="readNotif({{ $notif->id }}, '{{ $notif->action_url }}')"
            style="background:var(--warm-white);border-radius:14px;border:1px solid {{ $notif->is_read ? 'var(--border)' : 'rgba(90,124,101,.25)' }};padding:14px 16px;display:flex;align-items:flex-start;gap:12px;cursor:pointer;transition:all .2s;{{ !$notif->is_read ? 'background:rgba(90,124,101,.04)' : '' }}"
            onmouseover="this.style.transform='translateX(4px)'" onmouseout="this.style.transform='none'">
            <div style="width:38px;height:38px;border-radius:50%;background:{{ !$notif->is_read ? 'rgba(90,124,101,.15)' : 'var(--beige)' }};display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <i class="ti {{ $notif->icon ?? 'ti-bell' }}" style="font-size:17px;color:{{ !$notif->is_read ? 'var(--sage)' : 'var(--text-muted)' }}"></i>
            </div>
            <div style="flex:1">
                <div style="font-weight:{{ $notif->is_read ? '500' : '700' }};font-size:14px;margin-bottom:3px">{{ $notif->title }}</div>
                <div style="font-size:13px;color:var(--text-muted);line-height:1.5">{{ $notif->message }}</div>
                <div style="font-size:11px;color:var(--text-muted);margin-top:6px">{{ $notif->created_at->diffForHumans() }}</div>
            </div>
            @if(!$notif->is_read)
            <div style="width:8px;height:8px;border-radius:50%;background:var(--sage);flex-shrink:0;margin-top:4px"></div>
            @endif
        </div>
        @endforeach
    </div>
    <div style="margin-top:20px">{{ $notifications->links() }}</div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function readNotif(id, url) {
    fetch('/notifications/' + id + '/read', {
        method: 'POST',
        headers: {'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json'}
    }).then(() => {
        const el = document.getElementById('notif-' + id);
        if (el) {
            el.style.background = 'var(--warm-white)';
            el.style.borderColor = 'var(--border)';
            const dot = el.querySelector('[style*="border-radius:50%;background:var(--sage)"]');
            if (dot) dot.remove();
        }
        if (url && url !== 'null') setTimeout(() => window.location.href = url, 200);
    });
}
function markAll() {
    fetch('/notifications/read-all', {
        method: 'POST',
        headers: {'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json'}
    }).then(() => { showToast('Semua notifikasi ditandai dibaca', 'success'); setTimeout(() => location.reload(), 600); });
}
</script>
@endpush


