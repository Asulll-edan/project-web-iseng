@extends('layouts.kitchen')
@section('content')

@push('styles')
<style>
.order-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:16px}
.kitchen-card{border-radius:16px;border:2px solid rgba(138,170,146,.15);padding:18px;transition:all .3s;position:relative;overflow:hidden}
.kitchen-card.menunggu{background:#1a2b22;border-color:rgba(245,158,11,.3)}
.kitchen-card.cooking{background:#1a2510;border-color:rgba(74,222,128,.45);animation:cookGlow 3s ease infinite}
@keyframes cookGlow{0%,100%{border-color:rgba(74,222,128,.45)}50%{border-color:rgba(74,222,128,.9)}}
.priority-bar{position:absolute;top:0;left:0;right:0;height:3px;border-radius:2px 2px 0 0}
.priority-menunggu{background:linear-gradient(90deg,#f59e0b,transparent)}
.priority-cooking{background:linear-gradient(90deg,#4ade80,transparent)}
.card-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:12px}
.order-num{font-size:17px;font-weight:700;color:#e8f0eb}
.time-badge{font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px}
.time-ok{background:rgba(74,222,128,.12);color:#4ade80}
.time-warn{background:rgba(245,158,11,.12);color:#f59e0b}
.time-late{background:rgba(220,80,60,.12);color:#e07a5f}
.item-row{display:flex;align-items:baseline;justify-content:space-between;padding:7px 0;border-bottom:1px solid rgba(138,170,146,.08)}
.item-row:last-child{border:none}
.item-name{color:#e8f0eb;font-weight:500;font-size:14px}
.item-qty{font-size:20px;font-weight:700;color:#4ade80}
.item-note{font-size:11px;color:#f59e0b;display:block;margin-top:2px}
.customer-info{font-size:12px;color:#8fa897;margin-bottom:10px;display:flex;gap:12px;flex-wrap:wrap}
.action-btn{width:100%;margin-top:12px;padding:11px;border-radius:10px;border:none;cursor:pointer;font-weight:700;font-size:14px;font-family:inherit;transition:all .2s;display:flex;align-items:center;justify-content:center;gap:8px}
.btn-start{background:rgba(245,158,11,.15);color:#f59e0b}.btn-start:hover{background:#d97706;color:#fff}
.btn-done{background:rgba(74,222,128,.15);color:#4ade80}.btn-done:hover{background:#16a34a;color:#fff}
.btn-undo{width:100%;margin-top:6px;padding:7px;border-radius:8px;border:1px solid rgba(138,170,146,.2);background:rgba(138,170,146,.06);color:#8fa897;cursor:pointer;font-size:12px;font-weight:600;font-family:inherit;transition:all .2s;display:flex;align-items:center;justify-content:center;gap:6px}
.btn-undo:hover{background:rgba(245,158,11,.1);color:#f59e0b;border-color:rgba(245,158,11,.3)}
.empty-state{text-align:center;padding:80px 20px;color:#8fa897;grid-column:1/-1}
</style>
@endpush

{{-- Summary bar --}}
<div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;margin-bottom:18px">
    <div style="background:#1e2b25;border-radius:12px;padding:14px 18px;border:1px solid rgba(245,158,11,.2);display:flex;align-items:center;gap:12px">
        <i class="ti ti-clock" style="font-size:24px;color:#f59e0b"></i>
        <div><div style="font-size:24px;font-weight:700;color:#e8f0eb" id="sum-menunggu">0</div><div style="font-size:12px;color:#8fa897">Menunggu Dimasak</div></div>
    </div>
    <div style="background:#1a2510;border-radius:12px;padding:14px 18px;border:1px solid rgba(74,222,128,.2);display:flex;align-items:center;gap:12px">
        <i class="ti ti-chef-hat" style="font-size:24px;color:#4ade80"></i>
        <div><div style="font-size:24px;font-weight:700;color:#e8f0eb" id="sum-cooking">0</div><div style="font-size:12px;color:#8fa897">Sedang Dimasak</div></div>
    </div>
</div>

<div id="order-grid" class="order-grid">
    <div class="empty-state">
        <i class="ti ti-loader" style="font-size:40px;animation:spin 1s linear infinite;display:block;margin-bottom:12px"></i>
        Memuat pesanan...
    </div>
</div>

<script>
let knownOrderIds = new Set();
let isFirstKitchenLoad = true;
let kitchenCountdown = 8;

function getTimeColor(min) {
    if(min<5)  return 'time-ok';
    if(min<10) return 'time-warn';
    return 'time-late';
}

function loadKitchenOrders() {
    fetch('/kitchen/orders',{headers:{'Accept':'application/json'}})
    .then(r=>r.json())
    .then(d=>{
        const orders = d.orders||[];
        // Detect & sound new orders
        if(!isFirstKitchenLoad) {
            orders.forEach(o=>{
                if(!knownOrderIds.has(o.id)) playKitchenBeep();
            });
        }
        knownOrderIds = new Set(orders.map(o=>o.id));
        isFirstKitchenLoad = false;

        const menunggu = orders.filter(o=>o.status==='menunggu').length;
        const cooking  = orders.filter(o=>o.status==='cooking').length;
        document.getElementById('sum-menunggu').textContent = menunggu;
        document.getElementById('sum-cooking').textContent  = cooking;
        // update navbar counts
        const km=document.getElementById('k-menunggu'); if(km)km.textContent=menunggu;
        const kc=document.getElementById('k-cooking');  if(kc)kc.textContent=cooking;

        const grid = document.getElementById('order-grid');
        if(!orders.length){
            grid.innerHTML='<div class="empty-state"><i class="ti ti-checks" style="font-size:48px;display:block;margin-bottom:12px;color:#4ade80"></i><div style="font-size:18px;font-weight:600;color:#e8f0eb;margin-bottom:6px">Semua pesanan selesai!</div><div>Tidak ada pesanan aktif</div></div>';
            return;
        }

        grid.innerHTML = orders.map(o=>`
        <div class="kitchen-card ${o.status}" id="kc-${o.id}">
            <div class="priority-bar priority-${o.status}"></div>
            <div class="card-head">
                <div>
                    <div class="order-num">${o.order_number}</div>
                    <div class="customer-info">
                        <span><i class="ti ti-user" style="font-size:12px"></i> ${o.customer}</span>
                        ${o.table_number ? `<span><i class="ti ti-armchair" style="font-size:12px"></i> Meja ${o.table_number}</span>` : '<span>Takeaway</span>'}
                    </div>
                </div>
                <span class="time-badge ${getTimeColor(o.minutes_ago)}">${o.time_ago}</span>
            </div>
            <div>
                ${o.items.map(item=>`
                <div class="item-row">
                    <div>
                        <span class="item-name">${item.name}</span>
                        ${item.note?`<span class="item-note">📝 ${item.note}</span>`:''}
                    </div>
                    <span class="item-qty">×${item.quantity}</span>
                </div>`).join('')}
            </div>
            ${o.notes?`<div style="background:rgba(245,158,11,.08);border-radius:8px;padding:8px 10px;margin-top:8px;font-size:12px;color:#f59e0b"><i class="ti ti-notes" style="font-size:13px"></i> ${o.notes}</div>`:''}
            <button class="action-btn ${o.status==='menunggu'?'btn-start':'btn-done'}" onclick="updateKitchenOrder(${o.id},this)">
                ${o.status==='menunggu'
                    ?'<i class="ti ti-chef-hat"></i> Mulai Masak'
                    :'<i class="ti ti-check"></i> Selesai Dimasak'}
            </button>
            ${o.status==='cooking'?`
            <button class="btn-undo" onclick="undoKitchenOrder(${o.id},this)">
                <i class="ti ti-arrow-back-up" style="font-size:14px"></i> Undo ke Menunggu
            </button>`:''}
        </div>`).join('');
    }).catch(()=>{});
}

function updateKitchenOrder(id, btn) {
    Swal.fire({
        title: btn.textContent.trim().includes('Mulai') ? 'Mulai Masak?' : 'Tandai Selesai?',
        icon:'question', showCancelButton:true,
        confirmButtonColor:'#3d5c47',
        cancelButtonColor:'rgba(138,170,146,.15)',
        confirmButtonText:'Ya', cancelButtonText:'Batal'
    }).then(r=>{
        if(!r.isConfirmed) return;
        btn.disabled=true;
        btn.innerHTML='<i class="ti ti-loader" style="animation:spin 1s linear infinite"></i> Memproses...';
        fetch('/kitchen/orders/'+id+'/done',{
            method:'POST',
            headers:{'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content,'Accept':'application/json'}
        }).then(r=>r.json()).then(d=>{
            if(d.success) loadKitchenOrders();
            else{btn.disabled=false;btn.textContent='Coba lagi';}
        });
    });
}

function undoKitchenOrder(id, btn) {
    Swal.fire({
        title:'Undo ke Menunggu?', text:'Status akan dikembalikan ke Menunggu.',
        icon:'warning', showCancelButton:true,
        confirmButtonColor:'#d97706',
        cancelButtonColor:'rgba(138,170,146,.15)',
        confirmButtonText:'Ya, Undo', cancelButtonText:'Batal'
    }).then(r=>{
        if(!r.isConfirmed) return;
        btn.disabled=true;
        fetch('/kasir/orders/'+id+'/status',{
            method:'POST',
            headers:{'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content,'Accept':'application/json','Content-Type':'application/json'},
            body:JSON.stringify({status:'menunggu'})
        }).then(r=>r.json()).then(d=>{
            if(d.success) loadKitchenOrders();
            else btn.disabled=false;
        });
    });
}

function playKitchenBeep() {
    try {
        const ctx=new(window.AudioContext||window.webkitAudioContext)();
        [660,880,660].forEach((f,i)=>{
            const o=ctx.createOscillator(),g=ctx.createGain();
            o.connect(g);g.connect(ctx.destination);
            o.frequency.value=f;
            g.gain.setValueAtTime(0.25,ctx.currentTime+i*.18);
            g.gain.exponentialRampToValueAtTime(0.001,ctx.currentTime+i*.18+.15);
            o.start(ctx.currentTime+i*.18);o.stop(ctx.currentTime+i*.18+.15);
        });
    }catch(e){}
}

// Countdown di navbar refresh indicator
setInterval(()=>{
    kitchenCountdown--;
    if(kitchenCountdown<=0){ kitchenCountdown=8; loadKitchenOrders(); }
},1000);

loadKitchenOrders();
</script>
<style>@keyframes spin{to{transform:rotate(360deg)}}</style>
@endsection
