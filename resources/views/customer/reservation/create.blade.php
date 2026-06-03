@extends('layouts.app')
@section('title','Buat Reservasi')

@push('styles')
<style>
.page-top{padding:110px 0 40px;background:linear-gradient(135deg,#1a2e22,#2c3e35)}
.form-card{background:var(--warm-white);border-radius:20px;border:1px solid var(--border);padding:28px;max-width:720px;margin:0 auto}
.form-group{margin-bottom:20px}
.form-label{display:block;font-size:13px;font-weight:600;color:var(--text-main);margin-bottom:7px}
.form-input,.form-select,.form-textarea{width:100%;padding:11px 14px;border-radius:10px;border:1.5px solid var(--border);background:var(--beige);color:var(--text-main);font-size:14px;font-family:inherit;outline:none;transition:border-color .2s}
.form-input:focus,.form-select:focus,.form-textarea:focus{border-color:var(--sage);background:#fff}
.table-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(110px,1fr));gap:10px}
.table-btn{padding:12px 8px;border-radius:12px;border:2px solid var(--border);background:var(--beige);cursor:pointer;transition:all .2s;text-align:center;font-size:13px;font-weight:600}
.table-btn:hover:not(.occupied){border-color:var(--sage);background:rgba(90,124,101,.06);color:var(--sage-dark)}
.table-btn.selected{background:var(--sage);color:#fff;border-color:var(--sage)}
.table-btn.occupied{opacity:.45;cursor:not-allowed;background:var(--beige)}
.grid-2{display:grid;grid-template-columns:1fr 1fr;gap:16px}
@media(max-width:600px){.grid-2{grid-template-columns:1fr}}
</style>
@endpush

@section('content')
<div class="page-top">
    <div class="container">
        <a href="{{ route('reservation.index') }}" style="color:rgba(255,255,255,.6);font-size:13px;display:inline-flex;align-items:center;gap:6px;margin-bottom:12px">
            <i class="ti ti-arrow-left"></i> Kembali
        </a>
        <h1 style="font-family:'INeedCoffee',serif;font-size:clamp(22px,4vw,32px);font-weight:600;color:#fff">Buat Reservasi Meja</h1>
    </div>
</div>

<div class="container" style="padding-top:32px;padding-bottom:80px">
    <div class="form-card">
        @if($errors->any())
        <div style="background:rgba(220,80,60,.08);border:1px solid rgba(220,80,60,.2);border-radius:12px;padding:14px;margin-bottom:20px;font-size:13px;color:#c0392b">
            @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
        </div>
        @endif

        <form action="{{ route('reservation.store') }}" method="POST">
            @csrf

            {{-- Pilih Meja Visual --}}
            <div class="form-group">
                <label class="form-label"><i class="ti ti-armchair" style="color:var(--sage)"></i> Pilih Meja</label>
                <input type="hidden" name="table_id" id="selected-table" required>

                @php $locations = $tables->groupBy('location'); @endphp
                @foreach($locations as $loc => $locTables)
                <div style="margin-bottom:14px">
                    <div style="font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px">{{ $loc }}</div>
                    <div class="table-grid">
                        @foreach($locTables as $table)
                        <button type="button"
                            class="table-btn {{ $table->status !== 'available' ? 'occupied' : '' }}"
                            onclick="{{ $table->status === 'available' ? 'selectTable('.$table->id.', '.$table->capacity.', this)' : '' }}"
                            data-id="{{ $table->id }}"
                            {{ $table->status !== 'available' ? 'disabled' : '' }}>
                            <div style="font-size:18px;margin-bottom:4px">🪑</div>
                            <div>{{ $table->table_number }}</div>
                            <div style="font-size:11px;font-weight:400;margin-top:2px">Max {{ $table->capacity }} org</div>
                            @if($table->status !== 'available')
                            <div style="font-size:10px;color:#c0392b;margin-top:2px">Terpesan</div>
                            @endif
                        </button>
                        @endforeach
                    </div>
                </div>
                @endforeach

                <div id="table-info" style="display:none;background:rgba(90,124,101,.08);border:1px solid rgba(90,124,101,.2);border-radius:10px;padding:10px 14px;font-size:13px;margin-top:8px;color:var(--sage-dark)">
                    <i class="ti ti-circle-check" style="font-size:15px"></i>
                    Meja dipilih: <strong id="table-info-text"></strong>
                </div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label"><i class="ti ti-calendar" style="color:var(--sage)"></i> Tanggal</label>
                    <input type="date" name="reservation_date" class="form-input"
                        min="{{ date('Y-m-d') }}" value="{{ old('reservation_date') }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label"><i class="ti ti-clock" style="color:var(--sage)"></i> Jam</label>
                    <input type="time" name="reservation_time" class="form-input"
                        min="07:00" max="20:00" value="{{ old('reservation_time') }}" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label"><i class="ti ti-users" style="color:var(--sage)"></i> Jumlah Tamu</label>
                <input type="number" name="guest_count" class="form-input"
                    min="1" max="20" placeholder="Berapa orang?" value="{{ old('guest_count',2) }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">Jenis Event (opsional)</label>
                <select name="event_type" class="form-select">
                    <option value="">Pilih jenis event</option>
                    <option value="Ulang Tahun" {{ old('event_type')=='Ulang Tahun'?'selected':'' }}>🎂 Ulang Tahun</option>
                    <option value="Anniversary" {{ old('event_type')=='Anniversary'?'selected':'' }}>💑 Anniversary</option>
                    <option value="Reuni" {{ old('event_type')=='Reuni'?'selected':'' }}>👥 Reuni</option>
                    <option value="Meeting" {{ old('event_type')=='Meeting'?'selected':'' }}>💼 Meeting</option>
                    <option value="Lainnya" {{ old('event_type')=='Lainnya'?'selected':'' }}>✨ Lainnya</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Permintaan Khusus (opsional)</label>
                <textarea name="special_request" class="form-textarea" rows="3"
                    placeholder="Contoh: Siapkan dekorasi ulang tahun, kursi bayi, dll...">{{ old('special_request') }}</textarea>
            </div>

            <div style="display:flex;gap:12px">
                <a href="{{ route('reservation.index') }}" class="btn-outline" style="flex:1;justify-content:center">
                    Batal
                </a>
                <button type="submit" class="btn-primary" style="flex:2;justify-content:center">
                    <i class="ti ti-calendar-check"></i> Konfirmasi Reservasi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function selectTable(id, capacity, el) {
    document.querySelectorAll('.table-btn').forEach(b => b.classList.remove('selected'));
    el.classList.add('selected');
    document.getElementById('selected-table').value = id;
    document.getElementById('table-info').style.display = 'block';
    document.getElementById('table-info-text').textContent = el.querySelector('div:nth-child(2)').textContent + ' (max ' + capacity + ' orang)';
    const guestInput = document.querySelector('[name="guest_count"]');
    if (parseInt(guestInput.value) > capacity) guestInput.value = capacity;
    guestInput.max = capacity;
}
</script>
@endpush


