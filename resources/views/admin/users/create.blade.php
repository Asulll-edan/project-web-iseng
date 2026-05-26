@extends('layouts.admin')
@section('title','Tambah User Baru')
@section('page-title','Tambah User')
@section('breadcrumb')
<i class="ti ti-chevron-right" style="font-size:12px"></i>
<a href="{{ route('admin.users.index') }}" style="color:var(--muted);text-decoration:none">Users</a>
<i class="ti ti-chevron-right" style="font-size:12px"></i>
<span>Tambah</span>
@endsection

@push('styles')
<style>
.form-card{background:var(--warm);border-radius:var(--r);border:1px solid var(--border);padding:28px;max-width:600px}
.role-option{display:flex;align-items:center;gap:12px;padding:12px 16px;border-radius:12px;border:2px solid var(--border);cursor:pointer;transition:all .2s;margin-bottom:8px}
.role-option:hover{border-color:var(--sage);background:rgba(90,124,101,.04)}
.role-option.selected{border-color:var(--sage);background:rgba(90,124,101,.08)}
.role-option input{display:none}
.role-icon{width:38px;height:38px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0}
.grid-2{display:grid;grid-template-columns:1fr 1fr;gap:14px}
</style>
@endpush

@section('content')
<div class="page-header">
    <div>
        <div class="page-title">Tambah User Baru</div>
        <div class="page-sub">Buat akun baru untuk semua role yang tersedia</div>
    </div>
    <a href="{{ route('admin.users.index') }}" class="btn-secondary"><i class="ti ti-arrow-left"></i> Kembali</a>
</div>

@if($errors->any())
<div style="background:rgba(220,80,60,.06);border:1px solid rgba(220,80,60,.2);border-radius:var(--r);padding:14px 18px;margin-bottom:18px;font-size:13px;color:#c0392b">
    @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
</div>
@endif

<div class="form-card">
    <form action="{{ route('admin.users.store') }}" method="POST">
        @csrf

        {{-- Role pilihan --}}
        <div class="form-group">
            <label class="form-label">Role User <span style="color:#c0392b">*</span></label>
            @php
            $roles = [
                'customer'   => ['🛒','Customer','Pelanggan restoran biasa','rgba(90,124,101,.1)'],
                'kasir'      => ['💰','Kasir','Akses POS & kelola order','rgba(59,130,246,.1)'],
                'kitchen'    => ['👨‍🍳','Kitchen','Akses kitchen display','rgba(245,158,11,.1)'],
                'manager'    => ['📊','Manager','Akses laporan & analytics','rgba(168,85,247,.08)'],
                'admin'      => ['🔧','Admin','Kelola konten & user','rgba(16,185,129,.08)'],
            ];
            if(auth()->user()->isSuperadmin())
                $roles['superadmin'] = ['👑','Superadmin','Akses penuh semua sistem','rgba(220,80,60,.08)'];
            @endphp
            <div id="role-list">
                @foreach($roles as $val => $info)
                <label class="role-option {{ old('role') === $val ? 'selected' : '' }}" onclick="selectRole('{{ $val }}',this)">
                    <input type="radio" name="role" value="{{ $val }}" {{ old('role') === $val ? 'checked' : '' }}>
                    <div class="role-icon" style="background:{{ $info[3] }}">{{ $info[0] }}</div>
                    <div>
                        <div style="font-weight:700;font-size:14px">{{ $info[1] }}</div>
                        <div style="font-size:12px;color:var(--muted)">{{ $info[2] }}</div>
                    </div>
                    <i class="ti ti-circle-check" style="font-size:18px;color:var(--sage);margin-left:auto;display:{{ old('role') === $val ? 'block' : 'none' }}" id="check-{{ $val }}"></i>
                </label>
                @endforeach
            </div>
        </div>

        <div style="height:1px;background:var(--border);margin:20px 0"></div>

        {{-- Info dasar --}}
        <div class="grid-2">
            <div class="form-group">
                <label class="form-label">Nama Lengkap <span style="color:#c0392b">*</span></label>
                <input type="text" name="name" class="form-input" placeholder="John Doe" value="{{ old('name') }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">No. HP</label>
                <input type="text" name="phone" class="form-input" placeholder="08xxxxxxxxxx" value="{{ old('phone') }}">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Email <span style="color:#c0392b">*</span></label>
            <input type="email" name="email" class="form-input" placeholder="user@example.com" value="{{ old('email') }}" required>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label class="form-label">Password <span style="color:#c0392b">*</span></label>
                <div style="position:relative">
                    <input type="password" name="password" id="pw1" class="form-input" placeholder="Min. 8 karakter" style="padding-right:40px" required>
                    <button type="button" onclick="togglePw('pw1','eye1')" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--muted)">
                        <i class="ti ti-eye" id="eye1" style="font-size:17px"></i>
                    </button>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Konfirmasi Password <span style="color:#c0392b">*</span></label>
                <div style="position:relative">
                    <input type="password" name="password_confirmation" id="pw2" class="form-input" placeholder="Ulangi password" style="padding-right:40px" required>
                    <button type="button" onclick="togglePw('pw2','eye2')" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--muted)">
                        <i class="ti ti-eye" id="eye2" style="font-size:17px"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Status Awal</label>
            <div style="display:flex;gap:10px">
                <label style="display:flex;align-items:center;gap:8px;padding:10px 16px;border-radius:10px;border:2px solid var(--border);cursor:pointer;flex:1;transition:all .2s" id="lbl-active" onclick="selectStatus('active')">
                    <input type="radio" name="status" value="active" checked style="display:none">
                    <i class="ti ti-circle-check" style="font-size:18px;color:var(--sage)"></i>
                    <span style="font-size:13px;font-weight:600">Aktif</span>
                </label>
                <label style="display:flex;align-items:center;gap:8px;padding:10px 16px;border-radius:10px;border:2px solid var(--border);cursor:pointer;flex:1;transition:all .2s" id="lbl-suspended" onclick="selectStatus('suspended')">
                    <input type="radio" name="status" value="suspended" style="display:none">
                    <i class="ti ti-ban" style="font-size:18px;color:#c0392b"></i>
                    <span style="font-size:13px;font-weight:600">Suspended</span>
                </label>
            </div>
        </div>

        <div style="display:flex;gap:10px;margin-top:8px">
            <button type="submit" class="btn-primary" style="flex:1;justify-content:center;padding:13px">
                <i class="ti ti-user-plus"></i> Buat User Baru
            </button>
            <a href="{{ route('admin.users.index') }}" class="btn-secondary" style="padding:13px 20px">Batal</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function selectRole(val, el) {
    document.querySelectorAll('.role-option').forEach(o => {
        o.classList.remove('selected');
        o.querySelector('input').checked = false;
    });
    document.querySelectorAll('[id^="check-"]').forEach(i => i.style.display='none');
    el.classList.add('selected');
    el.querySelector('input').checked = true;
    const chk = document.getElementById('check-'+val);
    if(chk) chk.style.display = 'block';
}
function selectStatus(val) {
    document.querySelectorAll('[name="status"]').forEach(r => r.checked = false);
    document.getElementById('lbl-active').style.borderColor    = val==='active'    ? 'var(--sage)' : 'var(--border)';
    document.getElementById('lbl-suspended').style.borderColor = val==='suspended' ? '#c0392b'     : 'var(--border)';
    document.querySelector(`[name="status"][value="${val}"]`).checked = true;
}
function togglePw(id, iconId) {
    const inp = document.getElementById(id);
    const ic  = document.getElementById(iconId);
    inp.type  = inp.type === 'password' ? 'text' : 'password';
    ic.className = inp.type === 'password' ? 'ti ti-eye' : 'ti ti-eye-off';
}
// Init status style
selectStatus('active');
</script>
@endpush