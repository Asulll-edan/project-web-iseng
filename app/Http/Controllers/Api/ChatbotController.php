<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ChatbotService;
use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    public function __construct(private ChatbotService $chatbot) {}

    public function respond(Request $request)
    {
        $request->validate(['message' => 'required|string|max:500']);

        $reply = $this->chatbot->respond($request->message);

        return response()->json([
            'reply'     => $reply,
            'timestamp' => now()->format('H:i'),
        ]);
    }
}