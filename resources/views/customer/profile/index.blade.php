@extends('layouts.app')
@section('title','Profil Saya')

@push('styles')
<style>
.page-top{padding:110px 0 40px;background:linear-gradient(135deg,#1a2e22,#2c3e35)}
.profile-grid{display:grid;grid-template-columns:300px 1fr;gap:24px;align-items:start}
.profile-sidebar{background:var(--warm-white);border-radius:20px;border:1px solid var(--border);padding:24px;position:sticky;top:90px}
.profile-card{background:var(--warm-white);border-radius:20px;border:1px solid var(--border);padding:24px;margin-bottom:20px}
.avatar-wrap{position:relative;width:90px;height:90px;margin:0 auto 14px;cursor:pointer}
.avatar-wrap img{width:90px;height:90px;border-radius:50%;object-fit:cover;border:3px solid var(--sage)}
.avatar-edit{position:absolute;bottom:0;right:0;width:28px;height:28px;background:var(--sage);border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;border:2px solid #fff}
.progress-bar-wrap{height:6px;background:var(--beige);border-radius:3px;overflow:hidden;margin-top:6px}
.progress-bar-fill{height:100%;background:var(--sage);border-radius:3px;transition:width .5s}
.form-group{margin-bottom:16px}
.form-label{display:block;font-size:13px;font-weight:600;color:var(--text-main);margin-bottom:7px}
.form-input,.form-textarea,.form-select{width:100%;padding:10px 14px;border-radius:10px;border:1.5px solid var(--border);background:var(--beige);color:var(--text-main);font-size:14px;font-family:inherit;outline:none;transition:border-color .2s}
.form-input:focus,.form-textarea:focus,.form-select:focus{border-color:var(--sage);background:#fff}
.grid-2{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.tab-nav{display:flex;gap:4px;border-bottom:1px solid var(--border);margin-bottom:20px}
.tab-link{padding:10px 16px;font-size:14px;font-weight:600;color:var(--text-muted);cursor:pointer;border-bottom:2px solid transparent;margin-bottom:-1px;transition:all .2s;background:none;border-top:none;border-left:none;border-right:none}
.tab-link.active{color:var(--sage-dark);border-bottom-color:var(--sage)}
.tab-pane{display:none}.tab-pane.active{display:block}
@media(max-width:900px){.profile-grid{grid-template-columns:1fr}.profile-sidebar{position:static}}
@media(max-width:600px){.grid-2{grid-template-columns:1fr}}
</style>
@endpush

@section('content')
<div class="page-top">
    <div class="container">
        <h1 style="font-family:'INeedCoffee',serif;font-size:clamp(22px,4vw,32px);font-weight:600;color:#fff">Profil Saya</h1>
    </div>
</div>

<div class="container" style="padding-top:32px;padding-bottom:80px">
    <div class="profile-grid">
        {{-- Sidebar --}}
        <div class="profile-sidebar">
            <div style="text-align:center">
                <div class="avatar-wrap" onclick="document.getElementById('avatar-input').click()">
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" id="avatar-preview">
                    <div class="avatar-edit">
                        <i class="ti ti-camera" style="font-size:13px;color:#fff"></i>
                    </div>
                </div>
                <input type="file" id="avatar-input" accept="image/*" style="display:none" onchange="uploadAvatar(this)">

                <div style="font-weight:700;font-size:17px">{{ $user->profile->display_name ?? $user->name }}</div>
                <div style="font-size:13px;color:var(--text-muted);margin-top:3px">{{ $user->email }}</div>

                {{-- Membership badge --}}
                @if($user->membership && $user->membership->tier !== 'none')
                <div style="margin-top:10px">
                    <span style="background:rgba(90,124,101,.1);color:var(--sage-dark);border-radius:20px;padding:5px 14px;font-size:12px;font-weight:700;display:inline-flex;align-items:center;gap:5px">
                        <i class="ti ti-award" style="font-size:14px"></i>
                        {{ ucfirst($user->membership->tier) }} Member
                    </span>
                </div>
                @endif

                {{-- Profile completion --}}
                <div style="margin-top:16px;text-align:left">
                    <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:5px">
                        <span style="color:var(--text-muted)">Kelengkapan profil</span>
                        <span style="font-weight:700;color:var(--sage-dark)">{{ $user->profile->profile_completion ?? 40 }}%</span>
                    </div>
                    <div class="progress-bar-wrap">
                        <div class="progress-bar-fill" style="width:{{ $user->profile->profile_completion ?? 40 }}%"></div>
                    </div>
                </div>
            </div>

            <div style="border-top:1px solid var(--border);margin:18px 0;padding-top:16px;display:flex;flex-direction:column;gap:6px">
                @foreach([['ti-shopping-bag','Total Pesanan',$orderStats['total']],['ti-circle-check','Selesai',$orderStats['completed']],['ti-clock','Aktif',$orderStats['active']]] as $s)
                <div style="display:flex;align-items:center;justify-content:space-between;font-size:13px;padding:6px 0">
                    <span style="display:flex;align-items:center;gap:8px;color:var(--text-muted)">
                        <i class="ti {{ $s[0] }}" style="font-size:15px;color:var(--sage)"></i> {{ $s[1] }}
                    </span>
                    <span style="font-weight:700">{{ $s[2] }}</span>
                </div>
                @endforeach
                @if($user->wallet)
                <div style="display:flex;align-items:center;justify-content:space-between;font-size:13px;padding:6px 0">
                    <span style="display:flex;align-items:center;gap:8px;color:var(--text-muted)">
                        <i class="ti ti-wallet" style="font-size:15px;color:var(--sage)"></i> Saldo Wallet
                    </span>
                    <span style="font-weight:700;color:var(--sage-dark)">Rp {{ number_format($user->wallet->balance,0,',','.') }}</span>
                </div>
                @endif
                @if($user->loyaltyPoint)
                <div style="display:flex;align-items:center;justify-content:space-between;font-size:13px;padding:6px 0">
                    <span style="display:flex;align-items:center;gap:8px;color:var(--text-muted)">
                        <i class="ti ti-star" style="font-size:15px;color:#f59e0b"></i> Loyalty Points
                    </span>
                    <span style="font-weight:700">{{ number_format($user->loyaltyPoint->available_points) }} pts</span>
                </div>
                @endif
            </div>

            {{-- Dark mode toggle --}}
            <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-top:1px solid var(--border)">
                <span style="font-size:13px;font-weight:600;display:flex;align-items:center;gap:8px">
                    <i class="ti ti-moon" style="font-size:16px;color:var(--sage)"></i> Dark Mode
                </span>
                <label style="position:relative;width:42px;height:24px;cursor:pointer">
                    <input type="checkbox" id="dark-toggle" {{ $user->dark_mode ? 'checked' : '' }} style="display:none" onchange="toggleDark()">
                    <span id="dark-track" style="position:absolute;inset:0;border-radius:12px;background:{{ $user->dark_mode ? 'var(--sage)' : 'var(--beige)' }};border:1.5px solid var(--border);transition:background .2s"></span>
                    <span id="dark-thumb" style="position:absolute;top:2px;{{ $user->dark_mode ? 'left:20px' : 'left:2px' }};width:18px;height:18px;border-radius:50%;background:#fff;box-shadow:0 1px 4px rgba(0,0,0,.15);transition:left .2s"></span>
                </label>
            </div>
        </div>

        {{-- Main content --}}
        <div>
            <div class="profile-card">
                <div class="tab-nav">
                    <button class="tab-link active" onclick="switchTab('info',this)">Info Profil</button>
                    <button class="tab-link" onclick="switchTab('security',this)">Keamanan</button>
                    <button class="tab-link" onclick="switchTab('history',this)">Riwayat Login</button>
                </div>

                {{-- Tab: Info --}}
                <div class="tab-pane active" id="tab-info">
                    @if(session('success'))
                    <div style="background:rgba(16,185,129,.08);border:1px solid rgba(16,185,129,.2);border-radius:10px;padding:12px 14px;margin-bottom:16px;font-size:13px;color:#065f46;display:flex;align-items:center;gap:8px">
                        <i class="ti ti-circle-check" style="font-size:16px"></i> {{ session('success') }}
                    </div>
                    @endif
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf @method('PUT')
                        <div class="grid-2">
                            <div class="form-group">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" name="name" class="form-input" value="{{ old('name',$user->name) }}" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Display Name</label>
                                <input type="text" name="display_name" class="form-input" value="{{ old('display_name',$user->profile->display_name ?? '') }}">
                            </div>
                        </div>
                        <div class="grid-2">
                            <div class="form-group">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-input" value="{{ $user->email }}" disabled style="opacity:.6;cursor:not-allowed">
                            </div>
                            <div class="form-group">
                                <label class="form-label">No. HP</label>
                                <input type="text" name="phone" class="form-input" value="{{ old('phone',$user->phone) }}">
                            </div>
                        </div>
                        <div class="grid-2">
                            <div class="form-group">
                                <label class="form-label">Tanggal Lahir</label>
                                <input type="date" name="birthdate" class="form-input" value="{{ old('birthdate', optional($user->profile->birthdate)->format('Y-m-d') ?? '') }}">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Jenis Kelamin</label>
                                <select name="gender" class="form-select">
                                    <option value="">Pilih</option>
                                    <option value="male" {{ ($user->profile->gender ?? '') == 'male' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="female" {{ ($user->profile->gender ?? '') == 'female' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Kota</label>
                            <input type="text" name="city" class="form-input" value="{{ old('city', $user->profile->city ?? '') }}" placeholder="Jakarta, Bandung, dll">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Alamat</label>
                            <textarea name="address" class="form-textarea" rows="2" placeholder="Alamat lengkap">{{ old('address', $user->profile->address ?? '') }}</textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Bio</label>
                            <textarea name="bio" class="form-textarea" rows="2" placeholder="Ceritakan sedikit tentang kamu..." maxlength="300">{{ old('bio', $user->bio) }}</textarea>
                        </div>
                        <button type="submit" class="btn-primary">
                            <i class="ti ti-device-floppy"></i> Simpan Perubahan
                        </button>
                    </form>
                </div>

                {{-- Tab: Security --}}
                <div class="tab-pane" id="tab-security">
                    {{-- Ganti Password --}}
                    <div style="background:var(--beige);border-radius:12px;padding:16px;margin-bottom:18px">
                        <div style="font-weight:700;font-size:14px;margin-bottom:14px;display:flex;align-items:center;gap:8px;color:var(--text-main)">
                            <i class="ti ti-lock" style="color:var(--sage);font-size:17px"></i> Ganti Password
                        </div>
                        <form action="{{ route('profile.password') }}" method="POST">
                            @csrf @method('PUT')
                            @if($errors->has('current_password'))
                            <div style="background:rgba(220,80,60,.08);border:1px solid rgba(220,80,60,.2);border-radius:10px;padding:12px 14px;margin-bottom:12px;font-size:13px;color:#c0392b">
                                {{ $errors->first('current_password') }}
                            </div>
                            @endif
                            <div class="form-group">
                                <label class="form-label">Password Saat Ini</label>
                                <div style="position:relative">
                                    <input type="password" name="current_password" id="cp1" class="form-input" style="padding-right:40px" required>
                                    <button type="button" onclick="togglePwField('cp1','eye-cp1')" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--muted)"><i class="ti ti-eye" id="eye-cp1" style="font-size:16px"></i></button>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Password Baru</label>
                                <div style="position:relative">
                                    <input type="password" name="password" id="np1" class="form-input" style="padding-right:40px" minlength="8" required oninput="checkPasswordStrength(this.value)">
                                    <button type="button" onclick="togglePwField('np1','eye-np1')" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--muted)"><i class="ti ti-eye" id="eye-np1" style="font-size:16px"></i></button>
                                </div>
                                <div id="pw-strength-bar" style="height:4px;border-radius:2px;margin-top:6px;background:var(--beige);overflow:hidden;display:none">
                                    <div id="pw-strength-fill" style="height:100%;border-radius:2px;transition:width .3s,background .3s"></div>
                                </div>
                                <div id="pw-strength-label" style="font-size:11px;margin-top:4px;display:none"></div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Konfirmasi Password Baru</label>
                                <div style="position:relative">
                                    <input type="password" name="password_confirmation" id="cp2" class="form-input" style="padding-right:40px" required>
                                    <button type="button" onclick="togglePwField('cp2','eye-cp2')" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--muted)"><i class="ti ti-eye" id="eye-cp2" style="font-size:16px"></i></button>
                                </div>
                            </div>
                            <button type="submit" class="btn-primary">
                                <i class="ti ti-lock"></i> Ubah Password
                            </button>
                        </form>
                    </div>

                    {{-- Sesi Aktif --}}
                    <div style="background:var(--beige);border-radius:12px;padding:16px;margin-bottom:18px">
                        <div style="font-weight:700;font-size:14px;margin-bottom:12px;display:flex;align-items:center;gap:8px;color:var(--text-main)">
                            <i class="ti ti-device-desktop" style="color:var(--sage);font-size:17px"></i> Sesi Login Aktif
                        </div>
                        @foreach($loginHistory as $log)
                        @if($log->success)
                        <div style="display:flex;align-items:center;gap:12px;padding:10px;background:var(--warm-white);border-radius:10px;margin-bottom:8px;font-size:13px">
                            <i class="ti ti-{{ str_contains($log->device??'','Mobile') ? 'device-mobile' : 'device-laptop' }}" style="font-size:18px;color:var(--sage)"></i>
                            <div style="flex:1">
                                <div style="font-weight:600">{{ $log->device ?? 'Unknown Device' }}</div>
                                <div style="font-size:11px;color:var(--muted)">{{ $log->ip_address }} · {{ $log->logged_at->format('d M Y H:i') }}</div>
                            </div>
                            @if($loop->first)
                            <span style="background:rgba(90,124,101,.1);color:var(--sage-dark);font-size:10px;font-weight:700;padding:2px 8px;border-radius:10px">Sesi Ini</span>
                            @endif
                        </div>
                        @endif
                        @endforeach
                        <button onclick="logoutAllDevices()" style="width:100%;margin-top:8px;padding:10px;border-radius:10px;border:1.5px solid rgba(220,80,60,.25);background:rgba(220,80,60,.06);color:#c0392b;font-size:13px;font-weight:600;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:6px;transition:all .2s" onmouseover="this.style.background='rgba(220,80,60,.12)'" onmouseout="this.style.background='rgba(220,80,60,.06)'">
                            <i class="ti ti-logout" style="font-size:15px"></i> Logout Semua Perangkat
                        </button>
                    </div>

                    {{-- Password Strength Info --}}
                    <div style="background:rgba(90,124,101,.06);border-radius:12px;padding:14px 16px;font-size:12px;color:var(--text-muted)">
                        <div style="font-weight:700;margin-bottom:8px;color:var(--sage-dark)"><i class="ti ti-shield-check" style="font-size:14px"></i> Tips Keamanan Akun</div>
                        <div style="display:flex;flex-direction:column;gap:5px">
                            <span><i class="ti ti-check" style="color:var(--sage);font-size:13px"></i> Gunakan password min. 8 karakter</span>
                            <span><i class="ti ti-check" style="color:var(--sage);font-size:13px"></i> Kombinasikan huruf besar, kecil, angka & simbol</span>
                            <span><i class="ti ti-check" style="color:var(--sage);font-size:13px"></i> Jangan gunakan password yang sama di semua akun</span>
                            <span><i class="ti ti-check" style="color:var(--sage);font-size:13px"></i> Ganti password secara berkala</span>
                        </div>
                    </div>
                </div>

                {{-- Tab: Login History --}}
                <div class="tab-pane" id="tab-history">
                    <div style="display:flex;flex-direction:column;gap:10px">
                        @forelse($loginHistory as $log)
                        <div style="display:flex;align-items:center;gap:14px;padding:12px;background:var(--beige);border-radius:12px">
                            <div style="width:36px;height:36px;background:{{ $log->success ? 'rgba(90,124,101,.12)' : 'rgba(220,80,60,.08)' }};border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                <i class="ti {{ $log->success ? 'ti-login' : 'ti-alert-circle' }}" style="font-size:16px;color:{{ $log->success ? 'var(--sage)' : '#c0392b' }}"></i>
                            </div>
                            <div style="flex:1">
                                <div style="font-size:13px;font-weight:600">{{ $log->device ?? 'Unknown Device' }}</div>
                                <div style="font-size:12px;color:var(--text-muted)">{{ $log->ip_address }} · {{ $log->logged_at->format('d M Y H:i') }}</div>
                            </div>
                            <span style="font-size:11px;font-weight:600;padding:3px 10px;border-radius:20px;background:{{ $log->success ? 'rgba(90,124,101,.1)' : 'rgba(220,80,60,.08)' }};color:{{ $log->success ? 'var(--sage-dark)' : '#c0392b' }}">
                                {{ $log->success ? 'Berhasil' : 'Gagal' }}
                            </span>
                        </div>
                        @empty
                        <div style="text-align:center;padding:30px;color:var(--text-muted);font-size:14px">Belum ada riwayat login</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function switchTab(name, btn) {
    document.querySelectorAll('.tab-link').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('tab-' + name).classList.add('active');
}
function uploadAvatar(input) {
    if (!input.files || !input.files[0]) return;
    const fd = new FormData();
    fd.append('avatar', input.files[0]);
    fd.append('_token', CSRF);
    fetch('/profile/avatar', { method: 'POST', body: fd, headers: { 'Accept': 'application/json' } })
    .then(r => r.json())
    .then(d => {
        if (d.success) { document.getElementById('avatar-preview').src = d.avatar_url + '?t=' + Date.now(); showToast('Avatar diperbarui!', 'success'); }
        else showToast('Gagal upload avatar', 'error');
    });
}
function togglePwField(id, iconId) {
    const inp = document.getElementById(id);
    const ic  = document.getElementById(iconId);
    inp.type  = inp.type === 'password' ? 'text' : 'password';
    ic.className = inp.type === 'password' ? 'ti ti-eye' : 'ti ti-eye-off';
    ic.style.fontSize = '16px';
}

function checkPasswordStrength(pw) {
    const bar   = document.getElementById('pw-strength-bar');
    const fill  = document.getElementById('pw-strength-fill');
    const label = document.getElementById('pw-strength-label');
    if (!pw) { bar.style.display='none'; label.style.display='none'; return; }
    bar.style.display='block'; label.style.display='block';
    let score = 0;
    if (pw.length >= 8)  score++;
    if (pw.length >= 12) score++;
    if (/[A-Z]/.test(pw)) score++;
    if (/[0-9]/.test(pw)) score++;
    if (/[^A-Za-z0-9]/.test(pw)) score++;
    const levels = [
        {pct:'20%', color:'#c0392b', text:'Sangat Lemah', tc:'#c0392b'},
        {pct:'40%', color:'#e07a5f', text:'Lemah', tc:'#e07a5f'},
        {pct:'60%', color:'#f59e0b', text:'Sedang', tc:'#92400e'},
        {pct:'80%', color:'#8aaa92', text:'Kuat', tc:'#065f46'},
        {pct:'100%',color:'#16a34a', text:'Sangat Kuat 💪', tc:'#065f46'},
    ];
    const lv = levels[Math.max(0,score-1)];
    fill.style.width = lv.pct; fill.style.background = lv.color;
    label.textContent = lv.text; label.style.color = lv.tc;
}

function logoutAllDevices() {
    Swal.fire({
        title: 'Logout Semua Perangkat?',
        text: 'Semua sesi aktif akan diakhiri. Kamu perlu login ulang.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#c0392b',
        cancelButtonColor: '#6b7c72',
        confirmButtonText: 'Ya, Logout Semua',
        cancelButtonText: 'Batal'
    }).then(r => {
        if (!r.isConfirmed) return;
        Swal.fire({
            title: 'Konfirmasi Password',
            input: 'password',
            inputPlaceholder: 'Masukkan password kamu',
            inputAttributes: { autocomplete: 'current-password' },
            showCancelButton: true,
            confirmButtonColor: '#c0392b',
            confirmButtonText: 'Logout Semua',
            cancelButtonText: 'Batal',
        }).then(r2 => {
            if (!r2.isConfirmed || !r2.value) return;
            fetch('/profile/security', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ current_password: r2.value, action: 'logout_all_devices' })
            }).then(res => res.json()).then(d => {
                if (d.success) {
                    Swal.fire({ icon: 'success', title: 'Berhasil!', text: d.message, timer: 2000, showConfirmButton: false });
                    setTimeout(() => window.location.href = d.redirect || '/login', 2000);
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: d.message });
                }
            });
        });
    });
}

function toggleDark() {
    fetch('/profile/darkmode', { method: 'PUT', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' } })
    .then(r => r.json())
    .then(d => {
        const html = document.documentElement;
        const track = document.getElementById('dark-track');
        const thumb = document.getElementById('dark-thumb');
        if (d.dark_mode) {
            html.classList.add('dark');
            track.style.background = 'var(--sage)';
            thumb.style.left = '20px';
        } else {
            html.classList.remove('dark');
            track.style.background = 'var(--beige)';
            thumb.style.left = '2px';
        }
    });
}
</script>
@endpush

