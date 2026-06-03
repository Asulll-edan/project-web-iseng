@extends('layouts.admin')
@section('title','Kelola Banner')
@section('page-title','Banner Slider')

@push('styles')
<style>
.banner-card{background:var(--warm);border-radius:var(--r);border:1px solid var(--border);overflow:hidden;transition:all .2s}
.banner-card:hover{box-shadow:var(--shadow-md)}
.banner-thumb{width:100%;height:150px;object-fit:cover;background:linear-gradient(135deg,var(--sage-dark),var(--sage));display:flex;align-items:center;justify-content:center}
.banner-thumb img{width:100%;height:100%;object-fit:cover}
.modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:500;display:none;align-items:center;justify-content:center;padding:20px}
.modal-overlay.show{display:flex}
.modal-box{background:var(--warm);border-radius:20px;border:1px solid var(--border);padding:28px;width:100%;max-width:480px;max-height:90vh;overflow-y:auto}
.modal-title{font-weight:700;font-size:17px;margin-bottom:20px;display:flex;align-items:center;justify-content:space-between}
</style>
@endpush

@section('content')
<div class="page-header">
    <div>
        <div class="page-title">Banner Slider</div>
        <div class="page-sub">{{ $banners->count() }} banner terdaftar · Tampil di halaman utama</div>
    </div>
    <button onclick="openModal()" class="btn-primary">
        <i class="ti ti-plus"></i> Tambah Banner
    </button>
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px">
    @forelse($banners as $banner)
    <div class="banner-card">
        <div class="banner-thumb">
            @if($banner->image && !$banner->trashed())
            <img src="{{ asset('storage/'.$banner->image) }}" alt="{{ $banner->title }}">
            @else
            <div style="text-align:center;color:rgba(255,255,255,.7)">
                <i class="ti ti-photo" style="font-size:36px;display:block;margin-bottom:6px"></i>
                <div style="font-size:12px">No Image</div>
            </div>
            @endif
        </div>
        <div style="padding:14px 16px">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px;margin-bottom:8px">
                <div style="font-weight:700;font-size:14px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;flex:1">{{ $banner->title }}</div>
                <span class="badge {{ $banner->is_active ? 'badge-green' : 'badge-gray' }}" style="flex-shrink:0;font-size:10px">{{ $banner->is_active ? 'Aktif' : 'Nonaktif' }}</span>
            </div>
            @if($banner->description)
            <div style="font-size:12px;color:var(--muted);margin-bottom:8px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $banner->description }}</div>
            @endif
            @if($banner->link)
            <div style="font-size:11px;color:var(--sage);margin-bottom:8px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                <i class="ti ti-link" style="font-size:12px"></i> {{ $banner->link }}
            </div>
            @endif
            <div style="display:flex;align-items:center;justify-content:space-between;border-top:1px solid var(--border);padding-top:10px">
                <span style="font-size:12px;color:var(--muted)">Sort: {{ $banner->sort_order }}</span>
                <div style="display:flex;gap:6px">
                    <button onclick="editBanner({{ $banner->id }})" class="btn-icon btn-icon-sage" title="Edit">
                        <i class="ti ti-edit" style="font-size:14px"></i>
                    </button>
                    @if(!$banner->trashed())
                    <form action="{{ route('admin.banners.destroy',$banner->id) }}" method="POST" onsubmit="return false" onclick="this.closest('form').submit()">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-icon btn-icon-red" title="Hapus">
                            <i class="ti ti-trash" style="font-size:14px"></i>
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @empty
    <div style="grid-column:1/-1;text-align:center;padding:60px;background:var(--warm);border-radius:var(--r);border:1px solid var(--border)">
        <i class="ti ti-photo" style="font-size:48px;color:var(--muted);display:block;margin-bottom:12px"></i>
        <div style="font-weight:700;font-size:16px;margin-bottom:8px">Belum ada banner</div>
        <button onclick="openModal()" class="btn-primary">Tambah Banner Pertama</button>
    </div>
    @endforelse
</div>

{{-- Add/Edit Modal --}}
<div class="modal-overlay" id="banner-modal" onclick="closeModal()">
    <div class="modal-box" onclick="event.stopPropagation()">
        <div class="modal-title">
            <span id="modal-title">Tambah Banner</span>
            <button onclick="closeModal()" style="background:none;border:none;cursor:pointer;color:var(--muted);font-size:20px"><i class="ti ti-x"></i></button>
        </div>
        <form id="banner-form" action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div id="method-field"></div>
            <div class="form-group">
                <label class="form-label">Judul Banner <span style="color:#c0392b">*</span></label>
                <input type="text" name="title" id="f-title" class="form-input" required>
            </div>
            <div class="form-group">
                <label class="form-label">Gambar <span id="img-hint" style="color:var(--muted);font-weight:400">(wajib)</span></label>
                <input type="file" name="image" id="f-image" accept="image/*" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Link URL</label>
                <input type="text" name="link" id="f-link" class="form-input" placeholder="/menu atau /wallet">
            </div>
            <div class="form-group">
                <label class="form-label">Deskripsi</label>
                <textarea name="description" id="f-desc" class="form-textarea" rows="2"></textarea>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                <div class="form-group">
                    <label class="form-label">Mulai Aktif</label>
                    <input type="datetime-local" name="start_at" id="f-start" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Berakhir</label>
                    <input type="datetime-local" name="end_at" id="f-end" class="form-input">
                </div>
            </div>
            <div class="form-group">
                <label style="display:flex;align-items:center;gap:10px;cursor:pointer;font-size:13px;font-weight:600">
                    <input type="checkbox" name="is_active" value="1" id="f-active" style="accent-color:var(--sage);width:16px;height:16px"> Tampilkan Banner
                </label>
            </div>
            <div style="display:flex;gap:10px">
                <button type="submit" class="btn-primary" style="flex:1;justify-content:center">
                    <i class="ti ti-device-floppy"></i> Simpan
                </button>
                <button type="button" onclick="closeModal()" class="btn-secondary">Batal</button>
            </div>
        </form>
    </div>
</div>

{{-- Banners data for JS edit --}}
<script>
const BANNERS = @json($banners->keyBy('id'));

function openModal() {
    document.getElementById('modal-title').textContent = 'Tambah Banner';
    document.getElementById('banner-form').action = '{{ route("admin.banners.store") }}';
    document.getElementById('method-field').innerHTML = '';
    document.getElementById('f-title').value = '';
    document.getElementById('f-link').value = '';
    document.getElementById('f-desc').value = '';
    document.getElementById('f-start').value = '';
    document.getElementById('f-end').value = '';
    document.getElementById('f-active').checked = true;
    document.getElementById('img-hint').textContent = '(wajib)';
    document.getElementById('banner-modal').classList.add('show');
}

function editBanner(id) {
    const b = BANNERS[id];
    if(!b) return;
    document.getElementById('modal-title').textContent = 'Edit Banner';
    document.getElementById('banner-form').action = '/admin/banners/'+id;
    document.getElementById('method-field').innerHTML = '<input type="hidden" name="_method" value="PUT">';
    document.getElementById('f-title').value = b.title || '';
    document.getElementById('f-link').value = b.link || '';
    document.getElementById('f-desc').value = b.description || '';
    document.getElementById('f-start').value = b.start_at ? b.start_at.replace(' ','T') : '';
    document.getElementById('f-end').value = b.end_at ? b.end_at.replace(' ','T') : '';
    document.getElementById('f-active').checked = b.is_active;
    document.getElementById('img-hint').textContent = '(opsional, biarkan kosong jika tidak diubah)';
    document.getElementById('banner-modal').classList.add('show');
}

function closeModal() {
    document.getElementById('banner-modal').classList.remove('show');
}
</script>
@endsection
