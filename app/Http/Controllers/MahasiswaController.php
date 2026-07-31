<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MahasiswaController extends Controller
{
    public function index()
    {
        $mahasiswa = Mahasiswa::orderBy('nim')->get();
        return view('data-mahasiswa', compact('mahasiswa'));
    }

    public function create()
    {
        return view('create-mahasiswa');
    }

    public function store(Request $request)
    {
        $data = [
            'nim'           => $request->nim,
            'nama'          => $request->nama,
            'email'         => $request->email,
            'no_telp'       => $request->no_telp,
            'tanggal_lahir' => $request->tanggal_lahir,
            'jenis_kelamin' => $request->jenis_kelamin,
            'kota_asal'     => $request->kota_asal,
            'alamat'        => $request->alamat,
            'prodi'         => $request->prodi,
            'fakultas'      => $request->fakultas,
            'angkatan'      => $request->angkatan,
            'semester'      => $request->semester,
            'ipk'           => $request->ipk,
            'status'        => $request->status,
            'catatan'       => $request->catatan,
        ];

        // Upload foto jika ada
        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('foto_mahasiswa', 'public');
        }

        Mahasiswa::create($data);

        return redirect()->route('data-mahasiswa');
    }

    public function show($nim)
    {
        //
    }

    public function edit($nim)
    {
        // Gunakan nim bukan id karena PK adalah nim
        $mahasiswa = Mahasiswa::where('nim', $nim)->first();
        return view('edit-mahasiswa', compact('mahasiswa'));
    }

    public function update(Request $request, $nim)
    {
        $mahasiswa = Mahasiswa::where('nim', $nim)->first();

        $data = [
            'nim'           => $request->nim,
            'nama'          => $request->nama,
            'email'         => $request->email,
            'no_telp'       => $request->no_telp,
            'tanggal_lahir' => $request->tanggal_lahir,
            'jenis_kelamin' => $request->jenis_kelamin,
            'kota_asal'     => $request->kota_asal,
            'alamat'        => $request->alamat,
            'prodi'         => $request->prodi,
            'fakultas'      => $request->fakultas,
            'angkatan'      => $request->angkatan,
            'semester'      => $request->semester,
            'ipk'           => $request->ipk,
            'status'        => $request->status,
            'catatan'       => $request->catatan,
        ];

        // Upload foto baru jika ada
        if ($request->hasFile('foto')) {
            // Hapus foto lama
            if ($mahasiswa->foto) {
                Storage::disk('public')->delete($mahasiswa->foto);
            }
            $data['foto'] = $request->file('foto')->store('foto_mahasiswa', 'public');
        }

        $mahasiswa->update($data);

        return redirect()->route('data-mahasiswa');
    }

    public function destroy($nim)
    {
        $mahasiswa = Mahasiswa::where('nim', $nim)->first();

        // Hapus data relasi terlebih dahulu
        // Hapus prestasi mahasiswa
        \App\Models\PrestasiMahasiswa::where('nim', $nim)->delete();
        
        // Hapus pembayaran SPP
        \App\Models\PembayaranSpp::where('nim', $nim)->delete();
        
        // Hapus pengambilan matakuliah
        \App\Models\PengambilanMatakuliah::where('nim', $nim)->delete();
        
        // Hapus peminjaman buku
        \App\Models\PeminjamanBuku::where('nim', $nim)->delete();

        // Hapus foto jika ada
        if ($mahasiswa->foto) {
            Storage::disk('public')->delete($mahasiswa->foto);
        }

        $mahasiswa->delete();

        return redirect()->route('data-mahasiswa');
    }
}