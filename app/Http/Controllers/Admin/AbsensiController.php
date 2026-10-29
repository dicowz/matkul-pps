<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Karyawan;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AbsensiController extends Controller
{
    public function index(Request $request): View
    {
        $tanggal = $request->query('tanggal', Carbon::today()->toDateString());
        $karyawanId = $request->query('karyawan_id');
        $status = $request->query('status');

        $query = Absensi::with('karyawan');

        if ($tanggal) {
            $query->whereDate('tanggal', $tanggal);
        }

        if ($karyawanId) {
            $query->where('karyawan_id', $karyawanId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $absensi = $query->orderByDesc('tanggal')->orderByDesc('jam_masuk')->paginate(15)->withQueryString();
        $karyawanList = Karyawan::where('status', 'aktif')->orderBy('nama')->get();

        // Summary for current date filter
        $totalHadir = Absensi::whereDate('tanggal', $tanggal)->count();
        $totalKaryawanAktif = Karyawan::where('status', 'aktif')->count();
        $belumHadir = max(0, $totalKaryawanAktif - $totalHadir);

        return view('admin.absensi.index', compact(
            'absensi',
            'karyawanList',
            'tanggal',
            'karyawanId',
            'status',
            'totalHadir',
            'totalKaryawanAktif',
            'belumHadir'
        ));
    }

    public function destroy(Absensi $absensi): RedirectResponse
    {
        $absensi->delete();

        return back()->with('success', 'Catatan absensi berhasil dihapus.');
    }
}
