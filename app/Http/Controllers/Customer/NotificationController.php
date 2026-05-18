<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->latest()->paginate(20);

        return view('customer.notifications.index', compact('notifications'));
    }

    public function markRead(int $id)
    {
        Notification::where('user_id', Auth::id())->where('id', $id)->update(['is_read' => true]);
        return response()->json(['success' => true]);
    }

    public function markAllRead()
    {
        Notification::where('user_id', Auth::id())->where('is_read', false)->update(['is_read' => true]);
        return response()->json(['success' => true]);
    }

    public function count()
    {
        $count = Notification::where('user_id', Auth::id())->where('is_read', false)->count();
        return response()->json(['count' => $count]);
    }
}