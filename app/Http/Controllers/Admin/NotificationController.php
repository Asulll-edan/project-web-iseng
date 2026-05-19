<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
private $notif;
public function __construct(NotificationService $notif)
{
$this->notif=$notif;
}
    public function broadcast(Request $request)
    {
        $request->validate([
            'title'   => 'required|string|max:200',
            'message' => 'required|string|max:500',
            'type'    => 'required|in:info,success,error',
        ]);

        $this->notif->broadcast($request->title, $request->message, $request->type);

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi berhasil dikirim ke semua customer!',
        ]);
    }
}
