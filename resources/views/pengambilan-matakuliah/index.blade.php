@extends('layouts.app')

@section('title', 'Pengambilan Matakuliah')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Pengambilan Matakuliah</h1>
    @if(auth()->user()?->hasPermission('matakuliah.manage'))
        <a href="{{ route('pengambilan-matakuliah.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus fa-sm"></i> Tambah Matakuliah
        </a>
    @endif
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3 bg-gradient-primary">
        <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-book-open mr-2"></i>Data Pengambilan Matakuliah</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%">
                <thead class="thead-dark">
                    <tr>
                        <th>No</th>
                        <th>NIM</th>
                        <th>Mahasiswa</th>
                        <th>Kode</th>
                        <th>Matakuliah</th>
                        <th>SKS</th>
                        <th>Dosen</th>
                        <th>Semester</th>
                        <th>Tahun</th>
                        <th>Status</th>
                        <th>Nilai</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pengambilanMatakuliah as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->nim }}</td>
                            <td>{{ $item->mahasiswa->nama ?? '-' }}</td>
                            <td>{{ $item->kode_matkul }}</td>
                            <td>{{ $item->nama_matkul }}</td>
                            <td>{{ $item->sks }}</td>
                            <td>{{ $item->dosen }}</td>
                            <td>{{ $item->semester }}</td>
                            <td>{{ $item->tahun_akademik }}</td>
                            <td><span class="badge badge-info">{{ ucfirst($item->status) }}</span></td>
                            <td>{{ $item->nilai_akhir ?? '-' }} {{ $item->grade ? '(' . $item->grade . ')' : '' }}</td>
                            <td>
                                @if(auth()->user()?->hasPermission('matakuliah.manage'))
                                    <a href="{{ route('pengambilan-matakuliah.edit', $item->id) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('pengambilan-matakuliah.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
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
                        <tr><td colspan="12" class="text-center text-muted">Belum ada data pengambilan matakuliah.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
