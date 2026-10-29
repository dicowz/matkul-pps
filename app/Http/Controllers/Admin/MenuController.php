<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\Menu;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MenuController extends Controller
{
    public function index(Request $request): View
    {
        $kategoriId = $request->query('kategori_id');
        $search = $request->query('search');
        $status = $request->query('status');

        $query = Menu::with('kategori')->withCount('detailTransaksi');

        if ($kategoriId) {
            $query->where('kategori_id', $kategoriId);
        }

        if ($status && in_array($status, ['tersedia', 'tidak'])) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_menu', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        $menus = $query->orderBy('nama_menu')->paginate(12)->withQueryString();
        $categories = Kategori::orderBy('nama_kategori')->get();

        return view('admin.menu.index', compact('menus', 'categories', 'kategoriId', 'search', 'status'));
    }

    public function create(): View
    {
        $categories = Kategori::orderBy('nama_kategori')->get();

        return view('admin.menu.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_menu' => ['required', 'string', 'max:100'],
            'kategori_id' => ['required', 'exists:kategori,id'],
            'harga' => ['required', 'numeric', 'min:0'],
            'deskripsi' => ['nullable', 'string'],
            'status' => ['required', 'in:tersedia,tidak'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ], [
            'nama_menu.required' => 'Nama menu wajib diisi.',
            'kategori_id.required' => 'Kategori menu wajib dipilih.',
            'harga.required' => 'Harga wajib diisi.',
            'foto.image' => 'File foto harus berupa format gambar (jpg, png, webp).',
        ]);

        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('menu', 'public');
            $validated['foto'] = $path;
        }

        Menu::create($validated);

        return redirect()->route('admin.menu.index')
            ->with('success', 'Menu baru berhasil ditambahkan.');
    }

    public function edit(Menu $menu): View
    {
        $categories = Kategori::orderBy('nama_kategori')->get();

        return view('admin.menu.edit', compact('menu', 'categories'));
    }

    public function update(Request $request, Menu $menu): RedirectResponse
    {
        $validated = $request->validate([
            'nama_menu' => ['required', 'string', 'max:100'],
            'kategori_id' => ['required', 'exists:kategori,id'],
            'harga' => ['required', 'numeric', 'min:0'],
            'deskripsi' => ['nullable', 'string'],
            'status' => ['required', 'in:tersedia,tidak'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        if ($request->hasFile('foto')) {
            if ($menu->foto && Storage::disk('public')->exists($menu->foto)) {
                Storage::disk('public')->delete($menu->foto);
            }
            $path = $request->file('foto')->store('menu', 'public');
            $validated['foto'] = $path;
        }

        $menu->update($validated);

        return redirect()->route('admin.menu.index')
            ->with('success', 'Menu '.$menu->nama_menu.' berhasil diperbarui.');
    }

    public function toggleStatus(Menu $menu): RedirectResponse
    {
        $menu->status = $menu->status === 'tersedia' ? 'tidak' : 'tersedia';
        $menu->save();

        $statusText = $menu->status === 'tersedia' ? 'Tersedia' : 'Habis / Tidak Tersedia';

        return back()->with('success', 'Status menu '.$menu->nama_menu.' diubah menjadi '.$statusText.'.');
    }

    public function destroy(Menu $menu): RedirectResponse
    {
        // NF-04 Integritas data: cek apakah menu memiliki transaksi
        if ($menu->detailTransaksi()->count() > 0) {
            $menu->status = 'tidak';
            $menu->save();

            return back()->with('warning', 'Menu '.$menu->nama_menu.' telah memiliki riwayat transaksi, sehingga statusnya otomatis diubah menjadi "Tidak Tersedia" untuk menjaga integritas data.');
        }

        if ($menu->foto && Storage::disk('public')->exists($menu->foto)) {
            Storage::disk('public')->delete($menu->foto);
        }

        $menu->delete();

        return redirect()->route('admin.menu.index')
            ->with('success', 'Menu berhasil dihapus dari sistem.');
    }
}
