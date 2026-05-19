@extends('layouts.admin')
@section('title','Dashboard')
@section('page-title','Dashboard')

@push('styles')
<style>
.grid-4{display:grid;grid-template-columns:repeat(4,1fr);gap:16px}
.grid-2{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.grid-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px}
.stat-card{background:var(--warm);border-radius:var(--r);border:1px solid var(--border);padding:20px;display:flex;align-items:flex-start;gap:14px;position:relative;overflow:hidden;transition:all .2s}
.stat-card::after{content:'';position:absolute;bottom:-20px;right:-20px;width:80px;height:80px;border-radius:50%;opacity:.04}
.stat-card:hover{transform:translateY(-2px);box-shadow:var(--shadow-md)}
.stat-icon{width:48px;height:48px;border-radius:14px;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:22px}
.stat-val{font-size:26px;font-weight:700;color:var(--text);line-height:1;margin-bottom:4px}
.stat-lbl{font-size:12px;color:var(--muted)}
.stat-change{font-size:11px;font-weight:600;margin-top:6px;display:flex;align-items:center;gap:3px}
.stat-change.up{color:#16a34a}.stat-change.down{color:#c0392b}
.chart-card{background:var(--warm);border-radius:var(--r);border:1px solid var(--border);overflow:hidden}
.chart-head{padding:16px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between}
.chart-title{font-weight:700;font-size:14px;color:var(--text)}
.chart-body{padding:20px}
.order-row{display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid rgba(90,124,101,.06)}
.order-row:last-child{border:none}
.order-num{font-weight:700;font-size:13px;color:var(--text);min-width:110px}
.order-customer{font-size:12px;color:var(--muted);flex:1}
.order-amount{font-weight:700;font-size:13px;color:var(--sage-dark);min-width:80px;text-align:right}
.menu-rank{display:flex;align-items:center;gap:12px;padding:9px 0;border-bottom:1px solid rgba(90,124,101,.06)}
.menu-rank:last-child{border:none}
.rank-num{width:24px;height:24px;border-radius:50%;background:var(--beige);font-size:11px;font-weight:700;display:flex;align-items:center;justify-content:center;color:var(--muted);flex-shrink:0}
.rank-num.top{background:rgba(245,158,11,.12);color:#92400e}
.alert-card{border-radius:var(--r);padding:14px 16px;display:flex;align-items:center;gap:12px;font-size:13px;font-weight:500}
.alert-warning{background:rgba(245,158,11,.08);border:1px solid rgba(245,158,11,.2);color:#92400e}
.alert-info{background:rgba(90,124,101,.08);border:1px solid rgba(90,124,101,.2);color:var(--sage-dark)}
.live-dot{width:8px;height:8px;border-radius:50%;background:#4ade80;display:inline-block;animation:livepulse 2s infinite;margin-right:4px}
@keyframes livepulse{0%,100%{box-shadow:0 0 0 0 rgba(74,222,128,.4)}70%{box-shadow:0 0 0 6px rgba(74,222,128,0)}}
@media(max-width:1100px){.grid-4{grid-template-columns:repeat(2,1fr)}}
@media(max-width:700px){.grid-4{grid-template-columns:1fr}.grid-2,.grid-3{grid-template-columns:1fr}}
</style>
@endpush

@section('content')

{{-- Alert banners --}}
@if($stats['pending_topup'] > 0)
<div class="alert-card alert-warning" style="margin-bottom:16px">
    <i class="ti ti-alert-circle" style="font-size:20px;flex-shrink:0"></i>
    <div>Ada <strong>{{ $stats['pending_topup'] }} topup wallet</strong> yang menunggu konfirmasi.</div>
    <a href="{{ route('admin.wallet.topup-requests') }}" class="btn-primary" style="margin-left:auto;padding:7px 16px;font-size:12px">Proses Sekarang</a>
</div>
@endif
@if($stats['pending_reservation'] > 0)
<div class="alert-card alert-info" style="margin-bottom:16px">
    <i class="ti ti-calendar" style="font-size:20px;flex-shrink:0"></i>
    <div>Ada <strong>{{ $stats['pending_reservation'] }} reservasi</strong> menunggu konfirmasi.</div>
    <a href="{{ route('admin.reservations.index') }}" class="btn-secondary" style="margin-left:auto;padding:7px 16px;font-size:12px">Lihat Reservasi</a>
</div>
@endif

{{-- Stat cards --}}
<div class="grid-4" style="margin-bottom:20px">
    @php
    $cards = [
        ['icon'=>'ti-shopping-bag','bg'=>'rgba(90,124,101,.1)','color'=>'var(--sage)','val'=>$stats['total_orders_today'],'lbl'=>'Order Hari Ini','sub'=>'Active: '.$stats['active_orders'].' order'],
        ['icon'=>'ti-coin','bg'=>'rgba(245,158,11,.1)','color'=>'#b45309','val'=>'Rp '.number_format($stats['revenue_today'],0,',','.'),'lbl'=>'Revenue Hari Ini','sub'=>'Bulan ini: Rp '.number_format($stats['total_revenue_month'],0,',','.')],
        ['icon'=>'ti-users','bg'=>'rgba(59,130,246,.08)','color'=>'#1d4ed8','val'=>$stats['total_customers'],'lbl'=>'Total Customer','sub'=>'+'.$stats['new_customers_month'].' bulan ini'],
        ['icon'=>'ti-activity','bg'=>'rgba(168,85,247,.08)','color'=>'#7c3aed','val'=>$stats['active_orders'],'lbl'=>'Order Aktif','sub'=>'Perlu penanganan segera'],
    ];
    @endphp
    @foreach($cards as $card)
    <div class="stat-card" data-aos="fade-up" data-aos-delay="{{ $loop->index * 60 }}">
        <div class="stat-icon" style="background:{{ $card['bg'] }}">
            <i class="ti {{ $card['icon'] }}" style="color:{{ $card['color'] }};font-size:22px"></i>
        </div>
        <div>
            <div class="stat-val">{{ $card['val'] }}</div>
            <div class="stat-lbl">{{ $card['lbl'] }}</div>
            <div style="font-size:11px;color:var(--muted);margin-top:5px">{{ $card['sub'] }}</div>
        </div>
    </div>
    @endforeach
</div>

{{-- Charts row --}}
<div class="grid-2" style="margin-bottom:20px">
    {{-- Revenue chart --}}
    <div class="chart-card" data-aos="fade-up">
        <div class="chart-head">
            <div>
                <div class="chart-title">Revenue 7 Hari Terakhir</div>
                <div style="font-size:12px;color:var(--muted);margin-top:2px"><span class="live-dot"></span>Realtime</div>
            </div>
            <div style="font-size:22px;font-weight:700;color:var(--sage-dark)">
                Rp {{ number_format(collect($revenueChart)->sum('revenue'),0,',','.') }}
            </div>
        </div>
        <div class="chart-body">
            <canvas id="revenueChart" height="160"></canvas>
        </div>
    </div>

    {{-- Orders chart --}}
    <div class="chart-card" data-aos="fade-up" data-aos-delay="80">
        <div class="chart-head">
            <div class="chart-title">Order per Hari</div>
        </div>
        <div class="chart-body">
            <canvas id="orderChart" height="160"></canvas>
        </div>
    </div>
</div>

{{-- Bottom row --}}
<div class="grid-3" style="margin-bottom:20px">
    {{-- Recent orders --}}
    <div class="chart-card" style="grid-column:span 2" data-aos="fade-up">
        <div class="chart-head">
            <div class="chart-title"><i class="ti ti-receipt" style="color:var(--sage)"></i> Order Terbaru</div>
            <a href="{{ route('admin.orders.index') }}" style="font-size:12px;color:var(--sage);font-weight:600">Lihat semua →</a>
        </div>
        <div style="padding:0 0 8px">
            @forelse($recentOrders as $order)
            <div class="order-row" style="padding:10px 20px">
                <div>
                    <div class="order-num">{{ $order->order_number }}</div>
                    <div style="font-size:11px;color:var(--muted);margin-top:2px">{{ $order->created_at->diffForHumans() }}</div>
                </div>
                <div class="order-customer">
                    <div style="font-weight:500;font-size:13px">{{ $order->user->name }}</div>
                    <div style="font-size:11px;color:var(--muted)">{{ $order->items->sum('quantity') }} item</div>
                </div>
                @php $statusColor=['menunggu'=>'amber','cooking'=>'coral','selesai'=>'teal','completed'=>'green','dibatalkan'=>'red']; @endphp
                <span class="badge badge-{{ $statusColor[$order->status] ?? 'gray' }}">{{ $order->status_label }}</span>
                <div class="order-amount">Rp {{ number_format($order->total_amount,0,',','.') }}</div>
                <a href="{{ route('admin.orders.show',$order->id) }}" class="btn-icon btn-icon-sage">
                    <i class="ti ti-eye" style="font-size:14px"></i>
                </a>
            </div>
            @empty
            <div style="padding:30px;text-align:center;color:var(--muted);font-size:13px">Belum ada order hari ini</div>
            @endforelse
        </div>
    </div>

    {{-- Top menus --}}
    <div class="chart-card" data-aos="fade-up" data-aos-delay="80">
        <div class="chart-head">
            <div class="chart-title"><i class="ti ti-award" style="color:var(--sage)"></i> Top Menu</div>
        </div>
        <div style="padding:12px 20px">
            @foreach($topMenus as $i => $menu)
            <div class="menu-rank">
                <span class="rank-num {{ $i < 3 ? 'top' : '' }}">{{ $i+1 }}</span>
                <div style="flex:1;min-width:0">
                    <div style="font-size:13px;font-weight:600;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $menu->name }}</div>
                    <div style="font-size:11px;color:var(--muted)">{{ number_format($menu->order_count) }}× dipesan</div>
                </div>
                <div style="text-align:right">
                    <div style="display:flex;gap:2px">
                        @for($j=1;$j<=5;$j++)
                        <i class="ti ti-star-filled" style="font-size:11px;color:{{ $j<=round($menu->rating) ? '#f59e0b' : 'var(--border)' }}"></i>
                        @endfor
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Pending topup --}}
@if($recentTopups->count())
<div class="chart-card" data-aos="fade-up">
    <div class="chart-head">
        <div class="chart-title"><i class="ti ti-wallet" style="color:var(--sage)"></i> Topup Menunggu Konfirmasi</div>
        <a href="{{ route('admin.wallet.topup-requests') }}" style="font-size:12px;color:var(--sage);font-weight:600">Lihat semua →</a>
    </div>
    <div style="overflow-x:auto">
        <table class="admin-table">
            <thead><tr>
                <th>Kode</th><th>User</th><th>Nominal</th><th>Metode</th><th>Waktu</th><th>Aksi</th>
            </tr></thead>
            <tbody>
                @foreach($recentTopups as $topup)
                <tr>
                    <td><span style="font-family:monospace;font-size:12px;font-weight:600">{{ $topup->transaction_code }}</span></td>
                    <td>
                        <div style="font-weight:600;font-size:13px">{{ $topup->user->name }}</div>
                        <div style="font-size:11px;color:var(--muted)">{{ $topup->user->email }}</div>
                    </td>
                    <td><strong style="color:var(--sage-dark)">Rp {{ number_format($topup->amount,0,',','.') }}</strong></td>
                    <td style="font-size:12px;text-transform:capitalize">{{ str_replace('_',' ',$topup->payment_method) }}</td>
                    <td style="font-size:12px;color:var(--muted)">{{ $topup->created_at->diffForHumans() }}</td>
                    <td>
                        <div style="display:flex;gap:6px">
                            <button onclick="approveTopup({{ $topup->id }},this)" class="btn-primary" style="padding:5px 12px;font-size:11px;border-radius:8px">
                                <i class="ti ti-check"></i> Approve
                            </button>
                            <button onclick="rejectTopup({{ $topup->id }},this)" class="btn-danger" style="padding:5px 12px;font-size:11px;border-radius:8px">
                                Tolak
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css">
<script>
AOS.init({duration:600,once:true,offset:20});

const chartData = @json($revenueChart);
const labels  = chartData.map(d => d.date);
const revenue = chartData.map(d => d.revenue);
const orders  = chartData.map(d => d.orders);

const chartDefaults = {
    plugins:{ legend:{display:false}, tooltip:{callbacks:{label:ctx=>ctx.dataset.label+': '+ctx.formattedValue}} },
    scales:{ x:{grid:{display:false},ticks:{font:{size:11}}}, y:{grid:{color:'rgba(90,124,101,.06)'},ticks:{font:{size:11}}} },
    responsive:true, maintainAspectRatio:false,
};

// Revenue chart
new Chart(document.getElementById('revenueChart'),{
    type:'line',
    data:{
        labels,
        datasets:[{
            label:'Revenue',
            data:revenue,
            borderColor:'#5a7c65',
            backgroundColor:'rgba(90,124,101,.08)',
            borderWidth:2.5,
            fill:true,
            tension:.4,
            pointBackgroundColor:'#5a7c65',
            pointRadius:4,
            pointHoverRadius:6,
        }]
    },
    options:{...chartDefaults, scales:{...chartDefaults.scales, y:{...chartDefaults.scales.y, ticks:{callback:v=>'Rp '+Number(v).toLocaleString('id-ID'),font:{size:10}}}}}
});

// Order chart
new Chart(document.getElementById('orderChart'),{
    type:'bar',
    data:{
        labels,
        datasets:[{
            label:'Order',
            data:orders,
            backgroundColor:'rgba(90,124,101,.15)',
            borderColor:'#5a7c65',
            borderWidth:1.5,
            borderRadius:6,
        }]
    },
    options:chartDefaults
});

function approveTopup(id, btn) {
    if(!confirm('Approve topup ini?')) return;
    btn.disabled = true; btn.innerHTML = '<i class="ti ti-loader"></i>';
    ajax('/admin/wallet/topup/'+id+'/approve','POST')
    .then(d => { showToast(d.message,'success'); setTimeout(()=>location.reload(),800); })
    .catch(()=>{btn.disabled=false;});
}
function rejectTopup(id, btn) {
    const note = prompt('Alasan penolakan (opsional):');
    if(note === null) return;
    btn.disabled = true;
    ajax('/admin/wallet/topup/'+id+'/reject','POST',{note})
    .then(d => { showToast(d.message,'info'); setTimeout(()=>location.reload(),800); });
}
</script>
@endpush