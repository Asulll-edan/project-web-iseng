@extends('layouts.admin')
@section('title','Detail Order — '.$order->order_number)
@section('page-title','Detail Pesanan')
@section('breadcrumb')
<i class="ti ti-chevron-right" style="font-size:12px"></i>
<a href="{{ route('admin.orders.index') }}" style="color:var(--muted);text-decoration:none">Pesanan</a>
<i class="ti ti-chevron-right" style="font-size:12px"></i>
<span>{{ $order->order_number }}</span>
@endsection

@section('content')
<div class="page-header">
    <div>
        <div class="page-title">{{ $order->order_number }}</div>
        <div class="page-sub">{{ $order->created_at->format('d M Y, H:i') }} WIB</div>
    </div>
    <div style="display:flex;gap:10px">
        <a href="{{ route('admin.orders.index') }}" class="btn-secondary"><i class="ti ti-arrow-left"></i> Kembali</a>
        @if(!in_array($order->status,['completed','dibatalkan']))
        <button onclick="cancelOrder({{ $order->id }})" class="btn-danger">
            <i class="ti ti-x"></i> Batalkan Order
        </button>
        @endif
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 320px;gap:20px;align-items:start">
    <div>
        {{-- Order items --}}
        <div class="content-card" style="margin-bottom:16px">
            <div class="card-head">
                <div class="card-title"><i class="ti ti-receipt" style="color:var(--sage)"></i> Item Pesanan</div>
                <span class="badge {{ ['menunggu'=>'badge-amber','cooking'=>'badge-amber','selesai'=>'badge-sage','completed'=>'badge-green','dibatalkan'=>'badge-red'][$order->status] ?? 'badge-gray' }}">
                    {{ $order->status_label }}
                </span>
            </div>
            <div style="overflow-x:auto">
                <table class="admin-table">
                    <thead><tr><th>Menu</th><th>Harga Satuan</th><th>Qty</th><th>Subtotal</th><th>Catatan</th></tr></thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr>
                            <td style="font-weight:600;font-size:13px">{{ $item->menu_name }}</td>
                            <td>Rp {{ number_format($item->price,0,',','.') }}</td>
                            <td><span style="font-weight:700;font-size:14px">×{{ $item->quantity }}</span></td>
                            <td style="font-weight:700;color:var(--sage-dark)">Rp {{ number_format($item->subtotal,0,',','.') }}</td>
                            <td style="font-size:12px;color:var(--muted)">{{ $item->note ?: '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div style="padding:16px 20px;border-top:1px solid var(--border)">
                @foreach([['Subtotal',$order->subtotal],['Diskon',$order->discount_amount],['Pajak (10%)',$order->tax_amount]] as $r)
                @if($r[1] > 0)
                <div style="display:flex;justify-content:space-between;font-size:13px;color:var(--muted);margin-bottom:7px">
                    <span>{{ $r[0] }}</span>
                    <span>{{ $r[0]==='Diskon' ? '-' : '' }}Rp {{ number_format($r[1],0,',','.') }}</span>
                </div>
                @endif
                @endforeach
                <div style="display:flex;justify-content:space-between;font-size:17px;font-weight:700;color:var(--text);padding-top:10px;border-top:1px solid var(--border);margin-top:6px">
                    <span>Total</span>
                    <span style="color:var(--sage-dark)">Rp {{ number_format($order->total_amount,0,',','.') }}</span>
                </div>
            </div>
        </div>

        {{-- Timeline --}}
        <div class="content-card">
            <div class="card-head">
                <div class="card-title"><i class="ti ti-timeline" style="color:var(--sage)"></i> Timeline Status</div>
            </div>
            <div style="padding:20px">
                @foreach($order->statusLogs as $log)
                <div style="display:flex;gap:14px;align-items:flex-start;padding-bottom:16px;position:relative">
                    @if(!$loop->last)
                    <div style="position:absolute;left:14px;top:28px;bottom:0;width:1px;background:var(--border)"></div>
                    @endif
                    <div style="width:28px;height:28px;border-radius:50%;background:rgba(90,124,101,.12);border:2px solid var(--sage);display:flex;align-items:center;justify-content:center;flex-shrink:0;z-index:1">
                        <i class="ti ti-check" style="font-size:13px;color:var(--sage)"></i>
                    </div>
                    <div style="background:var(--beige);border-radius:10px;padding:10px 14px;flex:1">
                        <div style="font-weight:700;font-size:13px;color:var(--text);text-transform:capitalize;margin-bottom:3px">{{ $log->status }}</div>
                        <div style="font-size:12px;color:var(--muted)">{{ $log->note }}</div>
                        <div style="font-size:11px;color:var(--muted);margin-top:4px;display:flex;gap:10px">
                            <span><i class="ti ti-clock" style="font-size:12px"></i> {{ $log->created_at->format('d M Y H:i:s') }}</span>
                            @if($log->user)
                            <span><i class="ti ti-user" style="font-size:12px"></i> {{ $log->user->name }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Right sidebar --}}
    <div>
        {{-- Customer info --}}
        <div class="content-card" style="margin-bottom:16px">
            <div class="card-head"><div class="card-title"><i class="ti ti-user" style="color:var(--sage)"></i> Customer</div></div>
            <div style="padding:16px">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px">
                    <img src="{{ $order->user->avatar_url }}" style="width:42px;height:42px;border-radius:50%;object-fit:cover" alt="">
                    <div>
                        <div style="font-weight:700;font-size:14px">{{ $order->user->name }}</div>
                        <div style="font-size:12px;color:var(--muted)">{{ $order->user->email }}</div>
                    </div>
                </div>
                <a href="{{ route('admin.users.show',$order->user_id) }}" class="btn-secondary" style="width:100%;justify-content:center;font-size:12px">
                    Lihat Profil User
                </a>
            </div>
        </div>

        {{-- Order info --}}
        <div class="content-card" style="margin-bottom:16px">
            <div class="card-head"><div class="card-title"><i class="ti ti-info-circle" style="color:var(--sage)"></i> Info Pesanan</div></div>
            <div style="padding:16px">
                @foreach([
                    ['Tipe Order',ucfirst(str_replace('_',' ',$order->order_type ?? 'dine_in'))],
                    ['Nomor Meja',$order->table_number ?? 'Takeaway'],
                    ['Metode Bayar',ucfirst(str_replace('_',' ',$order->payment_method))],
                    ['Status Bayar',ucfirst($order->payment_status)],
                    ['Voucher',$order->voucher_code ?? '—'],
                    ['Est. Waktu',$order->estimated_time ?? '—'],
                ] as $row)
                <div style="display:flex;justify-content:space-between;padding:7px 0;border-bottom:1px solid var(--border);font-size:13px">
                    <span style="color:var(--muted)">{{ $row[0] }}</span>
                    <span style="font-weight:600">{{ $row[1] }}</span>
                </div>
                @endforeach
                @if($order->notes)
                <div style="margin-top:10px;background:rgba(245,158,11,.06);border-radius:8px;padding:10px;font-size:12px;color:var(--text)">
                    <div style="font-weight:700;margin-bottom:4px;color:#92400e"><i class="ti ti-notes"></i> Catatan</div>
                    {{ $order->notes }}
                </div>
                @endif
            </div>
        </div>

        {{-- Payment --}}
        @if($order->payment)
        <div class="content-card" style="margin-bottom:16px">
            <div class="card-head"><div class="card-title"><i class="ti ti-credit-card" style="color:var(--sage)"></i> Pembayaran</div></div>
            <div style="padding:16px;font-size:13px">
                <div style="display:flex;justify-content:space-between;margin-bottom:7px">
                    <span style="color:var(--muted)">Kode</span>
                    <span style="font-family:monospace;font-size:12px;font-weight:600">{{ $order->payment->payment_code }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;margin-bottom:7px">
                    <span style="color:var(--muted)">Status</span>
                    <span class="badge {{ $order->payment->status === 'paid' ? 'badge-green' : 'badge-amber' }}">{{ ucfirst($order->payment->status) }}</span>
                </div>
                @if($order->payment->paid_at)
                <div style="display:flex;justify-content:space-between">
                    <span style="color:var(--muted)">Dibayar</span>
                    <span>{{ $order->payment->paid_at->format('d M H:i') }}</span>
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- Cashback & Points earned --}}
        @if($order->status === 'completed')
        <div class="content-card">
            <div class="card-head"><div class="card-title"><i class="ti ti-coin" style="color:#f59e0b"></i> Reward Diperoleh</div></div>
            <div style="padding:16px">
                <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:7px">
                    <span style="color:var(--muted)">Loyalty Points</span>
                    <span style="font-weight:700;color:var(--sage-dark)">+{{ $order->loyalty_points_earned }} pts</span>
                </div>
                <div style="display:flex;justify-content:space-between;font-size:13px">
                    <span style="color:var(--muted)">Cashback</span>
                    <span style="font-weight:700;color:var(--sage-dark)">+Rp {{ number_format($order->cashback_earned,0,',','.') }}</span>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function cancelOrder(id){confirmDanger('Batalkan Order?','Tindakan ini tidak bisa dibatalkan.',()=>{
    ajax('/admin/orders/'+id+'/cancel','POST')
    .then(d => { showToast(d.message,'info'); setTimeout(()=>location.reload(),700); });
});}
</script>
@endpush
