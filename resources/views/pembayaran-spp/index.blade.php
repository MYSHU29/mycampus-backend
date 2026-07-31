@extends('layouts.app')

@section('title', 'Pembayaran SPP')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Pembayaran SPP</h1>
    @if(auth()->user()?->hasPermission('pembayaran.manage'))
        <a href="{{ route('pembayaran-spp.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus fa-sm"></i> Tambah Pembayaran
        </a>
    @endif
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3 bg-gradient-primary">
        <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-money-bill mr-2"></i>Data Pembayaran SPP</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%">
                <thead class="thead-dark">
                    <tr>
                        <th>No</th>
                        <th>NIM</th>    
                        <th>Mahasiswa</th>
                        <th>Kode Bayar</th>
                        <th>Semester</th>
                        <th>Tahun</th>
                        <th>Jumlah</th>
                        <th>Metode</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th>Bukti</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pembayaranSpp as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->nim }}</td>
                            <td>{{ $item->mahasiswa->nama ?? '-' }}</td>
                             <td>{{ $item->kode_bayar }}</td>   
                            <td>{{ $item->semester }}</td>
                            <td>{{ $item->tahun_akademik }}</td>
                            <td>Rp {{ number_format($item->jumlah_bayar, 0, ',', '.') }}</td>
                            <td>{{ strtoupper($item->metode_bayar) }}</td>
                            <td><span class="badge badge-info">{{ ucfirst($item->status_bayar) }}</span></td>
                            <td>{{ $item->tanggal_bayar ? \Carbon\Carbon::parse($item->tanggal_bayar)->format('d/m/Y') : '-' }}</td>
                            <td>
                                @if($item->bukti_bayar)
                                    <a href="{{ asset('storage/' . $item->bukti_bayar) }}" target="_blank">Lihat</a>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if(auth()->user()?->hasPermission('pembayaran.manage'))
                                    <a href="{{ route('pembayaran-spp.edit', $item->id) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('pembayaran-spp.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
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
                        <tr><td colspan="12" class="text-center text-muted">Belum ada data pembayaran SPP.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
