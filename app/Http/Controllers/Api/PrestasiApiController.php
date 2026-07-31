<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AdminPrestasi;
use App\Models\JenisPrestasi;
use App\Models\Mahasiswa;
use App\Models\PrestasiMahasiswa;
use App\Models\TingkatPrestasi;
use App\Models\VerifikasiPrestasi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\ActivityLog;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PrestasiApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = PrestasiMahasiswa::with([
            'mahasiswa',
            'jenisPrestasi',
            'tingkatPrestasi',
            'verifikasi.admin',
            'fuzzyHasil',
        ]);

        if ($request->filled('status_verifikasi')) {
            $query->where('status_verifikasi', $request->status_verifikasi);
        }

        if ($request->filled('nim')) {
            $query->where('nim', $request->nim);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('mahasiswa', function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                      ->orWhere('nim', 'like', "%{$search}%");
                })->orWhere('nama_lomba', 'like', "%{$search}%");
            });
        }

        $prestasi = $query->latest()->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $prestasi,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nim' => 'required|exists:mahasiswa,nim',
            'id_jenis' => 'required|exists:jenis_prestasi,id_jenis',
            'id_tingkat' => 'required|exists:tingkat_prestasi,id_tingkat',
            'nama_lomba' => 'required|string|max:150',
            'penyelenggara' => 'required|string|max:150',
            'tanggal' => 'required|date',
            'juara' => 'required|string|max:100',
            'sertifikat' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'status_verifikasi' => 'required|in:menunggu,diterima,ditolak',
        ]);

        $data['status_verifikasi'] = auth()->user()?->hasPermission('prestasi.verify')
            ? $data['status_verifikasi']
            : 'menunggu';

        if ($request->hasFile('sertifikat')) {
            $data['sertifikat'] = $request->file('sertifikat')->store('sertifikat_prestasi', 'public');
        }

        $prestasi = DB::transaction(function () use (&$data) {
            $data['kode_prestasi'] = $this->nextKodePrestasi();
            return PrestasiMahasiswa::create($data);
        });

        return response()->json([
            'success' => true,
            'message' => 'Data prestasi berhasil ditambahkan',
            'data' => $prestasi->load(['mahasiswa', 'jenisPrestasi', 'tingkatPrestasi']),
        ], 201);
    }

    public function show(PrestasiMahasiswa $prestasiMahasiswa): JsonResponse
    {
        $prestasiMahasiswa->load(['mahasiswa', 'jenisPrestasi', 'tingkatPrestasi', 'verifikasi.admin', 'fuzzyHasil']);

        return response()->json([
            'success' => true,
            'data' => $prestasiMahasiswa,
        ]);
    }

    public function update(Request $request, PrestasiMahasiswa $prestasiMahasiswa): JsonResponse
    {
        if (auth()->user()?->hasPermission('prestasi.manage')) {
            $data = $request->validate([
                'nim' => 'sometimes|exists:mahasiswa,nim',
                'id_jenis' => 'sometimes|exists:jenis_prestasi,id_jenis',
                'id_tingkat' => 'sometimes|exists:tingkat_prestasi,id_tingkat',
                'nama_lomba' => 'sometimes|string|max:150',
                'penyelenggara' => 'sometimes|string|max:150',
                'tanggal' => 'sometimes|date',
                'juara' => 'sometimes|string|max:100',
                'sertifikat' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            ]);

            if ($request->hasFile('sertifikat')) {
                if ($prestasiMahasiswa->sertifikat) {
                    Storage::disk('public')->delete($prestasiMahasiswa->sertifikat);
                }
                $data['sertifikat'] = $request->file('sertifikat')->store('sertifikat_prestasi', 'public');
            }

            $prestasiMahasiswa->update($data);
        }

        if (auth()->user()?->hasPermission('prestasi.verify')) {
            $verifData = $request->validate([
                'status_verifikasi' => 'required|in:diterima,ditolak',
                'id_admin' => 'required|exists:admin_prestasi,id_admin',
                'tanggal_verifikasi' => 'required|date',
                'catatan' => 'nullable|string',
            ]);

            $prestasiMahasiswa->update([
                'status_verifikasi' => $verifData['status_verifikasi'],
            ]);

            VerifikasiPrestasi::updateOrCreate(
                ['id_prestasi' => $prestasiMahasiswa->id_prestasi],
                [
                    'id_admin' => $verifData['id_admin'],
                    'tanggal_verifikasi' => $verifData['tanggal_verifikasi'],
                    'catatan' => $verifData['catatan'] ?? null,
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Data prestasi berhasil diperbarui',
            'data' => $prestasiMahasiswa->fresh()->load(['mahasiswa', 'jenisPrestasi', 'tingkatPrestasi']),
        ]);
    }

    public function destroy(PrestasiMahasiswa $prestasiMahasiswa): JsonResponse
    {
        if ($prestasiMahasiswa->sertifikat) {
            Storage::disk('public')->delete($prestasiMahasiswa->sertifikat);
        }

        $prestasiMahasiswa->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data prestasi berhasil dihapus',
        ]);
    }

    public function verifikasi(Request $request, PrestasiMahasiswa $prestasiMahasiswa): JsonResponse
    {
        $data = $request->validate([
            'status_verifikasi' => 'required|in:diterima,ditolak',
            'id_admin' => 'nullable|exists:admin_prestasi,id_admin',
            'tanggal_verifikasi' => 'nullable|date',
            'catatan' => 'nullable|string',
        ]);

        $prestasiMahasiswa->update([
            'status_verifikasi' => $data['status_verifikasi'],
        ]);

        $user = auth()->user();
        if (empty($data['id_admin'])) {
            $admin = AdminPrestasi::where('email', $user->email)->first();
            if (! $admin) {
                $admin = AdminPrestasi::create([
                    'nama' => $user->nama ?? $user->name ?? $user->email,
                    'email' => $user->email,
                    'password' => bcrypt(Str::random(16)),
                    'role' => 'admin',
                ]);
                ActivityLog::log('create', 'admin_prestasi', "Membuat admin prestasi otomatis: {$admin->nama}", null, $admin->toArray());
            }
            $data['id_admin'] = $admin->id_admin;
        }

        VerifikasiPrestasi::updateOrCreate(
            ['id_prestasi' => $prestasiMahasiswa->id_prestasi],
            [
                'id_admin' => $data['id_admin'],
                'tanggal_verifikasi' => $data['tanggal_verifikasi'] ?? now()->toDateString(),
                'catatan' => $data['catatan'] ?? null,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Verifikasi prestasi berhasil disimpan',
            'data' => $prestasiMahasiswa->fresh()->load(['verifikasi.admin']),
        ]);
    }

    public function laporan(): JsonResponse
    {
        $prestasiMahasiswa = PrestasiMahasiswa::with([
            'mahasiswa', 'jenisPrestasi', 'tingkatPrestasi', 'verifikasi.admin',
        ])->latest()->get();

        $rekapStatus = [
            'menunggu' => $prestasiMahasiswa->where('status_verifikasi', 'menunggu')->count(),
            'diterima' => $prestasiMahasiswa->where('status_verifikasi', 'diterima')->count(),
            'ditolak' => $prestasiMahasiswa->where('status_verifikasi', 'ditolak')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'prestasi' => $prestasiMahasiswa,
                'rekap' => $rekapStatus,
            ],
        ]);
    }

    public function formData(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'mahasiswa' => Mahasiswa::orderBy('nama')->get(['nim', 'nama']),
                'jenis_prestasi' => JenisPrestasi::orderBy('nama_jenis')->get(),
                'tingkat_prestasi' => TingkatPrestasi::orderBy('nama_tingkat')->get(),
                'admins' => AdminPrestasi::orderBy('nama')->get(),
            ],
        ]);
    }

    private function nextKodePrestasi(): string
    {
        $sequence = DB::table('prestasi_kode_sequences')
            ->where('nama_sequence', 'prestasi')
            ->lockForUpdate()
            ->first();

        if (! $sequence) {
            throw new \RuntimeException('Counter kode prestasi tidak ditemukan.');
        }

        $nextNumber = $sequence->nomor_terakhir + 1;

        DB::table('prestasi_kode_sequences')
            ->where('nama_sequence', 'prestasi')
            ->update([
                'nomor_terakhir' => $nextNumber,
                'updated_at' => now(),
            ]);

        return sprintf('PRESM-%03d', $nextNumber);
    }
}
