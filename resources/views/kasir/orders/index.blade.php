@extends('layouts.kasir')
@section('title','Kelola Order')

@push('styles')
<style>
.filter-bar{background:#1e2b25;border-radius:12px;border:1px solid rgba(138,170,146,.1);padding:14px 18px;display:flex;gap:10px;align-items:center;flex-wrap:wrap;margin-bottom:20px}
.search-inp{background:#0f1a16;border:1px solid rgba(138,170,146,.15);border-radius:8px;padding:8px 14px 8px 36px;color:#e8f0eb;font-size:13px;font-family:inherit;outline:none;width:200px;transition:border-color .2s}
.search-inp:focus{border-color:rgba(138,170,146,.4)}
.search-wrap{position:relative}
.search-wrap i{position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#8fa897;font-size:16px;pointer-events:none}
.filter-btn{padding:7px 16px;border-radius:20px;border:1px solid rgba(138,170,146,.15);background:transparent;color:#8fa897;font-size:12px;font-weight:600;cursor:pointer;transition:all .2s}
.filter-btn.active,.filter-btn:hover{background:rgba(90,124,101,.2);border-color:rgba(90,124,101,.4);color:#8aaa92}
.order-row{background:#1e2b25;border-radius:12px;border:1px solid rgba(138,170,146,.1);padding:14px 16px;display:flex;align-items:center;gap:14px;transition:border-color .2s}
.order-row:hover{border-color:rgba(138,170,146,.25)}
.order-row.menunggu{border-left:3px solid #f59e0b}
.order-row.cooking{border-left:3px solid #e07a5f}
.order-row.selesai{border-left:3px solid #4ade80}
.status-badge{padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700}
.badge-menunggu{background:rgba(245,158,11,.15);color:#f59e0b}
.badge-cooking{background:rgba(224,122,95,.15);color:#e07a5f}
.badge-selesai{background:rgba(74,222,128,.12);color:#4ade80}
.badge-completed{background:rgba(138,170,146,.15);color:#8aaa92}
.badge-dibatalkan{background:rgba(220,80,60,.1);color:#e07a5f}
.btn-sm{padding:6px 14px;border-radius:8px;font-size:12px;font-weight:600;border:none;cursor:pointer;transition:all .2s;display:inline-flex;align-items:center;gap:5px}
.btn-masak{background:rgba(224,122,95,.15);color:#e07a5f}.btn-masak:hover{background:#e07a5f;color:#fff}
.btn-done{background:rgba(74,222,128,.12);color:#4ade80}.btn-done:hover{background:#16a34a;color:#fff}
.btn-detail{background:rgba(138,170,146,.1);color:#8aaa92}.btn-detail:hover{background:rgba(138,170,146,.2);color:#e8f0eb}
</style>
@endpush

@section('content')
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px">
    <div style="font-size:20px;font-weight:700;color:#e8f0eb">Kelola Order</div>
    <div style="display:flex;align-items:center;gap:8px">
        <span class="status-dot"></span>
        <span style="font-size:13px;color:#8fa897" id="last-update">Live</span>
    </div>
</div>

<div class="filter-bar">
    <div class="search-wrap">
        <i class="ti ti-search"></i>
        <input type="text" id="search-inp" class="search-inp" placeholder="Cari no. order...">
    </div>
    <div style="display:flex;gap:6px;flex-wrap:wrap">
        @foreach(['all'=>'Semua','menunggu'=>'Menunggu','cooking'=>'Memasak','selesai'=>'Siap'] as $val=>$lbl)
        <button class="filter-btn {{ $val==='all'?'active':'' }}" onclick="filterStatus('{{ $val }}',this)">{{ $lbl }}</button>
        @endforeach
    </div>
    <div style="margin-left:auto;display:flex;gap:8px">
        <div style="background:rgba(245,158,11,.15);color:#f59e0b;border-radius:8px;padding:6px 12px;font-size:12px;font-weight:700">
            <span id="cnt-menunggu">0</span> Menunggu
        </div>
        <div style="background:rgba(224,122,95,.15);color:#e07a5f;border-radius:8px;padding:6px 12px;font-size:12px;font-weight:700">
            <span id="cnt-cooking">0</span> Masak
        </div>
    </div>
</div>

<div id="orders-container" style="display:flex;flex-direction:column;gap:8px">
    <div style="text-align:center;padding:40px;color:#8fa897">
        <i class="ti ti-loader" style="font-size:28px;animation:spin 1s linear infinite;display:block;margin-bottom:8px"></i>
        Memuat order...
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentStatus = 'all';
let currentSearch = '';
let pollInterval;

function filterStatus(status, btn) {
    currentStatus = status;
    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    loadOrders();
}

document.getElementById('search-inp').addEventListener('input', function() {
    clearTimeout(window._st);
    currentSearch = this.value;
    window._st = setTimeout(loadOrders, 400);
});

function loadOrders() {
    let url = '/kasir/orders?status=' + currentStatus;
    if (currentSearch) url += '&search=' + encodeURIComponent(currentSearch);
    ajax(url).then(d => {
        if (d.orders) renderOrders(d.orders);
        if (d.counts) {
            document.getElementById('cnt-menunggu').textContent = d.counts.menunggu;
            document.getElementById('cnt-cooking').textContent = d.counts.cooking;
            document.getElementById('count-menunggu').textContent = d.counts.menunggu;
            document.getElementById('count-cooking').textContent = d.counts.cooking;
        }
        document.getElementById('last-update').textContent = 'Update: ' + new Date().toLocaleTimeString('id-ID');
    });
}

function renderOrders(orders) {
    const c = document.getElementById('orders-container');
    if (!orders.length) {
        c.innerHTML = '<div style="text-align:center;padding:40px;background:#1e2b25;border-radius:12px;color:#8fa897"><i class="ti ti-inbox" style="font-size:32px;display:block;margin-bottom:8px"></i>Tidak ada order</div>';
        return;
    }
    c.innerHTML = orders.map(o => `
    <div class="order-row ${o.status}" id="ord-${o.id}">
        <div style="flex:1;min-width:0">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;flex-wrap:wrap">
                <span style="font-weight:700;font-size:14px;color:#e8f0eb">${o.order_number}</span>
                <span class="status-badge badge-${o.status}">${o.status_label}</span>
                <span style="font-size:11px;color:#8fa897">${o.time_ago}</span>
            </div>
            <div style="font-size:13px;color:#8fa897">${o.customer_name} · ${o.items_count} item · ${o.total_amount}</div>
            <div style="font-size:12px;color:#8fa897;margin-top:4px">
                ${o.items.map(i => i.name + ' ×' + i.quantity + (i.note ? ' ('+i.note+')' : '')).join(', ')}
            </div>
            ${o.table_number ? `<div style="font-size:12px;color:#8fa897;margin-top:3px">🪑 Meja ${o.table_number}</div>` : ''}
            ${o.notes ? `<div style="font-size:12px;background:rgba(245,158,11,.08);padding:4px 8px;border-radius:6px;margin-top:5px;color:#f59e0b">📝 ${o.notes}</div>` : ''}
        </div>
        <div style="display:flex;align-items:center;gap:8px;flex-shrink:0">
            ${o.status === 'menunggu' ? `<button onclick="updateStatus(${o.id},'cooking',this)" class="btn-sm btn-masak"><i class="ti ti-chef-hat"></i> Masak</button>` : ''}
            ${o.status === 'cooking' ? `<button onclick="updateStatus(${o.id},'selesai',this)" class="btn-sm btn-done"><i class="ti ti-check"></i> Selesai</button>` : ''}
            <a href="/kasir/orders/${o.id}" class="btn-sm btn-detail"><i class="ti ti-eye"></i></a>
        </div>
    </div>`).join('');
}

function updateStatus(orderId, newStatus, btn) {
    btn.disabled = true;
    btn.innerHTML = '<i class="ti ti-loader" style="animation:spin 1s linear infinite"></i>';
    ajax('/kasir/orders/' + orderId + '/status', 'POST', { status: newStatus })
    .then(d => {
        if (d.success) { showToast(d.message, 'success'); loadOrders(); }
        else { showToast(d.message || 'Gagal update status', 'error'); btn.disabled = false; }
    });
}

// Auto load + poll
loadOrders();
pollInterval = setInterval(loadOrders, 8000);
</script>
<style>@keyframes spin{to{transform:rotate(360deg)}}</style>
@endpush
