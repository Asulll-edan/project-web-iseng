<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ChatbotService;
use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    // public function __construct(private ChatbotService $chatbot) {}

    private $chatbot;
    public function __construct(ChatbotService $chatbot)
    {
        $this->chatbot = $chatbot;
    }
    public function respond(Request $request)
    {
        $request->validate(['message' => 'required|string|max:500']);

        $history = $request->get('history', []);
        $result  = $this->chatbot->respond($request->message, $history);

        return response()->json($result);
    }
}