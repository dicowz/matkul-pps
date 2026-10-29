@extends('layouts.admin')

@section('title', 'Dashboard Operasional')
@section('page_title', 'Dashboard Operasional')

@section('content')
<div class="space-y-6">

    <!-- KPI Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Card 1: Transaksi Hari Ini -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between hover:shadow-md transition-shadow">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Transaksi Hari Ini</p>
                <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($totalTransaksiHariIni) }}</h3>
                <p class="text-xs text-emerald-600 font-medium mt-1 flex items-center gap-1">
                    <i class="fa-solid fa-arrow-trend-up"></i> Real-time pos data
                </p>
            </div>
            <div class="w-13 h-13 rounded-2xl bg-amber-500/10 text-amber-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-receipt"></i>
            </div>
        </div>

        <!-- Card 2: Total Pendapatan Hari Ini -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between hover:shadow-md transition-shadow">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Omzet Hari Ini</p>
                <h3 class="text-2xl font-bold text-amber-600 mt-1">Rp {{ number_format($totalPendapatanHariIni, 0, ',', '.') }}</h3>
                <p class="text-xs text-slate-400 mt-1">Penjualan kasir terakumulasi</p>
            </div>
            <div class="w-13 h-13 rounded-2xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-coins"></i>
            </div>
        </div>

        <!-- Card 3: Karyawan Hadir Hari Ini -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between hover:shadow-md transition-shadow">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Karyawan Hadir</p>
                <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ $karyawanHadirHariIni }} <span class="text-sm font-normal text-slate-400">/ {{ $totalKaryawan }} Staf</span></h3>
                <p class="text-xs text-blue-600 font-medium mt-1 flex items-center gap-1">
                    <i class="fa-solid fa-fingerprint"></i> Presensi ESP32 aktif
                </p>
            </div>
            <div class="w-13 h-13 rounded-2xl bg-blue-500/10 text-blue-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-user-check"></i>
            </div>
        </div>

        <!-- Card 4: Menu Terlaris -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between hover:shadow-md transition-shadow">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Menu Terlaris</p>
                <h3 class="text-base font-bold text-slate-900 mt-1 truncate max-w-[170px]" title="{{ $menuTerlaris->first()->menu->nama_menu ?? '-' }}">
                    {{ $menuTerlaris->first()->menu->nama_menu ?? '-' }}
                </h3>
                <p class="text-xs text-amber-700 font-medium mt-1">
                    Terjual {{ $menuTerlaris->first()->total_terjual ?? 0 }} porsi
                </p>
            </div>
            <div class="w-13 h-13 rounded-2xl bg-orange-500/10 text-orange-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-fire"></i>
            </div>
        </div>
    </div>

    <!-- Charts & Menu Terlaris Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Sales Chart (2 cols) -->
        <div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="font-bold text-slate-900 text-base">Tren Penjualan 7 Hari Terakhir</h3>
                    <p class="text-xs text-slate-500">Omzet penjualan harian Cafe Resto Kopi Kutho</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 text-xs text-slate-600 bg-slate-100 px-2.5 py-1 rounded-lg">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> Omzet (Rp)
                    </span>
                </div>
            </div>
            <div class="h-64">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        <!-- Top 5 Best Sellers (1 col) -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-slate-900 text-base">Top 5 Menu Favorit</h3>
                <a href="{{ route('admin.laporan.menu_terjual') }}" class="text-xs text-amber-600 hover:text-amber-700 font-medium">Lihat Semua</a>
            </div>
            <div class="space-y-3.5 flex-1 overflow-y-auto">
                @forelse($menuTerlaris as $index => $item)
                    <div class="flex items-center justify-between p-2.5 rounded-xl hover:bg-slate-50 transition-colors">
                        <div class="flex items-center gap-3">
                            <span class="w-6 h-6 rounded-full {{ $index === 0 ? 'bg-amber-500 text-white' : ($index === 1 ? 'bg-slate-300 text-slate-800' : 'bg-slate-100 text-slate-500') }} flex items-center justify-center text-xs font-bold shrink-0">
                                {{ $index + 1 }}
                            </span>
                            <div>
                                <p class="text-xs font-semibold text-slate-800 leading-tight">{{ $item->menu->nama_menu ?? 'Menu' }}</p>
                                <span class="text-[11px] text-slate-400">{{ $item->menu->kategori->nama_kategori ?? 'Umum' }}</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-xs font-bold text-amber-600">{{ $item->total_terjual }} terjual</p>
                            <p class="text-[10px] text-slate-400">Rp {{ number_format($item->total_omzet, 0, ',', '.') }}</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-slate-400 text-xs">
                        Belum ada data penjualan tercatat.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent Transactions & Recent Chatbot Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Transaksi Terkini -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="font-bold text-slate-900 text-base">Transaksi Kasir Terkini</h3>
                    <p class="text-xs text-slate-500">Aktivitas pembayaran kasir terakhir</p>
                </div>
                <a href="{{ route('kasir.riwayat') }}" target="_blank" class="text-xs text-amber-600 hover:text-amber-700 font-medium flex items-center gap-1">
                    <span>Semua Transaksi</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-100 text-slate-400 font-medium">
                            <th class="pb-2.5">No. Transaksi</th>
                            <th class="pb-2.5">Waktu</th>
                            <th class="pb-2.5">Metode</th>
                            <th class="pb-2.5 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentTransactions as $trx)
                            <tr class="hover:bg-slate-50/70">
                                <td class="py-2.5 font-medium text-slate-800">
                                    <a href="{{ route('kasir.struk', $trx->id) }}" target="_blank" class="hover:text-amber-600 flex items-center gap-1.5">
                                        <i class="fa-solid fa-receipt text-amber-500"></i>
                                        <span>{{ $trx->nomor_transaksi ?? 'TRK-'.$trx->id }}</span>
                                    </a>
                                </td>
                                <td class="py-2.5 text-slate-500">{{ $trx->tanggal ? $trx->tanggal->format('H:i') : '-' }}</td>
                                <td class="py-2.5">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold uppercase {{ $trx->metode_pembayaran === 'tunai' ? 'bg-emerald-100 text-emerald-700' : ($trx->metode_pembayaran === 'qris' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700') }}">
                                        {{ $trx->metode_pembayaran }}
                                    </span>
                                </td>
                                <td class="py-2.5 text-right font-bold text-slate-900">
                                    Rp {{ number_format($trx->total, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-6 text-center text-slate-400 text-xs">Belum ada transaksi hari ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Chatbot AI Activity -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <i class="fa-brands fa-whatsapp text-lg"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900 text-base">Aktivitas Chatbot AI Terkini</h3>
                        <p class="text-xs text-slate-500">Interaksi pelanggan via WhatsApp</p>
                    </div>
                </div>
                <a href="{{ route('admin.chatbot.index') }}" class="text-xs text-emerald-600 hover:text-emerald-700 font-medium flex items-center gap-1">
                    <span>Lihat Log & Chat</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>

            <div class="space-y-3">
                @forelse($recentChatbotLogs as $log)
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 space-y-1.5 text-xs">
                        <div class="flex items-center justify-between text-slate-400 text-[11px]">
                            <span class="font-semibold text-slate-700"><i class="fa-solid fa-phone text-[10px] mr-1 text-slate-400"></i> {{ $log->masked_nomor }}</span>
                            <span>{{ $log->waktu ? $log->waktu->diffForHumans() : '-' }}</span>
                        </div>
                        <p class="text-slate-800 font-medium bg-white px-2.5 py-1.5 rounded-lg border border-slate-100 flex items-start gap-1.5">
                            <i class="fa-solid fa-comment-dots text-emerald-500 mt-0.5"></i>
                            <span>{{ $log->pesan_pengguna }}</span>
                        </p>
                        <p class="text-slate-600 text-[11px] pl-4 italic truncate">
                            <span class="font-semibold text-slate-500">AI:</span> {{ Str::limit($log->respons_chatbot, 90) }}
                        </p>
                    </div>
                @empty
                    <div class="text-center py-6 text-slate-400 text-xs">
                        Belum ada interaksi chatbot hari ini.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const ctx = document.getElementById('salesChart').getContext('2d');
        const labels = @json($chartLabels);
        const dataOmzet = @json($chartOmzet);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Omzet Penjualan (Rp)',
                    data: dataOmzet,
                    borderColor: '#d97706',
                    backgroundColor: 'rgba(217, 119, 6, 0.12)',
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#b45309',
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Omzet: Rp ' + context.parsed.y.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                if (value >= 1000000) {
                                    return (value / 1000000).toFixed(1) + ' Jt';
                                } else if (value >= 1000) {
                                    return (value / 1000) + ' Rb';
                                }
                                return value;
                            },
                            font: { size: 11 }
                        },
                        grid: {
                            color: '#f1f5f9'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: { size: 11 }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
