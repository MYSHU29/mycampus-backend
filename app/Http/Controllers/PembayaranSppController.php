<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\PembayaranSpp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PembayaranSppController extends Controller
{
    public function index()
    {
        $pembayaranSpp = PembayaranSpp::with('mahasiswa')->latest()->get();
        return view('pembayaran-spp.index', compact('pembayaranSpp'));
    }

    public function create()
    {
        $mahasiswa = Mahasiswa::orderBy('nama')->get();
        return view('pembayaran-spp.create', compact('mahasiswa'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nim' => ['required', 'exists:mahasiswa,nim'],
            'kode_bayar' => ['required', 'string', 'max:255', 'unique:pembayaran_spp,kode_bayar'],
            'semester' => ['required', 'integer', 'min:1', 'max:14'],
            'tahun_akademik' => ['required', 'integer', 'min:2000', 'max:2099'],
            'jumlah_bayar' => ['required', 'numeric', 'min:0'],
            'metode_bayar' => ['required', Rule::in(['tunai', 'transfer', 'va'])],
            'status_bayar' => ['required', Rule::in(['lunas', 'belum', 'cicil'])],
            'tanggal_bayar' => ['nullable', 'date'],
            'bukti_bayar' => ['nullable', 'image', 'max:2048'],
            'keterangan' => ['nullable', 'string'],
        ]);

        if ($request->hasFile('bukti_bayar')) {
            $data['bukti_bayar'] = $request->file('bukti_bayar')->store('bukti_bayar', 'public');
        }

        PembayaranSpp::create($data);

        return redirect()->route('pembayaran-spp.index')->with('success', 'Data pembayaran SPP berhasil ditambahkan.');
    }

    public function edit(PembayaranSpp $pembayaranSpp)
    {
        $mahasiswa = Mahasiswa::orderBy('nama')->get();
        return view('pembayaran-spp.edit', compact('pembayaranSpp', 'mahasiswa'));
    }

    public function update(Request $request, PembayaranSpp $pembayaranSpp)
    {
        $data = $request->validate([
            'nim' => ['required', 'exists:mahasiswa,nim'],
            'kode_bayar' => ['required', 'string', 'max:255', Rule::unique('pembayaran_spp', 'kode_bayar')->ignore($pembayaranSpp->id)],
            'semester' => ['required', 'integer', 'min:1', 'max:14'],
            'tahun_akademik' => ['required', 'integer', 'min:2000', 'max:2099'],
            'jumlah_bayar' => ['required', 'numeric', 'min:0'],
            'metode_bayar' => ['required', Rule::in(['tunai', 'transfer', 'va'])],
            'status_bayar' => ['required', Rule::in(['lunas', 'belum', 'cicil'])],
            'tanggal_bayar' => ['nullable', 'date'],
            'bukti_bayar' => ['nullable', 'image', 'max:2048'],
            'keterangan' => ['nullable', 'string'],
        ]);

        if ($request->hasFile('bukti_bayar')) {
            if ($pembayaranSpp->bukti_bayar) {
                Storage::disk('public')->delete($pembayaranSpp->bukti_bayar);
            }

            $data['bukti_bayar'] = $request->file('bukti_bayar')->store('bukti_bayar', 'public');
        }

        $pembayaranSpp->update($data);

        return redirect()->route('pembayaran-spp.index')->with('success', 'Data pembayaran SPP berhasil diperbarui.');
    }

    public function destroy(PembayaranSpp $pembayaranSpp)
    {
        if ($pembayaranSpp->bukti_bayar) {
            Storage::disk('public')->delete($pembayaranSpp->bukti_bayar);
        }

        $pembayaranSpp->delete();

        return redirect()->route('pembayaran-spp.index')->with('success', 'Data pembayaran SPP berhasil dihapus.');
    }
}
