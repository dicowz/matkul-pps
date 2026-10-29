<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\ChatbotLog;
use App\Models\DetailTransaksi;
use App\Models\Karyawan;
use App\Models\Menu;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = Carbon::today();

        // 1. Ringkasan KPI Hari Ini
        $transaksiHariIni = Transaksi::whereDate('tanggal', $today)->get();
        $totalTransaksiHariIni = $transaksiHariIni->count();
        $totalPendapatanHariIni = (int) $transaksiHariIni->sum('total');

        $totalKaryawan = Karyawan::where('status', 'aktif')->count();
        $karyawanHadirHariIni = Absensi::whereDate('tanggal', $today)
            ->distinct('karyawan_id')
            ->count('karyawan_id');

        // 2. Menu Terlaris Hari Ini / Keseluruhan
        $menuTerlaris = DetailTransaksi::select('menu_id', DB::raw('SUM(jumlah) as total_terjual'), DB::raw('SUM(subtotal) as total_omzet'))
            ->groupBy('menu_id')
            ->orderByDesc('total_terjual')
            ->with('menu.kategori')
            ->take(5)
            ->get();

        // 3. Data Grafik Penjualan 7 Hari Terakhir
        $chartLabels = [];
        $chartOmzet = [];
        $chartTransaksi = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dayLabel = $date->translatedFormat('D, d M');
            $dailyTransactions = Transaksi::whereDate('tanggal', $date)->get();

            $chartLabels[] = $dayLabel;
            $chartOmzet[] = (int) $dailyTransactions->sum('total');
            $chartTransaksi[] = $dailyTransactions->count();
        }

        // 4. Aktivitas Chatbot Terkini
        $recentChatbotLogs = ChatbotLog::orderByDesc('waktu')->take(5)->get();

        // 5. Transaksi Terkini Hari Ini
        $recentTransactions = Transaksi::with(['user', 'detailTransaksi.menu'])
            ->orderByDesc('tanggal')
            ->take(6)
            ->get();

        return view('admin.dashboard', compact(
            'totalTransaksiHariIni',
            'totalPendapatanHariIni',
            'totalKaryawan',
            'karyawanHadirHariIni',
            'menuTerlaris',
            'chartLabels',
            'chartOmzet',
            'chartTransaksi',
            'recentChatbotLogs',
            'recentTransactions'
        ));
    }
}
