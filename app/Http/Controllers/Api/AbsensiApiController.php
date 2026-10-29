<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Karyawan;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AbsensiApiController extends Controller
{
    /**
     * Endpoint API Absensi ESP32 + Fingerprint
     * Sesuai Flowchart Sistem Absensi & DFD 3.0 (Proses 3.1 - 3.6)
     */
    public function scan(Request $request): JsonResponse
    {
        // 1. Validasi Token Perangkat (NF-02 Keamanan)
        $expectedToken = config('services.esp32.token') ?? env('ESP32_DEVICE_TOKEN', 'kopikutho_esp32_secret_key_2026');
        $receivedToken = $request->header('X-Device-Token') ?? $request->input('token');

        if (! $receivedToken || $receivedToken !== $expectedToken) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token perangkat tidak valid. Akses ditolak.',
            ], 401);
        }

        // 2. Validasi input fingerprint_id
        $request->validate([
            'fingerprint_id' => ['required', 'integer'],
        ]);

        $fingerprintId = (int) $request->input('fingerprint_id');

        // 3. Identifikasi Karyawan berdasarkan fingerprint_id
        $karyawan = Karyawan::where('fingerprint_id', $fingerprintId)->first();

        if (! $karyawan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Fingerprint ID #'.$fingerprintId.' tidak terdaftar dalam database sistem.',
            ], 404);
        }

        if (! $karyawan->isAktif()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Karyawan '.$karyawan->nama.' berstatus nonaktif. Akses ditolak.',
            ], 403);
        }

        // 4. Periksa Catatan Absensi Hari Ini
        $now = Carbon::now();
        $today = $now->toDateString();
        $currentTime = $now->format('H:i:s');

        $absensiHariIni = Absensi::where('karyawan_id', $karyawan->id)
            ->whereDate('tanggal', $today)
            ->first();

        // Kasus 1: Belum ada catatan absensi hari ini -> Catat Jam Masuk
        if (! $absensiHariIni) {
            // Jam operasional masuk standard: 08:00
            $jamBatasMasuk = Carbon::createFromTimeString('08:00:00');
            $statusKehadiran = $now->gt($jamBatasMasuk) ? 'Terlambat' : 'Tepat Waktu';

            $absensi = Absensi::create([
                'karyawan_id' => $karyawan->id,
                'tanggal' => $today,
                'jam_masuk' => $currentTime,
                'jam_keluar' => null,
                'status' => $statusKehadiran,
            ]);

            return response()->json([
                'status' => 'success',
                'action' => 'masuk',
                'message' => 'Absensi Berhasil (Jam Masuk)',
                'karyawan' => [
                    'id' => $karyawan->id,
                    'nama' => $karyawan->nama,
                    'jabatan' => $karyawan->jabatan,
                    'fingerprint_id' => $karyawan->fingerprint_id,
                ],
                'jam_masuk' => $currentTime,
                'status_kehadiran' => $statusKehadiran,
                'tanggal' => $today,
            ], 200);
        }

        // Kasus 2: Sudah ada jam masuk & jam keluar masih kosong -> Catat Jam Keluar
        if ($absensiHariIni->jam_keluar === null) {
            $absensiHariIni->jam_keluar = $currentTime;
            $absensiHariIni->save();

            return response()->json([
                'status' => 'success',
                'action' => 'keluar',
                'message' => 'Absensi Berhasil (Jam Keluar)',
                'karyawan' => [
                    'id' => $karyawan->id,
                    'nama' => $karyawan->nama,
                    'jabatan' => $karyawan->jabatan,
                    'fingerprint_id' => $karyawan->fingerprint_id,
                ],
                'jam_masuk' => $absensiHariIni->jam_masuk,
                'jam_keluar' => $currentTime,
                'status_kehadiran' => $absensiHariIni->status,
                'tanggal' => $today,
            ], 200);
        }

        // Kasus 3: Jam masuk dan jam keluar sudah terisi lengkap hari ini
        return response()->json([
            'status' => 'info',
            'action' => 'lengkap',
            'message' => 'Absensi hari ini sudah lengkap untuk '.$karyawan->nama.' (Masuk: '.substr($absensiHariIni->jam_masuk, 0, 5).', Keluar: '.substr($absensiHariIni->jam_keluar, 0, 5).').',
            'karyawan' => [
                'id' => $karyawan->id,
                'nama' => $karyawan->nama,
                'jabatan' => $karyawan->jabatan,
            ],
            'jam_masuk' => $absensiHariIni->jam_masuk,
            'jam_keluar' => $absensiHariIni->jam_keluar,
            'status_kehadiran' => $absensiHariIni->status,
            'tanggal' => $today,
        ], 200);
    }
}
