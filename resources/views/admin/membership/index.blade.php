@extends('layouts.admin')
@section('title','Kelola Membership')
@section('page-title','Membership')

@section('content')
<div class="page-header">
    <div>
        <div class="page-title">Membership</div>
        <div class="page-sub">Monitor dan kelola tier membership seluruh customer</div>
    </div>
</div>

{{-- Tier distribution --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:24px">
    @foreach(['none'=>['—','gray','var(--beige)','var(--muted)'],'silver'=>['🥈','blue','rgba(148,163,184,.12)','#475569'],'gold'=>['🥇','amber','rgba(245,158,11,.1)','#92400e'],'platinum'=>['💎','purple','rgba(168,85,247,.08)','#7c3aed']] as $tier=>$cfg)
    <div style="background:var(--warm);border-radius:var(--r);border:1px solid var(--border);padding:18px;display:flex;align-items:center;gap:12px">
        <div style="width:44px;height:44px;border-radius:12px;background:{{ $cfg[2] }};display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0">{{ $cfg[0] }}</div>
        <div>
            <div style="font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.05em">{{ ucfirst($tier === 'none' ? 'Non Member' : $tier) }}</div>
            <div style="font-size:26px;font-weight:700;color:var(--text)">{{ $tierCounts[$tier] ?? 0 }}</div>
        </div>
    </div>
    @endforeach
</div>

{{-- Filter --}}
<div style="background:var(--warm);border-radius:var(--r);border:1px solid var(--border);padding:14px 18px;display:flex;gap:10px;align-items:center;flex-wrap:wrap;margin-bottom:20px">
    <div style="position:relative;flex:1;min-width:180px">
        <i class="ti ti-search" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--muted);font-size:16px;pointer-events:none"></i>
        <input type="text" placeholder="Cari user..." value="{{ request('search') }}"
            style="width:100%;padding:9px 14px 9px 38px;border-radius:10px;border:1.5px solid var(--border);background:var(--beige);font-size:13px;font-family:inherit;outline:none"
            onchange="window.location='?search='+this.value+'&tier={{ request('tier') }}'">
    </div>
    <select class="form-select" style="width:auto" onchange="window.location='?tier='+this.value+'&search={{ request('search') }}'">
        <option value="">Semua Tier</option>
        @foreach(['none'=>'Non Member','silver'=>'Silver','gold'=>'Gold','platinum'=>'Platinum'] as $v=>$l)
        <option value="{{ $v }}" {{ request('tier')===$v?'selected':'' }}>{{ $l }}</option>
        @endforeach
    </select>
</div>

<div class="content-card">
    <div style="overflow-x:auto">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Tier</th>
                    <th>Order Selesai</th>
                    <th>Cashback Rate</th>
                    <th>Bergabung Tier</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($memberships as $mem)
                @php $tierColor=['none'=>'badge-gray','silver'=>'badge-blue','gold'=>'badge-amber','platinum'=>'badge-sage']; @endphp
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px">
                            <img src="{{ $mem->user->avatar_url }}" style="width:34px;height:34px;border-radius:50%;object-fit:cover" alt="">
                            <div>
                                <div style="font-weight:600;font-size:13px">{{ $mem->user->name }}</div>
                                <div style="font-size:11px;color:var(--muted)">{{ $mem->user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge {{ $tierColor[$mem->tier] ?? 'badge-gray' }}" style="font-size:12px">
                            @switch($mem->tier)
                                @case('silver') 🥈 @break
                                @case('gold') 🥇 @break
                                @case('platinum') 💎 @break
                                @default —
                            @endswitch
                            {{ ucfirst($mem->tier === 'none' ? 'Non Member' : $mem->tier) }}
                        </span>
                    </td>
                    <td style="font-weight:700;font-size:14px">{{ $mem->completed_orders }}</td>
                    <td>
                        @if($mem->cashback_rate > 0)
                        <span style="background:rgba(90,124,101,.1);color:var(--sage-dark);padding:3px 10px;border-radius:20px;font-size:12px;font-weight:700">{{ $mem->cashback_rate }}%</span>
                        @else
                        <span style="color:var(--muted);font-size:12px">—</span>
                        @endif
                    </td>
                    <td style="font-size:12px;color:var(--muted)">
                        {{ $mem->tier_achieved_at ? $mem->tier_achieved_at->format('d M Y') : '—' }}
                    </td>
                    <td>
                        <div style="display:flex;gap:6px">
                            <a href="{{ route('admin.users.show',$mem->user_id) }}" class="btn-icon btn-icon-sage" title="Lihat User">
                                <i class="ti ti-eye" style="font-size:14px"></i>
                            </a>
                            @if($mem->tier !== 'platinum')
                            <button onclick="approvePlatinum({{ $mem->id }},'{{ $mem->user->name }}')" style="padding:5px 12px;background:rgba(168,85,247,.1);color:#7c3aed;border:1px solid rgba(168,85,247,.2);border-radius:8px;font-size:11px;font-weight:700;cursor:pointer;transition:all .2s" onmouseover="this.style.background='#7c3aed';this.style.color='#fff'" onmouseout="this.style.background='rgba(168,85,247,.1)';this.style.color='#7c3aed'">
                                💎 Platinum
                            </button>
                            @else
                            <span style="font-size:11px;color:var(--muted)">
                                @if($mem->platinum_expires_at)
                                Exp: {{ $mem->platinum_expires_at->format('d M Y') }}
                                @endif
                            </span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center;padding:30px;color:var(--muted)">Belum ada data membership</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:16px 20px;border-top:1px solid var(--border)">{{ $memberships->links() }}</div>
</div>
@endsection

@push('scripts')
<script>
function approvePlatinum(id,name){confirmAction('Approve Platinum?','Membership Platinum untuk "'+name+'" akan diaktifkan.',()=>{
    ajax('/admin/membership/'+id+'/approve-platinum','POST')
    .then(d => { showToast(d.message,'success'); setTimeout(()=>location.reload(),700); });
});}
</script>
@endpush