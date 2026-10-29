<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatbotLog;
use App\Models\Kategori;
use App\Models\Menu;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatbotApiController extends Controller
{
    /**
     * Webhook AI WhatsApp Chatbot
     * Sesuai Flowchart AI WhatsApp Chatbot & DFD 4.0 (Proses 4.1 - 4.6)
     */
    public function webhook(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nomor_whatsapp' => ['required', 'string'],
            'pesan' => ['required', 'string'],
        ]);

        $nomorWhatsApp = trim($validated['nomor_whatsapp']);
        $pesan = trim($validated['pesan']);

        // Proses Analisis Maksud (Intent) & Query Database Sistem
        $responsText = $this->generateResponse($pesan);

        // Simpan log ke tabel chatbot_logs (DFD 4.6 & F-14)
        $log = ChatbotLog::create([
            'nomor_whatsapp' => $nomorWhatsApp,
            'pesan_pengguna' => $pesan,
            'respons_chatbot' => $responsText,
            'waktu' => Carbon::now(),
        ]);

        return response()->json([
            'status' => 'success',
            'nomor_whatsapp' => $nomorWhatsApp,
            'pesan_pengguna' => $pesan,
            'respons_chatbot' => $responsText,
            'waktu' => $log->waktu->toDateTimeString(),
        ]);
    }

    /**
     * AI Intent Analyzer & Context-Aware Knowledge Engine
     */
    private function generateResponse(string $pesan): string
    {
        $pesanLower = strtolower($pesan);
        $cafeNama = env('CAFE_NAMA', 'Cafe Resto Kopi Kutho');
        $cafeAlamat = env('CAFE_ALAMAT', 'Jl. Semolowaru No. 45, Surabaya');
        $cafeJamBuka = env('CAFE_JAM_BUKA', '09.00 - 23.00 WIB');

        // 1. Sapaan / Salam Pembuka
        if (preg_match('/\b(halo|hai|hi|pagi|siang|sore|malam|assalam|permisi|p)\b/i', $pesanLower) && strlen($pesanLower) < 25) {
            return "Halo! Selamat datang di layanan asisten virtual {$cafeNama} ☕✨\n\nAda yang bisa saya bantu hari ini? Anda dapat menanyakan:\n- Daftar menu & harga terbaru\n- Rekomendasi menu sesuai budget (misal: 'ada makanan di bawah 20 ribu?')\n- Jam operasional dan lokasi kafe\n- Status ketersediaan menu favorit Anda";
        }

        // 2. Pertanyaan Jam Buka / Operasional
        if (preg_match('/\b(jam buka|operasional|jam berapa|buka sampai|tutup|hari apa)\b/i', $pesanLower)) {
            return "🕒 {$cafeNama} buka setiap hari:\n• Jam Operasional: {$cafeJamBuka}\n• Dine-in & Takeaway tersedia\nSilakan berkunjung bersama teman atau keluarga kak!";
        }

        // 3. Pertanyaan Lokasi / Alamat
        if (preg_match('/\b(lokasi|alamat|dimana|tempat|daerah mana)\b/i', $pesanLower)) {
            return "📍 Lokasi {$cafeNama}:\n{$cafeAlamat}\nTempat kami nyaman ber-AC, tersedia stopkontak di setiap meja, dan fasilitas Wi-Fi berkecepatan tinggi.";
        }

        // 4. Pertanyaan Fasilitas / Wi-Fi / Pembayaran
        if (preg_match('/\b(wifi|colokan|pembayaran|bayar|qris|debit|parkir)\b/i', $pesanLower)) {
            return "💳 Informasi Fasilitas {$cafeNama}:\n• Metode Pembayaran: Tunai, Kartu Debit, dan QRIS (GoPay/OVO/Dana/BCA/ShopeePay)\n• Fasilitas: Free High-Speed Wi-Fi, Full AC, Area Outdoor/Smoking, Musholla, dan Colokan Listrik di tiap meja.";
        }

        // 5. Pertanyaan Menu berdasarkan Budget / Kisaran Harga
        // Contoh: "ada makanan di bawah 20 ribu?", "menu di bawah 15rb"
        if (preg_match('/(di\s*bawah|kurang\s*dari|maksimal|budget|murah)\s*(?:rp\.?\s*)?(\d+)(?:\s*(?:k|ribu))?/i', $pesanLower, $matches)) {
            $nominal = (int) $matches[2];
            if (isset($matches[3]) && (strtolower(trim($matches[3])) === 'k' || strtolower(trim($matches[3])) === 'ribu')) {
                $nominal *= 1000;
            } elseif ($nominal < 100) {
                $nominal *= 1000; // jika user ketik "20" maksudnya 20k
            }

            $query = Menu::where('status', 'tersedia')->where('harga', '<=', $nominal);

            // Cek apakah ada spesifik kategori (makanan/minuman/kopi)
            if (str_contains($pesanLower, 'makan')) {
                $query->whereHas('kategori', function ($q) {
                    $q->where('nama_kategori', 'like', '%makan%');
                });
            } elseif (str_contains($pesanLower, 'minum') || str_contains($pesanLower, 'kopi')) {
                $query->whereHas('kategori', function ($q) {
                    $q->where('nama_kategori', 'like', '%kopi%');
                });
            }

            $items = $query->orderBy('harga')->get();

            if ($items->isEmpty()) {
                return 'Mohon maaf kak, untuk saat ini belum ada menu dengan harga di bawah Rp '.number_format($nominal, 0, ',', '.').'. Menu termurah kami mulai dari Rp 5.000 (Es Teh Manis Kutho) dan Rp 12.000 untuk kopi.';
            }

            $response = "Berikut pilihan menu di {$cafeNama} dengan harga di bawah Rp ".number_format($nominal, 0, ',', '.')." yang tersedia:\n";
            foreach ($items as $item) {
                $response .= "• {$item->nama_menu} — Rp ".number_format($item->harga, 0, ',', '.')."\n";
            }
            $response .= "\nMau saya bantu rekomendasi atau ada yang ingin ditanyakan lagi kak?";

            return $response;
        }

        // 6. Pertanyaan Kategori Menu Spesifik
        if (str_contains($pesanLower, 'kopi') && ! str_contains($pesanLower, 'non-kopi')) {
            $menus = Menu::whereHas('kategori', function ($q) {
                $q->where('nama_kategori', 'like', 'kopi');
            })->where('status', 'tersedia')->get();

            $response = "☕ Daftar Menu Kopi Spesial {$cafeNama}:\n";
            foreach ($menus as $m) {
                $response .= "• {$m->nama_menu} — Rp ".number_format($m->harga, 0, ',', '.')."\n";
            }
            $response .= "\nSignature kami adalah *Kopi Susu Kutho* (Rp 18.000) dengan racikan espresso blend dan aren asli!";

            return $response;
        }

        if (str_contains($pesanLower, 'non kopi') || str_contains($pesanLower, 'non-kopi') || str_contains($pesanLower, 'minuman')) {
            $menus = Menu::whereHas('kategori', function ($q) {
                $q->where('nama_kategori', 'like', '%non%');
            })->where('status', 'tersedia')->get();

            $response = "🧋 Daftar Minuman Non-Kopi {$cafeNama}:\n";
            foreach ($menus as $m) {
                $response .= "• {$m->nama_menu} — Rp ".number_format($m->harga, 0, ',', '.')."\n";
            }

            return $response;
        }

        if (str_contains($pesanLower, 'makan') || str_contains($pesanLower, 'laper') || str_contains($pesanLower, 'kenyang')) {
            $menus = Menu::whereHas('kategori', function ($q) {
                $q->where('nama_kategori', 'like', '%makan%');
            })->where('status', 'tersedia')->get();

            $response = "🍛 Daftar Menu Makanan Utama {$cafeNama}:\n";
            foreach ($menus as $m) {
                $response .= "• {$m->nama_menu} — Rp ".number_format($m->harga, 0, ',', '.')."\n";
            }
            $response .= "\nFavorit pelanggan kami adalah *Ayam Geprek Sambal Bawang Kutho* dan *Beef Teriyaki Bowl*!";

            return $response;
        }

        if (str_contains($pesanLower, 'snack') || str_contains($pesanLower, 'cemil') || str_contains($pesanLower, 'gorengan')) {
            $menus = Menu::whereHas('kategori', function ($q) {
                $q->where('nama_kategori', 'like', '%snack%');
            })->where('status', 'tersedia')->get();

            $response = "🍟 Pilihan Snack & Cemilan Santai {$cafeNama}:\n";
            foreach ($menus as $m) {
                $response .= "• {$m->nama_menu} — Rp ".number_format($m->harga, 0, ',', '.')."\n";
            }

            return $response;
        }

        // 7. Cek Spesifik Item Menu (Pencarian nama menu dalam database)
        $allAvailableMenus = Menu::with('kategori')->get();
        foreach ($allAvailableMenus as $menuItem) {
            $namaLower = strtolower($menuItem->nama_menu);
            $keywords = array_filter(explode(' ', $namaLower), fn ($w) => strlen($w) > 3);

            $matched = false;
            if (str_contains($pesanLower, $namaLower)) {
                $matched = true;
            } else {
                foreach ($keywords as $kw) {
                    if (str_contains($pesanLower, $kw)) {
                        $matched = true;
                        break;
                    }
                }
            }

            if ($matched) {
                $statusKet = $menuItem->isTersedia()
                    ? 'Tersedia ✅ (Ready to order)'
                    : 'Saat ini sedang Habis / Kosong ❌';

                $desc = $menuItem->deskripsi ? "\nDeskripsi: {$menuItem->deskripsi}" : '';

                return "🍽️ Informasi Menu *{$menuItem->nama_menu}*:\n• Kategori: {$menuItem->kategori->nama_kategori}\n• Harga: Rp ".number_format($menuItem->harga, 0, ',', '.')."\n• Status: {$statusKet}{$desc}\n\nIngin mencari menu lainnya kak?";
            }
        }

        // 8. Pertanyaan Daftar Keseluruhan Menu / Rekomendasi
        if (str_contains($pesanLower, 'menu') || str_contains($pesanLower, 'katalog') || str_contains($pesanLower, 'daftar') || str_contains($pesanLower, 'pilihan')) {
            $categories = Kategori::with(['menu' => function ($q) {
                $q->where('status', 'tersedia')->take(3);
            }])->get();

            $response = "📋 Berikut cuplikan menu favorit di {$cafeNama}:\n";
            foreach ($categories as $cat) {
                $response .= "\n📂 *{$cat->nama_kategori}*:\n";
                foreach ($cat->menu as $item) {
                    $response .= "  - {$item->nama_menu} (Rp ".number_format($item->harga, 0, ',', '.').")\n";
                }
            }
            $response .= "\nUntuk melihat daftar lebih lengkap atau kategori tertentu, kakak bisa ketik 'menu kopi', 'menu makanan', atau 'menu di bawah 20 ribu'.";

            return $response;
        }

        // 9. Fallback Sesuai Standar Proposal (NF-06): Jangan mengarang jawaban di luar data sistem
        return "Mohon maaf kak, informasi mengenai hal tersebut belum tersedia dalam sistem database {$cafeNama}. 🙏\n\nKakak dapat menanyakan seputar daftar menu kopi, minuman non-kopi, makanan utama, snack cemilan, rekomendasi harga, atau jam operasional kami.";
    }
}
