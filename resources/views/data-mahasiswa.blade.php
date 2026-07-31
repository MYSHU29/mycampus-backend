<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Mahasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/startbootstrap-sb-admin-2@4.1.4/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/mycampus-theme.css') }}" rel="stylesheet">
</head>
<body id="page-top">
<div id="wrapper">

    {{-- Sidebar --}}
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="#">
            <div class="sidebar-brand-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" fill="none" width="28" height="28"><defs><linearGradient id="sbG" x1="0" y1="0" x2="64" y2="64" gradientUnits="userSpaceOnUse"><stop offset="0%" stop-color="#00f0ff"/><stop offset="100%" stop-color="#a855f7"/></linearGradient></defs><polygon points="32,8 56,22 32,30 8,22" fill="url(#sbG)" opacity="0.9"/><line x1="48" y1="16" x2="54" y2="28" stroke="#00f0ff" stroke-width="1.5" stroke-linecap="round" opacity="0.7"/><circle cx="54" cy="29" r="2" fill="#00f0ff" opacity="0.8"/><polygon points="18,24 32,32 46,24 46,28 32,36 18,28" fill="url(#sbG)" opacity="0.7"/><rect x="20" y="36" width="24" height="16" rx="2" fill="none" stroke="url(#sbG)" stroke-width="1.5" opacity="0.6"/><line x1="32" y1="36" x2="32" y2="52" stroke="url(#sbG)" stroke-width="1" opacity="0.4"/><circle cx="8" cy="22" r="1.5" fill="#00f0ff" opacity="0.6"/><circle cx="56" cy="22" r="1.5" fill="#a855f7" opacity="0.6"/></svg>
            </div>
            <div class="sidebar-brand-text mx-3">MyCampus</div>
        </a>
        <hr class="sidebar-divider my-0">

        <li class="nav-item">
            <a class="nav-link" href="{{ route('dashboard') }}">
                <i class="fas fa-fw fa-home"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <li class="nav-item active">
            <a class="nav-link" href="{{ route('data-mahasiswa') }}">
                <i class="fas fa-fw fa-table"></i>
                <span>Data Mahasiswa</span>
            </a>
        </li>

        @if(auth()->user()?->hasPermission('mahasiswa.manage'))
            <li class="nav-item">
                <a class="nav-link" href="{{ route('create-mahasiswa') }}">
                    <i class="fas fa-fw fa-user-plus"></i>
                    <span>Tambah Mahasiswa</span>
                </a>
            </li>
        @endif

        <hr class="sidebar-divider">

        <div class="sidebar-heading text-white px-3 mb-1" style="font-size:11px;">
            AKADEMIK
        </div>

        <li class="nav-item">
            <a class="nav-link" href="{{ route('pembayaran-spp.index') }}">
                <i class="fas fa-fw fa-money-bill"></i>
                <span>Pembayaran SPP</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="{{ route('peminjaman-buku.index') }}">
                <i class="fas fa-fw fa-book"></i>
                <span>Peminjaman Buku</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="{{ route('pengambilan-matakuliah.index') }}">
                <i class="fas fa-fw fa-book-open"></i>
                <span>Pengambilan Matakuliah</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="{{ route('prestasi-mahasiswa.index') }}">
                <i class="fas fa-fw fa-trophy"></i>
                <span>Prestasi Mahasiswa</span>
            </a>
        </li>

        @if(auth()->user()?->hasPermission('prestasi.report'))
            <li class="nav-item">
                <a class="nav-link" href="{{ route('laporan-prestasi') }}">
                    <i class="fas fa-fw fa-file-alt"></i>
                    <span>Laporan Prestasi</span>
                </a>
            </li>
        @endif

        @if(auth()->user()?->hasPermission('role.manage'))
            <hr class="sidebar-divider">

            <div class="sidebar-heading text-white px-3 mb-1" style="font-size:11px;">
                MANAJEMEN AKSES
            </div>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('data-role') }}">
                    <i class="fas fa-fw fa-user-shield"></i>
                    <span>Hak Akses</span>
                </a>
            </li>
        @endif
    </ul>
    {{-- End Sidebar --}}

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">

            {{-- Topbar --}}
            <nav class="navbar navbar-expand navbar-dark topbar topbar-dark mb-4 static-top">
                <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                    <i class="fa fa-bars"></i>
                </button>
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item d-flex align-items-center">
                        <span class="nav-link"><i class="fas fa-user fa-fw"></i> {{ auth()->user()->name ?? 'Admin' }} ({{ auth()->user()->role->nama_role ?? '-' }})</span>
                        <form action="{{ route('logout') }}" method="POST" class="mb-0">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </nav>

            {{-- Main Content --}}
            <div class="container-fluid">

                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 mb-0 text-gray-800">Data Mahasiswa</h1>
                    <div>
                        <a href="{{ route('pembayaran-spp.index') }}" class="btn btn-success btn-sm mr-2 mb-2 mb-sm-0">
                            <i class="fas fa-money-bill fa-sm"></i> Pembayaran SPP
                        </a>
                        <a href="{{ route('peminjaman-buku.index') }}" class="btn btn-info btn-sm mr-2 mb-2 mb-sm-0">
                            <i class="fas fa-book fa-sm"></i> Peminjaman Buku
                        </a>
                        <a href="{{ route('pengambilan-matakuliah.index') }}" class="btn btn-secondary btn-sm mr-2 mb-2 mb-sm-0">
                            <i class="fas fa-book-open fa-sm"></i> Matakuliah
                        </a>
                        <a href="{{ route('prestasi-mahasiswa.index') }}" class="btn btn-dark btn-sm mr-2 mb-2 mb-sm-0">
                            <i class="fas fa-trophy fa-sm"></i> Prestasi Mahasiswa
                        </a>
                        @if(auth()->user()?->hasPermission('prestasi.report'))
                            <a href="{{ route('laporan-prestasi') }}" class="btn btn-info btn-sm mr-2 mb-2 mb-sm-0">
                                <i class="fas fa-file-alt fa-sm"></i> Laporan Prestasi
                            </a>
                        @endif
                        @if(auth()->user()?->hasPermission('role.manage'))
                            <a href="{{ route('data-role') }}" class="btn btn-warning btn-sm mr-2">
                                <i class="fas fa-user-shield fa-sm"></i> Hak Akses
                            </a>
                        @endif
                        @if(auth()->user()?->hasPermission('mahasiswa.manage'))
                            <a href="{{ route('create-mahasiswa') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-user-plus fa-sm"></i> Tambah Data
                            </a>
                        @endif
                    </div>
                </div>

                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-gradient-primary">
                        <h6 class="m-0 font-weight-bold text-white">
                            <i class="fas fa-table mr-2"></i> Tabel Data Mahasiswa
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" width="100%">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>NIM</th>
                                        <th>Nama</th>
                                        <th>Email</th>
                                        <th>No. Telp</th>
                                        <th>Tgl Lahir</th>
                                        <th>Jenis Kelamin</th>
                                        <th>Kota Asal</th>
                                        <th>Alamat</th>
                                        <th>Prodi</th>
                                        <th>Fakultas</th>
                                        <th>Angkatan</th>
                                        <th>Semester</th>
                                        <th>IPK</th>
                                        <th>Status</th>
                                        <th>Foto</th>
                                        <th>Catatan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($mahasiswa as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->nim }}</td>
                                        <td>{{ $item->nama }}</td>
                                        <td>{{ $item->email }}</td>
                                        <td>{{ $item->no_telp }}</td>
                                        <td>{{ \Carbon\Carbon::parse($item->tanggal_lahir)->format('d/m/Y') }}</td>
                                        <td>{{ $item->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                                        <td>{{ $item->kota_asal }}</td>
                                        <td>{{ $item->alamat }}</td>
                                        <td>{{ $item->prodi }}</td>
                                        <td>{{ $item->fakultas }}</td>
                                        <td>{{ $item->angkatan }}</td>
                                        <td>{{ $item->semester }}</td>
                                        <td>{{ number_format($item->ipk, 2) }}</td>
                                        <td>
                                            @if($item->status == 'aktif')
                                                <span class="badge badge-success">Aktif</span>
                                            @elseif($item->status == 'cuti')
                                                <span class="badge badge-warning">Cuti</span>
                                            @elseif($item->status == 'lulus')
                                                <span class="badge badge-primary">Lulus</span>
                                            @else
                                                <span class="badge badge-danger">Drop Out</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($item->foto)
                                                <img src="{{ asset('storage/' . $item->foto) }}" width="45" height="45"
                                                     style="object-fit: cover; border-radius: 50%;">
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>{{ $item->catatan ?? '-' }}</td>
                                        <td>
                                            @if(auth()->user()?->hasPermission('mahasiswa.manage'))
                                                <a href="{{ route('edit-mahasiswa', $item->nim) }}" class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('hapus-mahasiswa', $item->nim) }}" method="POST"
                                                      style="display:inline;"
                                                      onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-muted">Lihat saja</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/startbootstrap-sb-admin-2@4.1.4/js/sb-admin-2.min.js"></script>
</body>
</html>
