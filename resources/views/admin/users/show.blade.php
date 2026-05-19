@extends('layouts.admin')
@section('title','Detail User — '.$user->name)
@section('page-title','Detail User')
@section('breadcrumb')
<i class="ti ti-chevron-right" style="font-size:12px"></i>
<a href="{{ route('admin.users.index') }}" style="color:var(--muted);text-decoration:none">Users</a>
<i class="ti ti-chevron-right" style="font-size:12px"></i>
<span>{{ $user->name }}</span>
@endsection

@section('content')
<div class="page-header">
    <div>
        <div class="page-title">{{ $user->name }}</div>
        <div class="page-sub">{{ $user->email }} · Bergabung {{ $user->created_at->format('d M Y') }}</div>
    </div>
    <div style="display:flex;gap:10px">
        <a href="{{ route('admin.users.index') }}" class="btn-secondary"><i class="ti ti-arrow-left"></i> Kembali</a>
        @if($user->status === 'active' && !$user->isSuperadmin())
        <button onclick="suspendUser({{ $user->id }},'{{ $user->name }}')" class="btn-danger">
            <i class="ti ti-ban"></i> Suspend User
        </button>
        @elseif($user->status === 'suspended')
        <button onclick="activateUser({{ $user->id }})" class="btn-primary">
            <i class="ti ti-circle-check"></i> Aktifkan User
        </button>
        @endif
    </div>
</div>

<div style="display:grid;grid-template-columns:300px 1fr;gap:20px;align-items:start">
    {{-- Profile sidebar --}}
    <div>
        <div class="content-card" style="padding:24px;text-align:center;margin-bottom:16px">
            <img src="{{ $user->avatar_url }}" style="width:80px;height:80px;border-radius:50%;object-fit:cover;border:3px solid var(--sage);margin-bottom:12px" alt="">
            <div style="font-weight:700;font-size:17px;margin-bottom:4px">{{ $user->name }}</div>
            <div style="font-size:13px;color:var(--muted);margin-bottom:12px">{{ $user->email }}</div>
            <span class="badge badge-{{ $user->status === 'active' ? 'green' : 'red' }}" style="margin-bottom:8px">
                {{ ucfirst($user->status) }}
            </span>
            @if($user->role !== 'customer')
            <div><span style="background:rgba(168,85,247,.1);color:#7c3aed;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700">{{ ucfirst($user->role) }}</span></div>
            @endif
        </div>

        <div class="content-card" style="padding:18px;margin-bottom:16px">
            <div style="font-weight:700;font-size:13px;margin-bottom:12px">Info Akun</div>
            @foreach([
                ['ti-phone','No. HP',$user->phone ?? '—'],
                ['ti-clock','Last Login',$user->last_login_at ? $user->last_login_at->diffForHumans() : '—'],
                ['ti-calendar','Bergabung',$user->created_at->format('d M Y')],
            ] as $info)
            <div style="display:flex;gap:10px;align-items:flex-start;padding:8px 0;border-bottom:1px solid var(--border);font-size:13px">
                <i class="ti {{ $info[0] }}" style="font-size:15px;color:var(--sage);flex-shrink:0;margin-top:1px"></i>
                <div><div style="color:var(--muted);font-size:11px">{{ $info[1] }}</div><div style="font-weight:500">{{ $info[2] }}</div></div>
            </div>
            @endforeach
        </div>

        @if($user->wallet)
        <div class="content-card" style="padding:18px;margin-bottom:16px">
            <div style="font-weight:700;font-size:13px;margin-bottom:10px">SOHIBA Wallet</div>
            <div style="background:linear-gradient(135deg,var(--sage-dark),var(--sage));border-radius:10px;padding:14px;color:#fff;text-align:center">
                <div style="font-size:11px;opacity:.8;margin-bottom:4px">Saldo</div>
                <div style="font-size:22px;font-weight:700">Rp {{ number_format($user->wallet->balance,0,',','.') }}</div>
            </div>
            <div style="margin-top:10px;display:grid;grid-template-columns:1fr 1fr;gap:8px">
                <div style="background:var(--beige);border-radius:8px;padding:8px;text-align:center">
                    <div style="font-size:11px;color:var(--muted)">Total Topup</div>
                    <div style="font-weight:700;font-size:12px">Rp {{ number_format($user->wallet->total_topup,0,',','.') }}</div>
                </div>
                <div style="background:var(--beige);border-radius:8px;padding:8px;text-align:center">
                    <div style="font-size:11px;color:var(--muted)">Total Spent</div>
                    <div style="font-weight:700;font-size:12px">Rp {{ number_format($user->wallet->total_spent,0,',','.') }}</div>
                </div>
            </div>
        </div>
        @endif

        @if($user->membership)
        <div class="content-card" style="padding:18px">
            <div style="font-weight:700;font-size:13px;margin-bottom:10px">Membership</div>
            @php $tierColors=['none'=>'gray','silver'=>'blue','gold'=>'amber','platinum'=>'purple']; @endphp
            <span class="badge badge-{{ $tierColors[$user->membership->tier] ?? 'gray' }}" style="margin-bottom:10px">
                {{ ucfirst($user->membership->tier) }}
            </span>
            <div style="font-size:12px;color:var(--muted);margin-bottom:5px">{{ $user->membership->completed_orders }} order selesai</div>
            <div style="font-size:12px;color:var(--muted)">Cashback {{ $user->membership->cashback_rate }}%</div>
            @if($user->membership->tier !== 'platinum')
            <button onclick="approvePlatinum({{ $user->membership->id }})" class="btn-primary" style="width:100%;justify-content:center;margin-top:12px;padding:8px;font-size:12px">
                <i class="ti ti-award"></i> Approve Platinum
            </button>
            @endif
        </div>
        @endif
    </div>

    {{-- Main content --}}
    <div>
        {{-- Orders --}}
        <div class="content-card" style="margin-bottom:16px">
            <div class="card-head">
                <div class="card-title"><i class="ti ti-shopping-bag" style="color:var(--sage)"></i> Riwayat Pesanan</div>
                <span style="font-size:12px;color:var(--muted)">{{ $user->orders->count() }} pesanan terbaru</span>
            </div>
            <div style="overflow-x:auto">
                <table class="admin-table">
                    <thead><tr><th>No. Order</th><th>Status</th><th>Total</th><th>Waktu</th><th></th></tr></thead>
                    <tbody>
                        @forelse($user->orders as $order)
                        @php $oc=['menunggu'=>'badge-amber','cooking'=>'badge-amber','selesai'=>'badge-sage','completed'=>'badge-green','dibatalkan'=>'badge-red']; @endphp
                        <tr>
                            <td><span style="font-weight:700;font-size:13px">{{ $order->order_number }}</span></td>
                            <td><span class="badge {{ $oc[$order->status] ?? 'badge-gray' }}">{{ $order->status_label }}</span></td>
                            <td style="font-weight:600;color:var(--sage-dark)">Rp {{ number_format($order->total_amount,0,',','.') }}</td>
                            <td style="font-size:12px;color:var(--muted)">{{ $order->created_at->format('d M Y H:i') }}</td>
                            <td><a href="{{ route('admin.orders.show',$order->id) }}" class="btn-icon btn-icon-sage"><i class="ti ti-eye" style="font-size:13px"></i></a></td>
                        </tr>
                        @empty
                        <tr><td colspan="5" style="text-align:center;padding:20px;color:var(--muted)">Belum ada pesanan</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Login history --}}
        <div class="content-card">
            <div class="card-head">
                <div class="card-title"><i class="ti ti-history" style="color:var(--sage)"></i> Riwayat Login</div>
            </div>
            <div style="padding:0 20px 8px">
                @forelse($user->loginHistories as $log)
                <div style="display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid var(--border);font-size:13px">
                    <div style="width:32px;height:32px;border-radius:50%;background:{{ $log->success ? 'rgba(16,185,129,.1)' : 'rgba(220,80,60,.08)' }};display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <i class="ti {{ $log->success ? 'ti-login' : 'ti-alert-circle' }}" style="font-size:15px;color:{{ $log->success ? '#16a34a' : '#c0392b' }}"></i>
                    </div>
                    <div style="flex:1">
                        <div style="font-weight:500">{{ $log->device ?? 'Unknown' }} · {{ $log->ip_address }}</div>
                        <div style="font-size:11px;color:var(--muted)">{{ $log->logged_at->format('d M Y H:i') }}</div>
                    </div>
                    <span class="badge {{ $log->success ? 'badge-green' : 'badge-red' }}" style="font-size:10px">{{ $log->success ? 'Berhasil' : 'Gagal' }}</span>
                </div>
                @empty
                <div style="padding:20px;text-align:center;color:var(--muted);font-size:13px">Tidak ada riwayat login</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function suspendUser(id, name) {
    if(!confirm('Suspend "'+name+'"?')) return;
    ajax('/admin/users/'+id+'/suspend','POST').then(d=>{showToast(d.message,'info');setTimeout(()=>location.reload(),700);});
}
function activateUser(id) {
    ajax('/admin/users/'+id+'/activate','POST').then(d=>{showToast(d.message,'success');setTimeout(()=>location.reload(),700);});
}
function approvePlatinum(mid) {
    if(!confirm('Approve Platinum Membership?')) return;
    ajax('/admin/membership/'+mid+'/approve-platinum','POST').then(d=>{showToast(d.message,'success');setTimeout(()=>location.reload(),700);});
}
</script>
@endpush