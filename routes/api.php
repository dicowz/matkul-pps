<?php

use App\Http\Controllers\Api\AbsensiApiController;
use App\Http\Controllers\Api\ChatbotApiController;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes for Kopi Kutho
|--------------------------------------------------------------------------
|
| Endpoint ESP32 IoT Absensi dan AI WhatsApp Chatbot Webhook
|
*/

// Endpoint ESP32 Fingerprint Scan (DFD 3.0 & Flowchart Absensi)
Route::post('/absensi/scan', [AbsensiApiController::class, 'scan'])->name('api.absensi.scan');

// Endpoint AI WhatsApp Chatbot Webhook (DFD 4.0 & Flowchart Chatbot)
Route::post('/chatbot/webhook', [ChatbotApiController::class, 'webhook'])->name('api.chatbot.webhook');

// Public Menu API for integrations
Route::get('/menu', function (Request $request) {
    return response()->json([
        'status' => 'success',
        'data' => Menu::with('kategori')->where('status', 'tersedia')->get(),
    ]);
});
