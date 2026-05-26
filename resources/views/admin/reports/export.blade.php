@extends('layouts.admin')
@section('title','Buat Laporan')
@section('page-title','Buat & Export Laporan')
@section('breadcrumb')
<i class="ti ti-chevron-right" style="font-size:12px"></i>
<a href="{{ route('admin.reports.index') }}" style="color:var(--muted);text-decoration:none">Laporan</a>
<i class="ti ti-chevron-right" style="font-size:12px"></i>
<span>Buat Baru</span>
@endsection

@push('styles')
<style>
.export-grid{display:grid;grid-template-columns:1fr 340px;gap:20px;align-items:start}
.form-card{background:var(--warm);border-radius:var(--r);border:1px solid var(--border);padding:24px}
.tipe-card{border:2px solid var(--border);border-radius:12px;padding:14px;cursor:pointer;transition:all .2s;display:flex;align-items:center;gap:12px}
.tipe-card:hover{border-color:var(--sage);background:rgba(90,124,101,.03)}
.tipe-card.selected{border-color:var(--sage);background:rgba(90,124,101,.06)}
.tipe-card input{display:none}
.filetype-btn{flex:1;padding:11px;border-radius:10px;border:2px solid var(--border);background:transparent;cursor:pointer;font-weight:600;font-size:13px;display:flex;align-items:center;justify-content:center;gap:8px;transition:all .2s;color:var(--muted)}
.filetype-btn.selected{border-color:var(--sage);color:var(--sage-dark);background:rgba(90,124,101,.06)}
@media(max-width:900px){.export-grid{grid-template-columns:1fr}}
</style>
@endpush

@section('content')
<div class="page-header">
    <div>
        <div class="page-title">Buat & Export Laporan</div>
        <div class="page-sub">Generate laporan dan export ke PDF atau Excel</div>
    </div>
    <a href="{{ route('admin.reports.index') }}" class="btn-secondary"><i class="ti ti-arrow-left"></i> Daftar Laporan</a>
</div>

<div class="export-grid">
    <div>
        <div class="form-card">
            <div style="font-weight:700;font-size:15px;margin-bottom:18px;padding-bottom:12px;border-bottom:1px solid var(--border)">
                <i class="ti ti-file-description" style="color:var(--sage)"></i> Detail Laporan
            </div>

            <div class="form-group">
                <label class="form-label">Judul Laporan <span style="color:#c0392b">*</span></label>
                <input type="text" id="f-judul" class="form-input" placeholder="Laporan Penjualan Minggu Ini">
            </div>

            <div class="form-group">
                <label class="form-label">Tipe Laporan <span style="color:#c0392b">*</span></label>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
                    @foreach([
                        ['sales','💰','Laporan Sales','Total penjualan & revenue'],
                        ['orders','🛒','Laporan Order','Semua transaksi order'],
                        ['wallet','💳','Laporan Wallet','Transaksi topup & wallet'],
                        ['membership','🏆','Laporan Membership','Data tier & cashback'],
                        ['customer','👤','Laporan Customer','Daftar & aktivitas user'],
                        ['custom','📋','Laporan Custom','Data gabungan semua'],
                    ] as $t)
                    <label class="tipe-card" onclick="selectTipe('{{ $t[0] }}',this)">
                        <input type="radio" name="tipe" value="{{ $t[0] }}">
                        <span style="font-size:22px">{{ $t[1] }}</span>
                        <div>
                            <div style="font-weight:700;font-size:13px">{{ $t[2] }}</div>
                            <div style="font-size:11px;color:var(--muted)">{{ $t[3] }}</div>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
                <div class="form-group">
                    <label class="form-label">Periode Mulai</label>
                    <input type="date" id="f-start" class="form-input" value="{{ date('Y-m-01') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Periode Akhir</label>
                    <input type="date" id="f-end" class="form-input" value="{{ date('Y-m-d') }}">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Format Export</label>
                <div style="display:flex;gap:10px">
                    <button type="button" class="filetype-btn selected" id="btn-excel" onclick="selectFile('excel')">
                        <i class="ti ti-table" style="font-size:18px;color:#16a34a"></i> Excel / CSV
                    </button>
                    <button type="button" class="filetype-btn" id="btn-pdf" onclick="selectFile('pdf')">
                        <i class="ti ti-file-type-pdf" style="font-size:18px;color:#c0392b"></i> PDF
                    </button>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Catatan (opsional)</label>
                <textarea id="f-catatan" class="form-textarea" rows="2" placeholder="Keterangan tambahan..."></textarea>
            </div>

            <button onclick="generateLaporan(this)" class="btn-primary" style="width:100%;justify-content:center;padding:13px;font-size:15px">
                <i class="ti ti-file-export"></i> Generate & Export Laporan
            </button>
        </div>
    </div>

    <div>
        <div class="form-card" style="margin-bottom:16px">
            <div style="font-weight:700;font-size:14px;margin-bottom:14px"><i class="ti ti-info-circle" style="color:var(--sage)"></i> Info</div>
            <div style="font-size:13px;color:var(--muted);line-height:1.8">
                <div style="margin-bottom:8px">📋 Laporan akan disimpan dan bisa didownload kapan saja</div>
                <div style="margin-bottom:8px">✅ Superadmin otomatis approved</div>
                <div style="margin-bottom:8px">⏳ Admin & Manager perlu approval dari Superadmin/Admin</div>
                <div>📁 File tersedia dalam format Excel (CSV) atau PDF</div>
            </div>
        </div>

        <div class="form-card">
            <div style="font-weight:700;font-size:14px;margin-bottom:12px"><i class="ti ti-user" style="color:var(--sage)"></i> Dibuat Oleh</div>
            <div style="display:flex;align-items:center;gap:10px">
                <img src="{{ auth()->user()->avatar_url }}" style="width:40px;height:40px;border-radius:50%;object-fit:cover" alt="">
                <div>
                    <div style="font-weight:600;font-size:14px">{{ auth()->user()->name }}</div>
                    <div style="font-size:12px;color:var(--muted)">{{ ucfirst(auth()->user()->role) }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="result-box" style="display:none;background:rgba(16,185,129,.06);border:1px solid rgba(16,185,129,.2);border-radius:var(--r);padding:18px 20px;margin-top:16px">
    <div style="font-weight:700;font-size:14px;color:#065f46;margin-bottom:10px"><i class="ti ti-circle-check" style="font-size:18px"></i> Laporan Berhasil Dibuat!</div>
    <div id="result-nomor" style="font-family:monospace;font-size:13px;margin-bottom:8px"></div>
    <div id="result-status" style="font-size:13px;color:var(--muted);margin-bottom:12px"></div>
    <div style="display:flex;gap:10px">
        <a id="result-download" href="#" class="btn-primary" style="font-size:13px;padding:9px 18px">
            <i class="ti ti-download"></i> Download Sekarang
        </a>
        <a href="{{ route('admin.reports.index') }}" class="btn-secondary" style="font-size:13px;padding:9px 18px">
            <i class="ti ti-list"></i> Lihat Semua Laporan
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script>
let selectedTipe = '';
let selectedFile = 'excel';

function selectTipe(val, el) {
    document.querySelectorAll('.tipe-card').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');
    el.querySelector('input').checked = true;
    selectedTipe = val;
}
function selectFile(val) {
    selectedFile = val;
    document.getElementById('btn-excel').classList.toggle('selected', val==='excel');
    document.getElementById('btn-pdf').classList.toggle('selected', val==='pdf');
}
function generateLaporan(btn) {
    const judul  = document.getElementById('f-judul').value.trim();
    const start  = document.getElementById('f-start').value;
    const end    = document.getElementById('f-end').value;
    const note   = document.getElementById('f-catatan').value;
    if(!judul)       { Swal.fire({icon:'warning',title:'Judul diperlukan',text:'Isi judul laporan dulu ya!',confirmButtonColor:'#3d5c47'}); return; }
    if(!selectedTipe){ Swal.fire({icon:'warning',title:'Pilih tipe laporan',text:'Pilih salah satu tipe laporan.',confirmButtonColor:'#3d5c47'}); return; }
    if(!start||!end) { Swal.fire({icon:'warning',title:'Periode diperlukan',text:'Tentukan periode laporan.',confirmButtonColor:'#3d5c47'}); return; }

    btn.disabled = true;
    btn.innerHTML = '<i class="ti ti-loader" style="animation:spin 1s linear infinite"></i> Generating...';

    ajax('/admin/reports/generate','POST',{
        judul, tipe:selectedTipe, periode_start:start, periode_end:end,
        file_type:selectedFile, catatan:note
    })
    .then(d => {
        btn.disabled = false;
        btn.innerHTML = '<i class="ti ti-file-export"></i> Generate & Export Laporan';
        if(d.success) {
            document.getElementById('result-box').style.display='block';
            document.getElementById('result-nomor').textContent = 'No. Laporan: '+d.nomor_laporan;
            document.getElementById('result-status').textContent = d.message;
            if(d.download_url) document.getElementById('result-download').href = d.download_url;
            showToast('Laporan berhasil dibuat!','success');
            document.getElementById('result-box').scrollIntoView({behavior:'smooth'});
        } else {
            Swal.fire({icon:'error',title:'Gagal',text:d.message||'Terjadi kesalahan',confirmButtonColor:'#3d5c47'});
        }
    })
    .catch(()=>{
        btn.disabled=false;
        btn.innerHTML='<i class="ti ti-file-export"></i> Generate & Export Laporan';
        Swal.fire({icon:'error',title:'Error',text:'Gagal menghubungi server',confirmButtonColor:'#3d5c47'});
    });
}
</script>
<style>@keyframes spin{to{transform:rotate(360deg)}}</style>
@endpush