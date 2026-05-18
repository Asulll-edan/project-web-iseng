@extends('layouts.kitchen')
@section('content')

@push('styles')
<style>
.order-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:16px;padding:4px 0}
.kitchen-card{border-radius:16px;border:2px solid rgba(138,170,146,.15);padding:18px;transition:all .3s;position:relative;overflow:hidden}
.kitchen-card.menunggu{background:#1a2b22;border-color:rgba(245,158,11,.3)}
.kitchen-card.cooking{background:#1e2510;border-color:rgba(74,222,128,.4);animation:cookPulse 3s ease infinite}
@keyframes cookPulse{0%,100%{border-color:rgba(74,222,128,.4)}50%{border-color:rgba(74,222,128,.8)}}
.card-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:14px}
.order-num{font-size:18px;font-weight:700;color:#e8f0eb}
.time-badge{font-size:12px;font-weight:700;padding:4px 10px;border-radius:20px}
.time-ok{background:rgba(74,222,128,.12);color:#4ade80}
.time-warn{background:rgba(245,158,11,.12);color:#f59e0b}
.time-late{background:rgba(220,80,60,.12);color:#e07a5f}
.item-row{display:flex;align-items:baseline;justify-content:space-between;padding:7px 0;border-bottom:1px solid rgba(138,170,146,.08);font-size:14px}
.item-row:last-child{border:none}
.item-name{color:#e8f0eb;font-weight:500}
.item-qty{font-size:20px;font-weight:700;color:#4ade80;min-width:32px;text-align:right}
.item-note{font-size:11px;color:#f59e0b;display:block;margin-top:2px}
.action-btn{width:100%;margin-top:14px;padding:11px;border-radius:10px;border:none;cursor:pointer;font-weight:700;font-size:14px;font-family:inherit;transition:all .2s;display:flex;align-items:center;justify-content:center;gap:8px}
.btn-start{background:rgba(245,158,11,.15);color:#f59e0b}.btn-start:hover{background:#d97706;color:#fff}
.btn-done{background:rgba(74,222,128,.15);color:#4ade80}.btn-done:hover{background:#16a34a;color:#fff}
.customer-info{font-size:12px;color:#8fa897;margin-bottom:10px;display:flex;gap:12px;flex-wrap:wrap}
.empty-state{text-align:center;padding:80px 20px;color:#8fa897}
.empty-state i{font-size:52px;display:block;margin-bottom:14px;opacity:.5}
.priority-bar{position:absolute;top:0;left:0;right:0;height:3px;border-radius:2px 2px 0 0}
.priority-menunggu{background:linear-gradient(90deg,#f59e0b,transparent)}
.priority-cooking{background:linear-gradient(90deg,#4ade80,transparent)}
.sound-btn{position:fixed;bottom:20px;left:20px;background:#1e2b25;border:1px solid rgba(138,170,146,.2);border-radius:50%;width:44px;height:44px;display:flex;align-items:center;justify-content:center;cursor:pointer;color:#8fa897;transition:all .2s;z-index:10}
.sound-btn:hover{color:#e8f0eb;border-color:rgba(138,170,146,.4)}
</style>
@endpush

{{-- Summary bar --}}
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:20px">
    <div style="background:#1e2b25;border-radius:12px;padding:14px 18px;border:1px solid rgba(245,158,11,.2);display:flex;align-items:center;gap:12px">
        <i class="ti ti-clock" style="font-size:24px;color:#f59e0b"></i>
        <div><div style="font-size:24px;font-weight:700;color:#e8f0eb" id="sum-menunggu">0</div><div style="font-size:12px;color:#8fa897">Menunggu</div></div>
    </div>
    <div style="background:#1a2510;border-radius:12px;padding:14px 18px;border:1px solid rgba(74,222,128,.2);display:flex;align-items:center;gap:12px">
        <i class="ti ti-chef-hat" style="font-size:24px;color:#4ade80"></i>
        <div><div style="font-size:24px;font-weight:700;color:#e8f0eb" id="sum-cooking">0</div><div style="font-size:12px;color:#8fa897">Sedang Dimasak</div></div>
    </div>
    <div style="background:#1e2b25;border-radius:12px;padding:14px 18px;border:1px solid rgba(138,170,146,.1);display:flex;align-items:center;gap:12px">
        <i class="ti ti-refresh" style="font-size:24px;color:#8aaa92" id="refresh-icon"></i>
        <div><div style="font-size:13px;font-weight:600;color:#e8f0eb">Auto Refresh</div><div style="font-size:12px;color:#8fa897" id="next-refresh">5s</div></div>
    </div>
</div>

<div id="order-grid" class="order-grid">
    <div class="empty-state" style="grid-column:1/-1">
        <i class="ti ti-loader" style="animation:spin 1s linear infinite"></i>
        Memuat pesanan...
    </div>
</div>

{{-- Sound toggle --}}
<button class="sound-btn" id="sound-btn" onclick="toggleSound()" title="Toggle notifikasi suara">
    <i class="ti ti-volume" id="sound-icon" style="font-size:20px"></i>
</button>

<script>
let soundEnabled = true;
let countdown = 5;
let orderIds = new Set();

function toggleSound() {
    soundEnabled = !soundEnabled;
    document.getElementById('sound-icon').className = soundEnabled ? 'ti ti-volume' : 'ti ti-volume-off';
}

function playBeep() {
    if (!soundEnabled) return;
    try {
        const ctx = new (window.AudioContext || window.webkitAudioContext)();
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.connect(gain); gain.connect(ctx.destination);
        osc.frequency.setValueAtTime(880, ctx.currentTime);
        osc.frequency.exponentialRampToValueAtTime(440, ctx.currentTime + 0.3);
        gain.gain.setValueAtTime(0.3, ctx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.5);
        osc.start(ctx.currentTime); osc.stop(ctx.currentTime + 0.5);
    } catch(e) {}
}

function getTimeColor(minutesAgo) {
    if (minutesAgo < 5) return 'time-ok';
    if (minutesAgo < 10) return 'time-warn';
    return 'time-late';
}

function loadOrders() {
    fetch('/kitchen/orders', { headers: { 'Accept': 'application/json' } })
    .then(r => r.json())
    .then(d => {
        const orders = d.orders || [];
        const newIds = new Set(orders.map(o => o.id));
        const hasNew = orders.some(o => !orderIds.has(o.id));
        if (hasNew && orderIds.size > 0) playBeep();
        orderIds = newIds;

        const menunggu = orders.filter(o => o.status === 'menunggu').length;
        const cooking  = orders.filter(o => o.status === 'cooking').length;
        document.getElementById('sum-menunggu').textContent = menunggu;
        document.getElementById('sum-cooking').textContent  = cooking;

        const grid = document.getElementById('order-grid');
        if (!orders.length) {
            grid.innerHTML = '<div class="empty-state" style="grid-column:1/-1"><i class="ti ti-checks"></i><div style="font-size:18px;font-weight:600;color:#e8f0eb;margin-bottom:8px">Semua pesanan selesai!</div><div>Tidak ada pesanan aktif saat ini</div></div>';
            return;
        }

        grid.innerHTML = orders.map(o => `
        <div class="kitchen-card ${o.status}" id="kcard-${o.id}">
            <div class="priority-bar priority-${o.status}"></div>
            <div class="card-header">
                <div>
                    <div class="order-num">${o.order_number}</div>
                    <div class="customer-info">
                        <span><i class="ti ti-user" style="font-size:13px"></i> ${o.customer}</span>
                        ${o.table_number ? `<span><i class="ti ti-armchair" style="font-size:13px"></i> Meja ${o.table_number}</span>` : '<span>Takeaway</span>'}
                    </div>
                </div>
                <span class="time-badge ${getTimeColor(o.minutes_ago)}">${o.time_ago}</span>
            </div>

            <div style="margin-bottom:4px">
                ${o.items.map(item => `
                <div class="item-row">
                    <div>
                        <span class="item-name">${item.name}</span>
                        ${item.note ? `<span class="item-note">📝 ${item.note}</span>` : ''}
                    </div>
                    <span class="item-qty">×${item.quantity}</span>
                </div>`).join('')}
            </div>

            ${o.notes ? `<div style="background:rgba(245,158,11,.08);border-radius:8px;padding:8px 10px;margin-top:8px;font-size:12px;color:#f59e0b"><i class="ti ti-notes" style="font-size:13px"></i> ${o.notes}</div>` : ''}

            <button class="action-btn ${o.status === 'menunggu' ? 'btn-start' : 'btn-done'}"
                onclick="updateOrder(${o.id}, this)">
                ${o.status === 'menunggu'
                    ? '<i class="ti ti-chef-hat"></i> Mulai Masak'
                    : '<i class="ti ti-check"></i> Selesai Dimasak'}
            </button>
        </div>`).join('');
    })
    .catch(() => {});
}

function updateOrder(id, btn) {
    btn.disabled = true;
    btn.innerHTML = '<i class="ti ti-loader" style="animation:spin 1s linear infinite"></i> Memproses...';
    fetch('/kitchen/orders/' + id + '/done', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) { loadOrders(); }
        else { btn.disabled = false; btn.textContent = 'Coba lagi'; }
    });
}

// Countdown timer display
setInterval(() => {
    countdown--;
    if (countdown <= 0) { countdown = 8; loadOrders(); }
    document.getElementById('next-refresh').textContent = countdown + 's';
}, 1000);

// Initial load
loadOrders();
</script>
<style>@keyframes spin{to{transform:rotate(360deg)}}</style>
@endsection