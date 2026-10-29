@extends('layouts.admin')

@section('title', 'Absensi Karyawan & IoT ESP32')
@section('page_title', 'Absensi Karyawan & IoT ESP32')

@section('content')
<div class="space-y-6">

    <!-- KPI Summary Row -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Karyawan Aktif</p>
                <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ $totalKaryawanAktif }} Orang</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Terdaftar di database sistem</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-500/10 text-amber-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-users"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Hadir Pada Tanggal Ini</p>
                <h3 class="text-2xl font-bold text-emerald-600 mt-1">{{ $totalHadir }} Orang</h3>
                <p class="text-[11px] text-emerald-600 mt-0.5">Tercatat di sensor fingerprint</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Belum Presensi</p>
                <h3 class="text-2xl font-bold text-slate-700 mt-1">{{ $belumHadir }} Orang</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Belum scan fingerprint hari ini</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-500 flex items-center justify-center text-xl">
                <i class="fa-solid fa-user-clock"></i>
            </div>
        </div>
    </div>

    <!-- ESP32 Fingerprint Hardware Simulator Widget -->
    <div class="bg-gradient-to-br from-slate-900 to-[#18110b] text-white p-6 rounded-3xl shadow-xl border border-white/10">
        <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
            <!-- Hardware Specs & OLED Display Simulation -->
            <div class="flex-1 space-y-4">
                <div class="flex items-center gap-3">
                    <span class="px-2.5 py-1 bg-amber-500/20 text-amber-300 border border-amber-500/30 rounded-lg text-xs font-mono font-semibold">
                        <i class="fa-solid fa-microchip mr-1.5"></i> ESP32 + Fingerprint Sensor
                    </span>
                    <span class="inline-flex items-center gap-1.5 text-xs text-emerald-400 font-medium">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span> Wi-Fi Connected
                    </span>
                </div>

                <div>
                    <h3 class="text-lg font-bold text-white tracking-tight">Simulator Perangkat Keras ESP32</h3>
                    <p class="text-xs text-stone-300 mt-1 max-w-xl">
                        Simulasikan pengiriman data sidik jari dari mikrokontroler ESP32 melalui endpoint API <code class="bg-white/10 px-1.5 py-0.5 rounded text-amber-300 font-mono text-[11px]">POST /api/absensi/scan</code> dengan token keamanan <code class="bg-white/10 px-1.5 py-0.5 rounded text-amber-300 font-mono text-[11px]">X-Device-Token</code>.
                    </p>
                </div>

                <!-- OLED LCD Screen Simulation -->
                <div class="bg-black/80 rounded-2xl p-4 border border-emerald-500/40 font-mono max-w-lg shadow-inner">
                    <div class="flex items-center justify-between text-[11px] text-emerald-500/70 border-b border-emerald-500/20 pb-1.5 mb-2">
                        <span>OLED 128x64 DISPLAY</span>
                        <span id="oled-clock">{{ now()->format('H:i:s') }}</span>
                    </div>
                    <div id="oled-screen" class="text-xs text-emerald-400 space-y-1">
                        <p class="text-emerald-300 font-bold">&gt; KOPI KUTHO PRESENSI</p>
                        <p class="text-emerald-400/90">&gt; Status: Siap Membaca Jari...</p>
                        <p class="text-[11px] text-emerald-500/70">&gt; Silakan tempelkan sidik jari pada sensor optik.</p>
                    </div>
                </div>
            </div>

            <!-- Simulator Controls -->
            <div class="bg-white/5 border border-white/10 p-5 rounded-2xl w-full lg:w-96 space-y-4 shrink-0">
                <p class="text-xs font-bold uppercase tracking-wider text-amber-400">Pilih ID Jari Untuk Scan:</p>
                
                <div class="space-y-2">
                    <label class="block text-xs text-stone-300">Pilih Karyawan Terdaftar:</label>
                    <select id="sim-karyawan-select" onchange="updateSimFingerprintId()" class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                        @foreach($karyawanList as $k)
                            <option value="{{ $k->fingerprint_id }}" class="text-slate-900">
                                {{ $k->nama }} (ID #{{ $k->fingerprint_id }} - {{ $k->jabatan }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] text-stone-400 mb-1">ID Fingerprint:</label>
                        <input type="number" id="sim-fingerprint-id" value="{{ $karyawanList->first()->fingerprint_id ?? 101 }}" class="w-full px-3 py-1.5 bg-white/10 border border-white/20 rounded-xl text-xs font-mono text-amber-300 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] text-stone-400 mb-1">Token Status:</label>
                        <select id="sim-token-type" class="w-full px-2 py-1.5 bg-white/10 border border-white/20 rounded-xl text-xs text-white focus:outline-none">
                            <option value="valid">Token Valid</option>
                            <option value="invalid">Token Salah (Test 401)</option>
                        </select>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="button" onclick="triggerFingerprintScan()" id="btn-scan" class="w-full py-2.5 px-4 bg-gradient-to-r from-amber-600 to-amber-500 hover:from-amber-500 hover:to-amber-400 text-white font-bold rounded-xl text-xs flex items-center justify-center gap-2 shadow-lg shadow-amber-950/50 transition-all">
                        <i class="fa-solid fa-fingerprint text-base"></i>
                        <span>Scan Sidik Jari Sekarang</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Attendance Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden space-y-4 p-5">
        <!-- Filter Form -->
        <form method="GET" action="{{ route('admin.absensi.index') }}" class="flex flex-wrap items-center justify-between gap-4 pb-4 border-b border-slate-100">
            <div class="flex flex-wrap items-center gap-3">
                <!-- Tanggal Filter -->
                <div class="flex items-center gap-2 text-xs text-slate-600 font-medium">
                    <label>Tanggal:</label>
                    <input type="date" name="tanggal" value="{{ $tanggal }}" onchange="this.form.submit()"
                        class="px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>

                <!-- Karyawan Filter -->
                <select name="karyawan_id" onchange="this.form.submit()" class="px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <option value="">Semua Karyawan</option>
                    @foreach($karyawanList as $k)
                        <option value="{{ $k->id }}" {{ $karyawanId == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
                    @endforeach
                </select>

                <!-- Status Filter -->
                <select name="status" onchange="this.form.submit()" class="px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <option value="">Semua Status</option>
                    <option value="Tepat Waktu" {{ $status === 'Tepat Waktu' ? 'selected' : '' }}>Tepat Waktu</option>
                    <option value="Terlambat" {{ $status === 'Terlambat' ? 'selected' : '' }}>Terlambat</option>
                </select>

                <button type="submit" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold">
                    Terapkan
                </button>
            </div>

            <div class="text-xs text-slate-500 font-medium">
                Menampilkan data tanggal: <span class="font-bold text-slate-800">{{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}</span>
            </div>
        </form>

        <!-- Attendance Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-500 font-semibold uppercase tracking-wider">
                        <th class="py-3 px-4">Nama Karyawan</th>
                        <th class="py-3 px-4">Jabatan</th>
                        <th class="py-3 px-4 text-center">ID Fingerprint</th>
                        <th class="py-3 px-4 text-center">Jam Masuk</th>
                        <th class="py-3 px-4 text-center">Jam Keluar</th>
                        <th class="py-3 px-4 text-center">Status Kehadiran</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($absensi as $item)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3 px-4 font-bold text-slate-900">
                                {{ $item->karyawan->nama ?? 'Tidak Diketahui' }}
                            </td>
                            <td class="py-3 px-4 text-slate-600">
                                {{ $item->karyawan->jabatan ?? '-' }}
                            </td>
                            <td class="py-3 px-4 text-center font-mono text-slate-500">
                                #{{ $item->karyawan->fingerprint_id ?? '-' }}
                            </td>
                            <td class="py-3 px-4 text-center font-mono font-bold text-emerald-700">
                                <span class="bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200/60">
                                    {{ substr($item->jam_masuk, 0, 5) }} WIB
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center font-mono">
                                @if($item->jam_keluar)
                                    <span class="bg-blue-50 text-blue-700 px-2.5 py-1 rounded-lg border border-blue-200/60 font-bold">
                                        {{ substr($item->jam_keluar, 0, 5) }} WIB
                                    </span>
                                @else
                                    <span class="text-amber-600 italic bg-amber-50 px-2 py-0.5 rounded text-[11px]">Sedang Bekerja</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $item->status === 'Tepat Waktu' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                    {{ $item->status }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <form action="{{ route('admin.absensi.destroy', $item->id) }}" method="POST" onsubmit="return confirmDeleteAbsensi(event)">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus Catatan">
                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400 text-xs">
                                <i class="fa-solid fa-fingerprint text-3xl text-slate-300 mb-2 block"></i>
                                Belum ada riwayat absensi pada tanggal ini. Coba gunakan simulator ESP32 di atas!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($absensi->hasPages())
            <div class="pt-4 border-t border-slate-100">
                {{ $absensi->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    function updateSimFingerprintId() {
        const select = document.getElementById('sim-karyawan-select');
        document.getElementById('sim-fingerprint-id').value = select.value;
    }

    async function triggerFingerprintScan() {
        const fpId = document.getElementById('sim-fingerprint-id').value;
        const tokenType = document.getElementById('sim-token-type').value;
        const token = (tokenType === 'valid') ? 'kopikutho_esp32_secret_key_2026' : 'invalid_token_xyz';
        const oled = document.getElementById('oled-screen');
        const btn = document.getElementById('btn-scan');

        // Update OLED screen UI
        oled.innerHTML = `
            <p class="text-amber-300 font-bold">&gt; SENSOR AKTIF...</p>
            <p class="text-amber-400">&gt; Membaca pola sidik jari ID #${fpId}...</p>
            <p class="text-amber-400/80">&gt; Mengirim paket via Wi-Fi ke server...</p>
        `;
        btn.disabled = true;

        try {
            const response = await fetch('/api/absensi/scan', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Device-Token': token
                },
                body: JSON.stringify({
                    fingerprint_id: parseInt(fpId)
                })
            });

            const data = await response.json();

            if (response.ok && (data.status === 'success' || data.status === 'info')) {
                const actionLabel = data.action === 'masuk' ? 'JAM MASUK TERCATAT' : (data.action === 'keluar' ? 'JAM KELUAR TERCATAT' : 'SUDAH LENGKAP');
                oled.innerHTML = `
                    <p class="text-emerald-300 font-bold">&gt; ABSENSI BERHASIL! [${actionLabel}]</p>
                    <p class="text-emerald-400">&gt; Nama: ${data.karyawan.nama} (${data.karyawan.jabatan})</p>
                    <p class="text-emerald-400/90">&gt; Status: ${data.status_kehadiran || '-'} | Waktu: ${data.jam_masuk || data.jam_keluar || ''}</p>
                    <p class="text-[11px] text-emerald-500/80">&gt; Pesan: ${data.message}</p>
                `;

                Swal.fire({
                    title: data.action === 'masuk' ? 'Absensi Masuk Berhasil!' : (data.action === 'keluar' ? 'Absensi Keluar Berhasil!' : 'Info Absensi'),
                    html: `<b>${data.karyawan.nama}</b><br><span class="text-sm text-gray-500">${data.message}</span>`,
                    icon: data.status === 'success' ? 'success' : 'info',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.reload();
                });
            } else {
                oled.innerHTML = `
                    <p class="text-rose-400 font-bold">&gt; GAGAL: ${data.status || 'ERROR'}</p>
                    <p class="text-rose-300">&gt; ${data.message || 'Permintaan ditolak oleh server.'}</p>
                `;

                Swal.fire({
                    title: 'Gagal Absensi!',
                    text: data.message || 'Terjadi kesalahan pada validasi fingerprint / token.',
                    icon: 'error',
                    confirmButtonColor: '#d97706'
                });
            }
        } catch (error) {
            oled.innerHTML = `
                <p class="text-rose-400 font-bold">&gt; KONEKSI ERROR</p>
                <p class="text-rose-300">&gt; Gagal terhubung ke endpoint API server.</p>
            `;
            Swal.fire({
                title: 'Error Jaringan!',
                text: 'Tidak dapat menghubungi server API.',
                icon: 'error'
            });
        } finally {
            btn.disabled = false;
        }
    }

    function confirmDeleteAbsensi(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Hapus Catatan?',
            text: 'Apakah Anda yakin ingin menghapus data absensi ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d97706',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                e.target.submit();
            }
        });
        return false;
    }
</script>
@endpush
@endsection
