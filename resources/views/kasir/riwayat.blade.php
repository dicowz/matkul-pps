@extends('layouts.kasir')

@section('title', 'Riwayat Transaksi Kasir')

@section('content')
<div class="flex-1 overflow-y-auto bg-slate-100 p-6 space-y-6 custom-scroll">
    <!-- Header with Summary & Filters -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-amber-700 uppercase tracking-wider mb-1">
                <i class="fa-solid fa-clock-rotate-left"></i>
                <span>Arsip Transaksi</span>
            </div>
            <h2 class="text-xl font-bold text-slate-900">Riwayat Transaksi Penjualan</h2>
            <p class="text-xs text-slate-500 mt-0.5">Daftar nota transaksi dan cetak ulang struk pembayaran pelanggan</p>
        </div>

        <div class="flex items-center gap-4 bg-amber-50 border border-amber-200/80 px-4 py-2.5 rounded-2xl">
            <div>
                <p class="text-[10px] uppercase font-bold text-amber-800 tracking-wider">Total Omzet Tanggal Ini</p>
                <p class="text-base font-bold text-amber-900 font-mono">Rp {{ number_format($totalOmzetHariIni, 0, ',', '.') }}</p>
            </div>
            <div class="border-l border-amber-200 pl-4">
                <p class="text-[10px] uppercase font-bold text-amber-800 tracking-wider">Total Nota</p>
                <p class="text-base font-bold text-slate-800">{{ $totalTrxHariIni }} Struk</p>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex flex-wrap items-center justify-between gap-4">
        <form method="GET" action="{{ route('kasir.riwayat') }}" class="flex flex-wrap items-center gap-3">
            <div class="flex items-center gap-2 text-xs text-slate-700 font-medium">
                <label>Tanggal:</label>
                <input type="date" name="tanggal" value="{{ $tanggal }}" onchange="this.form.submit()"
                    class="px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800">
            </div>

            <div class="relative">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari No. Transaksi..."
                    class="pl-8 pr-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400">
                <i class="fa-solid fa-magnifying-glass absolute left-2.5 top-2.5 text-xs text-slate-400"></i>
            </div>

            <button type="submit" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-semibold">
                Cari
            </button>
            @if($search)
                <a href="{{ route('kasir.riwayat', ['tanggal' => $tanggal]) }}" class="text-xs text-rose-500 hover:text-rose-700 underline font-medium">Reset</a>
            @endif
        </form>

        <a href="{{ route('kasir.index') }}" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-bold flex items-center gap-2 transition-all shadow-sm">
            <i class="fa-solid fa-cash-register"></i>
            <span>Kembali ke Kasir</span>
        </a>
    </div>

    <!-- Table of Transactions -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-500 font-semibold uppercase tracking-wider">
                        <th class="py-3.5 px-6">No. Transaksi</th>
                        <th class="py-3.5 px-6">Waktu Transaksi</th>
                        <th class="py-3.5 px-6">Kasir Bertugas</th>
                        <th class="py-3.5 px-6">Rincian Menu</th>
                        <th class="py-3.5 px-6">Metode</th>
                        <th class="py-3.5 px-6 text-right">Total Tagihan</th>
                        <th class="py-3.5 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($transaksi as $t)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3.5 px-6 font-bold text-slate-800 font-mono">
                                {{ $t->nomor_transaksi ?? 'TRK-'.$t->id }}
                            </td>
                            <td class="py-3.5 px-6 text-slate-500 font-mono">
                                {{ $t->tanggal ? $t->tanggal->format('d/m/Y H:i:s') : '-' }}
                            </td>
                            <td class="py-3.5 px-6 font-medium text-slate-700">
                                {{ $t->user->name ?? 'Kasir' }}
                            </td>
                            <td class="py-3.5 px-6 text-slate-600 max-w-xs">
                                <span class="line-clamp-1">
                                    @foreach($t->detailTransaksi as $index => $d)
                                        {{ $d->menu->nama_menu ?? 'Item' }} ({{ $d->jumlah }}x){{ !$loop->last ? ', ' : '' }}
                                    @endforeach
                                </span>
                            </td>
                            <td class="py-3.5 px-6">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase {{ $t->metode_pembayaran === 'tunai' ? 'bg-emerald-100 text-emerald-800' : ($t->metode_pembayaran === 'qris' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800') }}">
                                    {{ $t->metode_pembayaran }}
                                </span>
                            </td>
                            <td class="py-3.5 px-6 text-right font-bold text-slate-900 font-mono text-sm">
                                Rp {{ number_format($t->total, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-6 text-right">
                                <a href="{{ route('kasir.struk', $t->id) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-amber-100 text-slate-700 hover:text-amber-800 font-semibold text-xs transition-colors">
                                    <i class="fa-solid fa-receipt text-xs"></i>
                                    <span>Cetak Struk</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400 text-xs">
                                <i class="fa-solid fa-receipt text-4xl text-slate-300 mb-2 block"></i>
                                Tidak ada transaksi yang tercatat pada tanggal ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transaksi->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $transaksi->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
