<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\RestaurantTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    public function index()
    {
        $reservations = Reservation::where('user_id', Auth::id())
            ->with('table')
            ->latest()
            ->paginate(10);

        return view('customer.reservation.index', compact('reservations'));
    }

    public function create()
    {
        $tables = RestaurantTable::where('is_active', true)->orderBy('table_number')->get();
        return view('customer.reservation.create', compact('tables'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'table_id'          => 'required|exists:restaurant_tables,id',
            'reservation_date'  => 'required|date|after_or_equal:today',
            'reservation_time'  => 'required|date_format:H:i',
            'guest_count'       => 'required|integer|min:1|max:20',
            'special_request'   => 'nullable|string|max:500',
            'event_type'        => 'nullable|string|max:100',
        ]);

        // Check table conflicts
        $conflict = Reservation::where('table_id', $request->table_id)
            ->where('reservation_date', $request->reservation_date)
            ->where('status', 'confirmed')
            ->whereBetween('reservation_time', [
                now()->setTimeFromTimeString($request->reservation_time)->subHours(2),
                now()->setTimeFromTimeString($request->reservation_time)->addHours(2),
            ])->exists();

        if ($conflict) {
            return back()->with('error', 'Meja sudah dipesan pada waktu tersebut. Pilih meja atau waktu lain.')->withInput();
        }

        Reservation::create([
            'user_id'          => Auth::id(),
            'table_id'         => $request->table_id,
            'reservation_code' => Reservation::generateCode(),
            'reservation_date' => $request->reservation_date,
            'reservation_time' => $request->reservation_date . ' ' . $request->reservation_time,
            'guest_count'      => $request->guest_count,
            'status'           => 'pending',
            'special_request'  => $request->special_request,
            'event_type'       => $request->event_type,
        ]);

        return redirect()->route('reservation.index')
            ->with('success', 'Reservasi berhasil! Admin akan mengkonfirmasi segera. 📅');
    }

    public function cancel(int $id)
    {
        $reservation = Reservation::where('user_id', Auth::id())
            ->whereIn('status', ['pending', 'confirmed'])
            ->findOrFail($id);

        $reservation->update(['status' => 'cancelled']);

        return response()->json(['success' => true, 'message' => 'Reservasi dibatalkan.']);
    }
}