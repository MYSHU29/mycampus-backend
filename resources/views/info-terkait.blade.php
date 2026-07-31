@extends('layouts.app')

@section('title', 'Info Terkait')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Info Terkait</h1>
    <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left fa-sm"></i> Dashboard
    </a>
</div>

<div class="row">
    <div class="col-lg-4 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 bg-gradient-primary">
                <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-users-gear mr-2"></i>Hak Akses</h6>
            </div>
            <div class="card-body">
                <p><strong>Admin</strong><br>Kelola master data, user, prestasi, verifikasi, dan laporan.</p>
                <p><strong>Dosen</strong><br>Verifikasi prestasi mahasiswa dan melihat laporan prestasi.</p>
                <p class="mb-0"><strong>Mahasiswa</strong><br>Input data prestasi dan melihat status verifikasi.</p>
            </div>
        </div>
    </div>

    <div class="col-lg-4 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 bg-gradient-success">
                <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-route mr-2"></i>Alur Prestasi</h6>
            </div>
            <div class="card-body">
                <ol class="mb-0 pl-3">
                    <li>Mahasiswa menginput prestasi.</li>
                    <li>Status awal menjadi menunggu.</li>
                    <li>Admin atau dosen melakukan verifikasi.</li>
                    <li>Mahasiswa melihat status diterima atau ditolak.</li>
                    <li>Laporan dapat dicetak oleh admin dan dosen.</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="col-lg-4 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 bg-gradient-info">
                <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-circle-question mr-2"></i>Petunjuk Singkat</h6>
            </div>
            <div class="card-body">
                <p>Pastikan data lomba, penyelenggara, tanggal, juara, dan sertifikat diisi dengan benar.</p>
                <p>File sertifikat dapat berupa PDF, JPG, JPEG, atau PNG dengan ukuran maksimal 2 MB.</p>
                <p class="mb-0">Jika data ditolak, periksa catatan verifikasi sebelum mengajukan kembali.</p>
            </div>
        </div>
    </div>
</div>
@endsection
