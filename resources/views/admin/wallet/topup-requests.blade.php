@extends('layouts.admin')
@section('title','Topup Requests')
@section('page-title','Topup Requests')
@section('breadcrumb')
<i class="ti ti-chevron-right" style="font-size:12px"></i>
<a href="{{ route('admin.wallet.index') }}" style="color:var(--muted);text-decoration:none">Wallet</a>
<i class="ti ti-chevron-right" style="font-size:12px"></i>
<span>Topup Requests</span>
@endsection

@push('styles')
<style>
.proof-thumb{width:52px;height:52px;border-radius:8px;object-fit:cover;cursor:pointer;border:1px solid var(--border);transition:transform .2s}
.proof-thumb:hover{transform:scale(1.05)}
.modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.7);z-index:500;display:none;align-items:center;justify-content:center}
.modal-overlay.show{display:flex}
.modal-img{max-width:90vw;max-height:85vh;border-radius:12px;box-shadow:0 20px 60px rgba(0,0,0,.5)}
</style>
@endpush

@section('content')
<div class="page-header">
    <div>
        <div class="page-title">Topup Requests</div>
        <div class="page-sub">{{ $requests->total() }} permintaan</div>
    </div>
    <div style="display:flex;gap:8px">
        @foreach([''=>'Semua','pending'=>'Pending','approved'=>'Approved','rejected'=>'Ditolak'] as $v=>$l)
        <a href="?status={{ $v }}" style="padding:7px 14px;border-radius:20px;font-size:12px;font-weight:600;border:1.5px solid {{ request('status')===$v ? 'var(--sage)' : 'var(--border)' }};background:{{ request('status')===$v ? 'var(--sage)' : 'transparent' }};color:{{ request('status')===$v ? '#fff' : 'var(--muted)' }};text-decoration:none">{{ $l }}</a>
        @endforeach
    </div>
</div>

<div class="content-card">
    <div style="overflow-x:auto">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Kode</th><th>User</th><th>Nominal</th><th>Metode</th><th>Bukti</th><th>Status</th><th>Waktu</th><th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $req)
                <tr>
                    <td><span style="font-family:monospace;font-size:12px;font-weight:600;color:var(--sage-dark)">{{ $req->transaction_code }}</span></td>
                    <td>
                        <div style="display:flex;align-items:center;gap:8px">
                            <img src="{{ $req->user->avatar_url }}" style="width:30px;height:30px;border-radius:50%;object-fit:cover" alt="">
                            <div>
                                <div style="font-weight:600;font-size:13px">{{ $req->user->name }}</div>
                                <div style="font-size:11px;color:var(--muted)">{{ $req->user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td><strong style="font-size:14px;color:var(--sage-dark)">Rp {{ number_format($req->amount,0,',','.') }}</strong></td>
                    <td style="font-size:12px;text-transform:capitalize">{{ str_replace('_',' ',$req->payment_method) }}</td>
                    <td>
                        @if($req->proof_image)
                        <img src="{{ asset('storage/'.$req->proof_image) }}" class="proof-thumb" onclick="showProof('{{ asset('storage/'.$req->proof_image) }}')" alt="Bukti">
                        @else
                        <span style="font-size:12px;color:var(--muted)">—</span>
                        @endif
                    </td>
                    <td>
                        @php $sc=['pending'=>'badge-amber','approved'=>'badge-green','rejected'=>'badge-red']; @endphp
                        <span class="badge {{ $sc[$req->status] ?? 'badge-gray' }}">{{ ucfirst($req->status) }}</span>
                        @if($req->admin_note)
                        <div style="font-size:10px;color:var(--muted);margin-top:3px">{{ Str::limit($req->admin_note,30) }}</div>
                        @endif
                    </td>
                    <td>
                        <div style="font-size:12px;color:var(--muted)">
                            <div>{{ $req->created_at->format('d M Y') }}</div>
                            <div>{{ $req->created_at->format('H:i') }}</div>
                        </div>
                        @if($req->expires_at && $req->status === 'pending')
                        <div style="font-size:10px;color:{{ $req->isExpired() ? '#c0392b' : '#f59e0b' }};margin-top:2px">
                            {{ $req->isExpired() ? 'Kadaluarsa' : 'Exp: '.$req->expires_at->format('d M H:i') }}
                        </div>
                        @endif
                    </td>
                    <td>
                        @if($req->status === 'pending')
                        <div style="display:flex;gap:6px">
                            <button onclick="approveTopup({{ $req->id }},this)" class="btn-primary" style="padding:6px 14px;font-size:11px;border-radius:8px">
                                <i class="ti ti-check"></i> Approve
                            </button>
                            <button onclick="rejectTopup({{ $req->id }},this)" class="btn-danger" style="padding:6px 12px;font-size:11px;border-radius:8px">
                                Tolak
                            </button>
                        </div>
                        @elseif($req->approved_at)
                        <div style="font-size:11px;color:var(--muted)">
                            Oleh admin<br>{{ $req->approved_at->format('d M H:i') }}
                        </div>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" style="text-align:center;padding:40px;color:var(--muted)">Tidak ada topup request</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:16px 20px;border-top:1px solid var(--border)">{{ $requests->links() }}</div>
</div>

{{-- Proof image modal --}}
<div class="modal-overlay" id="proof-modal" onclick="hideProof()">
    <div onclick="event.stopPropagation()">
        <img id="proof-modal-img" src="" class="modal-img" alt="Bukti Transfer">
        <div style="text-align:center;margin-top:12px">
            <button onclick="hideProof()" style="background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.2);color:#fff;border-radius:8px;padding:8px 20px;cursor:pointer;font-size:13px">
                <i class="ti ti-x"></i> Tutup
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function showProof(url) {
    document.getElementById('proof-modal-img').src = url;
    document.getElementById('proof-modal').classList.add('show');
}
function hideProof() {
    document.getElementById('proof-modal').classList.remove('show');
}
function approveTopup(id, btn) {
    confirmAction('Approve Topup?','Saldo akan langsung masuk ke wallet user.',()=>{
        btn.disabled=true; btn.innerHTML='<i class="ti ti-loader"></i>';
        ajax('/admin/wallet/topup/'+id+'/approve','POST')
        .then(d=>{showToast(d.message,'success');setTimeout(()=>location.reload(),800);})
        .catch(()=>{btn.disabled=false;btn.innerHTML='<i class="ti ti-check"></i> Approve';});
    },'Ya, Approve');
}
function rejectTopup(id, btn) {
    Swal.fire({title:'Tolak Topup?',input:'text',inputPlaceholder:'Alasan penolakan (opsional)',icon:'warning',showCancelButton:true,confirmButtonColor:'#c0392b',cancelButtonColor:'#6b7c72',confirmButtonText:'Tolak',cancelButtonText:'Batal'})
    .then(r=>{
        if(!r.isConfirmed) return;
        btn.disabled=true;
        ajax('/admin/wallet/topup/'+id+'/reject','POST',{note:r.value||''})
        .then(d=>{showToast(d.message,'info');setTimeout(()=>location.reload(),800);})
        .catch(()=>btn.disabled=false);
    });
}
</script>
@endpush