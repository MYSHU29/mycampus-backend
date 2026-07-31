<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\PengambilanMatakuliah;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PengambilanMatakuliahApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = PengambilanMatakuliah::with('mahasiswa');

        if ($request->filled('nim')) {
            $query->where('nim', $request->nim);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('mahasiswa', function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                      ->orWhere('nim', 'like', "%{$search}%");
                })->orWhere('nama_matkul', 'like', "%{$search}%")
                  ->orWhere('kode_matkul', 'like', "%{$search}%");
            });
        }

        $matakuliah = $query->latest()->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $matakuliah,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nim' => 'required|exists:mahasiswa,nim',
            'kode_matkul' => 'required|string|max:255',
            'nama_matkul' => 'required|string|max:255',
            'sks' => 'required|integer|min:1|max:6',
            'dosen' => 'required|string|max:255',
            'semester' => 'required|integer|min:1|max:14',
            'tahun_akademik' => 'required|integer|min:2000|max:2099',
            'status' => 'required|in:aktif,mengulang,lulus',
            'nilai_akhir' => 'nullable|numeric|min:0|max:100',
            'grade' => 'nullable|in:A,B+,B,C+,C,D,E',
        ]);

        $matakuliah = PengambilanMatakuliah::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Data pengambilan matakuliah berhasil ditambahkan',
            'data' => $matakuliah->load('mahasiswa'),
        ], 201);
    }

    public function show(PengambilanMatakuliah $pengambilanMatakuliah): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $pengambilanMatakuliah->load('mahasiswa'),
        ]);
    }

    public function update(Request $request, PengambilanMatakuliah $pengambilanMatakuliah): JsonResponse
    {
        $data = $request->validate([
            'nim' => 'sometimes|exists:mahasiswa,nim',
            'kode_matkul' => 'sometimes|string|max:255',
            'nama_matkul' => 'sometimes|string|max:255',
            'sks' => 'sometimes|integer|min:1|max:6',
            'dosen' => 'sometimes|string|max:255',
            'semester' => 'sometimes|integer|min:1|max:14',
            'tahun_akademik' => 'sometimes|integer|min:2000|max:2099',
            'status' => 'sometimes|in:aktif,mengulang,lulus',
            'nilai_akhir' => 'nullable|numeric|min:0|max:100',
            'grade' => 'nullable|in:A,B+,B,C+,C,D,E',
        ]);

        $pengambilanMatakuliah->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Data pengambilan matakuliah berhasil diperbarui',
            'data' => $pengambilanMatakuliah->fresh()->load('mahasiswa'),
        ]);
    }

    public function formData(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'mahasiswa' => Mahasiswa::orderBy('nama')->get(['nim', 'nama']),
            ],
        ]);
    }

    public function destroy(PengambilanMatakuliah $pengambilanMatakuliah): JsonResponse
    {
        $pengambilanMatakuliah->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data pengambilan matakuliah berhasil dihapus',
        ]);
    }
}
