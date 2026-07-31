@extends('layouts.app')

@section('title', 'Prestasi Mahasiswa')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Prestasi Mahasiswa</h1>
    @if(auth()->user()?->hasPermission('prestasi.create'))
        <a href="{{ route('prestasi-mahasiswa.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus fa-sm"></i> Tambah Prestasi
        </a>
    @endif
</div>

<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Menunggu</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $prestasiMahasiswa->where('status_verifikasi', 'menunggu')->count() }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Diterima</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $prestasiMahasiswa->where('status_verifikasi', 'diterima')->count() }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Ditolak</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $prestasiMahasiswa->where('status_verifikasi', 'ditolak')->count() }}</div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3 bg-gradient-primary">
        <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-trophy mr-2"></i>Data Prestasi Mahasiswa</h6>
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
                        <th>Sertifikat</th>
                        <th>Status</th>
                        <th>Verifikator</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($prestasiMahasiswa as $item)
                        @php
                            $badge = [
                                'menunggu' => 'warning',
                                'diterima' => 'success',
                                'ditolak' => 'danger',
                            ][$item->status_verifikasi] ?? 'secondary';
                        @endphp
                        <tr>
                            <td>{{ $item->kode_prestasi }}</td>
                            <td>{{ $item->nim }}</td>
                            <td>{{ $item->mahasiswa->nama ?? '-' }}</td>
                            <td>
                                <strong>{{ $item->nama_lomba }}</strong><br>
                                <small>{{ $item->penyelenggara }} - {{ $item->juara }}</small>
                            </td>
                            <td>{{ $item->jenisPrestasi->nama_jenis ?? '-' }}</td>
                            <td>{{ $item->tingkatPrestasi->nama_tingkat ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                            <td>
                                @if($item->sertifikat)
                                    <a href="{{ asset('storage/' . $item->sertifikat) }}" target="_blank">Lihat</a>
                                @else
                                    -
                                @endif
                            </td>
                            <td><span class="badge badge-{{ $badge }}">{{ ucfirst($item->status_verifikasi) }}</span></td>
                            <td>
                                @if($item->verifikasi)
                                    {{ $item->verifikasi->admin->nama ?? '-' }}<br>
                                    <small>{{ \Carbon\Carbon::parse($item->verifikasi->tanggal_verifikasi)->format('d/m/Y') }}</small>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-nowrap">
                                @if(auth()->user()?->hasAnyPermission(['prestasi.manage', 'prestasi.verify']))
                                    <a href="{{ route('prestasi-mahasiswa.edit', $item->id_prestasi) }}" class="btn btn-warning btn-sm" title="Edit & Verifikasi">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                @endif
                                @if(auth()->user()?->hasPermission('prestasi.manage'))
                                    <form action="{{ route('prestasi-mahasiswa.destroy', $item->id_prestasi) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data prestasi ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Hapus"><i class="fas fa-trash"></i></button>
                                    </form>
                                @endif
                                @if(! auth()->user()?->hasAnyPermission(['prestasi.manage', 'prestasi.verify']))
                                    <span class="text-muted">Lihat saja</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="11" class="text-center text-muted">Belum ada data prestasi mahasiswa.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
