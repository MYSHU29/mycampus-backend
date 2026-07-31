@extends('layouts.app')

@section('title', 'Laporan Prestasi')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Laporan Prestasi Mahasiswa</h1>
    <div>
        <a href="{{ route('prestasi-mahasiswa.fuzzy-kualitas') }}" class="btn btn-info btn-sm">
            <i class="fas fa-brain fa-sm"></i> Kualitas Fuzzy
        </a>
        <button type="button" onclick="window.print()" class="btn btn-primary btn-sm">
            <i class="fas fa-print fa-sm"></i> Cetak Laporan
        </button>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Menunggu</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $rekapStatus['menunggu'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Diterima</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $rekapStatus['diterima'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Ditolak</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $rekapStatus['ditolak'] }}</div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3 bg-gradient-primary">
        <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-file-alt mr-2"></i>Rekap Data Prestasi</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>NIM</th>
                        <th>Mahasiswa</th>
                        <th>Prestasi</th>
                        <th>Jenis</th>
                        <th>Tingkat</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Verifikator</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($prestasiMahasiswa as $item)
                        <tr>
                            <td>{{ $item->kode_prestasi }}</td>
                            <td>{{ $item->nim }}</td>
                            <td>{{ $item->mahasiswa->nama ?? '-' }}</td>
                            <td>{{ $item->nama_lomba }}<br><small>{{ $item->penyelenggara }} - {{ $item->juara }}</small></td>
                            <td>{{ $item->jenisPrestasi->nama_jenis ?? '-' }}</td>
                            <td>{{ $item->tingkatPrestasi->nama_tingkat ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                            <td>{{ ucfirst($item->status_verifikasi) }}</td>
                            <td>{{ $item->verifikasi->admin->nama ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-center text-muted">Belum ada data prestasi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
