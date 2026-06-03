@extends('layouts.admin')
@section('title','Pengaturan Sistem')
@section('page-title','Pengaturan')

@push('styles')
<style>
.settings-grid{display:grid;grid-template-columns:220px 1fr;gap:20px;align-items:start}
.settings-nav{background:var(--warm);border-radius:var(--r);border:1px solid var(--border);overflow:hidden;position:sticky;top:90px}
.settings-nav-item{padding:11px 16px;font-size:13px;font-weight:600;cursor:pointer;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:8px;color:var(--muted);transition:all .15s}
.settings-nav-item:last-child{border:none}
.settings-nav-item:hover{background:var(--beige);color:var(--text)}
.settings-nav-item.active{background:rgba(90,124,101,.08);color:var(--sage-dark);border-left:3px solid var(--sage)}
.settings-section{background:var(--warm);border-radius:var(--r);border:1px solid var(--border);padding:24px;margin-bottom:16px;display:none}
.settings-section.active{display:block}
.settings-section-title{font-weight:700;font-size:15px;margin-bottom:18px;padding-bottom:12px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:8px;color:var(--text)}
.settings-row{display:grid;grid-template-columns:1fr 1fr;gap:14px}
@media(max-width:800px){.settings-grid{grid-template-columns:1fr}.settings-nav{position:static}.settings-row{grid-template-columns:1fr}}
</style>
@endpush

@section('content')
<div class="page-header">
    <div>
        <div class="page-title">Pengaturan Sistem</div>
        <div class="page-sub">Konfigurasi restoran, membership, dan sistem</div>
    </div>
</div>

<form action="{{ route('admin.settings.update') }}" method="POST" id="settings-form">
    @csrf @method('PUT')
    <div class="settings-grid">
        {{-- Nav --}}
        <div class="settings-nav">
            @foreach(['general'=>['ti-building-store','Restoran'],'contact'=>['ti-phone','Kontak'],'operation'=>['ti-clock','Operasional'],'payment'=>['ti-credit-card','Pembayaran'],'membership'=>['ti-award','Membership'],'loyalty'=>['ti-star','Loyalty Points']] as $group=>$info)
            <div class="settings-nav-item {{ $group==='general'?'active':'' }}" onclick="switchSection('{{ $group }}',this)">
                <i class="ti {{ $info[0] }}" style="font-size:16px"></i> {{ $info[1] }}
            </div>
            @endforeach
        </div>

        {{-- Content --}}
        <div>
            {{-- General --}}
            <div class="settings-section active" id="sec-general">
                <div class="settings-section-title"><i class="ti ti-building-store" style="color:var(--sage)"></i> Info Restoran</div>
                <div class="settings-row">
                    <div class="form-group">
                        <label class="form-label">Nama Restoran</label>
                        <input type="text" name="restaurant_name" class="form-input" value="{{ $settings['restaurant_name']->value ?? 'Rumahnya Anak Sekolah' }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tagline</label>
                        <input type="text" name="restaurant_tagline" class="form-input" value="{{ $settings['restaurant_tagline']->value ?? '' }}">
                    </div>
                </div>
            </div>

            {{-- Contact --}}
            <div class="settings-section" id="sec-contact">
                <div class="settings-section-title"><i class="ti ti-phone" style="color:var(--sage)"></i> Informasi Kontak</div>
                <div class="form-group">
                    <label class="form-label">No. Telepon</label>
                    <input type="text" name="restaurant_phone" class="form-input" value="{{ $settings['restaurant_phone']->value ?? '' }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="restaurant_email" class="form-input" value="{{ $settings['restaurant_email']->value ?? '' }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Alamat</label>
                    <textarea name="restaurant_address" class="form-textarea" rows="2">{{ $settings['restaurant_address']->value ?? '' }}</textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Nomor WhatsApp <span style="color:var(--muted);font-weight:400">(62xxx format)</span></label>
                    <input type="text" name="whatsapp_number" class="form-input" placeholder="6281234567890" value="{{ $settings['whatsapp_number']->value ?? '' }}">
                </div>
            </div>

            {{-- Operation --}}
            <div class="settings-section" id="sec-operation">
                <div class="settings-section-title"><i class="ti ti-clock" style="color:var(--sage)"></i> Jam Operasional</div>
                <div class="settings-row">
                    <div class="form-group">
                        <label class="form-label">Jam Buka</label>
                        <input type="time" name="open_time" class="form-input" value="{{ $settings['open_time']->value ?? '07:00' }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Jam Tutup</label>
                        <input type="time" name="close_time" class="form-input" value="{{ $settings['close_time']->value ?? '21:00' }}">
                    </div>
                </div>
            </div>

            {{-- Payment --}}
            <div class="settings-section" id="sec-payment">
                <div class="settings-section-title"><i class="ti ti-credit-card" style="color:var(--sage)"></i> Pengaturan Pembayaran</div>
                <div class="form-group">
                    <label class="form-label">Pajak (%)</label>
                    <input type="number" name="tax_rate" class="form-input" min="0" max="100" value="{{ $settings['tax_rate']->value ?? 10 }}">
                    <div class="form-hint">Persentase pajak yang dikenakan pada setiap transaksi</div>
                </div>
            </div>

            {{-- Membership --}}
            <div class="settings-section" id="sec-membership">
                <div class="settings-section-title"><i class="ti ti-award" style="color:var(--sage)"></i> Syarat Membership</div>
                <div style="background:var(--beige);border-radius:10px;padding:12px 16px;margin-bottom:16px;font-size:12px;color:var(--muted)">
                    <i class="ti ti-info-circle" style="font-size:14px;color:var(--sage)"></i> Minimal order selesai untuk naik tier
                </div>
                <div class="settings-row">
                    @foreach(['silver_min_orders'=>['🥈','Silver','10'],'gold_min_orders'=>['🥇','Gold','30'],'platinum_min_orders'=>['💎','Platinum','100']] as $key=>$data)
                    <div class="form-group">
                        <label class="form-label">{{ $data[0] }} Min. Order {{ $data[1] }}</label>
                        <input type="number" name="{{ $key }}" class="form-input" min="1" value="{{ $settings[$key]->value ?? $data[2] }}">
                    </div>
                    @endforeach
                </div>
                <div style="font-weight:700;font-size:13px;margin-bottom:12px;margin-top:4px">Cashback per Tier (%)</div>
                <div class="settings-row">
                    @foreach(['silver_cashback'=>['🥈','Silver','2'],'gold_cashback'=>['🥇','Gold','5'],'platinum_cashback'=>['💎','Platinum','10']] as $key=>$data)
                    <div class="form-group">
                        <label class="form-label">{{ $data[0] }} Cashback {{ $data[1] }}</label>
                        <div style="position:relative">
                            <input type="number" name="{{ $key }}" class="form-input" min="0" max="100" style="padding-right:36px" value="{{ $settings[$key]->value ?? $data[2] }}">
                            <span style="position:absolute;right:14px;top:50%;transform:translateY(-50%);color:var(--muted);font-weight:600">%</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Loyalty --}}
            <div class="settings-section" id="sec-loyalty">
                <div class="settings-section-title"><i class="ti ti-star" style="color:var(--sage)"></i> Loyalty Points</div>
                <div class="form-group">
                    <label class="form-label">Poin per Order Selesai</label>
                    <input type="number" name="points_per_order" class="form-input" min="0" value="{{ $settings['points_per_order']->value ?? 20 }}">
                    <div class="form-hint">Jumlah poin yang diberikan setiap kali customer menyelesaikan order</div>
                </div>
            </div>

            <button type="submit" class="btn-primary" style="padding:13px 32px;font-size:15px">
                <i class="ti ti-device-floppy"></i> Simpan Semua Pengaturan
            </button>
        </div>
    </div>
</form>

{{-- Push Notification --}}
<div style="background:var(--warm);border-radius:var(--r);border:1px solid var(--border);padding:24px;margin-top:20px">
    <div style="font-weight:700;font-size:15px;margin-bottom:14px;display:flex;align-items:center;gap:8px">
        <i class="ti ti-speakerphone" style="color:var(--sage)"></i> Kirim Push Notification ke Semua Customer
    </div>
    <div style="display:grid;grid-template-columns:1fr auto;gap:12px;align-items:end">
        <div>
            <div class="form-group" style="margin-bottom:10px">
                <label class="form-label">Judul Notifikasi</label>
                <input type="text" id="notif-title" class="form-input" placeholder="Promo Spesial Hari Ini! 🎉">
            </div>
            <div class="form-group" style="margin-bottom:0">
                <label class="form-label">Pesan</label>
                <textarea id="notif-message" class="form-textarea" rows="2" placeholder="Dapatkan diskon 20% untuk semua menu hari ini..."></textarea>
            </div>
        </div>
        <div>
            <div class="form-group" style="margin-bottom:10px">
                <label class="form-label">Tipe</label>
                <select id="notif-type" class="form-select">
                    <option value="info">Info</option>
                    <option value="success">Success</option>
                    <option value="error">Alert</option>
                </select>
            </div>
            <button onclick="sendBroadcast()" class="btn-primary" style="width:100%;justify-content:center">
                <i class="ti ti-send"></i> Kirim
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function switchSection(name, el) {
    document.querySelectorAll('.settings-section').forEach(s=>s.classList.remove('active'));
    document.querySelectorAll('.settings-nav-item').forEach(i=>i.classList.remove('active'));
    document.getElementById('sec-'+name).classList.add('active');
    el.classList.add('active');
}
function sendBroadcast() {
    const title   = document.getElementById('notif-title').value.trim();
    const message = document.getElementById('notif-message').value.trim();
    const type    = document.getElementById('notif-type').value;
    if(!title || !message) { showToast('Isi judul dan pesan dahulu','error'); return; }
    confirmAction('Kirim Broadcast?','Notifikasi akan dikirim ke semua customer.',()=>{
    ajax('/admin/notifications/broadcast','POST',{title,message,type})
    .then(d => { showToast(d.message,'success'); document.getElementById('notif-title').value=''; document.getElementById('notif-message').value=''; })
    .catch(()=>showToast('Gagal mengirim notifikasi','error'));
});
}
</script>
@endpush
