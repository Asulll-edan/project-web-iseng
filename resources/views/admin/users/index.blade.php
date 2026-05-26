@extends('layouts.admin')
@section('title','Kelola User')
@section('page-title','Kelola User')

@push('styles')
<style>
.filter-bar{background:var(--warm);border-radius:var(--r);border:1px solid var(--border);padding:14px 18px;display:flex;gap:10px;align-items:center;flex-wrap:wrap;margin-bottom:20px}
.search-wrap{position:relative;flex:1;min-width:200px}
.search-wrap i{position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--muted);font-size:16px;pointer-events:none}
.search-inp{width:100%;padding:9px 14px 9px 38px;border-radius:10px;border:1.5px solid var(--border);background:var(--beige);color:var(--text);font-size:13px;font-family:inherit;outline:none;transition:border-color .2s}
.search-inp:focus{border-color:var(--sage);background:#fff}
.user-avatar{width:36px;height:36px;border-radius:50%;object-fit:cover;flex-shrink:0}
.role-badge{padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700}
.role-superadmin{background:rgba(168,85,247,.1);color:#7c3aed}
.role-kasir{background:rgba(59,130,246,.08);color:#1d4ed8}
.role-kitchen{background:rgba(245,158,11,.1);color:#92400e}
.role-customer{background:rgba(90,124,101,.1);color:var(--sage-dark)}
.status-active{background:rgba(16,185,129,.08);color:#065f46}
.status-suspended{background:rgba(220,80,60,.08);color:#991b1b}
.tier-badge{padding:2px 8px;border-radius:20px;font-size:10px;font-weight:700}
.tier-none{background:var(--beige);color:var(--muted)}
.tier-silver{background:rgba(148,163,184,.15);color:#475569}
.tier-gold{background:rgba(245,158,11,.12);color:#92400e}
.tier-platinum{background:rgba(168,85,247,.1);color:#7c3aed}
</style>
@endpush

@section('content')
<div class="page-header">
    <div>
        <div class="page-title">Kelola User</div>
        <div class="page-sub">{{ $users->total() }} user terdaftar</div>
    </div>
</div>

<div class="filter-bar">
    <div class="search-wrap">
        <i class="ti ti-search"></i>
        <input type="text" class="search-inp" placeholder="Cari nama atau email..." value="{{ request('search') }}"
            onchange="applyFilter()">
    </div>
    <select id="filter-role" class="form-select" style="width:auto" onchange="applyFilter()">
        <option value="">Semua Role</option>
        <option value="customer" {{ request('role')=='customer'?'selected':'' }}>Customer</option>
        <option value="kasir" {{ request('role')=='kasir'?'selected':'' }}>Kasir</option>
        <option value="kitchen" {{ request('role')=='kitchen'?'selected':'' }}>Kitchen</option>
        <option value="superadmin" {{ request('role')=='superadmin'?'selected':'' }}>Superadmin</option>
    </select>
    <select id="filter-status" class="form-select" style="width:auto" onchange="applyFilter()">
        <option value="">Semua Status</option>
        <option value="active" {{ request('status')=='active'?'selected':'' }}>Aktif</option>
        <option value="suspended" {{ request('status')=='suspended'?'selected':'' }}>Suspended</option>
    </select>
</div>

<div class="content-card">
    <div style="overflow-x:auto">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Membership</th>
                    <th>Wallet</th>
                    <th>Bergabung</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px">
                            <img src="{{ $user->avatar_url }}" class="user-avatar" alt="">
                            <div>
                                <div style="font-weight:600;font-size:13px">{{ $user->name }}</div>
                                <div style="font-size:11px;color:var(--muted)">{{ $user->email }}</div>
                                @if($user->phone)
                                <div style="font-size:11px;color:var(--muted)">{{ $user->phone }}</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td><span class="role-badge role-{{ $user->role }}">{{ ucfirst($user->role) }}</span></td>
                    <td>
                        <span class="badge {{ $user->status === 'active' ? 'badge-green' : 'badge-red' }}">
                            <i class="ti {{ $user->status === 'active' ? 'ti-circle-check' : 'ti-ban' }}" style="font-size:11px"></i>
                            {{ ucfirst($user->status) }}
                        </span>
                    </td>
                    <td>
                        @if($user->membership && $user->membership->tier !== 'none')
                        <span class="tier-badge tier-{{ $user->membership->tier }}">
                            {{ ucfirst($user->membership->tier) }}
                        </span>
                        <div style="font-size:11px;color:var(--muted);margin-top:2px">{{ $user->membership->completed_orders }} order</div>
                        @else
                        <span style="font-size:12px;color:var(--muted)">—</span>
                        @endif
                    </td>
                    <td>
                        @if($user->wallet)
                        <div style="font-weight:600;font-size:13px;color:var(--sage-dark)">Rp {{ number_format($user->wallet->balance,0,',','.') }}</div>
                        @else
                        <span style="font-size:12px;color:var(--muted)">—</span>
                        @endif
                    </td>
                    <td style="font-size:12px;color:var(--muted)">{{ $user->created_at->format('d M Y') }}</td>
                    <td>
                        <div style="display:flex;gap:6px;align-items:center">
                            <a href="{{ route('admin.users.show',$user->id) }}" class="btn-icon btn-icon-sage" title="Detail">
                                <i class="ti ti-eye" style="font-size:14px"></i>
                            </a>
                            @if($user->status === 'active' && !$user->isSuperadmin())
                            <button onclick="suspendUser({{ $user->id }},'{{ $user->name }}',this)" class="btn-icon btn-icon-red" title="Suspend">
                                <i class="ti ti-ban" style="font-size:14px"></i>
                            </button>
                            @elseif($user->status === 'suspended')
                            <button onclick="activateUser({{ $user->id }},this)" class="btn-icon btn-icon-sage" title="Aktifkan">
                                <i class="ti ti-circle-check" style="font-size:14px"></i>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center;padding:40px;color:var(--muted)">Tidak ada user ditemukan</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:16px 20px;border-top:1px solid var(--border)">
        {{ $users->links() }}
    </div>
</div>
@endsection

@push('scripts')
<script>
function applyFilter() {
    const search = document.querySelector('.search-inp').value;
    const role   = document.getElementById('filter-role').value;
    const status = document.getElementById('filter-status').value;
    let url = '?';
    if(search) url += 'search='+encodeURIComponent(search)+'&';
    if(role)   url += 'role='+role+'&';
    if(status) url += 'status='+status+'&';
    window.location = url;
}
function suspendUser(id, name, btn) {
    confirmDanger('Suspend User?','"'+name+'" tidak bisa login setelah di-suspend.',()=>{
        btn.disabled=true;
        ajax('/admin/users/'+id+'/suspend','POST')
        .then(d=>{showToast(d.message,'info');setTimeout(()=>location.reload(),600);})
        .catch(()=>btn.disabled=false);
    },'Ya, Suspend');
}
function activateUser(id, btn) {
    btn.disabled = true;
    ajax('/admin/users/'+id+'/activate','POST')
    .then(d => { showToast(d.message,'success'); setTimeout(()=>location.reload(),600); })
    .catch(()=>btn.disabled=false);
}
</script>
@endpush