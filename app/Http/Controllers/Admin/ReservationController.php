<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class ReservationController extends Controller
{

    private $notif;

    public function __construct(NotificationService $notif )
    {
        $this->notif=$notif;
    }


    public function index(Request $request)
    {
        $query = Reservation::with(['user','table'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('date')) {
            $query->whereDate('reservation_date', $request->date);
        }

        $reservations = $query->paginate(20)->withQueryString();
        return view('admin.reservations.index', compact('reservations'));
    }

    public function approve(int $id)
    {
        $res = Reservation::where('status','pending')->findOrFail($id);
        $res->update(['status' => 'approved']);

        $this->notif->send(
            $res->user_id,
            'Reservasi Dikonfirmasi! 🎉',
            "Reservasi meja {$res->table->table_number} pada {$res->reservation_date->format('d M Y')} telah dikonfirmasi!",
            'success',
            '/reservasi',
            'ti-calendar-check'
        );

        return response()->json(['success' => true, 'message' => 'Reservasi dikonfirmasi.']);
    }

    public function reject(Request $request, int $id)
    {
        $res = Reservation::where('status','pending')->findOrFail($id);
        $res->update(['status' => 'rejected', 'admin_note' => $request->note ?? 'Ditolak oleh admin']);

        $this->notif->send(
            $res->user_id,
            'Reservasi Ditolak',
            "Maaf, reservasi kamu pada {$res->reservation_date->format('d M Y')} tidak dapat dikonfirmasi.",
            'error',
            '/reservasi',
            'ti-calendar-x'
        );

        return response()->json(['success' => true, 'message' => 'Reservasi ditolak.']);
    }
}
