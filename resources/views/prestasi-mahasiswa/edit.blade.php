@extends('layouts.app')

@section('title', 'Edit Prestasi Mahasiswa')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Edit Prestasi Mahasiswa</h1>
    <a href="{{ route('prestasi-mahasiswa.index') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left fa-sm"></i> Kembali
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3 bg-gradient-warning">
        <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-edit mr-2"></i>Form Edit Prestasi Mahasiswa</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('prestasi-mahasiswa.update', $prestasiMahasiswa->id_prestasi) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('prestasi-mahasiswa.form', ['item' => $prestasiMahasiswa])

            @if(auth()->user()?->hasPermission('prestasi.verify'))
                <div class="card border-left-success shadow-sm mb-4">
                    <div class="card-header py-3 bg-gradient-success">
                        <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-check mr-2"></i>Verifikasi Prestasi</h6>
                    </div>
                    <div class="card-body">
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
                    </div>
                </div>
            @endif

            <hr>
            <div class="d-flex justify-content-end">
                <a href="{{ route('prestasi-mahasiswa.index') }}" class="btn btn-secondary mr-2"><i class="fas fa-times"></i> Batal</a>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
