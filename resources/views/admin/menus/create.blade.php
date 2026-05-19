@extends('layouts.admin')
@section('title','Tambah Menu')
@section('page-title','Tambah Menu Baru')
@section('breadcrumb')
<i class="ti ti-chevron-right" style="font-size:12px"></i>
<a href="{{ route('admin.menus.index') }}" style="color:var(--muted);text-decoration:none">Menu</a>
<i class="ti ti-chevron-right" style="font-size:12px"></i>
<span>Tambah</span>
@endsection

@push('styles')
<style>
.form-grid{display:grid;grid-template-columns:1fr 360px;gap:20px;align-items:start}
.form-section{background:var(--warm);border-radius:var(--r);border:1px solid var(--border);padding:24px;margin-bottom:16px}
.form-section-title{font-weight:700;font-size:14px;color:var(--text);margin-bottom:18px;display:flex;align-items:center;gap:8px;padding-bottom:12px;border-bottom:1px solid var(--border)}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.upload-zone{border:2px dashed var(--border);border-radius:12px;padding:28px 20px;text-align:center;cursor:pointer;transition:all .2s;background:var(--beige);min-height:180px;display:flex;flex-direction:column;align-items:center;justify-content:center}
.upload-zone:hover{border-color:var(--sage);background:rgba(90,124,101,.04)}
.upload-zone.dragover{border-color:var(--sage);background:rgba(90,124,101,.06)}
.preview-img{max-height:200px;border-radius:8px;object-fit:cover;width:100%;display:none;margin-bottom:10px}
.toggle-row{display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--border)}
.toggle-row:last-child{border:none}
.toggle-label{font-size:13px;font-weight:500;color:var(--text)}
.toggle-sub{font-size:11px;color:var(--muted);margin-top:2px}
.toggle-switch{position:relative;width:42px;height:24px;cursor:pointer;flex-shrink:0}
.toggle-switch input{display:none}
.toggle-track{position:absolute;inset:0;border-radius:12px;background:var(--beige);border:1.5px solid var(--border);transition:background .2s}
.toggle-switch input:checked+.toggle-track{background:var(--sage);border-color:var(--sage)}
.toggle-thumb{position:absolute;top:3px;left:3px;width:16px;height:16px;border-radius:50%;background:#fff;box-shadow:0 1px 4px rgba(0,0,0,.15);transition:left .2s}
.toggle-switch input:checked~.toggle-thumb{left:21px}
@media(max-width:900px){.form-grid{grid-template-columns:1fr}}
</style>
@endpush

@section('content')
<div class="page-header">
    <div>
        <div class="page-title">Tambah Menu Baru</div>
        <div class="page-sub">Isi detail menu restoran</div>
    </div>
    <a href="{{ route('admin.menus.index') }}" class="btn-secondary">
        <i class="ti ti-arrow-left"></i> Kembali
    </a>
</div>

@if($errors->any())
<div style="background:rgba(220,80,60,.06);border:1px solid rgba(220,80,60,.2);border-radius:var(--r);padding:14px 18px;margin-bottom:18px;font-size:13px;color:#c0392b">
    @foreach($errors->all() as $e)<div style="margin-bottom:3px">• {{ $e }}</div>@endforeach
</div>
@endif

<form action="{{ route('admin.menus.store') }}" method="POST" enctype="multipart/form-data" id="menu-form">
    @csrf
    <div class="form-grid">
        {{-- Left: main info --}}
        <div>
            <div class="form-section">
                <div class="form-section-title"><i class="ti ti-info-circle" style="color:var(--sage)"></i> Informasi Menu</div>
                <div class="form-group">
                    <label class="form-label">Nama Menu <span style="color:#c0392b">*</span></label>
                    <input type="text" name="name" class="form-input" placeholder="contoh: Nasi Ayam Geprek Anak Sekolah" value="{{ old('name') }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Kategori <span style="color:#c0392b">*</span></label>
                    <select name="category_id" class="form-select" required>
                        <option value="">— Pilih Kategori —</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id')==$cat->id?'selected':'' }}>{{ $cat->icon }} {{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" class="form-textarea" rows="3" placeholder="Deskripsi menu yang menggugah selera...">{{ old('description') }}</textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Harga Normal <span style="color:#c0392b">*</span></label>
                        <div style="position:relative">
                            <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--muted);font-size:13px;font-weight:600">Rp</span>
                            <input type="number" name="price" class="form-input" style="padding-left:36px" placeholder="18000" min="0" value="{{ old('price') }}" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Harga Diskon <span style="color:var(--muted);font-weight:400">(opsional)</span></label>
                        <div style="position:relative">
                            <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--muted);font-size:13px;font-weight:600">Rp</span>
                            <input type="number" name="discount_price" class="form-input" style="padding-left:36px" placeholder="15000" min="0" value="{{ old('discount_price') }}">
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Stok <span style="color:#c0392b">*</span></label>
                        <input type="number" name="stock" class="form-input" placeholder="50" min="0" value="{{ old('stock',50) }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Waktu Persiapan</label>
                        <input type="text" name="preparation_time" class="form-input" placeholder="10-15 menit" value="{{ old('preparation_time') }}">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Kalori (kal)</label>
                        <input type="number" name="calories" class="form-input" placeholder="500" min="0" value="{{ old('calories') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tags</label>
                        <input type="text" name="tags" class="form-input" placeholder="pedas, gurih, ayam" value="{{ old('tags') }}">
                        <div class="form-hint">Pisahkan dengan koma</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: image + settings --}}
        <div>
            <div class="form-section">
                <div class="form-section-title"><i class="ti ti-photo" style="color:var(--sage)"></i> Foto Menu</div>
                <img id="preview-img" class="preview-img" src="" alt="Preview">
                <div class="upload-zone" id="upload-zone" onclick="document.getElementById('image-input').click()"
                    ondragover="event.preventDefault();this.classList.add('dragover')"
                    ondragleave="this.classList.remove('dragover')"
                    ondrop="handleDrop(event)">
                    <i class="ti ti-cloud-upload" id="upload-icon" style="font-size:38px;color:var(--sage);margin-bottom:8px;display:block"></i>
                    <div id="upload-text" style="font-weight:600;font-size:14px;margin-bottom:4px">Klik atau drag foto</div>
                    <div style="font-size:12px;color:var(--muted)">JPG, PNG, WebP · Maks 3MB</div>
                    <input type="file" id="image-input" name="image" accept="image/*" style="display:none" onchange="previewImage(this)">
                </div>
            </div>

            <div class="form-section">
                <div class="form-section-title"><i class="ti ti-settings" style="color:var(--sage)"></i> Pengaturan</div>
                @foreach([
                    ['is_available','Tampilkan Menu','Menu akan terlihat oleh customer',true],
                    ['is_best_seller','Tandai Best Seller','Tampilkan badge best seller',false],
                    ['is_featured','Tampilkan di Featured','Muncul di halaman utama',false],
                ] as $tog)
                <div class="toggle-row">
                    <div>
                        <div class="toggle-label">{{ $tog[1] }}</div>
                        <div class="toggle-sub">{{ $tog[2] }}</div>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="{{ $tog[0] }}" value="1" {{ (old($tog[0], $tog[3] ? 1 : 0) ? 'checked' : '') }}>
                        <span class="toggle-track"></span>
                        <span class="toggle-thumb"></span>
                    </label>
                </div>
                @endforeach
            </div>

            <button type="submit" class="btn-primary" style="width:100%;justify-content:center;padding:13px;font-size:15px">
                <i class="ti ti-device-floppy"></i> Simpan Menu
            </button>
            <a href="{{ route('admin.menus.index') }}" class="btn-secondary" style="width:100%;justify-content:center;margin-top:10px;display:flex">
                Batal
            </a>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
function previewImage(input) {
    if (!input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
        const img = document.getElementById('preview-img');
        img.src = e.target.result;
        img.style.display = 'block';
        document.getElementById('upload-icon').style.display = 'none';
        document.getElementById('upload-text').textContent = input.files[0].name;
    };
    reader.readAsDataURL(input.files[0]);
}
function handleDrop(e) {
    e.preventDefault();
    document.getElementById('upload-zone').classList.remove('dragover');
    const file = e.dataTransfer.files[0];
    if (!file || !file.type.startsWith('image/')) return;
    const inp = document.getElementById('image-input');
    const dt = new DataTransfer();
    dt.items.add(file);
    inp.files = dt.files;
    previewImage(inp);
}
</script>
@endpush