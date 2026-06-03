@extends('layouts.admin')
@section('title','Kelola Reservasi')
@section('page-title','Reservasi')

@section('content')
<div class="page-header">
    <div>
        <div class="page-title">Reservasi</div>
        <div class="page-sub">{{ $reservations->total() }} reservasi terdaftar</div>
    </div>
    <div style="display:flex;gap:8px">
@foreach([''=>'Semua','pending'=>'Pending','confirmed'=>'Approved','cancelled'=>'Dibatalkan'] as $v=>$l)
        <a href="?status={{ $v }}" style="padding:7px 14px;border-radius:20px;font-size:12px;font-weight:600;border:1.5px solid {{ request('status')===$v ? 'var(--sage)' : 'var(--border)' }};background:{{ request('status')===$v ? 'var(--sage)' : 'transparent' }};color:{{ request('status')===$v ? '#fff' : 'var(--muted)' }};text-decoration:none">{{ $l }}</a>
        @endforeach
    </div>
</div>

<div style="background:var(--warm);border-radius:var(--r);border:1px solid var(--border);padding:14px 18px;display:flex;gap:10px;margin-bottom:20px;flex-wrap:wrap;align-items:center">
    <input type="date" class="form-input" style="width:auto" value="{{ request('date') }}"
        onchange="window.location='?date='+this.value+'&status={{ request('status') }}'">
    @if(request()->hasAny(['date','status']))
    <a href="{{ route('admin.reservations.index') }}" class="btn-secondary" style="padding:9px 14px;font-size:12px"><i class="ti ti-x"></i> Reset</a>
    @endif
</div>

<div class="content-card">
    <div style="overflow-x:auto">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Kode</th><th>Customer</th><th>Meja</th><th>Tanggal & Jam</th><th>Tamu</th><th>Event</th><th>Status</th><th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reservations as $res)
                @php $sc=['pending'=>'badge-amber','confirmed'=>'badge-green','cancelled'=>'badge-gray']; @endphp
                <tr>
                    <td><span style="font-family:monospace;font-weight:700;font-size:12px;color:var(--sage-dark)">{{ $res->reservation_code }}</span></td>
                    <td>
                        <div style="font-weight:600;font-size:13px">{{ $res->user->name }}</div>
                        <div style="font-size:11px;color:var(--muted)">{{ $res->user->phone ?? $res->user->email }}</div>
                    </td>
                    <td>
                        <div style="font-weight:600">Meja {{ $res->table->table_number ?? '—' }}</div>
                        <div style="font-size:11px;color:var(--muted)">{{ $res->table->location ?? '' }} · {{ $res->table->capacity ?? '' }} org</div>
                    </td>
                    <td>
                        <div style="font-weight:600;font-size:13px">{{ \Carbon\Carbon::parse($res->reservation_date)->format('d M Y') }}</div>
                        <div style="font-size:12px;color:var(--muted)">{{ \Carbon\Carbon::parse($res->reservation_time)->format('H:i') }} WIB</div>
                    </td>
                    <td style="font-weight:600;font-size:14px;text-align:center">{{ $res->guest_count }}</td>
                    <td style="font-size:12px;color:var(--muted)">{{ $res->event_type ?: '—' }}</td>
                    <td>
                        <span class="badge {{ $sc[$res->status] ?? 'badge-gray' }}">{{ ucfirst($res->status) }}</span>
                        @if($res->admin_note)
                        <div style="font-size:10px;color:var(--muted);margin-top:3px">{{ Str::limit($res->admin_note,25) }}</div>
                        @endif
                    </td>
                    <td>
                        @if($res->status === 'pending')
                        <div style="display:flex;gap:6px">
                            <button onclick="approveRes({{ $res->id }},this)" class="btn-primary" style="padding:5px 12px;font-size:11px;border-radius:8px">
                                <i class="ti ti-check"></i> Approve
                            </button>
                            <button onclick="rejectRes({{ $res->id }},this)" class="btn-danger" style="padding:5px 12px;font-size:11px;border-radius:8px">
                                Tolak
                            </button>
                        </div>
                        @else
                        <span style="font-size:12px;color:var(--muted)">{{ $res->updated_at->diffForHumans() }}</span>
                        @endif
                    </td>
                </tr>
                @if($res->special_request)
                <tr>
                    <td colspan="8" style="padding:6px 20px 10px;border-bottom:1px solid var(--border)">
                        <div style="background:rgba(245,158,11,.06);border-radius:6px;padding:7px 12px;font-size:12px;color:var(--text)">
                            <i class="ti ti-notes" style="color:#b45309;font-size:13px"></i> <strong>Catatan:</strong> {{ $res->special_request }}
                        </div>
                    </td>
                </tr>
                @endif
                @empty
                <tr><td colspan="8" style="text-align:center;padding:40px;color:var(--muted)">Tidak ada reservasi ditemukan</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:16px 20px;border-top:1px solid var(--border)">{{ $reservations->links() }}</div>
</div>
@endsection

@push('scripts')
<script>
function approveRes(id, btn) {
    confirmAction('Konfirmasi Reservasi?','Reservasi akan dikonfirmasi dan customer dinotifikasi.',()=>{
    btn.disabled=true;btn.innerHTML='<i class="ti ti-loader"></i>';
    ajax('/admin/reservations/'+id+'/approve','POST')
    .then(d => { showToast(d.message,'success'); setTimeout(()=>location.reload(),700); })
    .catch(()=>btn.disabled=false);
});
}
function rejectRes(id, btn) {
    const note = prompt('Alasan penolakan (opsional):');
    if(note === null) return;
    btn.disabled = true;
    ajax('/admin/reservations/'+id+'/reject','POST',{note})
    .then(d => { showToast(d.message,'info'); setTimeout(()=>location.reload(),700); })
    .catch(()=>btn.disabled=false);
}
</script>
@endpush
