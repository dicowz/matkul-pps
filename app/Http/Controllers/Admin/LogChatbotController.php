<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatbotLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LogChatbotController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('search');

        $query = ChatbotLog::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('pesan_pengguna', 'like', "%{$search}%")
                    ->orWhere('respons_chatbot', 'like', "%{$search}%")
                    ->orWhere('nomor_whatsapp', 'like', "%{$search}%");
            });
        }

        $logs = $query->orderByDesc('waktu')->paginate(20)->withQueryString();
        $totalLogs = ChatbotLog::count();

        return view('admin.chatbot.index', compact('logs', 'search', 'totalLogs'));
    }

    public function destroy(ChatbotLog $log): RedirectResponse
    {
        $log->delete();

        return back()->with('success', 'Log percakapan berhasil dihapus.');
    }

    public function clear(): RedirectResponse
    {
        ChatbotLog::truncate();

        return redirect()->route('admin.chatbot.index')
            ->with('success', 'Semua riwayat log percakapan chatbot berhasil dibersihkan.');
    }
}
