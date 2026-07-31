<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\PembayaranSpp;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PembayaranSppApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = PembayaranSpp::with('mahasiswa');

        if ($request->filled('nim')) {
            $query->where('nim', $request->nim);
        }

        if ($request->filled('status_bayar')) {
            $query->where('status_bayar', $request->status_bayar);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('mahasiswa', function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                      ->orWhere('nim', 'like', "%{$search}%");
                })->orWhere('kode_bayar', 'like', "%{$search}%");
            });
        }

        $pembayaran = $query->latest()->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $pembayaran,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nim' => 'required|exists:mahasiswa,nim',
            'kode_bayar' => 'required|string|max:255|unique:pembayaran_spp,kode_bayar',
            'semester' => 'required|integer|min:1|max:14',
            'tahun_akademik' => 'required|integer|min:2000|max:2099',
            'jumlah_bayar' => 'required|numeric|min:0',
            'metode_bayar' => 'required|in:tunai,transfer,va',
            'status_bayar' => 'required|in:lunas,belum,cicil',
            'tanggal_bayar' => 'nullable|date',
            'bukti_bayar' => 'nullable|image|max:2048',
            'keterangan' => 'nullable|string',
        ]);

        if ($request->hasFile('bukti_bayar')) {
            $data['bukti_bayar'] = $request->file('bukti_bayar')->store('bukti_bayar', 'public');
        }

        $pembayaran = PembayaranSpp::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Data pembayaran SPP berhasil ditambahkan',
            'data' => $pembayaran->load('mahasiswa'),
        ], 201);
    }

    public function show(PembayaranSpp $pembayaranSpp): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $pembayaranSpp->load('mahasiswa'),
        ]);
    }

    public function update(Request $request, PembayaranSpp $pembayaranSpp): JsonResponse
    {
        $data = $request->validate([
            'nim' => 'sometimes|exists:mahasiswa,nim',
            'kode_bayar' => 'sometimes|string|max:255|unique:pembayaran_spp,kode_bayar,' . $pembayaranSpp->id,
            'semester' => 'sometimes|integer|min:1|max:14',
            'tahun_akademik' => 'sometimes|integer|min:2000|max:2099',
            'jumlah_bayar' => 'sometimes|numeric|min:0',
            'metode_bayar' => 'sometimes|in:tunai,transfer,va',
            'status_bayar' => 'sometimes|in:lunas,belum,cicil',
            'tanggal_bayar' => 'nullable|date',
            'bukti_bayar' => 'nullable|image|max:2048',
            'keterangan' => 'nullable|string',
        ]);

        if ($request->hasFile('bukti_bayar')) {
            if ($pembayaranSpp->bukti_bayar) {
                Storage::disk('public')->delete($pembayaranSpp->bukti_bayar);
            }
            $data['bukti_bayar'] = $request->file('bukti_bayar')->store('bukti_bayar', 'public');
        }

        $pembayaranSpp->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Data pembayaran SPP berhasil diperbarui',
            'data' => $pembayaranSpp->fresh()->load('mahasiswa'),
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

    public function destroy(PembayaranSpp $pembayaranSpp): JsonResponse
    {
        if ($pembayaranSpp->bukti_bayar) {
            Storage::disk('public')->delete($pembayaranSpp->bukti_bayar);
        }

        $pembayaranSpp->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data pembayaran SPP berhasil dihapus',
        ]);
    }
}
