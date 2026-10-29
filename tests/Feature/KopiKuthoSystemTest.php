<?php

namespace Tests\Feature;

use App\Models\ChatbotLog;
use App\Models\Karyawan;
use App\Models\Menu;
use App\Models\Transaksi;
use App\Models\User;
use Tests\TestCase;

class KopiKuthoSystemTest extends TestCase
{
    /**
     * Test autentikasi & pengalihan hak akses role (F-01, NF-01).
     */
    public function test_user_authentication_and_role_redirection(): void
    {
        $admin = User::where('role', 'admin')->first();
        $kasir = User::where('role', 'kasir')->first();

        // Admin login -> redirect ke dashboard
        $responseAdmin = $this->post('/login', [
            'username' => $admin->username,
            'password' => 'password',
        ]);
        $responseAdmin->assertRedirect(route('admin.dashboard'));

        // Kasir login -> redirect ke kasir
        $responseKasir = $this->post('/login', [
            'username' => $kasir->username,
            'password' => 'password',
        ]);
        $responseKasir->assertRedirect(route('kasir.index'));

        // Kasir diblokir dari membuka admin dashboard (NF-01)
        $this->actingAs($kasir);
        $responseKasirToAdmin = $this->get('/admin/dashboard');
        $responseKasirToAdmin->assertRedirect(route('kasir.index'));
    }

    /**
     * Test proses transaksi kasir, perhitungan otomatis, pembayaran tunai & kembalian (F-05 s/d F-09).
     */
    public function test_kasir_transaction_creation_and_receipt(): void
    {
        $kasir = User::where('role', 'kasir')->first();
        $this->actingAs($kasir);

        $menu = Menu::where('status', 'tersedia')->first();
        $qty = 2;
        $totalExpected = $menu->harga * $qty;
        $cashPaid = $totalExpected + 10000;

        $response = $this->postJson(route('kasir.store'), [
            'items' => [
                [
                    'menu_id' => $menu->id,
                    'jumlah' => $qty,
                ],
            ],
            'metode_pembayaran' => 'tunai',
            'uang_diterima' => $cashPaid,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'transaksi' => [
                'total' => $totalExpected,
                'uang_diterima' => $cashPaid,
                'kembalian' => 10000,
            ],
        ]);

        // Verifikasi struk dapat dibuka
        $trxId = $response->json('transaksi.id');
        $receiptResponse = $this->get(route('kasir.struk', $trxId));
        $receiptResponse->assertStatus(200);
        $receiptResponse->assertSee($menu->nama_menu);
    }

    /**
     * Test integrasi API Absensi ESP32 & Fingerprint (F-11, F-12, NF-02).
     */
    public function test_esp32_fingerprint_api_flow(): void
    {
        $token = env('ESP32_DEVICE_TOKEN', 'kopikutho_esp32_secret_key_2026');

        // 1. Permintaan tanpa token yang valid harus ditolak (NF-02)
        $invalidResponse = $this->postJson(route('api.absensi.scan'), [
            'fingerprint_id' => 101,
        ], ['X-Device-Token' => 'wrong_token']);
        $invalidResponse->assertStatus(401);

        // 2. Scan karyawan terdaftar (Anita Rahayu ID 105)
        $karyawan = Karyawan::where('fingerprint_id', 105)->first();

        // Bersihkan absensi hari ini jika ada untuk pengujian murni
        $karyawan->absensi()->whereDate('tanggal', now()->toDateString())->delete();

        // Scan pertama: Harus tercatat Jam Masuk
        $scanMasuk = $this->postJson(route('api.absensi.scan'), [
            'fingerprint_id' => 105,
        ], ['X-Device-Token' => $token]);

        $scanMasuk->assertStatus(200);
        $scanMasuk->assertJson([
            'status' => 'success',
            'action' => 'masuk',
            'karyawan' => ['nama' => $karyawan->nama],
        ]);

        // Scan kedua: Harus tercatat Jam Keluar
        $scanKeluar = $this->postJson(route('api.absensi.scan'), [
            'fingerprint_id' => 105,
        ], ['X-Device-Token' => $token]);

        $scanKeluar->assertStatus(200);
        $scanKeluar->assertJson([
            'status' => 'success',
            'action' => 'keluar',
        ]);

        // Scan ketiga: Harus menginfokan bahwa absensi hari ini sudah lengkap (Flowchart Ketentuan 9)
        $scanLengkap = $this->postJson(route('api.absensi.scan'), [
            'fingerprint_id' => 105,
        ], ['X-Device-Token' => $token]);

        $scanLengkap->assertStatus(200);
        $scanLengkap->assertJson([
            'status' => 'info',
            'action' => 'lengkap',
        ]);
    }

    /**
     * Test AI WhatsApp Chatbot Webhook & Intent Query (F-13, F-14, NF-03, NF-06).
     */
    public function test_ai_whatsapp_chatbot_webhook_and_logging(): void
    {
        $response = $this->postJson(route('api.chatbot.webhook'), [
            'nomor_whatsapp' => '081298765432',
            'pesan' => 'Ada makanan di bawah 20 ribu kak?',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
        ]);

        // Pastikan jawaban memuat nama makanan di bawah 20 ribu yang riil di database
        $this->assertStringContainsString('Ayam Geprek', $response->json('respons_chatbot'));

        // Cek log tersimpan di database
        $log = ChatbotLog::where('nomor_whatsapp', '081298765432')->latest()->first();
        $this->assertNotNull($log);

        // Verifikasi privasi penyamaran nomor (NF-03)
        $this->assertEquals('0812****5432', $log->masked_nomor);
    }
}
