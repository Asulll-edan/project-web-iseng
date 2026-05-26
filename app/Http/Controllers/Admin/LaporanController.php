<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        
        /** @var \Illuminate\Database\Eloquent\Builder $query */
        $query = Laporan::with(['user','approver'])->latest();

        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('tipe'))   $query->where('tipe', $request->tipe);
        
        // Manager hanya lihat laporannya sendiri
        if ($user->isManager()) {
            $query->where('user_id', $user->id);
            }
            
        $laporans = $query->paginate(20)->withQueryString();
        return view('admin.reports.index', compact('laporans'));
    }

    public function exportForm()
    {
        return view('admin.reports.export');
    }

    public function generate(Request $request)
    {
        /** @var User $user */
$user = Auth::user();
        $request->validate([
            'judul'         => 'required|string|max:200',
            'tipe'          => 'required|in:sales,orders,wallet,membership,customer,custom',
            'periode_start' => 'required|date',
            'periode_end'   => 'required|date|after_or_equal:periode_start',
            'file_type'     => 'required|in:pdf,excel',
            'catatan'       => 'nullable|string|max:500',
        ]);

        $data     = $this->collectData($request->tipe, $request->periode_start, $request->periode_end);
        $filePath = $this->generateFile($request->file_type, $request->tipe, $data, $request->judul);

        $laporan = Laporan::create([
            'nomor_laporan' => Laporan::generateNomor(),
            'user_id'       => Auth::id(),
            'judul'         => $request->judul,
            'tipe'          => $request->tipe,
            'periode_start' => $request->periode_start,
            'periode_end'   => $request->periode_end,
            'file_path'     => $filePath,
            'file_type'     => $request->file_type,
            'catatan'       => $request->catatan,
            'status'        => $user->isSuperadmin() ? 'approved' : 'pending',
            'approved_by'   => $user->isSuperadmin() ? Auth::id() : null,
            'approved_at'   => $user->isSuperadmin() ? now() : null,
        ]);

        return response()->json([
            'success'       => true,
            'message'       => 'Laporan berhasil dibuat! ' . ($laporan->status === 'pending' ? 'Menunggu approval.' : ''),
            'nomor_laporan' => $laporan->nomor_laporan,
            'download_url'  => $laporan->file_url,
        ]);
    }

    public function approve(int $id)
    {
        $laporan = Laporan::where('status','pending')->findOrFail($id);
        $laporan->update([
            'status'      => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);
        return response()->json(['success' => true, 'message' => 'Laporan disetujui.']);
    }

    public function reject(Request $request, int $id)
    {
        $laporan = Laporan::where('status','pending')->findOrFail($id);
        $laporan->update([
            'status'     => 'rejected',
            'admin_note' => $request->note ?? 'Ditolak',
            'approved_by'=> Auth::id(),
            'approved_at'=> now(),
        ]);
        return response()->json(['success' => true, 'message' => 'Laporan ditolak.']);
    }

    public function download(int $id)
    {
        $laporan = Laporan::findOrFail($id);

        /** @var User $user */
        $user = Auth::user();

        // Manager hanya bisa download miliknya
        if ($user->isManager() && $laporan->user_id !== $user->id) {
            abort(403);
        }

        if (!$laporan->file_path || !Storage::disk('public')->exists($laporan->file_path)) {
            return back()->with('error', 'File tidak ditemukan.');
        }
/** @var \Illuminate\Contracts\Filesystem\Filesystem $disk */
    
        return Storage::disk('public')->download($laporan->file_path, $laporan->judul.'.'.$laporan->file_type);
    }

    // ── Private helpers ────────────────────────────────────
    private function collectData(string $tipe, string $start, string $end): array
    {
        switch ($tipe) {
            case 'sales':
            case 'orders':
                return Order::whereBetween('created_at', [$start.' 00:00:00', $end.' 23:59:59'])
                    ->with(['user','items'])
                    ->get()
                    ->map(fn($o) => [
                        'no_order'    => $o->order_number,
                        'customer'    => $o->user->name,
                        'total'       => $o->total_amount,
                        'status'      => $o->status,
                        'pembayaran'  => $o->payment_method,
                        'tanggal'     => $o->created_at->format('d/m/Y H:i'),
                    ])->toArray();

            case 'wallet':
                return WalletTransaction::whereBetween('created_at', [$start.' 00:00:00', $end.' 23:59:59'])
                    ->with('user')
                    ->get()
                    ->map(fn($t) => [
                        'kode'    => $t->transaction_code,
                        'user'    => $t->user->name ?? '-',
                        'tipe'    => $t->type,
                        'jumlah'  => $t->amount,
                        'tanggal' => $t->created_at->format('d/m/Y H:i'),
                    ])->toArray();

            case 'customer':
                return User::where('role','customer')
                    ->whereBetween('created_at', [$start.' 00:00:00', $end.' 23:59:59'])
                    ->get()
                    ->map(fn($u) => [
                        'nama'   => $u->name,
                        'email'  => $u->email,
                        'status' => $u->status,
                        'daftar' => $u->created_at->format('d/m/Y'),
                    ])->toArray();

            default:
                return [];
        }
    }

    private function generateFile(string $type, string $tipe, array $data, string $title): string
    {
        $filename = 'laporan_'.strtolower(str_replace(' ','_',$title)).'_'.date('Ymd_His').'.'.$type;
        $path     = 'laporans/'.$filename;

        if ($type === 'excel') {
            $csv = $this->arrayToCsv($data);
            Storage::disk('public')->put($path, $csv);
        } else {
            // Simple HTML-based PDF via browser print (atau gunakan dompdf jika installed)
            $html = $this->buildHtmlReport($title, $tipe, $data);
            Storage::disk('public')->put('laporans/'.str_replace('.pdf','.html',$filename), $html);
            $path = 'laporans/'.str_replace('.pdf','.html',$filename);
        }

        return $path;
    }

    private function arrayToCsv(array $data): string
    {
        if (empty($data)) return '';
        $headers = implode(',', array_keys($data[0]))."\n";
        $rows    = array_map(fn($row) => implode(',', array_map(fn($v) => '"'.str_replace('"','""',$v).'"', $row)), $data);
        return $headers . implode("\n", $rows);
    }

    private function buildHtmlReport(string $title, string $tipe, array $data): string
    {
        $rows = '';
        if (!empty($data)) {
            $headers = '<tr>'.implode('',array_map(fn($h)=>'<th>'.ucfirst($h).'</th>',array_keys($data[0]))).'</tr>';
            $rows    = $headers . implode('', array_map(function($row) {
                return '<tr>'.implode('',array_map(fn($v)=>'<td>'.$v.'</td>',$row)).'</tr>';
            }, $data));
        }

        return "<!DOCTYPE html><html><head><meta charset='UTF-8'>
        <title>{$title}</title>
        <style>body{font-family:Arial,sans-serif;padding:20px}h1{color:#3d5c47}table{width:100%;border-collapse:collapse}th,td{border:1px solid #ddd;padding:8px;font-size:12px}th{background:#3d5c47;color:#fff}</style>
        </head><body>
        <h1>{$title}</h1>
        <p>Tipe: {$tipe} &nbsp;|&nbsp; Dibuat: ".date('d/m/Y H:i')." &nbsp;|&nbsp; Oleh: ".Auth::user()->name."</p>
        <table>{$rows}</table>
        <script>window.print();</script>
        </body></html>";
    }
}