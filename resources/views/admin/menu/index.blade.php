@extends('layouts.admin')

@section('title', 'Manajemen Menu')
@section('page_title', 'Manajemen Menu')

@section('content')
<div class="space-y-6">

    <!-- Top Bar: Filter, Search & Add Menu Button -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <!-- Search and Filter Form -->
        <form method="GET" action="{{ route('admin.menu.index') }}" class="flex flex-wrap items-center gap-3 flex-1">
            <!-- Search Input -->
            <div class="relative flex-1 min-w-[200px]">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </div>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama menu atau deskripsi..."
                    class="w-full pl-9 pr-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:bg-white transition-all">
            </div>

            <!-- Kategori Dropdown Filter -->
            <select name="kategori_id" onchange="this.form.submit()" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-amber-500 transition-all">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ $kategoriId == $cat->id ? 'selected' : '' }}>{{ $cat->nama_kategori }}</option>
                @endforeach
            </select>

            <!-- Status Dropdown Filter -->
            <select name="status" onchange="this.form.submit()" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-amber-500 transition-all">
                <option value="">Semua Status</option>
                <option value="tersedia" {{ $status === 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                <option value="tidak" {{ $status === 'tidak' ? 'selected' : '' }}>Habis / Tidak</option>
            </select>

            <button type="submit" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition-all">
                Filter
            </button>
            @if($search || $kategoriId || $status)
                <a href="{{ route('admin.menu.index') }}" class="text-xs text-rose-500 hover:text-rose-700 underline font-medium">Reset</a>
            @endif
        </form>

        <!-- Tambah Menu Button -->
        <a href="{{ route('admin.menu.create') }}" class="px-4 py-2.5 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-semibold flex items-center gap-2 shadow-sm transition-all shrink-0">
            <i class="fa-solid fa-plus"></i>
            <span>Tambah Menu Baru</span>
        </a>
    </div>

    <!-- Menu Grid / Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-500 font-semibold uppercase tracking-wider">
                        <th class="py-3.5 px-5 w-16">Foto</th>
                        <th class="py-3.5 px-5">Nama Menu</th>
                        <th class="py-3.5 px-5">Kategori</th>
                        <th class="py-3.5 px-5">Harga Satuan</th>
                        <th class="py-3.5 px-5 text-center">Status</th>
                        <th class="py-3.5 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($menus as $menu)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3 px-5">
                                <div class="w-12 h-12 rounded-xl bg-amber-50 border border-slate-200/70 overflow-hidden flex items-center justify-center text-amber-700 shrink-0">
                                    @if($menu->foto_url)
                                        <img src="{{ $menu->foto_url }}" alt="{{ $menu->nama_menu }}" class="w-full h-full object-cover">
                                    @else
                                        <i class="fa-solid fa-mug-hot text-base"></i>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3 px-5">
                                <p class="font-bold text-slate-900 text-sm leading-snug">{{ $menu->nama_menu }}</p>
                                <p class="text-[11px] text-slate-400 line-clamp-1 max-w-xs">{{ $menu->deskripsi ?: 'Tidak ada deskripsi.' }}</p>
                            </td>
                            <td class="py-3 px-5">
                                <span class="px-2.5 py-1 rounded-lg text-[11px] font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $menu->kategori->nama_kategori ?? '-' }}
                                </span>
                            </td>
                            <td class="py-3 px-5">
                                <span class="font-bold text-amber-700 text-sm">{{ $menu->formatted_harga }}</span>
                            </td>
                            <td class="py-3 px-5 text-center">
                                <form action="{{ route('admin.menu.toggle', $menu->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" title="Klik untuk ubah status ketersediaan"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold cursor-pointer transition-all {{ $menu->status === 'tersedia' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100' : 'bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-100' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $menu->status === 'tersedia' ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                                        <span>{{ $menu->status === 'tersedia' ? 'Tersedia' : 'Habis' }}</span>
                                    </button>
                                </form>
                            </td>
                            <td class="py-3 px-5 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.menu.edit', $menu->id) }}" class="p-2 text-slate-600 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors" title="Edit Menu">
                                        <i class="fa-solid fa-pen-to-square text-sm"></i>
                                    </a>
                                    <form action="{{ route('admin.menu.destroy', $menu->id) }}" method="POST" onsubmit="return confirmDeleteMenu(event, '{{ addslashes($menu->nama_menu) }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus / Nonaktifkan Menu">
                                            <i class="fa-solid fa-trash-can text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400 text-xs">
                                <i class="fa-solid fa-utensils text-3xl text-slate-300 mb-2 block"></i>
                                Tidak ada data menu yang cocok dengan pencarian / filter Anda.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($menus->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $menus->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    function confirmDeleteMenu(e, name) {
        e.preventDefault();
        Swal.fire({
            title: 'Hapus Menu?',
            text: `Apakah Anda yakin ingin menghapus/menonaktifkan menu "${name}"?`,
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
