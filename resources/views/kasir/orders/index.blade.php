@extends('layouts.kasir')
@section('title','Kelola Order')

@push('styles')
<style>
.filter-bar{background:var(--bg2);border-radius:var(--r);border:1px solid var(--border);padding:12px 16px;display:flex;gap:8px;align-items:center;flex-wrap:wrap;margin-bottom:16px}
.search-inp{background:var(--bg);border:1px solid var(--border);border-radius:8px;padding:8px 14px 8px 34px;color:var(--text);font-size:13px;font-family:inherit;outline:none;width:200px;transition:border-color .2s}
.search-inp:focus{border-color:rgba(138,170,146,.4)}
.search-wrap{position:relative}
.search-wrap i{position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--muted);font-size:15px;pointer-events:none}
.filter-btn{padding:7px 14px;border-radius:20px;border:1px solid var(--border);background:transparent;color:var(--muted);font-size:12px;font-weight:600;cursor:pointer;transition:all .2s}
.filter-btn.active,.filter-btn:hover{background:rgba(90,124,101,.2);border-color:rgba(90,124,101,.4);color:#8aaa92}
.order-row{background:var(--bg2);border-radius:var(--r);border:1px solid var(--border);padding:14px 16px;display:flex;align-items:center;gap:14px;transition:border-color .2s;position:relative}
.order-row:hover{border-color:rgba(138,170,146,.25)}
.order-row.menunggu{border-left:3px solid #f59e0b}
.order-row.cooking{border-left:3px solid #e07a5f}
.order-row.selesai{border-left:3px solid #4ade80}
.badge-menunggu{background:rgba(245,158,11,.15);color:#f59e0b;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700}
.badge-cooking{background:rgba(224,122,95,.15);color:#e07a5f;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700}
.badge-selesai{background:rgba(74,222,128,.12);color:#4ade80;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700}
.badge-completed{background:rgba(138,170,146,.12);color:#8aaa92;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700}
.badge-dibatalkan{background:rgba(220,80,60,.1);color:#e07a5f;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700}
.btn-sm{padding:6px 14px;border-radius:8px;font-size:12px;font-weight:600;border:none;cursor:pointer;transition:all .2s;display:inline-flex;align-items:center;gap:5px}
.btn-masak{background:rgba(224,122,95,.15);color:#e07a5f}.btn-masak:hover{background:#e07a5f;color:#fff}
.btn-done{background:rgba(74,222,128,.12);color:#4ade80}.btn-done:hover{background:#16a34a;color:#fff}
.btn-undo{background:rgba(245,158,11,.12);color:#f59e0b}.btn-undo:hover{background:#d97706;color:#fff}
.btn-detail{background:rgba(138,170,146,.1);color:#8aaa92;text-decoration:none}.btn-detail:hover{background:rgba(138,170,146,.2);color:var(--text)}
.new-badge{position:absolute;top:-6px;right:-6px;background:#e07a5f;color:#fff;font-size:9px;font-weight:700;padding:2px 7px;border-radius:10px;animation:newpulse 2s infinite}
@keyframes newpulse{0%,100%{transform:scale(1)}50%{transform:scale(1.08)}}
.empty-state{text-align:center;padding:50px;background:var(--bg2);border-radius:var(--r);color:var(--muted)}
</style>
@endpush

@section('content')
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:10px">
    <div style="font-size:19px;font-weight:700;color:var(--text)">Kelola Order</div>
    <div style="display:flex;gap:8px;align-items:center">
        <span style="font-size:12px;color:var(--muted)" id="last-update">Menunggu data...</span>
        <button onclick="loadOrders(true)" style="background:rgba(138,170,146,.1);border:1px solid var(--border);color:var(--muted);border-radius:8px;padding:6px 12px;font-size:12px;cursor:pointer;display:flex;align-items:center;gap:5px">
            <i class="ti ti-refresh" style="font-size:14px"></i> Refresh
        </button>
    </div>
</div>

<div class="filter-bar">
    <div class="search-wrap">
        <i class="ti ti-search"></i>
        <input type="text" id="search-inp" class="search-inp" placeholder="Cari no. order...">
    </div>
    <div style="display:flex;gap:5px;flex-wrap:wrap">
        @foreach(['all'=>'Semua','menunggu'=>'Menunggu','cooking'=>'Memasak','selesai'=>'Siap','completed'=>'Selesai'] as $val=>$lbl)
        <button class="filter-btn {{ $val==='all'?'active':'' }}" onclick="setFilter('{{ $val }}',this)">{{ $lbl }}</button>
        @endforeach
    </div>
    <div style="margin-left:auto;display:flex;gap:6px">
        <div style="background:rgba(245,158,11,.12);color:#f59e0b;border-radius:8px;padding:5px 12px;font-size:12px;font-weight:700">
            <i class="ti ti-clock" style="font-size:13px"></i> <span id="cnt-menunggu">0</span>
        </div>
        <div style="background:rgba(224,122,95,.12);color:#e07a5f;border-radius:8px;padding:5px 12px;font-size:12px;font-weight:700">
            <i class="ti ti-flame" style="font-size:13px"></i> <span id="cnt-cooking">0</span>
        </div>
    </div>
</div>

<div id="orders-container" style="display:flex;flex-direction:column;gap:8px">
    <div class="empty-state"><i class="ti ti-loader" style="font-size:28px;animation:spin 1s linear infinite;display:block;margin-bottom:8px"></i>Memuat pesanan...</div>
</div>
@endsection

@push('scripts')
<script>
let currentFilter = 'all';
let currentSearch = '';
let knownIds = new Set();
let pollInterval;
let isFirstLoad = true;

function setFilter(val, btn) {
    currentFilter = val;
    document.querySelectorAll('.filter-btn').forEach(b=>b.classList.remove('active'));
    btn.classList.add('active');
    loadOrders();
}

document.getElementById('search-inp').addEventListener('input', function() {
    clearTimeout(window._st);
    currentSearch = this.value;
    window._st = setTimeout(loadOrders, 400);
});

function loadOrders(forceRefresh=false) {
    const url = '/kasir/orders?status='+currentFilter+(currentSearch?'&search='+encodeURIComponent(currentSearch):'');
    fetch(url, {headers:{'Accept':'application/json','X-CSRF-TOKEN':CSRF}})
    .then(r=>r.json())
    .then(d=>{
        if(!d.orders) return;
        const orders = d.orders;
        // Detect new orders
        if(!isFirstLoad) {
            orders.forEach(o=>{
                if(!knownIds.has(o.id) && (o.status==='menunggu')) {
                    playNotifSound();
                    showToast('Order baru masuk: '+o.order_number,'success');
                }
            });
        }
        // Update known IDs
        knownIds = new Set(orders.map(o=>o.id));
        isFirstLoad = false;
        renderOrders(orders);
        if(d.counts) {
            document.getElementById('cnt-menunggu').textContent = d.counts.menunggu||0;
            document.getElementById('cnt-cooking').textContent  = d.counts.cooking||0;
        }
        document.getElementById('last-update').textContent = 'Update: '+new Date().toLocaleTimeString('id-ID',{timeZone:'Asia/Jakarta'});
    }).catch(()=>{});
}

function renderOrders(orders) {
    const c = document.getElementById('orders-container');
    if(!orders.length) {
        c.innerHTML='<div class="empty-state"><i class="ti ti-inbox" style="font-size:32px;display:block;margin-bottom:8px"></i>Tidak ada order</div>';
        return;
    }
    c.innerHTML = orders.map(o=>{
        const isNew = !knownIds.has(o.id) && o.minutes_ago < 2;
        return `
        <div class="order-row ${o.status}" id="ord-${o.id}">
            ${isNew ? '<span class="new-badge">BARU</span>' : ''}
            <div style="flex:1;min-width:0">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:5px;flex-wrap:wrap">
                    <span style="font-weight:700;font-size:14px;color:var(--text)">${o.order_number}</span>
                    <span class="badge-${o.status}">${o.status_label}</span>
                    <span style="font-size:11px;color:var(--muted)">${o.time_ago}</span>
                </div>
                <div style="font-size:13px;color:var(--muted);margin-bottom:4px">${o.customer_name} · ${o.items_count} item · ${o.total_amount}</div>
                <div style="font-size:12px;color:var(--muted)">
                    ${o.items.map(i=>i.name+'×'+i.quantity+(i.note?' ('+i.note+')':'')).join(', ')}
                </div>
                ${o.table_number ? `<div style="font-size:12px;color:var(--muted);margin-top:3px">🪑 Meja ${o.table_number}</div>` : ''}
                ${o.notes ? `<div style="font-size:12px;background:rgba(245,158,11,.08);padding:4px 8px;border-radius:6px;margin-top:4px;color:#f59e0b">📝 ${o.notes}</div>` : ''}
            </div>
            <div style="display:flex;align-items:center;gap:6px;flex-shrink:0;flex-wrap:wrap">
                ${o.status==='menunggu' ? `<button onclick="updateStatus(${o.id},'cooking',this)" class="btn-sm btn-masak"><i class="ti ti-chef-hat"></i> Masak</button>` : ''}
                ${o.status==='cooking'  ? `<button onclick="updateStatus(${o.id},'selesai',this)" class="btn-sm btn-done"><i class="ti ti-check"></i> Selesai</button>` : ''}
                ${o.status==='cooking'  ? `<button onclick="undoStatus(${o.id},this)" class="btn-sm btn-undo" title="Undo ke Menunggu"><i class="ti ti-arrow-back-up"></i></button>` : ''}
                ${o.status==='selesai'  ? `<button onclick="undoStatus(${o.id},this)" class="btn-sm btn-undo" title="Undo ke Cooking"><i class="ti ti-arrow-back-up"></i> Undo</button>` : ''}
                <a href="/kasir/orders/${o.id}" class="btn-sm btn-detail"><i class="ti ti-eye"></i></a>
            </div>
        </div>`;
    }).join('');
}

function updateStatus(id, newStatus, btn) {
    const labels = {cooking:'Tandai Sedang Dimasak?', selesai:'Tandai Selesai Dimasak?'};
    Swal.fire({
        title: labels[newStatus] || 'Update Status?',
        icon: 'question', showCancelButton:true,
        confirmButtonColor:'#3d5c47', cancelButtonColor:'rgba(138,170,146,.2)',
        confirmButtonText:'Ya', cancelButtonText:'Batal'
    }).then(r=>{
        if(!r.isConfirmed) return;
        btn.disabled=true; btn.innerHTML='<i class="ti ti-loader" style="animation:spin 1s linear infinite"></i>';
        ajax('/kasir/orders/'+id+'/status','POST',{status:newStatus})
        .then(d=>{
            if(d.success){ showToast(d.message,'success'); loadOrders(); }
            else{ showToast(d.message||'Gagal','error'); btn.disabled=false; }
        });
    });
}

function undoStatus(id, btn) {
    Swal.fire({
        title:'Undo Status?', text:'Status akan dikembalikan ke sebelumnya.',
        icon:'warning', showCancelButton:true,
        confirmButtonColor:'#d97706', cancelButtonColor:'rgba(138,170,146,.2)',
        confirmButtonText:'Ya, Undo', cancelButtonText:'Batal'
    }).then(r=>{
        if(!r.isConfirmed) return;
        btn.disabled=true;
        // Ambil status saat ini dari row
        const row = document.getElementById('ord-'+id);
        const prevStatus = row.classList.contains('cooking') ? 'menunggu' : 'cooking';
        ajax('/kasir/orders/'+id+'/status','POST',{status:prevStatus})
        .then(d=>{
            if(d.success){ showToast('Status dikembalikan','info'); loadOrders(); }
            else{ showToast(d.message||'Gagal','error'); btn.disabled=false; }
        });
    });
}

function playNotifSound() {
    try {
        const ctx=new(window.AudioContext||window.webkitAudioContext)();
        [880,660,880].forEach((freq,i)=>{
            const o=ctx.createOscillator(), g=ctx.createGain();
            o.connect(g); g.connect(ctx.destination);
            o.frequency.value=freq;
            g.gain.setValueAtTime(0.2,ctx.currentTime+i*0.15);
            g.gain.exponentialRampToValueAtTime(0.001,ctx.currentTime+i*0.15+0.12);
            o.start(ctx.currentTime+i*0.15); o.stop(ctx.currentTime+i*0.15+0.12);
        });
    } catch(e){}
}

// Initial load + delta poll setiap 8 detik (tidak reload semua, hanya cek perubahan)
loadOrders();
pollInterval = setInterval(()=>loadOrders(false), 8000);
</script>
<style>@keyframes spin{to{transform:rotate(360deg)}}</style>
@endpush
