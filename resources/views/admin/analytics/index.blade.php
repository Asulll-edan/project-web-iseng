@extends('layouts.admin')
@section('title','Analytics Dashboard')
@section('page-title','Analytics')

@push('styles')
<style>
.period-btn{padding:7px 16px;border-radius:20px;font-size:12px;font-weight:600;border:1.5px solid var(--border);background:transparent;color:var(--muted);cursor:pointer;transition:all .2s}
.period-btn.active,.period-btn:hover{background:var(--sage);color:#fff;border-color:var(--sage)}
.analytics-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:20px}
.analytics-card{background:var(--warm);border-radius:var(--r);border:1px solid var(--border);padding:18px}
.analytics-card-icon{width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;margin-bottom:10px}
.chart-wrap{background:var(--warm);border-radius:var(--r);border:1px solid var(--border);overflow:hidden;margin-bottom:16px}
.chart-head{padding:16px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between}
.chart-title{font-weight:700;font-size:14px;color:var(--text);display:flex;align-items:center;gap:8px}
.chart-body{padding:20px}
.top-menu-bar{display:flex;align-items:center;gap:12px;padding:9px 0;border-bottom:1px solid var(--border)}
.top-menu-bar:last-child{border:none}
.bar-track{flex:1;height:6px;background:var(--beige);border-radius:3px;overflow:hidden}
.bar-fill{height:100%;background:var(--sage);border-radius:3px;transition:width 1s ease}
@media(max-width:900px){.analytics-grid{grid-template-columns:1fr 1fr}}
@media(max-width:600px){.analytics-grid{grid-template-columns:1fr}}
</style>
@endpush

@section('content')
<div class="page-header">
    <div>
        <div class="page-title">Analytics Dashboard</div>
        <div class="page-sub">Data & insight performa restoran secara realtime</div>
    </div>
    <div style="display:flex;gap:6px" id="period-btns">
        @foreach(['7'=>'7 Hari','14'=>'14 Hari','30'=>'30 Hari'] as $v=>$l)
        <button class="period-btn {{ $v==='7'?'active':'' }}" onclick="setPeriod('{{ $v }}',this)">{{ $l }}</button>
        @endforeach
    </div>
</div>

{{-- KPI Summary --}}
<div class="analytics-grid" id="kpi-row">
    @foreach([
        ['ti-coin','rgba(90,124,101,.1)','var(--sage)','Total Revenue','loading...','revenue_total'],
        ['ti-shopping-bag','rgba(59,130,246,.08)','#1d4ed8','Total Order','loading...','order_total'],
        ['ti-users','rgba(245,158,11,.1)','#b45309','Customer Baru','loading...','customer_total'],
        ['ti-wallet','rgba(168,85,247,.08)','#7c3aed','Total Transaksi Wallet','loading...','wallet_tx'],
    ] as $k)
    <div class="analytics-card" data-kpi="{{ $k[5] }}">
        <div class="analytics-card-icon" style="background:{{ $k[1] }}">
            <i class="ti {{ $k[0] }}" style="font-size:20px;color:{{ $k[2] }}"></i>
        </div>
        <div style="font-size:12px;color:var(--muted);margin-bottom:5px">{{ $k[3] }}</div>
        <div style="font-size:22px;font-weight:700;color:var(--text)" id="kpi-{{ $k[5] }}">—</div>
    </div>
    @endforeach
</div>

{{-- Revenue + Orders chart row --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
    <div class="chart-wrap">
        <div class="chart-head">
            <div class="chart-title"><i class="ti ti-trending-up" style="color:var(--sage)"></i> Revenue Harian</div>
        </div>
        <div class="chart-body"><canvas id="chart-revenue" height="200"></canvas></div>
    </div>
    <div class="chart-wrap">
        <div class="chart-head">
            <div class="chart-title"><i class="ti ti-shopping-bag" style="color:var(--sage)"></i> Order Harian</div>
        </div>
        <div class="chart-body"><canvas id="chart-orders" height="200"></canvas></div>
    </div>
</div>

{{-- Customer + Status --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
    <div class="chart-wrap">
        <div class="chart-head">
            <div class="chart-title"><i class="ti ti-users" style="color:var(--sage)"></i> Customer Baru per Hari</div>
        </div>
        <div class="chart-body"><canvas id="chart-customers" height="200"></canvas></div>
    </div>
    <div class="chart-wrap">
        <div class="chart-head">
            <div class="chart-title"><i class="ti ti-chart-donut" style="color:var(--sage)"></i> Status Order</div>
        </div>
        <div class="chart-body" style="display:flex;align-items:center;justify-content:center;min-height:200px">
            <canvas id="chart-status" height="200" width="200"></canvas>
        </div>
    </div>
</div>

{{-- Membership + Top Menus --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
    <div class="chart-wrap">
        <div class="chart-head">
            <div class="chart-title"><i class="ti ti-award" style="color:var(--sage)"></i> Distribusi Membership</div>
        </div>
        <div class="chart-body" style="display:flex;align-items:center;justify-content:center;min-height:200px">
            <canvas id="chart-member" height="200" width="200"></canvas>
        </div>
    </div>

    <div class="chart-wrap">
        <div class="chart-head">
            <div class="chart-title"><i class="ti ti-award" style="color:var(--sage)"></i> Top 5 Menu</div>
        </div>
        <div class="chart-body" id="top-menus-list">
            <div style="text-align:center;color:var(--muted);padding:20px">Memuat data...</div>
        </div>
    </div>
</div>

{{-- Wallet stats --}}
<div class="chart-wrap">
    <div class="chart-head">
        <div class="chart-title"><i class="ti ti-wallet" style="color:var(--sage)"></i> Statistik Wallet</div>
    </div>
    <div class="chart-body">
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px" id="wallet-stats">
            @foreach(['wallet_topup'=>['Total Topup','ti-arrow-down-circle','rgba(90,124,101,.1)','var(--sage)'],'wallet_spent'=>['Total Terpakai','ti-arrow-up-circle','rgba(245,158,11,.1)','#b45309'],'wallet_tx'=>['Jumlah Transaksi','ti-transfer','rgba(59,130,246,.08)','#1d4ed8']] as $k=>$info)
            <div style="background:var(--beige);border-radius:12px;padding:16px;display:flex;align-items:center;gap:12px">
                <div style="width:40px;height:40px;border-radius:10px;background:{{ $info[2] }};display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <i class="ti {{ $info[1] }}" style="font-size:18px;color:{{ $info[3] }}"></i>
                </div>
                <div>
                    <div style="font-size:11px;color:var(--muted)">{{ $info[0] }}</div>
                    <div style="font-size:18px;font-weight:700" id="ws-{{ $k }}">—</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentPeriod = '7';
let charts = {};

const CHART_COLORS = {
    sage:   '#5a7c65',
    sageL:  'rgba(90,124,101,.12)',
    amber:  '#f59e0b',
    blue:   '#3b82f6',
    red:    '#ef4444',
    purple: '#8b5cf6',
    teal:   '#14b8a6',
};

function setPeriod(period, btn) {
    currentPeriod = period;
    document.querySelectorAll('.period-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    loadData();
}

function loadData() {
    fetch('/admin/analytics/data?period=' + currentPeriod, { headers: { 'Accept': 'application/json' } })
    .then(r => r.json())
    .then(d => {
        renderRevenue(d.revenue);
        renderOrders(d.revenue);
        renderCustomers(d.customers);
        renderStatus(d.orderStatus);
        renderMembership(d.memberDist);
        renderTopMenus(d.topMenus);
        renderWalletStats(d.walletStats);
        updateKPIs(d);
    })
    .catch(e => console.error('Analytics error:', e));
}

function destroyChart(id) {
    if (charts[id]) { charts[id].destroy(); delete charts[id]; }
}

function renderRevenue(data) {
    destroyChart('revenue');
    const ctx = document.getElementById('chart-revenue');
    charts['revenue'] = new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.map(d => d.date),
            datasets: [{
                label: 'Revenue',
                data: data.map(d => d.revenue),
                borderColor: CHART_COLORS.sage,
                backgroundColor: CHART_COLORS.sageL,
                borderWidth: 2.5, fill: true, tension: 0.4,
                pointBackgroundColor: CHART_COLORS.sage, pointRadius: 4, pointHoverRadius: 6,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, ticks: { font: { size: 11 } } },
                y: { grid: { color: 'rgba(90,124,101,.06)' }, ticks: { font: { size: 10 }, callback: v => 'Rp ' + Number(v).toLocaleString('id-ID') } }
            }
        }
    });
}

function renderOrders(data) {
    destroyChart('orders');
    charts['orders'] = new Chart(document.getElementById('chart-orders'), {
        type: 'bar',
        data: {
            labels: data.map(d => d.date),
            datasets: [{
                label: 'Order',
                data: data.map(d => d.orders),
                backgroundColor: 'rgba(59,130,246,.15)',
                borderColor: CHART_COLORS.blue,
                borderWidth: 1.5, borderRadius: 6,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, ticks: { font: { size: 11 } } },
                y: { grid: { color: 'rgba(90,124,101,.06)' }, ticks: { font: { size: 11 } } }
            }
        }
    });
}

function renderCustomers(data) {
    destroyChart('customers');
    charts['customers'] = new Chart(document.getElementById('chart-customers'), {
        type: 'bar',
        data: {
            labels: data.map(d => d.date),
            datasets: [{
                label: 'Customer Baru',
                data: data.map(d => d.count),
                backgroundColor: 'rgba(245,158,11,.15)',
                borderColor: CHART_COLORS.amber,
                borderWidth: 1.5, borderRadius: 6,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, ticks: { font: { size: 11 } } },
                y: { grid: { color: 'rgba(90,124,101,.06)' }, ticks: { font: { size: 11 } } }
            }
        }
    });
}

function renderStatus(data) {
    destroyChart('status');
    const labels = Object.keys(data);
    const values = Object.values(data);
    const colors = { menunggu: CHART_COLORS.amber, cooking: '#f97316', selesai: CHART_COLORS.teal, completed: CHART_COLORS.sage, dibatalkan: CHART_COLORS.red };
    charts['status'] = new Chart(document.getElementById('chart-status'), {
        type: 'doughnut',
        data: {
            labels: labels.map(l => l.charAt(0).toUpperCase() + l.slice(1)),
            datasets: [{ data: values, backgroundColor: labels.map(l => colors[l] || '#94a3b8'), borderWidth: 2, borderColor: '#fff' }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom', labels: { font: { size: 11 }, padding: 12 } } },
            cutout: '60%',
        }
    });
}

function renderMembership(data) {
    destroyChart('member');
    const labelMap = { none: 'Non Member', silver: 'Silver', gold: 'Gold', platinum: 'Platinum' };
    const colorMap = { none: '#94a3b8', silver: '#64748b', gold: CHART_COLORS.amber, platinum: CHART_COLORS.purple };
    const labels = Object.keys(data);
    charts['member'] = new Chart(document.getElementById('chart-member'), {
        type: 'doughnut',
        data: {
            labels: labels.map(l => labelMap[l] || l),
            datasets: [{ data: Object.values(data), backgroundColor: labels.map(l => colorMap[l] || '#94a3b8'), borderWidth: 2, borderColor: '#fff' }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom', labels: { font: { size: 11 }, padding: 10 } } },
            cutout: '60%',
        }
    });
}

function renderTopMenus(menus) {
    const container = document.getElementById('top-menus-list');
    if (!menus || !menus.length) { container.innerHTML = '<div style="text-align:center;color:var(--muted)">Belum ada data</div>'; return; }
    const max = Math.max(...menus.map(m => m.order_count)) || 1;
    container.innerHTML = menus.map((m, i) => `
    <div class="top-menu-bar">
        <div style="width:22px;height:22px;border-radius:50%;background:${i<3?'rgba(90,124,101,.15)':'var(--beige)'};font-size:11px;font-weight:700;display:flex;align-items:center;justify-content:center;color:${i<3?'var(--sage-dark)':'var(--muted)'};flex-shrink:0">${i+1}</div>
        <div style="flex:1;min-width:0">
            <div style="font-size:13px;font-weight:600;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${m.name}</div>
            <div class="bar-track" style="margin-top:5px"><div class="bar-fill" style="width:${Math.round((m.order_count/max)*100)}%"></div></div>
        </div>
        <div style="text-align:right;flex-shrink:0">
            <div style="font-weight:700;font-size:13px">${Number(m.order_count).toLocaleString('id-ID')}×</div>
            <div style="font-size:11px;color:var(--muted)">⭐ ${Number(m.rating).toFixed(1)}</div>
        </div>
    </div>`).join('');
}

function renderWalletStats(ws) {
    if (!ws) return;
    document.getElementById('ws-wallet_topup').textContent = 'Rp ' + Number(ws.total_topup).toLocaleString('id-ID');
    document.getElementById('ws-wallet_spent').textContent  = 'Rp ' + Number(ws.total_spent).toLocaleString('id-ID');
    document.getElementById('ws-wallet_tx').textContent     = Number(ws.transactions).toLocaleString('id-ID');
}

function updateKPIs(d) {
    const revenue = d.revenue ? d.revenue.reduce((s,r)=>s+r.revenue,0) : 0;
    const orders  = d.revenue ? d.revenue.reduce((s,r)=>s+r.orders,0)  : 0;
    const custNew = d.customers ? d.customers.reduce((s,c)=>s+c.count,0) : 0;
    document.getElementById('kpi-revenue_total').textContent  = 'Rp ' + revenue.toLocaleString('id-ID');
    document.getElementById('kpi-order_total').textContent    = orders.toLocaleString('id-ID') + ' order';
    document.getElementById('kpi-customer_total').textContent = custNew.toLocaleString('id-ID') + ' user';
    document.getElementById('kpi-wallet_tx').textContent      = (d.walletStats ? d.walletStats.transactions : 0).toLocaleString('id-ID') + ' tx';
}

// Initial load
loadData();
</script>
@endpush