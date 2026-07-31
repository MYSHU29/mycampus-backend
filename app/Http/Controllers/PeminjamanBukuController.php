<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\PeminjamanBuku;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PeminjamanBukuController extends Controller
{
    public function index()
    {
        $peminjamanBuku = PeminjamanBuku::with('mahasiswa')->latest()->get();
        return view('peminjaman-buku.index', compact('peminjamanBuku'));
    }

    public function create()
    {
        $mahasiswa = Mahasiswa::orderBy('nama')->get();
        return view('peminjaman-buku.create', compact('mahasiswa'));
    }

    public function store(Request $request)
    {
        PeminjamanBuku::create($this->validatedData($request));

        return redirect()->route('peminjaman-buku.index')->with('success', 'Data peminjaman buku berhasil ditambahkan.');
    }

    public function edit(PeminjamanBuku $peminjamanBuku)
    {
        $mahasiswa = Mahasiswa::orderBy('nama')->get();
        return view('peminjaman-buku.edit', compact('peminjamanBuku', 'mahasiswa'));
    }

    public function update(Request $request, PeminjamanBuku $peminjamanBuku)
    {
        $peminjamanBuku->update($this->validatedData($request));

        return redirect()->route('peminjaman-buku.index')->with('success', 'Data peminjaman buku berhasil diperbarui.');
    }

    public function destroy(PeminjamanBuku $peminjamanBuku)
    {
        $peminjamanBuku->delete();

        return redirect()->route('peminjaman-buku.index')->with('success', 'Data peminjaman buku berhasil dihapus.');
    }

    private function validatedData(Request $request): array
    {
        $data = $request->validate([
            'nim' => ['required', 'exists:mahasiswa,nim'],
            'kode_buku' => ['required', 'string', 'max:255'],
            'judul_buku' => ['required', 'string', 'max:255'],
            'pengarang' => ['required', 'string', 'max:255'],
            'tanggal_pinjam' => ['required', 'date'],
            'tanggal_kembali_rencana' => ['required', 'date', 'after_or_equal:tanggal_pinjam'],
            'tanggal_kembali_aktual' => ['nullable', 'date', 'after_or_equal:tanggal_pinjam'],
            'status' => ['required', Rule::in(['dipinjam', 'dikembalikan', 'terlambat'])],
            'denda' => ['nullable', 'integer', 'min:0'],
            'keterangan' => ['nullable', 'string'],
        ]);

        $data['denda'] = $data['denda'] ?? 0;

        return $data;
    }
}
