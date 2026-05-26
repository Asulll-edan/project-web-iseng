@extends('layouts.admin')
@section('title','Role Permissions')
@section('page-title','Role & Permission')

@push('styles')
<style>
.role-tabs{display:flex;gap:6px;margin-bottom:20px;flex-wrap:wrap}
.role-tab{padding:9px 20px;border-radius:20px;border:2px solid var(--border);background:transparent;font-size:13px;font-weight:600;cursor:pointer;transition:all .2s;color:var(--muted)}
.role-tab.active{background:var(--sage);color:#fff;border-color:var(--sage)}
.role-panel{display:none}.role-panel.active{display:block}
.perm-module{background:var(--warm);border-radius:var(--r);border:1px solid var(--border);margin-bottom:12px;overflow:hidden}
.perm-module-head{padding:13px 18px;background:var(--beige);font-weight:700;font-size:13px;display:flex;align-items:center;gap:8px;border-bottom:1px solid var(--border)}
.perm-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:0}
.perm-item{display:flex;align-items:center;gap:10px;padding:11px 18px;border-bottom:1px solid var(--border);border-right:1px solid var(--border);font-size:13px}
.perm-item:last-child{border-bottom:none}
.perm-toggle{position:relative;width:38px;height:22px;cursor:pointer;flex-shrink:0}
.perm-toggle input{display:none}
.perm-track{position:absolute;inset:0;border-radius:11px;background:var(--beige);border:1.5px solid var(--border);transition:background .2s}
.perm-toggle input:checked+.perm-track{background:var(--sage);border-color:var(--sage)}
.perm-thumb{position:absolute;top:2px;left:2px;width:16px;height:16px;border-radius:50%;background:#fff;box-shadow:0 1px 4px rgba(0,0,0,.15);transition:left .2s}
.perm-toggle input:checked~.perm-thumb{left:18px}
.perm-name{flex:1;color:var(--text)}
.perm-desc{font-size:11px;color:var(--muted);margin-top:1px}
.role-info{background:var(--beige);border-radius:10px;padding:12px 16px;margin-bottom:16px;font-size:13px;color:var(--muted);display:flex;align-items:center;gap:10px}
</style>
@endpush

@section('content')
<div class="page-header">
    <div>
        <div class="page-title">Role & Permission</div>
        <div class="page-sub">Atur hak akses setiap role (kecuali Superadmin — selalu full access)</div>
    </div>
</div>

@php
$roleIcons = [
    'admin'    => ['🔧','Admin','Kelola konten, user, dan operasional'],
    'manager'  => ['📊','Manager','Akses laporan dan analytics'],
    'kasir'    => ['💰','Kasir','POS dan kelola order'],
    'kitchen'  => ['👨‍🍳','Kitchen','Kitchen display'],
    'customer' => ['🛒','Customer','Akses fitur pelanggan'],
];
$permLabels = [
    'view_dashboard'        => 'Lihat Dashboard',
    'view_orders'           => 'Lihat Order',
    'manage_orders'         => 'Kelola Order',
    'manage_order_status'   => 'Update Status Order',
    'view_menus'            => 'Lihat Menu',
    'manage_menus'          => 'CRUD Menu',
    'view_users'            => 'Lihat Users',
    'manage_users'          => 'Kelola Users',
    'view_wallet'           => 'Lihat Wallet',
    'approve_topup'         => 'Approve Topup',
    'view_membership'       => 'Lihat Membership',
    'approve_platinum'      => 'Approve Platinum',
    'view_banners'          => 'Lihat Banner',
    'manage_banners'        => 'Kelola Banner',
    'view_vouchers'         => 'Lihat Voucher',
    'manage_vouchers'       => 'Kelola Voucher',
    'view_reservations'     => 'Lihat Reservasi',
    'manage_reservations'   => 'Kelola Reservasi',
    'view_analytics'        => 'Lihat Analytics',
    'view_reports'          => 'Lihat Laporan',
    'create_reports'        => 'Buat Laporan',
    'export_reports'        => 'Export Laporan',
    'approve_reports'       => 'Approve Laporan',
    'view_payment_history'  => 'Riwayat Pembayaran',
    'view_kitchen_orders'   => 'Lihat Order Kitchen',
    'manage_kitchen_status' => 'Update Status Kitchen',
    'manage_settings'       => 'Kelola Pengaturan',
    'order'                 => 'Buat Order',
    'view_own_orders'       => 'Lihat Order Sendiri',
    'wallet'                => 'Akses Wallet',
    'membership'            => 'Akses Membership',
    'reservation'           => 'Buat Reservasi',
    'profile'               => 'Edit Profil',
];
@endphp

{{-- Role Tabs --}}
<div class="role-tabs">
    @foreach($roles as $i => $role)
    @php $info = $roleIcons[$role] ?? [$role,'?','']; @endphp
    <button class="role-tab {{ $i===0?'active':'' }}" onclick="switchRole('{{ $role }}',this)">
        {{ $info[0] }} {{ $info[1] }}
    </button>
    @endforeach
</div>

@foreach($roles as $ri => $role)
@php $info = $roleIcons[$role] ?? [$role,'','']; @endphp
<div class="role-panel {{ $ri===0?'active':'' }}" id="rpanel-{{ $role }}">
    <div class="role-info">
        <span style="font-size:22px">{{ $info[0] }}</span>
        <div>
            <div style="font-weight:700;color:var(--text)">{{ $info[1] }}</div>
            <div>{{ $info[2] }}</div>
        </div>
        <button onclick="savePermissions('{{ $role }}')" class="btn-primary" style="margin-left:auto;padding:8px 20px;font-size:12px">
            <i class="ti ti-device-floppy"></i> Simpan Perubahan
        </button>
    </div>

    @foreach($allPerms as $module => $perms)
    <div class="perm-module">
        <div class="perm-module-head">
            <i class="ti ti-shield" style="font-size:16px;color:var(--sage)"></i> {{ $module }}
        </div>
        <div class="perm-grid">
            @foreach($perms as $perm)
            <div class="perm-item">
                <label class="perm-toggle">
                    <input type="checkbox"
                        id="perm-{{ $role }}-{{ $perm }}"
                        data-role="{{ $role }}"
                        data-perm="{{ $perm }}"
                        {{ ($permissions[$role][$perm] ?? false) ? 'checked' : '' }}>
                    <span class="perm-track"></span>
                    <span class="perm-thumb"></span>
                </label>
                <div>
                    <div class="perm-name">{{ $permLabels[$perm] ?? str_replace('_',' ',ucfirst($perm)) }}</div>
                    <div class="perm-desc">{{ $perm }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
</div>
@endforeach
@endsection

@push('scripts')
<script>
function switchRole(role, btn) {
    document.querySelectorAll('.role-tab').forEach(t=>t.classList.remove('active'));
    document.querySelectorAll('.role-panel').forEach(p=>p.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('rpanel-'+role).classList.add('active');
}

function savePermissions(role) {
    const checkboxes = document.querySelectorAll(`[data-role="${role}"]`);
    const selected   = [];
    checkboxes.forEach(cb => { if(cb.checked) selected.push(cb.dataset.perm); });

    Swal.fire({
        title: 'Simpan Permission?',
        text: `Update permission untuk role ${role}?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3d5c47',
        confirmButtonText: 'Ya, Simpan',
        cancelButtonText: 'Batal'
    }).then(r => {
        if(!r.isConfirmed) return;
        ajax('/admin/role-permissions','POST',{role, permissions: selected})
        .then(d => {
            if(d.success) showToast(d.message,'success');
            else showToast(d.message||'Gagal','error');
        })
        .catch(()=>showToast('Gagal menyimpan','error'));
    });
}
</script>
@endpush