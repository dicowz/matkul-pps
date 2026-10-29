<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\DetailTransaksi;
use App\Models\Kategori;
use App\Models\Menu;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class KasirController extends Controller
{
    /**
     * Halaman Kasir POS Utama
     */
    public function index(Request $request): View
    {
        $categories = Kategori::with(['menu' => function ($q) {
            $q->orderBy('nama_menu');
        }])->orderBy('nama_kategori')->get();

        $allMenus = Menu::with('kategori')->orderBy('nama_menu')->get();

        return view('kasir.index', compact('categories', 'allMenus'));
    }

    /**
     * Simpan Transaksi Kasir (AJAX / Form Submission)
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.menu_id' => ['required', 'exists:menu,id'],
            'items.*.jumlah' => ['required', 'integer', 'min:1'],
            'metode_pembayaran' => ['required', 'in:tunai,debit,qris'],
            'uang_diterima' => ['nullable', 'numeric'],
        ], [
            'items.required' => 'Keranjang pesanan masih kosong.',
            'items.min' => 'Pilih minimal satu menu untuk memproses transaksi.',
            'metode_pembayaran.required' => 'Pilih metode pembayaran.',
        ]);

        $metode = $validated['metode_pembayaran'];
        $uangDiterima = $request->filled('uang_diterima') ? (int) $validated['uang_diterima'] : null;

        // Validasi ketersediaan menu dan hitung total server-side
        $totalCalculated = 0;
        $itemsToSave = [];

        foreach ($validated['items'] as $item) {
            $menu = Menu::findOrFail($item['menu_id']);

            if (! $menu->isTersedia()) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => "Menu {$menu->nama_menu} saat ini sedang tidak tersedia atau habis.",
                    ], 422);
                }

                return back()->with('error', "Menu {$menu->nama_menu} saat ini tidak tersedia.");
            }

            $jumlah = (int) $item['jumlah'];
            $hargaSnapshot = (int) $menu->harga; // NF-04 Snapshot harga saat transaksi
            $subtotal = $hargaSnapshot * $jumlah;
            $totalCalculated += $subtotal;

            $itemsToSave[] = [
                'menu_id' => $menu->id,
                'nama_menu' => $menu->nama_menu,
                'jumlah' => $jumlah,
                'harga' => $hargaSnapshot,
                'subtotal' => $subtotal,
            ];
        }

        // Validasi uang diterima jika tunai (Flowchart Kasir)
        $kembalian = null;
        if ($metode === 'tunai') {
            if ($uangDiterima === null || $uangDiterima < $totalCalculated) {
                $pesanError = 'Uang yang diterima kurang dari total belanja (Total: Rp '.number_format($totalCalculated, 0, ',', '.').').';
                if ($request->expectsJson()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => $pesanError,
                    ], 422);
                }

                return back()->with('error', $pesanError);
            }
            $kembalian = $uangDiterima - $totalCalculated;
        }

        // Generate Nomor Transaksi: TRK-YYYYMMDD-HHIISS
        $nomorTransaksi = 'TRK-'.date('Ymd').'-'.strtoupper(substr(uniqid(), -5));

        // DB Transaction untuk menjamin integritas data transaksi dan detail transaksi
        $transaksi = DB::transaction(function () use (
            $nomorTransaksi,
            $totalCalculated,
            $metode,
            $uangDiterima,
            $kembalian,
            $itemsToSave
        ) {
            $trx = Transaksi::create([
                'user_id' => Auth::id(),
                'nomor_transaksi' => $nomorTransaksi,
                'tanggal' => Carbon::now(),
                'total' => $totalCalculated,
                'metode_pembayaran' => $metode,
                'uang_diterima' => $uangDiterima,
                'kembalian' => $kembalian,
            ]);

            foreach ($itemsToSave as $item) {
                DetailTransaksi::create([
                    'transaksi_id' => $trx->id,
                    'menu_id' => $item['menu_id'],
                    'jumlah' => $item['jumlah'],
                    'harga' => $item['harga'],
                    'subtotal' => $item['subtotal'],
                ]);
            }

            return $trx;
        });

        $transaksi->load(['user', 'detailTransaksi.menu']);

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Transaksi berhasil disimpan.',
                'transaksi' => $transaksi,
                'print_url' => route('kasir.struk', $transaksi->id),
            ]);
        }

        return redirect()->route('kasir.struk', $transaksi->id)
            ->with('success', 'Transaksi berhasil disimpan!');
    }

    /**
     * Riwayat Transaksi Kasir
     */
    public function riwayat(Request $request): View
    {
        $tanggal = $request->query('tanggal', Carbon::today()->toDateString());
        $search = $request->query('search');

        $query = Transaksi::with(['user', 'detailTransaksi.menu']);

        // Jika bukan admin, hanya tampilkan transaksi kasir yang sedang login atau hari ini
        if (! Auth::user()->isAdmin()) {
            $query->where('user_id', Auth::id());
        }

        if ($tanggal) {
            $query->whereDate('tanggal', $tanggal);
        }

        if ($search) {
            $query->where('nomor_transaksi', 'like', "%{$search}%");
        }

        $transaksi = $query->orderByDesc('tanggal')->paginate(15)->withQueryString();

        $totalOmzetHariIni = (int) Transaksi::whereDate('tanggal', $tanggal)->sum('total');
        $totalTrxHariIni = Transaksi::whereDate('tanggal', $tanggal)->count();

        return view('kasir.riwayat', compact('transaksi', 'tanggal', 'search', 'totalOmzetHariIni', 'totalTrxHariIni'));
    }

    /**
     * Tampilan Struk Pembayaran / Thermal Print
     */
    public function struk(Transaksi $transaksi): View
    {
        $transaksi->load(['user', 'detailTransaksi.menu']);

        return view('kasir.struk', compact('transaksi'));
    }
}
