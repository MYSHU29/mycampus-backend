@extends('layouts.app')

@section('title', 'Peminjaman Buku')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Peminjaman Buku</h1>
    @if(auth()->user()?->hasPermission('buku.manage'))
        <a href="{{ route('peminjaman-buku.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus fa-sm"></i> Tambah Peminjaman
        </a>
    @endif
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3 bg-gradient-primary">
        <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-book mr-2"></i>Data Peminjaman Buku</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%">
                <thead class="thead-dark">
                    <tr>
                        <th>No</th>
                        <th>NIM</th>
                        <th>Mahasiswa</th>
                        <th>Kode Buku</th>
                        <th>Judul</th>
                        <th>Pengarang</th>
                        <th>Pinjam</th>
                        <th>Rencana Kembali</th>
                        <th>Aktual Kembali</th>
                        <th>Status</th>
                        <th>Denda</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($peminjamanBuku as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->nim }}</td>
                            <td>{{ $item->mahasiswa->nama ?? '-' }}</td>
                            <td>{{ $item->kode_buku }}</td>
                            <td>{{ $item->judul_buku }}</td>
                            <td>{{ $item->pengarang }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d/m/Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal_kembali_rencana)->format('d/m/Y') }}</td>
                            <td>{{ $item->tanggal_kembali_aktual ? \Carbon\Carbon::parse($item->tanggal_kembali_aktual)->format('d/m/Y') : '-' }}</td>
                            <td><span class="badge badge-info">{{ ucfirst($item->status) }}</span></td>
                            <td>Rp {{ number_format($item->denda, 0, ',', '.') }}</td>
                            <td>
                                @if(auth()->user()?->hasPermission('buku.manage'))
                                    <a href="{{ route('peminjaman-buku.edit', $item->id) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('peminjaman-buku.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                    </form>
                                @else
                                    <span class="text-muted">Lihat saja</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="12" class="text-center text-muted">Belum ada data peminjaman buku.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
