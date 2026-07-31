<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\PeminjamanBuku;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PeminjamanBukuApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = PeminjamanBuku::with('mahasiswa');

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
                })->orWhere('judul_buku', 'like', "%{$search}%")
                  ->orWhere('kode_buku', 'like', "%{$search}%");
            });
        }

        $peminjaman = $query->latest()->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $peminjaman,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nim' => 'required|exists:mahasiswa,nim',
            'kode_buku' => 'required|string|max:255',
            'judul_buku' => 'required|string|max:255',
            'pengarang' => 'required|string|max:255',
            'tanggal_pinjam' => 'required|date',
            'tanggal_kembali_rencana' => 'required|date|after_or_equal:tanggal_pinjam',
            'tanggal_kembali_aktual' => 'nullable|date|after_or_equal:tanggal_pinjam',
            'status' => 'required|in:dipinjam,dikembalikan,terlambat',
            'denda' => 'nullable|integer|min:0',
            'keterangan' => 'nullable|string',
        ]);

        $data['denda'] = $data['denda'] ?? 0;

        $peminjaman = PeminjamanBuku::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Data peminjaman buku berhasil ditambahkan',
            'data' => $peminjaman->load('mahasiswa'),
        ], 201);
    }

    public function show(PeminjamanBuku $peminjamanBuku): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $peminjamanBuku->load('mahasiswa'),
        ]);
    }

    public function update(Request $request, PeminjamanBuku $peminjamanBuku): JsonResponse
    {
        $data = $request->validate([
            'nim' => 'sometimes|exists:mahasiswa,nim',
            'kode_buku' => 'sometimes|string|max:255',
            'judul_buku' => 'sometimes|string|max:255',
            'pengarang' => 'sometimes|string|max:255',
            'tanggal_pinjam' => 'sometimes|date',
            'tanggal_kembali_rencana' => 'sometimes|date|after_or_equal:tanggal_pinjam',
            'tanggal_kembali_aktual' => 'nullable|date|after_or_equal:tanggal_pinjam',
            'status' => 'sometimes|in:dipinjam,dikembalikan,terlambat',
            'denda' => 'nullable|integer|min:0',
            'keterangan' => 'nullable|string',
        ]);

        $peminjamanBuku->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Data peminjaman buku berhasil diperbarui',
            'data' => $peminjamanBuku->fresh()->load('mahasiswa'),
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

    public function destroy(PeminjamanBuku $peminjamanBuku): JsonResponse
    {
        $peminjamanBuku->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data peminjaman buku berhasil dihapus',
        ]);
    }
}
