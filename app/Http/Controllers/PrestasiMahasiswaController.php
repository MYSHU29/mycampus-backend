<?php

namespace App\Http\Controllers;

use App\Models\AdminPrestasi;
use App\Models\JenisPrestasi;
use App\Models\Mahasiswa;
use App\Models\PrestasiMahasiswa;
use App\Models\TingkatPrestasi;
use App\Models\VerifikasiPrestasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PrestasiMahasiswaController extends Controller
{
    public function index()
    {
        $prestasiMahasiswa = PrestasiMahasiswa::with([
            'mahasiswa',
            'jenisPrestasi',
            'tingkatPrestasi',
            'verifikasi.admin',
        ])->latest()->get();

        return view('prestasi-mahasiswa.index', compact('prestasiMahasiswa'));
    }

    public function laporan()
    {
        $prestasiMahasiswa = PrestasiMahasiswa::with([
            'mahasiswa',
            'jenisPrestasi',
            'tingkatPrestasi',
            'verifikasi.admin',
        ])->latest()->get();

        $rekapStatus = [
            'menunggu' => $prestasiMahasiswa->where('status_verifikasi', 'menunggu')->count(),
            'diterima' => $prestasiMahasiswa->where('status_verifikasi', 'diterima')->count(),
            'ditolak' => $prestasiMahasiswa->where('status_verifikasi', 'ditolak')->count(),
        ];

        return view('prestasi-mahasiswa.laporan', compact('prestasiMahasiswa', 'rekapStatus'));
    }

    public function create()
    {
        return view('prestasi-mahasiswa.create', $this->formData());
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        $data['status_verifikasi'] = auth()->user()?->hasPermission('prestasi.verify')
            ? $data['status_verifikasi']
            : 'menunggu';

        if ($request->hasFile('sertifikat')) {
            $data['sertifikat'] = $request->file('sertifikat')->store('sertifikat_prestasi', 'public');
        }

        DB::transaction(function () use ($data) {
            $data['kode_prestasi'] = $this->nextKodePrestasi();

            PrestasiMahasiswa::create($data);
        });

        return redirect()->route('prestasi-mahasiswa.index')
            ->with('success', 'Data prestasi mahasiswa berhasil ditambahkan.');
    }

    public function edit(PrestasiMahasiswa $prestasiMahasiswa)
    {
        $prestasiMahasiswa->load('verifikasi');

        return view('prestasi-mahasiswa.edit', array_merge(
            $this->formData(),
            compact('prestasiMahasiswa')
        ));
    }

    public function update(Request $request, PrestasiMahasiswa $prestasiMahasiswa)
    {
        if (auth()->user()?->hasPermission('prestasi.manage')) {
            $data = $this->validatedData($request);

            if ($request->hasFile('sertifikat')) {
                if ($prestasiMahasiswa->sertifikat) {
                    Storage::disk('public')->delete($prestasiMahasiswa->sertifikat);
                }

                $data['sertifikat'] = $request->file('sertifikat')->store('sertifikat_prestasi', 'public');
            }

            $prestasiMahasiswa->update($data);
        }

        if (auth()->user()?->hasPermission('prestasi.verify')) {
            $this->saveVerification($request, $prestasiMahasiswa);
        }

        return redirect()->route('prestasi-mahasiswa.index')
            ->with('success', 'Data prestasi mahasiswa berhasil diperbarui.');
    }

    public function destroy(PrestasiMahasiswa $prestasiMahasiswa)
    {
        if ($prestasiMahasiswa->sertifikat) {
            Storage::disk('public')->delete($prestasiMahasiswa->sertifikat);
        }

        $prestasiMahasiswa->delete();

        return redirect()->route('prestasi-mahasiswa.index')
            ->with('success', 'Data prestasi mahasiswa berhasil dihapus.');
    }

    public function verifikasi(PrestasiMahasiswa $prestasiMahasiswa)
    {
        $prestasiMahasiswa->load(['mahasiswa', 'jenisPrestasi', 'tingkatPrestasi', 'verifikasi']);
        $admins = AdminPrestasi::orderBy('nama')->get();

        return view('prestasi-mahasiswa.verifikasi', compact('prestasiMahasiswa', 'admins'));
    }

    public function simpanVerifikasi(Request $request, PrestasiMahasiswa $prestasiMahasiswa)
    {
        $data = $request->validate([
            'id_admin' => ['required', 'exists:admin_prestasi,id_admin'],
            'status_verifikasi' => ['required', Rule::in(['diterima', 'ditolak'])],
            'tanggal_verifikasi' => ['required', 'date'],
            'catatan' => ['nullable', 'string'],
        ]);

        $prestasiMahasiswa->update([
            'status_verifikasi' => $data['status_verifikasi'],
        ]);

        VerifikasiPrestasi::updateOrCreate(
            ['id_prestasi' => $prestasiMahasiswa->id_prestasi],
            [
                'id_admin' => $data['id_admin'],
                'tanggal_verifikasi' => $data['tanggal_verifikasi'],
                'catatan' => $data['catatan'] ?? null,
            ]
        );

        return redirect()->route('prestasi-mahasiswa.index')
            ->with('success', 'Verifikasi prestasi berhasil disimpan.');
    }

    private function formData(): array
    {
        return [
            'mahasiswa' => Mahasiswa::orderBy('nama')->get(),
            'jenisPrestasi' => JenisPrestasi::orderBy('nama_jenis')->get(),
            'tingkatPrestasi' => TingkatPrestasi::orderBy('nama_tingkat')->get(),
            'admins' => AdminPrestasi::orderBy('nama')->get(),
        ];
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

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'nim' => ['required', 'exists:mahasiswa,nim'],
            'id_jenis' => ['required', 'exists:jenis_prestasi,id_jenis'],
            'id_tingkat' => ['required', 'exists:tingkat_prestasi,id_tingkat'],
            'nama_lomba' => ['required', 'string', 'max:150'],
            'penyelenggara' => ['required', 'string', 'max:150'],
            'tanggal' => ['required', 'date'],
            'juara' => ['required', 'string', 'max:100'],
            'sertifikat' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'status_verifikasi' => ['required', Rule::in(['menunggu', 'diterima', 'ditolak'])],
        ]);
    }

    private function saveVerification(Request $request, PrestasiMahasiswa $prestasiMahasiswa): void
    {
        $data = $request->validate([
            'id_admin' => ['required', 'exists:admin_prestasi,id_admin'],
            'status_verifikasi' => ['required', Rule::in(['diterima', 'ditolak'])],
            'tanggal_verifikasi' => ['required', 'date'],
            'catatan' => ['nullable', 'string'],
        ]);

        $prestasiMahasiswa->update([
            'status_verifikasi' => $data['status_verifikasi'],
        ]);

        VerifikasiPrestasi::updateOrCreate(
            ['id_prestasi' => $prestasiMahasiswa->id_prestasi],
            [
                'id_admin' => $data['id_admin'],
                'tanggal_verifikasi' => $data['tanggal_verifikasi'],
                'catatan' => $data['catatan'] ?? null,
            ]
        );
    }
}
