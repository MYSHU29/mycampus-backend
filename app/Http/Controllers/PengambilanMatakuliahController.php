<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\PengambilanMatakuliah;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PengambilanMatakuliahController extends Controller
{
    public function index()
    {
        $pengambilanMatakuliah = PengambilanMatakuliah::with('mahasiswa')->latest()->get();
        return view('pengambilan-matakuliah.index', compact('pengambilanMatakuliah'));
    }

    public function create()
    {
        $mahasiswa = Mahasiswa::orderBy('nama')->get();
        return view('pengambilan-matakuliah.create', compact('mahasiswa'));
    }

    public function store(Request $request)
    {
        PengambilanMatakuliah::create($this->validatedData($request));

        return redirect()->route('pengambilan-matakuliah.index')->with('success', 'Data pengambilan matakuliah berhasil ditambahkan.');
    }

    public function edit(PengambilanMatakuliah $pengambilanMatakuliah)
    {
        $mahasiswa = Mahasiswa::orderBy('nama')->get();
        return view('pengambilan-matakuliah.edit', compact('pengambilanMatakuliah', 'mahasiswa'));
    }

    public function update(Request $request, PengambilanMatakuliah $pengambilanMatakuliah)
    {
        $pengambilanMatakuliah->update($this->validatedData($request));

        return redirect()->route('pengambilan-matakuliah.index')->with('success', 'Data pengambilan matakuliah berhasil diperbarui.');
    }

    public function destroy(PengambilanMatakuliah $pengambilanMatakuliah)
    {
        $pengambilanMatakuliah->delete();

        return redirect()->route('pengambilan-matakuliah.index')->with('success', 'Data pengambilan matakuliah berhasil dihapus.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'nim' => ['required', 'exists:mahasiswa,nim'],
            'kode_matkul' => ['required', 'string', 'max:255'],
            'nama_matkul' => ['required', 'string', 'max:255'],
            'sks' => ['required', 'integer', 'min:1', 'max:6'],
            'dosen' => ['required', 'string', 'max:255'],
            'semester' => ['required', 'integer', 'min:1', 'max:14'],
            'tahun_akademik' => ['required', 'integer', 'min:2000', 'max:2099'],
            'status' => ['required', Rule::in(['aktif', 'mengulang', 'lulus'])],
            'nilai_akhir' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'grade' => ['nullable', Rule::in(['A', 'B+', 'B', 'C+', 'C', 'D', 'E'])],
        ]);
    }
}
