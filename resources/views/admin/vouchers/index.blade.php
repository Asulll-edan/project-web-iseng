@extends('layouts.admin')
@section('title','Kelola Voucher')
@section('page-title','Voucher & Promo')

@push('styles')
<style>
.modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:500;display:none;align-items:center;justify-content:center;padding:20px}
.modal-overlay.show{display:flex}
.modal-box{background:var(--warm);border-radius:20px;border:1px solid var(--border);padding:28px;width:100%;max-width:480px;max-height:90vh;overflow-y:auto}
.voucher-card{background:var(--warm);border-radius:var(--r);border:2px dashed var(--border);padding:18px;position:relative;overflow:hidden;transition:all .2s}
.voucher-card:hover{border-color:var(--sage);box-shadow:var(--shadow-sm)}
.voucher-card.inactive{opacity:.6}
.voucher-dots{position:absolute;left:-10px;top:50%;transform:translateY(-50%);width:20px;height:20px;border-radius:50%;background:var(--beige);border:2px dashed var(--border)}
.voucher-dots2{position:absolute;right:-10px;top:50%;transform:translateY(-50%);width:20px;height:20px;border-radius:50%;background:var(--beige);border:2px dashed var(--border)}
.voucher-code{font-family:monospace;font-size:20px;font-weight:700;color:var(--sage-dark);letter-spacing:.08em}
</style>
@endpush

@section('content')
<div class="page-header">
    <div>
        <div class="page-title">Voucher & Promo</div>
        <div class="page-sub">{{ $vouchers->total() }} voucher terdaftar</div>
    </div>
    <button onclick="openModal()" class="btn-primary">
        <i class="ti ti-plus"></i> Buat Voucher
    </button>
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:14px">
    @forelse($vouchers as $voucher)
    <div class="voucher-card {{ !$voucher->is_active || $voucher->trashed() ? 'inactive' : '' }}">
        <div class="voucher-dots"></div>
        <div class="voucher-dots2"></div>
        <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:10px">
            <div>
                <div class="voucher-code">{{ $voucher->code }}</div>
                <div style="font-size:12px;font-weight:600;color:var(--text);margin-top:2px">{{ $voucher->name }}</div>
            </div>
            <div style="text-align:right">
                <div style="font-size:22px;font-weight:700;color:var(--sage-dark)">
                    {{ $voucher->type === 'percent' ? $voucher->value.'%' : 'Rp '.number_format($voucher->value,0,',','.') }}
                </div>
                <div style="font-size:11px;color:var(--muted)">{{ $voucher->type === 'percent' ? 'Diskon Persen' : 'Diskon Nominal' }}</div>
            </div>
        </div>

        <div style="border-top:1px dashed var(--border);padding-top:10px;margin-bottom:10px">
            @if($voucher->min_order)
            <div style="font-size:12px;color:var(--muted);margin-bottom:4px">Min. order: Rp {{ number_format($voucher->min_order,0,',','.') }}</div>
            @endif
            @if($voucher->max_discount)
            <div style="font-size:12px;color:var(--muted);margin-bottom:4px">Maks. diskon: Rp {{ number_format($voucher->max_discount,0,',','.') }}</div>
            @endif
            <div style="font-size:12px;color:var(--muted)">Terpakai: {{ $voucher->used_count }} / {{ $voucher->max_usage ?? '∞' }}</div>
        </div>

        @if($voucher->start_at || $voucher->end_at)
        <div style="font-size:11px;color:var(--muted);margin-bottom:10px">
            <i class="ti ti-calendar" style="font-size:12px"></i>
            {{ $voucher->start_at ? $voucher->start_at->format('d M') : '∞' }} —
            {{ $voucher->end_at ? $voucher->end_at->format('d M Y') : '∞' }}
        </div>
        @endif

        <div style="display:flex;align-items:center;justify-content:space-between">
            <span class="badge {{ $voucher->is_active && !$voucher->trashed() ? ($voucher->isValid() ? 'badge-green' : 'badge-amber') : 'badge-red' }}">
                {{ $voucher->trashed() ? 'Dihapus' : ($voucher->is_active ? ($voucher->isValid() ? 'Aktif' : 'Expired') : 'Nonaktif') }}
            </span>
            <div style="display:flex;gap:6px">
                @if(!$voucher->trashed())
                <button onclick="toggleVoucher({{ $voucher->id }},this)" class="btn-icon {{ $voucher->is_active ? 'btn-icon-red' : 'btn-icon-sage' }}" title="{{ $voucher->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                    <i class="ti {{ $voucher->is_active ? 'ti-eye-off' : 'ti-eye' }}" style="font-size:14px"></i>
                </button>
                <form action="{{ route('admin.vouchers.destroy',$voucher->id) }}" method="POST" onsubmit="return confirm('Hapus voucher ini?')" style="display:inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-icon btn-icon-red" title="Hapus">
                        <i class="ti ti-trash" style="font-size:14px"></i>
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div style="grid-column:1/-1;text-align:center;padding:60px;background:var(--warm);border-radius:var(--r);border:1px solid var(--border)">
        <i class="ti ti-ticket" style="font-size:48px;color:var(--muted);display:block;margin-bottom:12px"></i>
        <div style="font-weight:700;margin-bottom:8px">Belum ada voucher</div>
        <button onclick="openModal()" class="btn-primary">Buat Voucher Pertama</button>
    </div>
    @endforelse
</div>

{{-- Pagination --}}
<div style="margin-top:20px">{{ $vouchers->links() }}</div>

{{-- Create Modal --}}
<div class="modal-overlay" id="voucher-modal" onclick="closeModal()">
    <div class="modal-box" onclick="event.stopPropagation()">
        <div style="font-weight:700;font-size:17px;margin-bottom:20px;display:flex;align-items:center;justify-content:space-between">
            Buat Voucher Baru
            <button onclick="closeModal()" style="background:none;border:none;cursor:pointer;color:var(--muted);font-size:20px"><i class="ti ti-x"></i></button>
        </div>
        <form action="{{ route('admin.vouchers.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Kode Voucher <span style="color:#c0392b">*</span></label>
                <input type="text" name="code" class="form-input" placeholder="HEMAT20" style="text-transform:uppercase" required>
                <div class="form-hint">Huruf kapital & angka, tanpa spasi</div>
            </div>
            <div class="form-group">
                <label class="form-label">Nama / Deskripsi Voucher</label>
                <input type="text" name="name" class="form-input" placeholder="Diskon 20% untuk semua menu" required>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                <div class="form-group">
                    <label class="form-label">Tipe Diskon</label>
                    <select name="type" class="form-select" id="v-type" onchange="toggleVoucherType()">
                        <option value="percent">Persen (%)</option>
                        <option value="fixed">Nominal (Rp)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Nilai Diskon</label>
                    <input type="number" name="value" class="form-input" placeholder="20" min="0" required>
                </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                <div class="form-group">
                    <label class="form-label">Min. Order (Rp)</label>
                    <input type="number" name="min_order" class="form-input" placeholder="50000" min="0">
                </div>
                <div class="form-group" id="max-disc-group">
                    <label class="form-label">Maks. Diskon (Rp)</label>
                    <input type="number" name="max_discount" class="form-input" placeholder="30000" min="0">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Maks. Penggunaan</label>
                <input type="number" name="max_usage" class="form-input" placeholder="Kosongkan = unlimited" min="1">
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                <div class="form-group">
                    <label class="form-label">Berlaku Mulai</label>
                    <input type="datetime-local" name="start_at" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Berlaku Sampai</label>
                    <input type="datetime-local" name="end_at" class="form-input">
                </div>
            </div>
            <div class="form-group">
                <label style="display:flex;align-items:center;gap:10px;cursor:pointer;font-size:13px;font-weight:600">
                    <input type="checkbox" name="is_active" value="1" checked style="accent-color:var(--sage);width:16px;height:16px"> Aktifkan Langsung
                </label>
            </div>
            <div style="display:flex;gap:10px">
                <button type="submit" class="btn-primary" style="flex:1;justify-content:center">
                    <i class="ti ti-ticket"></i> Buat Voucher
                </button>
                <button type="button" onclick="closeModal()" class="btn-secondary">Batal</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openModal() { document.getElementById('voucher-modal').classList.add('show'); }
function closeModal() { document.getElementById('voucher-modal').classList.remove('show'); }
function toggleVoucherType() {
    const type = document.getElementById('v-type').value;
    document.getElementById('max-disc-group').style.display = type === 'percent' ? 'block' : 'none';
}
function toggleVoucher(id, btn) {
    ajax('/admin/vouchers/'+id+'/toggle','POST')
    .then(d => { showToast(d.is_active ? 'Voucher diaktifkan' : 'Voucher dinonaktifkan', 'info'); setTimeout(()=>location.reload(),600); })
    .catch(()=>showToast('Gagal','error'));
}
</script>
@endpush