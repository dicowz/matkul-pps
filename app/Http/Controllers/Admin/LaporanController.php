<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\DetailTransaksi;
use App\Models\Karyawan;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LaporanController extends Controller
{
    public function penjualan(Request $request): View
    {
        $startDate = $request->query('start_date', Carbon::today()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', Carbon::today()->toDateString());
        $metode = $request->query('metode');

        $query = Transaksi::with(['user', 'detailTransaksi.menu'])
            ->whereDate('tanggal', '>=', $startDate)
            ->whereDate('tanggal', '<=', $endDate);

        if ($metode && in_array($metode, ['tunai', 'debit', 'qris'])) {
            $query->where('metode_pembayaran', $metode);
        }

        $transaksi = $query->orderByDesc('tanggal')->get();

        $totalOmzet = (int) $transaksi->sum('total');
        $totalTransaksi = $transaksi->count();
        $totalTunai = (int) $transaksi->where('metode_pembayaran', 'tunai')->sum('total');
        $totalDebit = (int) $transaksi->where('metode_pembayaran', 'debit')->sum('total');
        $totalQris = (int) $transaksi->where('metode_pembayaran', 'qris')->sum('total');

        return view('admin.laporan.penjualan', compact(
            'transaksi',
            'startDate',
            'endDate',
            'metode',
            'totalOmzet',
            'totalTransaksi',
            'totalTunai',
            'totalDebit',
            'totalQris'
        ));
    }

    public function menuTerjual(Request $request): View
    {
        $startDate = $request->query('start_date', Carbon::today()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', Carbon::today()->toDateString());

        $menuTerjual = DetailTransaksi::join('transaksi', 'detail_transaksi.transaksi_id', '=', 'transaksi.id')
            ->join('menu', 'detail_transaksi.menu_id', '=', 'menu.id')
            ->join('kategori', 'menu.kategori_id', '=', 'kategori.id')
            ->whereDate('transaksi.tanggal', '>=', $startDate)
            ->whereDate('transaksi.tanggal', '<=', $endDate)
            ->select(
                'menu.id as menu_id',
                'menu.nama_menu',
                'kategori.nama_kategori',
                'menu.harga as harga_satuan',
                DB::raw('SUM(detail_transaksi.jumlah) as total_qty'),
                DB::raw('SUM(detail_transaksi.subtotal) as total_pendapatan')
            )
            ->groupBy('menu.id', 'menu.nama_menu', 'kategori.nama_kategori', 'menu.harga')
            ->orderByDesc('total_qty')
            ->get();

        $totalItemTerjual = (int) $menuTerjual->sum('total_qty');
        $totalPendapatanMenu = (int) $menuTerjual->sum('total_pendapatan');

        return view('admin.laporan.menu_terjual', compact(
            'menuTerjual',
            'startDate',
            'endDate',
            'totalItemTerjual',
            'totalPendapatanMenu'
        ));
    }

    public function absensi(Request $request): View
    {
        $bulan = $request->query('bulan', Carbon::now()->month);
        $tahun = $request->query('tahun', Carbon::now()->year);
        $karyawanId = $request->query('karyawan_id');

        $query = Absensi::with('karyawan')
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun);

        if ($karyawanId) {
            $query->where('karyawan_id', $karyawanId);
        }

        $absensi = $query->orderBy('tanggal')->orderBy('jam_masuk')->get();
        $karyawanList = Karyawan::orderBy('nama')->get();

        $rekapPerKaryawan = Karyawan::withCount([
            'absensi as total_hadir' => function ($q) use ($bulan, $tahun) {
                $q->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun);
            },
            'absensi as total_tepat_waktu' => function ($q) use ($bulan, $tahun) {
                $q->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)->where('status', 'Tepat Waktu');
            },
            'absensi as total_terlambat' => function ($q) use ($bulan, $tahun) {
                $q->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)->where('status', 'Terlambat');
            },
        ])->get();

        return view('admin.laporan.absensi', compact(
            'absensi',
            'karyawanList',
            'bulan',
            'tahun',
            'karyawanId',
            'rekapPerKaryawan'
        ));
    }
}
