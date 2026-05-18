<?php

use Illuminate\Support\Facades\Route;

// ── Order tracking (auth via sanctum or session) ───────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/orders/{id}/track', [App\Http\Controllers\Api\OrderTrackingController::class, 'track'])
        ->name('api.order.track');
});

// ── Chatbot (public) ───────────────────────────────────────────────────────
Route::post('/chatbot', [App\Http\Controllers\Api\ChatbotController::class, 'respond'])
    ->name('api.chatbot');