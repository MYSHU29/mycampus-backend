@extends('layouts.app')

@section('title', 'Verifikasi Prestasi')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Verifikasi Prestasi</h1>
    <a href="{{ route('prestasi-mahasiswa.index') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left fa-sm"></i> Kembali
    </a>
</div>

<div class="row">
    <div class="col-lg-5 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 bg-gradient-info">
                <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-info-circle mr-2"></i>Detail Prestasi</h6>
            </div>
            <div class="card-body">
                <p class="mb-1"><strong>Mahasiswa:</strong> {{ $prestasiMahasiswa->mahasiswa->nama ?? '-' }} ({{ $prestasiMahasiswa->nim }})</p>
                <p class="mb-1"><strong>Nama Lomba:</strong> {{ $prestasiMahasiswa->nama_lomba }}</p>
                <p class="mb-1"><strong>Penyelenggara:</strong> {{ $prestasiMahasiswa->penyelenggara }}</p>
                <p class="mb-1"><strong>Jenis:</strong> {{ $prestasiMahasiswa->jenisPrestasi->nama_jenis ?? '-' }}</p>
                <p class="mb-1"><strong>Tingkat:</strong> {{ $prestasiMahasiswa->tingkatPrestasi->nama_tingkat ?? '-' }}</p>
                <p class="mb-1"><strong>Juara:</strong> {{ $prestasiMahasiswa->juara }}</p>
                <p class="mb-1"><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($prestasiMahasiswa->tanggal)->format('d/m/Y') }}</p>
                <p class="mb-0"><strong>Sertifikat:</strong>
                    @if($prestasiMahasiswa->sertifikat)
                        <a href="{{ asset('storage/' . $prestasiMahasiswa->sertifikat) }}" target="_blank">Lihat file</a>
                    @else
                        -
                    @endif
                </p>
            </div>
        </div>
    </div>

    <div class="col-lg-7 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 bg-gradient-success">
                <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-check mr-2"></i>Form Verifikasi Admin</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('prestasi-mahasiswa.simpan-verifikasi', $prestasiMahasiswa->id_prestasi) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Admin/Petugas</label>
                            <select name="id_admin" class="form-control" required>
                                <option value="">-- Pilih Admin --</option>
                                @foreach($admins as $admin)
                                    <option value="{{ $admin->id_admin }}" {{ old('id_admin', $prestasiMahasiswa->verifikasi->id_admin ?? '') == $admin->id_admin ? 'selected' : '' }}>
                                        {{ $admin->nama }} - {{ $admin->role }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Status Verifikasi</label>
                            <select name="status_verifikasi" class="form-control" required>
                                @foreach(['diterima' => 'Diterima', 'ditolak' => 'Ditolak'] as $value => $label)
                                    <option value="{{ $value }}" {{ old('status_verifikasi', $prestasiMahasiswa->status_verifikasi) == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Tanggal Verifikasi</label>
                            <input type="date" name="tanggal_verifikasi" value="{{ old('tanggal_verifikasi', $prestasiMahasiswa->verifikasi->tanggal_verifikasi ?? now()->toDateString()) }}" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Catatan</label>
                            <textarea name="catatan" class="form-control" rows="3">{{ old('catatan', $prestasiMahasiswa->verifikasi->catatan ?? '') }}</textarea>
                        </div>
                    </div>

                    <hr>
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('prestasi-mahasiswa.index') }}" class="btn btn-secondary mr-2"><i class="fas fa-times"></i> Batal</a>
                        <button type="submit" class="btn btn-success"><i class="fas fa-check"></i> Simpan Verifikasi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
