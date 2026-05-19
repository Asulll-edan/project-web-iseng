@extends('layouts.admin')
@section('title','Kelola Menu')
@section('page-title','Kelola Menu')

@push('styles')
<style>
.filter-bar{background:var(--warm);border-radius:var(--r);border:1px solid var(--border);padding:14px 18px;display:flex;gap:10px;align-items:center;flex-wrap:wrap;margin-bottom:20px}
.search-wrap{position:relative;flex:1;min-width:180px}
.search-wrap i{position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--muted);font-size:16px;pointer-events:none}
.search-inp{width:100%;padding:9px 14px 9px 38px;border-radius:10px;border:1.5px solid var(--border);background:var(--beige);color:var(--text);font-size:13px;font-family:inherit;outline:none;transition:border-color .2s}
.search-inp:focus{border-color:var(--sage);background:#fff}
.menu-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:16px}
.menu-admin-card{background:var(--warm);border-radius:var(--r);border:1px solid var(--border);overflow:hidden;transition:all .2s;position:relative}
.menu-admin-card:hover{box-shadow:var(--shadow-md);transform:translateY(-2px)}
.menu-thumb{width:100%;height:160px;object-fit:cover;background:var(--beige);display:flex;align-items:center;justify-content:center;overflow:hidden}
.menu-thumb img{width:100%;height:100%;object-fit:cover;transition:transform .3s}
.menu-admin-card:hover .menu-thumb img{transform:scale(1.04)}
.menu-body{padding:14px}
.menu-name{font-weight:700;font-size:14px;margin-bottom:4px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.menu-price{font-weight:700;font-size:15px;color:var(--sage-dark)}
.toggle-switch{position:relative;width:38px;height:22px;cursor:pointer;flex-shrink:0}
.toggle-switch input{display:none}
.toggle-track{position:absolute;inset:0;border-radius:11px;background:var(--beige);border:1.5px solid var(--border);transition:background .2s}
.toggle-switch input:checked+.toggle-track{background:var(--sage);border-color:var(--sage)}
.toggle-thumb{position:absolute;top:2px;left:2px;width:16px;height:16px;border-radius:50%;background:#fff;box-shadow:0 1px 4px rgba(0,0,0,.15);transition:left .2s}
.toggle-switch input:checked~.toggle-thumb{left:18px}
.deleted-overlay{position:absolute;inset:0;background:rgba(255,255,255,.8);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:#c0392b;backdrop-filter:blur(2px)}
</style>
@endpush

@section('content')
<div class="page-header">
    <div>
        <div class="page-title">Kelola Menu</div>
        <div class="page-sub">{{ $menus->total() }} menu terdaftar</div>
    </div>
    <a href="{{ route('admin.menus.create') }}" class="btn-primary">
        <i class="ti ti-plus"></i> Tambah Menu
    </a>
</div>

<div class="filter-bar">
    <div class="search-wrap">
        <i class="ti ti-search"></i>
        <input type="text" class="search-inp" placeholder="Cari nama menu..." value="{{ request('search') }}"
            onchange="window.location='?search='+this.value+'&category={{ request('category') }}'">
    </div>
    <select class="form-select" style="width:auto" onchange="window.location='?category='+this.value+'&search={{ request('search') }}'">
        <option value="">Semua Kategori</option>
        @foreach($categories as $cat)
        <option value="{{ $cat->id }}" {{ request('category')==$cat->id?'selected':'' }}>{{ $cat->name }}</option>
        @endforeach
    </select>
    <div style="font-size:13px;color:var(--muted);margin-left:auto">
        Total: {{ $menus->total() }} menu
    </div>
</div>

<div class="menu-grid">
    @forelse($menus as $menu)
    <div class="menu-admin-card">
        @if($menu->trashed())
        <div class="deleted-overlay"><i class="ti ti-trash" style="font-size:16px;margin-right:6px"></i> Dihapus</div>
        @endif

        <div class="menu-thumb">
            @if($menu->image)
            <img src="{{ asset('storage/'.$menu->image) }}" alt="{{ $menu->name }}" loading="lazy">
            @else
            <span style="font-size:44px">🍱</span>
            @endif
        </div>

        <div class="menu-body">
            <div style="display:flex;gap:5px;flex-wrap:wrap;margin-bottom:8px">
                @if($menu->is_best_seller)
                <span class="badge badge-amber" style="font-size:10px"><i class="ti ti-flame" style="font-size:10px"></i> Best Seller</span>
                @endif
                @if($menu->is_featured)
                <span class="badge badge-sage" style="font-size:10px"><i class="ti ti-sparkles" style="font-size:10px"></i> Featured</span>
                @endif
            </div>

            <div class="menu-name" title="{{ $menu->name }}">{{ $menu->name }}</div>
            <div style="font-size:12px;color:var(--muted);margin-bottom:8px">{{ $menu->category->name ?? '-' }}</div>

            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
                <div>
                    <div class="menu-price">Rp {{ number_format($menu->price,0,',','.') }}</div>
                    @if($menu->discount_price)
                    <div style="font-size:11px;color:var(--muted);text-decoration:line-through">Rp {{ number_format($menu->discount_price,0,',','.') }}</div>
                    @endif
                </div>
                <div style="text-align:right">
                    <div style="font-size:12px;color:var(--muted)">Stok: <strong>{{ $menu->stock }}</strong></div>
                    <div style="display:flex;align-items:center;gap:3px;margin-top:2px">
                        <i class="ti ti-star-filled" style="font-size:12px;color:#f59e0b"></i>
                        <span style="font-size:12px;font-weight:600">{{ number_format($menu->rating,1) }}</span>
                    </div>
                </div>
            </div>

            <div style="display:flex;align-items:center;justify-content:space-between;border-top:1px solid var(--border);padding-top:10px">
                <div style="display:flex;align-items:center;gap:6px;font-size:12px;color:var(--muted)">
                    <label class="toggle-switch" title="{{ $menu->is_available ? 'Tersedia' : 'Tidak tersedia' }}">
                        <input type="checkbox" {{ $menu->is_available ? 'checked' : '' }} onchange="toggleMenu({{ $menu->id }},this)" {{ $menu->trashed() ? 'disabled' : '' }}>
                        <span class="toggle-track"></span>
                        <span class="toggle-thumb"></span>
                    </label>
                    <span id="avail-{{ $menu->id }}">{{ $menu->is_available ? 'Aktif' : 'Nonaktif' }}</span>
                </div>
                <div style="display:flex;gap:6px">
                    <a href="{{ route('admin.menus.edit',$menu->id) }}" class="btn-icon btn-icon-sage" title="Edit">
                        <i class="ti ti-edit" style="font-size:14px"></i>
                    </a>
                    @if(!$menu->trashed())
                    <form action="{{ route('admin.menus.destroy',$menu->id) }}" method="POST" onsubmit="return confirm('Hapus menu ini?')">
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
        <div style="font-size:48px;margin-bottom:14px">🍱</div>
        <div style="font-weight:700;font-size:16px;margin-bottom:8px">Belum ada menu</div>
        <a href="{{ route('admin.menus.create') }}" class="btn-primary">Tambah Menu Pertama</a>
    </div>
    @endforelse
</div>

<div style="margin-top:24px">{{ $menus->links() }}</div>
@endsection

@push('scripts')
<script>
function toggleMenu(id, el) {
    ajax('/admin/menus/'+id+'/toggle','POST')
    .then(d => {
        const lbl = document.getElementById('avail-'+id);
        if(lbl) lbl.textContent = d.is_available ? 'Aktif' : 'Nonaktif';
        showToast(d.is_available ? 'Menu diaktifkan' : 'Menu dinonaktifkan', 'info');
    })
    .catch(()=>{ el.checked = !el.checked; showToast('Gagal update','error'); });
}
</script>
@endpush