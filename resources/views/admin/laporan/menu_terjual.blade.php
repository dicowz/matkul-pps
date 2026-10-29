@extends('layouts.admin')

@section('title', 'Laporan Menu Terjual')
@section('page_title', 'Laporan Rekap Menu Terjual')

@section('content')
<div class="space-y-6">

    <!-- Filter & Action Bar -->
    <div class="no-print bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <form method="GET" action="{{ route('admin.laporan.menu_terjual') }}" class="flex flex-wrap items-center gap-3">
            <div class="flex items-center gap-2 text-xs text-slate-700 font-medium">
                <label>Dari:</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800">
            </div>
            <div class="flex items-center gap-2 text-xs text-slate-700 font-medium">
                <label>Sampai:</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800">
            </div>
            <button type="submit" class="px-4 py-1.5 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-semibold">
                Filter
            </button>
        </form>

        <button onclick="window.print()" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-semibold flex items-center gap-2 shadow-sm transition-all self-end md:self-auto">
            <i class="fa-solid fa-print"></i>
            <span>Cetak / Simpan PDF</span>
        </button>
    </div>

    <!-- Printable Report Container -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-8 space-y-6">
        <!-- Print Header -->
        <div class="border-b border-slate-200 pb-5 flex items-start justify-between">
            <div>
                <h2 class="text-xl font-bold text-slate-900 uppercase tracking-tight">Laporan Rekap Kuantitas & Omzet Menu</h2>
                <p class="text-xs text-slate-600 font-medium mt-1">Cafe Resto Kopi Kutho &bull; Jl. Semolowaru No. 45, Surabaya</p>
                <p class="text-xs text-slate-500 mt-0.5">
                    Periode: <span class="font-bold text-slate-800">{{ \Carbon\Carbon::parse($startDate)->format('d M Y') }}</span> s/d <span class="font-bold text-slate-800">{{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</span>
                </p>
            </div>
            <div class="text-right">
                <p class="text-[11px] text-slate-400">Dicetak pada:</p>
                <p class="text-xs font-semibold text-slate-700">{{ now()->translatedFormat('d F Y H:i') }} WIB</p>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/70">
                <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Total Item / Porsi Terjual</p>
                <h4 class="text-xl font-bold text-slate-900 mt-1">{{ number_format($totalItemTerjual) }} Porsi</h4>
            </div>
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/70">
                <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Total Pendapatan Terakumulasi</p>
                <h4 class="text-xl font-bold text-amber-700 mt-1">Rp {{ number_format($totalPendapatanMenu, 0, ',', '.') }}</h4>
            </div>
        </div>

        <!-- Table of Menu Sold -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-100/80 border-b border-slate-200 text-slate-600 font-semibold uppercase tracking-wider">
                        <th class="py-3 px-4 w-12 text-center">Peringkat</th>
                        <th class="py-3 px-4">Nama Menu</th>
                        <th class="py-3 px-4">Kategori</th>
                        <th class="py-3 px-4 text-right">Harga Satuan</th>
                        <th class="py-3 px-4 text-center">Total Terjual (Qty)</th>
                        <th class="py-3 px-4 text-right">Total Pendapatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($menuTerjual as $index => $item)
                        <tr class="hover:bg-slate-50/70">
                            <td class="py-3.5 px-4 text-center font-bold {{ $index < 3 ? 'text-amber-600' : 'text-slate-400' }}">
                                #{{ $index + 1 }}
                            </td>
                            <td class="py-3.5 px-4 font-bold text-slate-900">
                                {{ $item->nama_menu }}
                            </td>
                            <td class="py-3.5 px-4 text-slate-600">
                                <span class="px-2 py-0.5 rounded bg-slate-100 border border-slate-200 text-[11px]">
                                    {{ $item->nama_kategori }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono text-slate-700">
                                Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                    {{ number_format($item->total_qty) }} Porsi
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right font-bold text-slate-900 font-mono text-sm">
                                Rp {{ number_format($item->total_pendapatan, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-slate-400 text-xs">
                                Tidak ada data penjualan menu pada rentang tanggal yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="bg-slate-50 border-t-2 border-slate-300 font-bold text-slate-900">
                        <td colspan="4" class="py-3 px-4 text-right uppercase text-xs tracking-wider">Total Terakumulasi:</td>
                        <td class="py-3 px-4 text-center font-mono text-sm text-slate-900">
                            {{ number_format($totalItemTerjual) }} Porsi
                        </td>
                        <td class="py-3 px-4 text-right font-mono text-base text-amber-700">
                            Rp {{ number_format($totalPendapatanMenu, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
