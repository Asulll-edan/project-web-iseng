@extends('layouts.kasir')
@section('title','Detail Order ' . $order->order_number)

@section('content')
<div style="max-width:760px">
    <a href="{{ route('kasir.orders.index') }}" style="color:#8fa897;font-size:13px;display:inline-flex;align-items:center;gap:6px;margin-bottom:20px">
        <i class="ti ti-arrow-left"></i> Kembali ke daftar order
    </a>

    <div style="background:#1e2b25;border-radius:16px;border:1px solid rgba(138,170,146,.12);padding:24px;margin-bottom:16px">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:14px;margin-bottom:20px">
            <div>
                <div style="font-size:22px;font-weight:700;color:#e8f0eb">{{ $order->order_number }}</div>
                <div style="font-size:13px;color:#8fa897;margin-top:4px">{{ $order->created_at->format('d M Y H:i') }}</div>
            </div>
            @php $colors=['menunggu'=>'#f59e0b','cooking'=>'#e07a5f','selesai'=>'#4ade80','completed'=>'#8aaa92','dibatalkan'=>'#e07a5f']; @endphp
            <span style="background:rgba({{ implode(',',sscanf($colors[$order->status]??'#8fa897','#%02x%02x%02x')) }},.15);color:{{ $colors[$order->status]??'#8fa897' }};border-radius:20px;padding:6px 16px;font-size:13px;font-weight:700">
                {{ $order->status_label }}
            </span>
        </div>

        {{-- Customer info --}}
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:20px">
            @foreach([
                ['ti-user','Customer',$order->user->name],
                ['ti-phone','No. HP',$order->user->phone ?? '-'],
                ['ti-credit-card','Pembayaran',ucfirst($order->payment_method).' · '.ucfirst($order->payment_status)],
                ['ti-armchair','Meja',$order->table_number ?? 'Takeaway'],
            ] as $info)
            <div style="background:#0f1a16;border-radius:10px;padding:12px 14px">
                <div style="font-size:11px;color:#8fa897;text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;display:flex;align-items:center;gap:5px">
                    <i class="ti {{ $info[0] }}" style="font-size:13px"></i> {{ $info[1] }}
                </div>
                <div style="font-size:14px;font-weight:600;color:#e8f0eb">{{ $info[2] }}</div>
            </div>
            @endforeach
        </div>

        @if($order->notes)
        <div style="background:rgba(245,158,11,.08);border:1px solid rgba(245,158,11,.15);border-radius:10px;padding:12px 14px;margin-bottom:20px">
            <div style="font-size:12px;color:#f59e0b;font-weight:700;margin-bottom:4px"><i class="ti ti-notes" style="font-size:14px"></i> CATATAN PESANAN</div>
            <div style="font-size:14px;color:#e8f0eb">{{ $order->notes }}</div>
        </div>
        @endif

        {{-- Order items --}}
        <div style="border-top:1px solid rgba(138,170,146,.1);padding-top:16px;margin-bottom:16px">
            <div style="font-size:13px;font-weight:700;color:#8fa897;text-transform:uppercase;letter-spacing:.05em;margin-bottom:12px">Item Pesanan</div>
            @foreach($order->items as $item)
            <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:1px solid rgba(138,170,146,.07)">
                <div>
                    <div style="font-weight:600;font-size:14px;color:#e8f0eb">{{ $item->menu_name }}</div>
                    @if($item->note)
                    <div style="font-size:12px;color:#8fa897;margin-top:2px">📝 {{ $item->note }}</div>
                    @endif
                </div>
                <div style="text-align:right">
                    <div style="font-size:13px;color:#8fa897">×{{ $item->quantity }}</div>
                    <div style="font-weight:700;font-size:14px;color:#e8f0eb">Rp {{ number_format($item->subtotal,0,',','.') }}</div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Total --}}
        <div style="background:#0f1a16;border-radius:10px;padding:14px 16px">
            @foreach([['Subtotal','subtotal'],['Diskon','discount_amount'],['Pajak','tax_amount']] as $row)
            @if($order->{$row[1]} > 0)
            <div style="display:flex;justify-content:space-between;font-size:13px;color:#8fa897;margin-bottom:7px">
                <span>{{ $row[0] }}</span>
                <span>{{ $row[1]==='discount_amount'?'-':'' }}Rp {{ number_format($order->{$row[1]},0,',','.') }}</span>
            </div>
            @endif
            @endforeach
            <div style="display:flex;justify-content:space-between;font-size:17px;font-weight:700;color:#e8f0eb;border-top:1px solid rgba(138,170,146,.1);padding-top:10px;margin-top:4px">
                <span>Total</span>
                <span>Rp {{ number_format($order->total_amount,0,',','.') }}</span>
            </div>
        </div>
    </div>

    {{-- Status Update --}}
    @if(in_array($order->status, ['menunggu','cooking']))
    <div style="background:#1e2b25;border-radius:16px;border:1px solid rgba(138,170,146,.12);padding:20px;margin-bottom:16px">
        <div style="font-size:14px;font-weight:700;color:#e8f0eb;margin-bottom:14px">Update Status</div>
        <div style="display:flex;gap:10px">
            @if($order->status === 'menunggu')
            <button onclick="updateStatus('cooking')" style="flex:1;padding:12px;background:rgba(224,122,95,.15);color:#e07a5f;border-radius:10px;border:none;cursor:pointer;font-weight:700;font-size:14px;transition:all .2s" onmouseover="this.style.background='#e07a5f';this.style.color='#fff'" onmouseout="this.style.background='rgba(224,122,95,.15)';this.style.color='#e07a5f'">
                <i class="ti ti-chef-hat"></i> Mulai Masak
            </button>
            @endif
            @if($order->status === 'cooking')
            <button onclick="updateStatus('selesai')" style="flex:1;padding:12px;background:rgba(74,222,128,.12);color:#4ade80;border-radius:10px;border:none;cursor:pointer;font-weight:700;font-size:14px;transition:all .2s" onmouseover="this.style.background='#16a34a';this.style.color='#fff'" onmouseout="this.style.background='rgba(74,222,128,.12)';this.style.color='#4ade80'">
                <i class="ti ti-check"></i> Tandai Selesai
            </button>
            @endif
            @if($order->status === 'menunggu')
            <button onclick="updateStatus('dibatalkan')" style="padding:12px 20px;background:rgba(220,80,60,.1);color:#e07a5f;border-radius:10px;border:none;cursor:pointer;font-weight:600;font-size:13px;transition:all .2s">
                Batalkan
            </button>
            @endif
        </div>
    </div>
    @endif

    {{-- Status Timeline --}}
    <div style="background:#1e2b25;border-radius:16px;border:1px solid rgba(138,170,146,.12);padding:20px">
        <div style="font-size:14px;font-weight:700;color:#e8f0eb;margin-bottom:14px">Timeline Status</div>
        <div style="display:flex;flex-direction:column;gap:0">
            @foreach($order->statusLogs as $log)
            <div style="display:flex;gap:12px;align-items:flex-start;padding-bottom:14px;position:relative">
                <div style="width:28px;height:28px;border-radius:50%;background:rgba(90,124,101,.2);border:2px solid rgba(138,170,146,.3);display:flex;align-items:center;justify-content:center;flex-shrink:0;z-index:1">
                    <i class="ti ti-check" style="font-size:13px;color:#8aaa92"></i>
                </div>
                <div style="background:#0f1a16;border-radius:10px;padding:10px 14px;flex:1">
                    <div style="font-weight:600;font-size:13px;color:#e8f0eb">{{ $log->status }}</div>
                    <div style="font-size:12px;color:#8fa897;margin-top:2px">{{ $log->note }}</div>
                    <div style="font-size:11px;color:#8fa897;margin-top:4px">{{ $log->created_at->format('H:i:s') }} · oleh {{ $log->user->name ?? 'System' }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function updateStatus(status) {
    if (status === 'dibatalkan' && !confirm('Yakin ingin membatalkan order ini?')) return;
    ajax('/kasir/orders/{{ $order->id }}/status', 'POST', { status: status })
    .then(d => {
        if (d.success) { showToast(d.message, 'success'); setTimeout(() => location.reload(), 800); }
        else showToast(d.message || 'Gagal update', 'error');
    });
}
</script>
@endpush
