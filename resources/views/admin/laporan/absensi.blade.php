@extends('layouts.admin')

@section('title', 'Laporan Rekap Absensi')
@section('page_title', 'Laporan Rekap Absensi Karyawan')

@section('content')
<div class="space-y-6">

    <!-- Filter & Action Bar -->
    <div class="no-print bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <form method="GET" action="{{ route('admin.laporan.absensi') }}" class="flex flex-wrap items-center gap-3">
            <div class="flex items-center gap-2 text-xs text-slate-700 font-medium">
                <label>Bulan:</label>
                <select name="bulan" class="px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create(2026, $m, 1)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
            </div>

            <div class="flex items-center gap-2 text-xs text-slate-700 font-medium">
                <label>Tahun:</label>
                <select name="tahun" class="px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800">
                    @for($y = 2024; $y <= 2027; $y++)
                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <select name="karyawan_id" class="px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700">
                <option value="">Semua Karyawan</option>
                @foreach($karyawanList as $k)
                    <option value="{{ $k->id }}" {{ $karyawanId == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
                @endforeach
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
                <h2 class="text-xl font-bold text-slate-900 uppercase tracking-tight">Laporan Rekapitulasi Presensi Karyawan</h2>
                <p class="text-xs text-slate-600 font-medium mt-1">Cafe Resto Kopi Kutho &bull; Sistem Presensi Terintegrasi Sensor ESP32</p>
                <p class="text-xs text-slate-500 mt-0.5">
                    Periode: <span class="font-bold text-slate-800">{{ \Carbon\Carbon::create($tahun, $bulan, 1)->translatedFormat('F Y') }}</span>
                </p>
            </div>
            <div class="text-right">
                <p class="text-[11px] text-slate-400">Dicetak pada:</p>
                <p class="text-xs font-semibold text-slate-700">{{ now()->translatedFormat('d F Y H:i') }} WIB</p>
            </div>
        </div>

        <!-- Summary Per Karyawan Table -->
        <div>
            <h4 class="font-bold text-slate-900 text-sm mb-3">Ringkasan Kehadiran per Staf</h4>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border border-slate-200 rounded-xl overflow-hidden">
                    <thead class="bg-slate-100 text-slate-700 font-semibold uppercase tracking-wider">
                        <tr>
                            <th class="py-2.5 px-4">Nama Karyawan</th>
                            <th class="py-2.5 px-4">Jabatan</th>
                            <th class="py-2.5 px-4 text-center">ID Fingerprint</th>
                            <th class="py-2.5 px-4 text-center">Total Hadir</th>
                            <th class="py-2.5 px-4 text-center">Tepat Waktu</th>
                            <th class="py-2.5 px-4 text-center">Terlambat</th>
                            <th class="py-2.5 px-4 text-center">Kedisiplinan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($rekapPerKaryawan as $rk)
                            @php
                                $persen = $rk->total_hadir > 0 ? round(($rk->total_tepat_waktu / $rk->total_hadir) * 100) : 0;
                            @endphp
                            <tr class="hover:bg-slate-50/70">
                                <td class="py-2.5 px-4 font-bold text-slate-900">{{ $rk->nama }}</td>
                                <td class="py-2.5 px-4 text-slate-600">{{ $rk->jabatan }}</td>
                                <td class="py-2.5 px-4 text-center font-mono text-slate-500">#{{ $rk->fingerprint_id }}</td>
                                <td class="py-2.5 px-4 text-center font-bold text-slate-800">{{ $rk->total_hadir }} Hari</td>
                                <td class="py-2.5 px-4 text-center text-emerald-700 font-medium">{{ $rk->total_tepat_waktu }}x</td>
                                <td class="py-2.5 px-4 text-center text-rose-700 font-medium">{{ $rk->total_terlambat }}x</td>
                                <td class="py-2.5 px-4 text-center">
                                    <span class="px-2 py-0.5 rounded-full text-[11px] font-bold {{ $persen >= 80 ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                        {{ $persen }}%
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Detailed Daily Logs Table -->
        <div class="pt-4">
            <h4 class="font-bold text-slate-900 text-sm mb-3">Rincian Log Presensi Harian</h4>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border border-slate-200 rounded-xl overflow-hidden">
                    <thead class="bg-slate-100 text-slate-700 font-semibold uppercase tracking-wider">
                        <tr>
                            <th class="py-2.5 px-4">Tanggal</th>
                            <th class="py-2.5 px-4">Nama Karyawan</th>
                            <th class="py-2.5 px-4 text-center">Jam Masuk</th>
                            <th class="py-2.5 px-4 text-center">Jam Keluar</th>
                            <th class="py-2.5 px-4 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($absensi as $log)
                            <tr class="hover:bg-slate-50/70">
                                <td class="py-2 px-4 font-medium text-slate-700">{{ $log->tanggal ? $log->tanggal->translatedFormat('d M Y') : '-' }}</td>
                                <td class="py-2 px-4 font-semibold text-slate-900">{{ $log->karyawan->nama ?? '-' }}</td>
                                <td class="py-2 px-4 text-center font-mono font-medium text-emerald-700">{{ substr($log->jam_masuk, 0, 5) }}</td>
                                <td class="py-2 px-4 text-center font-mono text-slate-600">{{ $log->jam_keluar ? substr($log->jam_keluar, 0, 5) : '-' }}</td>
                                <td class="py-2 px-4 text-center">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold {{ $log->status === 'Tepat Waktu' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                        {{ $log->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-6 text-center text-slate-400 text-xs">Belum ada riwayat absensi pada bulan ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
