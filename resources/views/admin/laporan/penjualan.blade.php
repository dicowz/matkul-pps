@extends('layouts.admin')

@section('title', 'Laporan Penjualan')
@section('page_title', 'Laporan Penjualan Kasir')

@section('content')
<div class="space-y-6">

    <!-- Filter & Action Bar (Hidden on print) -->
    <div class="no-print bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <form method="GET" action="{{ route('admin.laporan.penjualan') }}" class="flex flex-wrap items-center gap-3">
            <div class="flex items-center gap-2 text-xs text-slate-700 font-medium">
                <label>Dari:</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800">
            </div>
            <div class="flex items-center gap-2 text-xs text-slate-700 font-medium">
                <label>Sampai:</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800">
            </div>
            <select name="metode" class="px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700">
                <option value="">Semua Metode</option>
                <option value="tunai" {{ $metode === 'tunai' ? 'selected' : '' }}>Tunai</option>
                <option value="debit" {{ $metode === 'debit' ? 'selected' : '' }}>Debit</option>
                <option value="qris" {{ $metode === 'qris' ? 'selected' : '' }}>QRIS</option>
            </select>
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
                <h2 class="text-xl font-bold text-slate-900 uppercase tracking-tight">Laporan Rekapitulasi Penjualan</h2>
                <p class="text-xs text-slate-600 font-medium mt-1">Cafe Resto Kopi Kutho &bull; Jl. Semolowaru No. 45, Surabaya</p>
                <p class="text-xs text-slate-500 mt-0.5">
                    Periode: <span class="font-bold text-slate-800">{{ \Carbon\Carbon::parse($startDate)->format('d M Y') }}</span> s/d <span class="font-bold text-slate-800">{{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</span>
                    @if($metode) &bull; Metode: <span class="uppercase font-bold">{{ $metode }}</span> @endif
                </p>
            </div>
            <div class="text-right">
                <p class="text-[11px] text-slate-400">Dicetak pada:</p>
                <p class="text-xs font-semibold text-slate-700">{{ now()->translatedFormat('d F Y H:i') }} WIB</p>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/70">
                <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Total Omzet</p>
                <h4 class="text-lg font-bold text-amber-700 mt-1">Rp {{ number_format($totalOmzet, 0, ',', '.') }}</h4>
            </div>
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/70">
                <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Total Transaksi</p>
                <h4 class="text-lg font-bold text-slate-900 mt-1">{{ number_format($totalTransaksi) }} Nota</h4>
            </div>
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/70">
                <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Metode QRIS</p>
                <h4 class="text-lg font-bold text-blue-700 mt-1">Rp {{ number_format($totalQris, 0, ',', '.') }}</h4>
            </div>
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/70">
                <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Tunai & Debit</p>
                <h4 class="text-lg font-bold text-emerald-700 mt-1">Rp {{ number_format($totalTunai + $totalDebit, 0, ',', '.') }}</h4>
            </div>
        </div>

        <!-- Detailed Transactions Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-100/80 border-b border-slate-200 text-slate-600 font-semibold uppercase tracking-wider">
                        <th class="py-3 px-4 w-12">No</th>
                        <th class="py-3 px-4">No. Transaksi</th>
                        <th class="py-3 px-4">Waktu</th>
                        <th class="py-3 px-4">Kasir</th>
                        <th class="py-3 px-4">Rincian Menu</th>
                        <th class="py-3 px-4">Metode</th>
                        <th class="py-3 px-4 text-right">Total Transaksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($transaksi as $index => $t)
                        <tr class="hover:bg-slate-50/70">
                            <td class="py-3 px-4 text-slate-400 font-medium">{{ $index + 1 }}</td>
                            <td class="py-3 px-4 font-bold text-slate-800">
                                <a href="{{ route('kasir.struk', $t->id) }}" target="_blank" class="hover:text-amber-600">
                                    {{ $t->nomor_transaksi ?? 'TRK-'.$t->id }}
                                </a>
                            </td>
                            <td class="py-3 px-4 text-slate-500 font-mono text-[11px]">
                                {{ $t->tanggal ? $t->tanggal->format('d/m/Y H:i') : '-' }}
                            </td>
                            <td class="py-3 px-4 text-slate-600">{{ $t->user->name ?? 'Kasir' }}</td>
                            <td class="py-3 px-4 text-slate-700">
                                <ul class="list-disc list-inside text-[11px] text-slate-600 space-y-0.5">
                                    @foreach($t->detailTransaksi as $d)
                                        <li>{{ $d->menu->nama_menu ?? 'Item' }} ({{ $d->jumlah }}x @ Rp {{ number_format($d->harga, 0, ',', '.') }})</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold uppercase {{ $t->metode_pembayaran === 'tunai' ? 'bg-emerald-100 text-emerald-800' : ($t->metode_pembayaran === 'qris' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800') }}">
                                    {{ $t->metode_pembayaran }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right font-bold text-slate-900 font-mono text-sm">
                                Rp {{ number_format($t->total, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center text-slate-400 text-xs">
                                Tidak ada data penjualan pada rentang tanggal yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="bg-slate-50 border-t-2 border-slate-300 font-bold text-slate-900">
                        <td colspan="6" class="py-3 px-4 text-right uppercase text-xs tracking-wider">Total Penjualan Keseluruhan:</td>
                        <td class="py-3 px-4 text-right font-mono text-base text-amber-700">
                            Rp {{ number_format($totalOmzet, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Signature for Print -->
        <div class="hidden print:flex items-center justify-between pt-12 text-xs text-slate-700">
            <div class="text-center">
                <p>Mengetahui,</p>
                <p class="font-bold mt-16">( _________________________ )</p>
                <p class="text-slate-500">Pemilik / Owner Kopi Kutho</p>
            </div>
            <div class="text-center">
                <p>Surabaya, {{ now()->translatedFormat('d F Y') }}</p>
                <p class="font-bold mt-16">{{ Auth::user()->name ?? 'Administrator' }}</p>
                <p class="text-slate-500">Kasir / Admin Bertugas</p>
            </div>
        </div>
    </div>
</div>
@endsection
