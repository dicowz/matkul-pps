<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KaryawanController extends Controller
{
    public function index(): View
    {
        $karyawan = Karyawan::withCount('absensi')->orderBy('nama')->get();

        return view('admin.karyawan.index', compact('karyawan'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'jabatan' => ['required', 'string', 'max:50'],
            'fingerprint_id' => ['required', 'integer', 'min:1', 'unique:karyawan,fingerprint_id'],
            'status' => ['required', 'in:aktif,nonaktif'],
        ], [
            'nama.required' => 'Nama karyawan wajib diisi.',
            'jabatan.required' => 'Jabatan karyawan wajib diisi.',
            'fingerprint_id.required' => 'Nomor ID Fingerprint wajib diisi.',
            'fingerprint_id.unique' => 'Nomor ID Fingerprint ini sudah terdaftar untuk karyawan lain.',
        ]);

        Karyawan::create($validated);

        return redirect()->route('admin.karyawan.index')
            ->with('success', 'Data karyawan baru berhasil didaftarkan ke sistem absensi.');
    }

    public function update(Request $request, Karyawan $karyawan): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'jabatan' => ['required', 'string', 'max:50'],
            'fingerprint_id' => ['required', 'integer', 'min:1', 'unique:karyawan,fingerprint_id,'.$karyawan->id],
            'status' => ['required', 'in:aktif,nonaktif'],
        ], [
            'nama.required' => 'Nama karyawan wajib diisi.',
            'jabatan.required' => 'Jabatan karyawan wajib diisi.',
            'fingerprint_id.required' => 'Nomor ID Fingerprint wajib diisi.',
            'fingerprint_id.unique' => 'Nomor ID Fingerprint ini sudah terdaftar untuk karyawan lain.',
        ]);

        $karyawan->update($validated);

        return redirect()->route('admin.karyawan.index')
            ->with('success', 'Data karyawan '.$karyawan->nama.' berhasil diperbarui.');
    }

    public function toggleStatus(Karyawan $karyawan): RedirectResponse
    {
        $karyawan->status = $karyawan->status === 'aktif' ? 'nonaktif' : 'aktif';
        $karyawan->save();

        $statusText = $karyawan->status === 'aktif' ? 'Aktif' : 'Non-Aktif';

        return back()->with('success', 'Status karyawan '.$karyawan->nama.' diubah menjadi '.$statusText.'.');
    }

    public function destroy(Karyawan $karyawan): RedirectResponse
    {
        if ($karyawan->absensi()->count() > 0) {
            $karyawan->status = 'nonaktif';
            $karyawan->save();

            return back()->with('warning', 'Karyawan '.$karyawan->nama.' memiliki riwayat absensi, statusnya otomatis dinonaktifkan untuk menjaga histori kehadiran.');
        }

        $karyawan->delete();

        return redirect()->route('admin.karyawan.index')
            ->with('success', 'Karyawan berhasil dihapus.');
    }
}
