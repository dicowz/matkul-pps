@extends('layouts.admin')

@section('title', 'Data Karyawan & Fingerprint')
@section('page_title', 'Data Karyawan & Fingerprint')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h3 class="font-bold text-slate-900 text-lg">Kelola Data Karyawan</h3>
            <p class="text-xs text-slate-500 mt-0.5">Daftar staf Cafe Resto Kopi Kutho beserta mapping ID Fingerprint sensor ESP32</p>
        </div>
        <button onclick="openAddModal()" class="px-4 py-2.5 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-semibold flex items-center gap-2 shadow-sm transition-all shrink-0">
            <i class="fa-solid fa-user-plus"></i>
            <span>Tambah Karyawan Baru</span>
        </button>
    </div>

    <!-- Table of Employees -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-500 font-semibold uppercase tracking-wider">
                        <th class="py-3.5 px-6 w-16">No</th>
                        <th class="py-3.5 px-6">Nama Karyawan</th>
                        <th class="py-3.5 px-6">Jabatan</th>
                        <th class="py-3.5 px-6 text-center">ID Fingerprint (ESP32)</th>
                        <th class="py-3.5 px-6 text-center">Status</th>
                        <th class="py-3.5 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($karyawan as $index => $k)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-4 px-6 text-slate-400 font-medium">{{ $index + 1 }}</td>
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-amber-100 text-amber-700 font-bold flex items-center justify-center text-xs shrink-0">
                                        {{ strtoupper(substr($k->nama, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-900 text-sm">{{ $k->nama }}</p>
                                        <span class="text-[11px] text-slate-400">Terdaftar {{ $k->created_at ? $k->created_at->format('d M Y') : '-' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <span class="px-2.5 py-1 rounded-lg text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $k->jabatan }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-center">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                    <i class="fa-solid fa-fingerprint text-amber-600"></i>
                                    ID #{{ $k->fingerprint_id }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-center">
                                <form action="{{ route('admin.karyawan.toggle', $k->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" title="Klik untuk ubah status aktif/nonaktif"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold cursor-pointer transition-all {{ $k->status === 'aktif' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100' : 'bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-100' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $k->status === 'aktif' ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                                        <span>{{ $k->status === 'aktif' ? 'Aktif' : 'Non-Aktif' }}</span>
                                    </button>
                                </form>
                            </td>
                            <td class="py-4 px-6 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button onclick="openEditModal({{ $k->id }}, '{{ addslashes($k->nama) }}', '{{ addslashes($k->jabatan) }}', {{ $k->fingerprint_id }}, '{{ $k->status }}')"
                                        class="p-2 text-slate-600 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors" title="Edit Data Karyawan">
                                        <i class="fa-solid fa-user-pen text-sm"></i>
                                    </button>
                                    <form action="{{ route('admin.karyawan.destroy', $k->id) }}" method="POST" onsubmit="return confirmDeleteKaryawan(event, '{{ addslashes($k->nama) }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus Karyawan">
                                            <i class="fa-solid fa-trash-can text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-slate-400 text-xs">
                                <i class="fa-solid fa-users text-3xl text-slate-300 mb-2 block"></i>
                                Belum ada data karyawan terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Karyawan -->
<div id="addModal" class="fixed inset-0 bg-black/60 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl animate-fade-in">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <h4 class="font-bold text-slate-900 text-base">Tambah Karyawan Baru</h4>
            <button onclick="closeAddModal()" class="text-slate-400 hover:text-slate-700"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>
        <form action="{{ route('admin.karyawan.store') }}" method="POST" class="mt-5 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Nama Lengkap</label>
                <input type="text" name="nama" required placeholder="Contoh: Dimas Ardiansyah"
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:bg-white transition-all">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Jabatan / Posisi</label>
                <input type="text" name="jabatan" required placeholder="Contoh: Barista, Kasir, Kitchen Chef..."
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:bg-white transition-all">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Nomor Fingerprint ID (ESP32)</label>
                <input type="number" name="fingerprint_id" required min="1" placeholder="Contoh: 1, 2, 101..."
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:bg-white transition-all">
                <p class="text-[11px] text-slate-400 mt-1">Harus angka unik yang sesuai dengan nomor ID slot pada sensor fingerprint.</p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Status Kepegawaian</label>
                <select name="status" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:bg-white transition-all">
                    <option value="aktif">Aktif Bekerja</option>
                    <option value="nonaktif">Non-Aktif</option>
                </select>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3">
                <button type="button" onclick="closeAddModal()" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-xs font-medium hover:bg-slate-50">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-semibold shadow-sm">Simpan Karyawan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Karyawan -->
<div id="editModal" class="fixed inset-0 bg-black/60 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl animate-fade-in">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <h4 class="font-bold text-slate-900 text-base">Ubah Data Karyawan</h4>
            <button onclick="closeEditModal()" class="text-slate-400 hover:text-slate-700"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>
        <form id="editForm" method="POST" class="mt-5 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Nama Lengkap</label>
                <input type="text" id="edit_nama" name="nama" required
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:bg-white transition-all">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Jabatan / Posisi</label>
                <input type="text" id="edit_jabatan" name="jabatan" required
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:bg-white transition-all">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Nomor Fingerprint ID (ESP32)</label>
                <input type="number" id="edit_fingerprint_id" name="fingerprint_id" required min="1"
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:bg-white transition-all">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Status Kepegawaian</label>
                <select id="edit_status" name="status" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:bg-white transition-all">
                    <option value="aktif">Aktif Bekerja</option>
                    <option value="nonaktif">Non-Aktif</option>
                </select>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-xs font-medium hover:bg-slate-50">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-semibold shadow-sm">Perbarui Data</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openAddModal() {
        document.getElementById('addModal').classList.remove('hidden');
    }
    function closeAddModal() {
        document.getElementById('addModal').classList.add('hidden');
    }

    function openEditModal(id, nama, jabatan, fpId, status) {
        const form = document.getElementById('editForm');
        form.action = `/admin/karyawan/${id}`;
        document.getElementById('edit_nama').value = nama;
        document.getElementById('edit_jabatan').value = jabatan;
        document.getElementById('edit_fingerprint_id').value = fpId;
        document.getElementById('edit_status').value = status;
        document.getElementById('editModal').classList.remove('hidden');
    }
    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }

    function confirmDeleteKaryawan(e, name) {
        e.preventDefault();
        Swal.fire({
            title: 'Hapus Karyawan?',
            text: `Apakah Anda yakin ingin menghapus atau menonaktifkan data karyawan "${name}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d97706',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Lanjutkan!',
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
