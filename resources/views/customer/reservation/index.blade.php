@extends('layouts.app')
@section('title','Reservasi Meja')

@push('styles')
<style>
.page-top{padding:110px 0 40px;background:linear-gradient(135deg,#1a2e22,#2c3e35)}
.res-card{background:var(--warm-white);border-radius:16px;border:1px solid var(--border);padding:20px;transition:all .2s}
.res-card:hover{box-shadow:var(--shadow-md)}
.status-pill{display:inline-flex;align-items:center;gap:5px;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:600}
.status-pending{background:rgba(245,158,11,.1);color:#92400e}
.status-approved{background:rgba(16,185,129,.1);color:#065f46}
.status-rejected,.status-cancelled{background:rgba(220,80,60,.08);color:#991b1b}
</style>
@endpush

@section('content')
<div class="page-top">
    <div class="container">
        <h1 style="font-family:'INeedCoffee',serif;font-size:clamp(22px,4vw,32px);font-weight:600;color:#fff">Reservasi Meja</h1>
        <p style="color:rgba(255,255,255,.65);font-size:14px;margin-top:6px">Pesan meja untuk pengalaman makan yang lebih nyaman</p>
    </div>
</div>

<div class="container" style="padding-top:32px;padding-bottom:80px">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px">
        <div style="font-weight:700;font-size:16px">Reservasi Saya</div>
        <a href="{{ route('reservation.create') }}" class="btn-primary">
            <i class="ti ti-calendar-plus"></i> Buat Reservasi Baru
        </a>
    </div>

    @if($reservations->isEmpty())
    <div style="text-align:center;padding:60px 20px;background:var(--warm-white);border-radius:20px;border:1px solid var(--border)">
        <div style="font-size:56px;margin-bottom:16px">📅</div>
        <div style="font-weight:700;font-size:18px;margin-bottom:8px">Belum ada reservasi</div>
        <p style="color:var(--text-muted);margin-bottom:20px">Reservasi meja sekarang untuk pengalaman premium!</p>
        <a href="{{ route('reservation.create') }}" class="btn-primary">Buat Reservasi</a>
    </div>
    @else
    <div style="display:flex;flex-direction:column;gap:14px">
        @foreach($reservations as $res)
        <div class="res-card">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px">
                <div style="display:flex;gap:14px;align-items:flex-start">
                    <div style="width:48px;height:48px;background:var(--beige);border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <i class="ti ti-armchair" style="font-size:22px;color:var(--sage)"></i>
                    </div>
                    <div>
                        <div style="font-weight:700;font-size:15px;margin-bottom:4px">
                            Meja {{ $res->table->table_number ?? '-' }}
                            <span style="font-size:12px;font-weight:400;color:var(--text-muted)">· {{ $res->table->location ?? '' }}</span>
                        </div>
                        <div style="display:flex;gap:16px;flex-wrap:wrap;font-size:13px;color:var(--text-muted);margin-bottom:6px">
                            <span><i class="ti ti-calendar" style="font-size:14px"></i> {{ \Carbon\Carbon::parse($res->reservation_date)->format('d M Y') }}</span>
                            <span><i class="ti ti-clock" style="font-size:14px"></i> {{ \Carbon\Carbon::parse($res->reservation_time)->format('H:i') }} WIB</span>
                            <span><i class="ti ti-users" style="font-size:14px"></i> {{ $res->guest_count }} orang</span>
                        </div>
                        @if($res->special_request)
                        <div style="font-size:12px;background:var(--beige);padding:4px 10px;border-radius:6px;display:inline-block;color:var(--text-muted)">
                            📝 {{ $res->special_request }}
                        </div>
                        @endif
                        @if($res->admin_note)
                        <div style="font-size:12px;background:rgba(90,124,101,.08);padding:6px 10px;border-radius:6px;margin-top:6px;color:var(--sage-dark)">
                            <i class="ti ti-message-circle" style="font-size:13px"></i> Admin: {{ $res->admin_note }}
                        </div>
                        @endif
                    </div>
                </div>
                <div style="display:flex;align-items:center;gap:10px">
                    <span class="status-pill status-{{ $res->status }}">
                        @switch($res->status)
                            @case('pending') <i class="ti ti-clock" style="font-size:13px"></i> Menunggu @break
                            @case('approved') <i class="ti ti-circle-check" style="font-size:13px"></i> Dikonfirmasi @break
                            @case('rejected') <i class="ti ti-x" style="font-size:13px"></i> Ditolak @break
                            @case('cancelled') <i class="ti ti-x" style="font-size:13px"></i> Dibatalkan @break
                        @endswitch
                    </span>
                    @if(in_array($res->status, ['pending','approved']))
                    <button onclick="cancelRes({{ $res->id }}, this)" style="background:none;border:1px solid var(--border);border-radius:8px;padding:5px 12px;font-size:12px;cursor:pointer;color:var(--text-muted);transition:all .2s" onmouseover="this.style.borderColor='#c0392b';this.style.color='#c0392b'" onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--text-muted)'">
                        Batalkan
                    </button>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div style="margin-top:24px">{{ $reservations->links() }}</div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function cancelRes(id, btn) {
    if (!confirm('Yakin ingin membatalkan reservasi ini?')) return;
    fetch('/reservasi/' + id, {
        method: 'DELETE',
        headers: {'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json'}
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) { showToast('Reservasi dibatalkan.', 'info'); setTimeout(() => location.reload(), 800); }
    });
}
</script>
@endpush


