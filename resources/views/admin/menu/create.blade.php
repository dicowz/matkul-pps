@extends('layouts.admin')

@section('title', 'Tambah Menu Baru')
@section('page_title', 'Tambah Menu Baru')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.menu.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1.5 transition-colors">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Daftar Menu</span>
        </a>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-7">
        <div class="mb-6 pb-4 border-b border-slate-100">
            <h3 class="font-bold text-slate-900 text-lg">Informasi Menu Baru</h3>
            <p class="text-xs text-slate-500">Lengkapi formulir di bawah ini untuk menambahkan menu baru ke kasir dan chatbot Kopi Kutho</p>
        </div>

        <form action="{{ route('admin.menu.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <!-- Nama Menu -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Nama Menu <span class="text-rose-500">*</span></label>
                <input type="text" name="nama_menu" value="{{ old('nama_menu') }}" required placeholder="Contoh: Kopi Susu Kutho Signature"
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:bg-white transition-all">
            </div>

            <!-- Kategori & Harga Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Kategori Menu <span class="text-rose-500">*</span></label>
                    <select name="kategori_id" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:bg-white transition-all">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('kategori_id') == $cat->id ? 'selected' : '' }}>{{ $cat->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Harga (Rupiah) <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-xs font-bold text-slate-400">Rp</span>
                        <input type="number" name="harga" value="{{ old('harga') }}" required min="0" step="500" placeholder="18000"
                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:bg-white transition-all">
                    </div>
                </div>
            </div>

            <!-- Status Ketersediaan -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Status Ketersediaan</label>
                <div class="flex items-center gap-4">
                    <label class="flex items-center gap-2 cursor-pointer text-xs font-medium text-slate-700">
                        <input type="radio" name="status" value="tersedia" {{ old('status', 'tersedia') === 'tersedia' ? 'checked' : '' }} class="text-amber-600 focus:ring-amber-500">
                        <span class="text-emerald-700 font-semibold bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200">Tersedia (Ready)</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer text-xs font-medium text-slate-700">
                        <input type="radio" name="status" value="tidak" {{ old('status') === 'tidak' ? 'checked' : '' }} class="text-amber-600 focus:ring-amber-500">
                        <span class="text-rose-700 font-semibold bg-rose-50 px-2.5 py-1 rounded-lg border border-rose-200">Habis / Tidak Tersedia</span>
                    </label>
                </div>
            </div>

            <!-- Deskripsi -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Deskripsi Menu & Komposisi</label>
                <textarea name="deskripsi" rows="3" placeholder="Jelaskan aroma, cita rasa, atau komposisi menu untuk referensi AI Chatbot dan kasir..."
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:bg-white transition-all">{{ old('deskripsi') }}</textarea>
            </div>

            <!-- Foto Menu Upload -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Foto Menu (Opsional)</label>
                <div class="flex items-center gap-4">
                    <div id="image-preview" class="w-20 h-20 rounded-2xl bg-slate-100 border border-dashed border-slate-300 flex items-center justify-center text-slate-400 overflow-hidden shrink-0">
                        <i class="fa-regular fa-image text-2xl"></i>
                    </div>
                    <div class="flex-1">
                        <input type="file" name="foto" id="foto-input" accept="image/*" onchange="previewImage(event)"
                            class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100 cursor-pointer">
                        <p class="text-[11px] text-slate-400 mt-1">Maksimal 2MB. Format: JPG, PNG, WEBP.</p>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('admin.menu.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-xs font-medium hover:bg-slate-50 transition-colors">Batal</a>
                <button type="submit" class="px-6 py-2.5 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-semibold shadow-md shadow-amber-900/20 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                    <span>Simpan Menu</span>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function previewImage(event) {
        const preview = document.getElementById('image-preview');
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
            }
            reader.readAsDataURL(file);
        }
    }
</script>
@endpush
@endsection
