@extends('layouts.admin')
@section('title','Laporan')
@section('page-title','Laporan')

@push('styles')
<style>
.lap-card{background:var(--warm);border-radius:var(--r);border:1px solid var(--border);padding:18px 20px;display:flex;align-items:center;gap:14px;transition:all .2s}
.lap-card:hover{box-shadow:var(--shadow-md)}
.lap-icon{width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.tipe-badge{padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700}
.tipe-sales{background:rgba(90,124,101,.1);color:var(--sage-dark)}
.tipe-orders{background:rgba(59,130,246,.08);color:#1d4ed8}
.tipe-wallet{background:rgba(245,158,11,.1);color:#92400e}
.tipe-membership{background:rgba(168,85,247,.08);color:#7c3aed}
.tipe-customer{background:rgba(16,185,129,.08);color:#065f46}
.tipe-custom{background:rgba(107,114,128,.08);color:#374151}
</style>
@endpush

@section('content')
<div class="page-header">
    <div>
        <div class="page-title">Daftar Laporan</div>
        <div class="page-sub">{{ $laporans->total() }} laporan dibuat</div>
    </div>
    <a href="{{ route('admin.reports.export') }}" class="btn-primary">
        <i class="ti ti-file-plus"></i> Buat Laporan Baru
    </a>
</div>

{{-- Filter --}}
<div style="background:var(--warm);border-radius:var(--r);border:1px solid var(--border);padding:14px 18px;display:flex;gap:10px;margin-bottom:20px;flex-wrap:wrap">
    <select class="form-select" style="width:auto" onchange="applyFilter()">
        <option value="">Semua Status</option>
        @foreach(['pending'=>'Pending','approved'=>'Approved','rejected'=>'Ditolak'] as $v=>$l)
        <option value="{{ $v }}" {{ request('status')===$v?'selected':'' }}>{{ $l }}</option>
        @endforeach
    </select>
    <select class="form-select" style="width:auto" onchange="applyFilter()">
        <option value="">Semua Tipe</option>
        @foreach(['sales','orders','wallet','membership','customer','custom'] as $t)
        <option value="{{ $t }}" {{ request('tipe')===$t?'selected':'' }}>{{ ucfirst($t) }}</option>
        @endforeach
    </select>
</div>

<div class="content-card">
    <div style="overflow-x:auto">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>No. Laporan</th>
                    <th>Judul</th>
                    <th>Tipe</th>
                    <th>Periode</th>
                    <th>Dibuat Oleh</th>
                    <th>File</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($laporans as $lap)
                @php $sc=['pending'=>'badge-amber','approved'=>'badge-green','rejected'=>'badge-red']; @endphp
                <tr>
                    <td><span style="font-family:monospace;font-size:12px;font-weight:700;color:var(--sage-dark)">{{ $lap->nomor_laporan }}</span></td>
                    <td>
                        <div style="font-weight:600;font-size:13px;max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="{{ $lap->judul }}">{{ $lap->judul }}</div>
                        @if($lap->catatan)
                        <div style="font-size:11px;color:var(--muted)">{{ Str::limit($lap->catatan,40) }}</div>
                        @endif
                    </td>
                    <td><span class="tipe-badge tipe-{{ $lap->tipe }}">{{ ucfirst($lap->tipe) }}</span></td>
                    <td style="font-size:12px;color:var(--muted)">
                        @if($lap->periode_start)
                        {{ \Carbon\Carbon::parse($lap->periode_start)->format('d M Y') }} —<br>
                        {{ \Carbon\Carbon::parse($lap->periode_end)->format('d M Y') }}
                        @else — @endif
                    </td>
                    <td>
                        <div style="font-weight:600;font-size:13px">{{ $lap->user->name }}</div>
                        <div style="font-size:11px;color:var(--muted)">{{ \Carbon\Carbon::parse($lap->created_at)->format('d M Y H:i') }}</div>
                    </td>
                    <td>
                        @if($lap->file_path)
                        <span class="badge badge-sage" style="font-size:10px">
                            <i class="ti ti-file" style="font-size:11px"></i>
                            {{ strtoupper($lap->file_type ?? 'file') }}
                        </span>
                        @else
                        <span style="color:var(--muted);font-size:12px">—</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge {{ $sc[$lap->status] ?? 'badge-gray' }}">{{ ucfirst($lap->status) }}</span>
                        @if($lap->approver && $lap->status !== 'pending')
                        <div style="font-size:10px;color:var(--muted);margin-top:2px">oleh {{ $lap->approver->name }}</div>
                        @endif
                        @if($lap->admin_note && $lap->status === 'rejected')
                        <div style="font-size:10px;color:#c0392b;margin-top:2px">{{ Str::limit($lap->admin_note,30) }}</div>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex;gap:5px;flex-wrap:wrap">
                            @if($lap->file_path)
                            <a href="{{ route('admin.reports.download',$lap->id) }}" class="btn-icon btn-icon-sage" title="Download">
                                <i class="ti ti-download" style="font-size:14px"></i>
                            </a>
                            @endif
                            @if($lap->status === 'pending' && (auth()->user()->isSuperadmin() || auth()->user()->isAdmin()))
                            <button onclick="approveLap({{ $lap->id }},this)" class="btn-primary" style="padding:5px 10px;font-size:11px;border-radius:7px">
                                <i class="ti ti-check"></i>
                            </button>
                            <button onclick="rejectLap({{ $lap->id }},this)" class="btn-danger" style="padding:5px 10px;font-size:11px;border-radius:7px">
                                <i class="ti ti-x"></i>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" style="text-align:center;padding:40px;color:var(--muted)">
                    <i class="ti ti-file-off" style="font-size:32px;display:block;margin-bottom:10px"></i>
                    Belum ada laporan
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:16px 20px;border-top:1px solid var(--border)">{{ $laporans->links() }}</div>
</div>
@endsection

@push('scripts')
<script>
function applyFilter() {
    const params = new URLSearchParams();
    document.querySelectorAll('select').forEach((s,i) => {
        if(s.value) params.set(i===0?'status':'tipe', s.value);
    });
    window.location = '?' + params.toString();
}
function approveLap(id, btn) {
    Swal.fire({
        title: 'Approve Laporan?',
        text: 'Laporan akan disetujui dan bisa didownload.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3d5c47',
        confirmButtonText: 'Ya, Approve',
        cancelButtonText: 'Batal'
    }).then(r => {
        if(!r.isConfirmed) return;
        btn.disabled = true;
        ajax('/admin/reports/'+id+'/approve','POST')
        .then(d => { showToast(d.message,'success'); setTimeout(()=>location.reload(),700); })
        .catch(()=>btn.disabled=false);
    });
}
function rejectLap(id, btn) {
    Swal.fire({
        title: 'Tolak Laporan?',
        input: 'text',
        inputPlaceholder: 'Alasan penolakan (opsional)',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#c0392b',
        confirmButtonText: 'Tolak',
        cancelButtonText: 'Batal'
    }).then(r => {
        if(!r.isConfirmed) return;
        btn.disabled = true;
        ajax('/admin/reports/'+id+'/reject','POST',{note: r.value||''})
        .then(d => { showToast(d.message,'info'); setTimeout(()=>location.reload(),700); })
        .catch(()=>btn.disabled=false);
    });
}
</script>
@endpush
