<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MahasiswaApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Mahasiswa::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nim', 'like', "%{$search}%")
                  ->orWhere('prodi', 'like', "%{$search}%")
                  ->orWhere('fakultas', 'like', "%{$search}%");
            });
        }

        if ($request->filled('prodi')) {
            $query->where('prodi', $request->prodi);
        }

        if ($request->filled('fakultas')) {
            $query->where('fakultas', $request->fakultas);
        }

        if ($request->filled('angkatan')) {
            $query->where('angkatan', $request->angkatan);
        }

        $mahasiswa = $query->orderBy('nim')->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $mahasiswa,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nim' => 'required|string|unique:mahasiswa,nim',
            'nama' => 'required|string|max:255',
            'email' => 'nullable|email',
            'no_telp' => 'nullable|string|max:20',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:L,P',
            'kota_asal' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'prodi' => 'nullable|string|max:255',
            'fakultas' => 'nullable|string|max:255',
            'angkatan' => 'nullable|integer|min:2000|max:2099',
            'semester' => 'nullable|integer|min:1|max:14',
            'ipk' => 'nullable|numeric|min:0|max:4',
            'status' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('foto_mahasiswa', 'public');
        }

        $mahasiswa = Mahasiswa::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Data mahasiswa berhasil ditambahkan',
            'data' => $mahasiswa,
        ], 201);
    }

    public function show($nim): JsonResponse
    {
        $mahasiswa = Mahasiswa::with(['pembayaranSpp', 'pengambilanMatakuliah', 'peminjamanBuku', 'prestasiMahasiswa'])
            ->where('nim', $nim)
            ->first();

        if (! $mahasiswa) {
            return response()->json([
                'success' => false,
                'message' => 'Mahasiswa tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $mahasiswa,
        ]);
    }

    public function update(Request $request, $nim): JsonResponse
    {
        $mahasiswa = Mahasiswa::where('nim', $nim)->first();

        if (! $mahasiswa) {
            return response()->json([
                'success' => false,
                'message' => 'Mahasiswa tidak ditemukan',
            ], 404);
        }

        $data = $request->validate([
            'nama' => 'sometimes|string|max:255',
            'email' => 'nullable|email',
            'no_telp' => 'nullable|string|max:20',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:L,P',
            'kota_asal' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'prodi' => 'nullable|string|max:255',
            'fakultas' => 'nullable|string|max:255',
            'angkatan' => 'nullable|integer|min:2000|max:2099',
            'semester' => 'nullable|integer|min:1|max:14',
            'ipk' => 'nullable|numeric|min:0|max:4',
            'status' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        if ($request->hasFile('foto')) {
            if ($mahasiswa->foto) {
                Storage::disk('public')->delete($mahasiswa->foto);
            }
            $data['foto'] = $request->file('foto')->store('foto_mahasiswa', 'public');
        }

        $mahasiswa->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Data mahasiswa berhasil diperbarui',
            'data' => $mahasiswa->fresh(),
        ]);
    }

    public function destroy($nim): JsonResponse
    {
        $mahasiswa = Mahasiswa::where('nim', $nim)->first();

        if (! $mahasiswa) {
            return response()->json([
                'success' => false,
                'message' => 'Mahasiswa tidak ditemukan',
            ], 404);
        }

        \App\Models\PrestasiMahasiswa::where('nim', $nim)->delete();
        \App\Models\PembayaranSpp::where('nim', $nim)->delete();
        \App\Models\PengambilanMatakuliah::where('nim', $nim)->delete();
        \App\Models\PeminjamanBuku::where('nim', $nim)->delete();

        if ($mahasiswa->foto) {
            Storage::disk('public')->delete($mahasiswa->foto);
        }

        $mahasiswa->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data mahasiswa berhasil dihapus',
        ]);
    }

    public function search($nim): JsonResponse
    {
        $mahasiswa = Mahasiswa::where('nim', $nim)->first();

        if (! $mahasiswa) {
            return response()->json([
                'success' => false,
                'message' => 'Mahasiswa tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $mahasiswa,
        ]);
    }
}
