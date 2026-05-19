@extends('layouts.admin')
@section('title','Edit Menu — '.$menu->name)
@section('page-title','Edit Menu')
@section('breadcrumb')
<i class="ti ti-chevron-right" style="font-size:12px"></i>
<a href="{{ route('admin.menus.index') }}" style="color:var(--muted);text-decoration:none">Menu</a>
<i class="ti ti-chevron-right" style="font-size:12px"></i>
<span>Edit</span>
@endsection

@push('styles')
<style>
.form-grid{display:grid;grid-template-columns:1fr 360px;gap:20px;align-items:start}
.form-section{background:var(--warm);border-radius:var(--r);border:1px solid var(--border);padding:24px;margin-bottom:16px}
.form-section-title{font-weight:700;font-size:14px;margin-bottom:18px;display:flex;align-items:center;gap:8px;padding-bottom:12px;border-bottom:1px solid var(--border)}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.toggle-row{display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--border)}
.toggle-row:last-child{border:none}
.toggle-switch{position:relative;width:42px;height:24px;cursor:pointer;flex-shrink:0}
.toggle-switch input{display:none}
.toggle-track{position:absolute;inset:0;border-radius:12px;background:var(--beige);border:1.5px solid var(--border);transition:background .2s}
.toggle-switch input:checked+.toggle-track{background:var(--sage);border-color:var(--sage)}
.toggle-thumb{position:absolute;top:3px;left:3px;width:16px;height:16px;border-radius:50%;background:#fff;box-shadow:0 1px 4px rgba(0,0,0,.15);transition:left .2s}
.toggle-switch input:checked~.toggle-thumb{left:21px}
.current-img{width:100%;border-radius:10px;object-fit:cover;max-height:200px;margin-bottom:10px}
@media(max-width:900px){.form-grid{grid-template-columns:1fr}}
</style>
@endpush

@section('content')
<div class="page-header">
    <div>
        <div class="page-title">Edit: {{ $menu->name }}</div>
        <div class="page-sub">ID #{{ $menu->id }} · {{ $menu->order_count }}× dipesan</div>
    </div>
    <a href="{{ route('admin.menus.index') }}" class="btn-secondary"><i class="ti ti-arrow-left"></i> Kembali</a>
</div>

@if($errors->any())
<div style="background:rgba(220,80,60,.06);border:1px solid rgba(220,80,60,.2);border-radius:var(--r);padding:14px 18px;margin-bottom:18px;font-size:13px;color:#c0392b">
    @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
</div>
@endif

<form action="{{ route('admin.menus.update',$menu->id) }}" method="POST" enctype="multipart/form-data">
    @csrf @method('PUT')
    <div class="form-grid">
        <div>
            <div class="form-section">
                <div class="form-section-title"><i class="ti ti-info-circle" style="color:var(--sage)"></i> Informasi Menu</div>
                <div class="form-group">
                    <label class="form-label">Nama Menu <span style="color:#c0392b">*</span></label>
                    <input type="text" name="name" class="form-input" value="{{ old('name',$menu->name) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Kategori</label>
                    <select name="category_id" class="form-select" required>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id',$menu->category_id)==$cat->id?'selected':'' }}>{{ $cat->icon }} {{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" class="form-textarea" rows="3">{{ old('description',$menu->description) }}</textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Harga Normal <span style="color:#c0392b">*</span></label>
                        <div style="position:relative">
                            <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--muted);font-size:13px;font-weight:600">Rp</span>
                            <input type="number" name="price" class="form-input" style="padding-left:36px" value="{{ old('price',$menu->price) }}" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Harga Diskon</label>
                        <div style="position:relative">
                            <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--muted);font-size:13px;font-weight:600">Rp</span>
                            <input type="number" name="discount_price" class="form-input" style="padding-left:36px" value="{{ old('discount_price',$menu->discount_price) }}">
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Stok</label>
                        <input type="number" name="stock" class="form-input" value="{{ old('stock',$menu->stock) }}" min="0" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Waktu Persiapan</label>
                        <input type="text" name="preparation_time" class="form-input" value="{{ old('preparation_time',$menu->preparation_time) }}">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Kalori (kal)</label>
                        <input type="number" name="calories" class="form-input" value="{{ old('calories',$menu->calories) }}" min="0">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tags</label>
                        <input type="text" name="tags" class="form-input" value="{{ old('tags', is_array($menu->tags) ? implode(', ',$menu->tags) : $menu->tags) }}">
                    </div>
                </div>
            </div>
        </div>

        <div>
            <div class="form-section">
                <div class="form-section-title"><i class="ti ti-photo" style="color:var(--sage)"></i> Foto Menu</div>
                @if($menu->image)
                <img id="current-img" src="{{ asset('storage/'.$menu->image) }}" class="current-img" alt="{{ $menu->name }}">
                @endif
                <img id="preview-img" src="" style="width:100%;border-radius:10px;object-fit:cover;max-height:200px;display:none;margin-bottom:10px" alt="Preview">
                <div style="border:2px dashed var(--border);border-radius:12px;padding:16px;text-align:center;cursor:pointer;background:var(--beige);transition:all .2s"
                    onclick="document.getElementById('image-input').click()" onmouseover="this.style.borderColor='var(--sage)'" onmouseout="this.style.borderColor='var(--border)'">
                    <i class="ti ti-replace" style="font-size:22px;color:var(--sage);display:block;margin-bottom:6px"></i>
                    <div style="font-size:13px;font-weight:600;margin-bottom:2px">Ganti Foto</div>
                    <div style="font-size:11px;color:var(--muted)">Biarkan kosong jika tidak diubah</div>
                    <input type="file" id="image-input" name="image" accept="image/*" style="display:none" onchange="previewNew(this)">
                </div>
            </div>

            <div class="form-section">
                <div class="form-section-title"><i class="ti ti-settings" style="color:var(--sage)"></i> Pengaturan</div>
                @foreach([
                    ['is_available','Tampilkan Menu','Menu terlihat oleh customer'],
                    ['is_best_seller','Tandai Best Seller','Tampilkan badge best seller'],
                    ['is_featured','Tampilkan di Featured','Muncul di halaman utama'],
                ] as $tog)
                <div class="toggle-row">
                    <div>
                        <div style="font-size:13px;font-weight:500">{{ $tog[1] }}</div>
                        <div style="font-size:11px;color:var(--muted)">{{ $tog[2] }}</div>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="{{ $tog[0] }}" value="1" {{ old($tog[0], $menu->{$tog[0]}) ? 'checked' : '' }}>
                        <span class="toggle-track"></span>
                        <span class="toggle-thumb"></span>
                    </label>
                </div>
                @endforeach
            </div>

            <div style="background:var(--beige);border-radius:12px;padding:14px;margin-bottom:16px;font-size:12px;color:var(--muted)">
                <div style="display:flex;justify-content:space-between;margin-bottom:5px">
                    <span>Total dipesan</span><strong>{{ number_format($menu->order_count) }}×</strong>
                </div>
                <div style="display:flex;justify-content:space-between;margin-bottom:5px">
                    <span>Total ulasan</span><strong>{{ $menu->review_count }}</strong>
                </div>
                <div style="display:flex;justify-content:space-between">
                    <span>Rating rata-rata</span>
                    <strong style="color:#f59e0b">⭐ {{ number_format($menu->rating,1) }}</strong>
                </div>
            </div>

            <button type="submit" class="btn-primary" style="width:100%;justify-content:center;padding:13px;font-size:15px">
                <i class="ti ti-device-floppy"></i> Simpan Perubahan
            </button>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
function previewNew(input) {
    if (!input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
        const cur = document.getElementById('current-img');
        const prev = document.getElementById('preview-img');
        if(cur) cur.style.display = 'none';
        prev.src = e.target.result;
        prev.style.display = 'block';
    };
    reader.readAsDataURL(input.files[0]);
}
</script>
@endpush