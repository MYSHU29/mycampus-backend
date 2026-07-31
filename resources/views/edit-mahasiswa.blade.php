<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Mahasiswa</title>
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

    {{-- Menu Data Mahasiswa --}}
    <li class="nav-item {{ request()->is('data-mahasiswa') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('data-mahasiswa') }}">
            <i class="fas fa-fw fa-table"></i>
            <span>Data Mahasiswa</span>
        </a>
    </li>

    {{-- Menu Tambah Mahasiswa --}}
    <li class="nav-item {{ request()->is('create-mahasiswa') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('create-mahasiswa') }}">
            <i class="fas fa-fw fa-user-plus"></i>
            <span>Tambah Mahasiswa</span>
        </a>
    </li>

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

    <hr class="sidebar-divider">

    {{-- Menu Hak Akses --}}
    <div class="sidebar-heading text-white px-3 mb-1" style="font-size:11px;">
        MANAJEMEN AKSES
    </div>

    <li class="nav-item {{ request()->is('data-role') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('data-role') }}">
            <i class="fas fa-fw fa-user-shield"></i>
            <span>Hak Akses</span>
        </a>
    </li>

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
                        <span class="nav-link"><i class="fas fa-user fa-fw"></i> {{ auth()->user()->name ?? 'Admin' }}</span>
                        <form action="{{ route('logout') }}" method="POST" class="mb-0">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </nav>
            {{-- End Topbar --}}

            {{-- Main Content --}}
            <div class="container-fluid">

                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 mb-0 text-gray-800">Edit Mahasiswa</h1>
                    <div>
                        <a href="{{ route('prestasi-mahasiswa.index') }}" class="btn btn-dark btn-sm mr-2 mb-2 mb-sm-0">
                            <i class="fas fa-trophy fa-sm"></i> Prestasi Mahasiswa
                        </a>
                        <a href="{{ route('data-mahasiswa') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left fa-sm"></i> Kembali
                        </a>
                    </div>
                </div>

                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-gradient-warning">
                        <h6 class="m-0 font-weight-bold text-white">
                            <i class="fas fa-edit mr-2"></i> Form Edit Mahasiswa
                        </h6>
                    </div>
                    <div class="card-body">

                        <form action="/update-mahasiswa/{{ $mahasiswa->nim }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            {{-- Row 1 --}}
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label><i class="fas fa-id-card mr-1"></i> NIM</label>
                                    <input type="text" name="nim" value="{{ $mahasiswa->nim }}" class="form-control" placeholder="NIM Mahasiswa">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label><i class="fas fa-user mr-1"></i> Nama Lengkap</label>
                                    <input type="text" name="nama" value="{{ $mahasiswa->nama }}" class="form-control" placeholder="Nama Mahasiswa">
                                </div>
                            </div>

                            {{-- Row 2 --}}
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label><i class="fas fa-envelope mr-1"></i> Email</label>
                                    <input type="email" name="email" value="{{ $mahasiswa->email }}" class="form-control" placeholder="Email Mahasiswa">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label><i class="fas fa-phone mr-1"></i> No. Telepon</label>
                                    <input type="text" name="no_telp" value="{{ $mahasiswa->no_telp }}" class="form-control" placeholder="08xxxxxxxxxx">
                                </div>
                            </div>

                            {{-- Row 3 --}}
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label><i class="fas fa-calendar mr-1"></i> Tanggal Lahir</label>
                                    <input type="date" name="tanggal_lahir" value="{{ $mahasiswa->tanggal_lahir }}" class="form-control">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label><i class="fas fa-venus-mars mr-1"></i> Jenis Kelamin</label>
                                    <select name="jenis_kelamin" class="form-control">
                                        <option value="">-- Pilih --</option>
                                        <option value="L" {{ $mahasiswa->jenis_kelamin == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                        <option value="P" {{ $mahasiswa->jenis_kelamin == 'P' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                </div>
                            </div>

                            {{-- Row 4 --}}
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label><i class="fas fa-map-marker-alt mr-1"></i> Kota Asal</label>
                                    <input type="text" name="kota_asal" value="{{ $mahasiswa->kota_asal }}" class="form-control" placeholder="Kota Asal">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label><i class="fas fa-home mr-1"></i> Alamat Lengkap</label>
                                    <textarea name="alamat" class="form-control" rows="2" placeholder="Alamat Lengkap">{{ $mahasiswa->alamat }}</textarea>
                                </div>
                            </div>

                            {{-- Row 5 --}}
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label><i class="fas fa-book mr-1"></i> Program Studi</label>
                                    <select name="prodi" class="form-control">
                                        <option value="">-- Pilih Prodi --</option>
                                        <option value="TI" {{ $mahasiswa->prodi == 'TI' ? 'selected' : '' }}>Teknik Informatika</option>
                                        <option value="SI" {{ $mahasiswa->prodi == 'SI' ? 'selected' : '' }}>Sistem Informasi</option>
                                        <option value="MI" {{ $mahasiswa->prodi == 'MI' ? 'selected' : '' }}>Manajemen Informatika</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label><i class="fas fa-university mr-1"></i> Fakultas</label>
                                    <input type="text" name="fakultas" value="{{ $mahasiswa->fakultas }}" class="form-control" placeholder="Nama Fakultas">
                                </div>
                            </div>

                            {{-- Row 6 --}}
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label><i class="fas fa-calendar-alt mr-1"></i> Angkatan</label>
                                    <input type="number" name="angkatan" value="{{ $mahasiswa->angkatan }}" class="form-control" placeholder="2024" min="2000" max="2099">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label><i class="fas fa-layer-group mr-1"></i> Semester</label>
                                    <input type="number" name="semester" value="{{ $mahasiswa->semester }}" class="form-control" placeholder="1" min="1" max="14">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label><i class="fas fa-star mr-1"></i> IPK</label>
                                    <input type="number" name="ipk" value="{{ $mahasiswa->ipk }}" class="form-control" placeholder="0.00" step="0.01" min="0" max="4">
                                </div>
                            </div>

                            {{-- Row 7 --}}
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label><i class="fas fa-toggle-on mr-1"></i> Status</label>
                                    <select name="status" class="form-control">
                                        <option value="aktif"  {{ $mahasiswa->status == 'aktif'  ? 'selected' : '' }}>Aktif</option>
                                        <option value="cuti"   {{ $mahasiswa->status == 'cuti'   ? 'selected' : '' }}>Cuti</option>
                                        <option value="lulus"  {{ $mahasiswa->status == 'lulus'  ? 'selected' : '' }}>Lulus</option>
                                        <option value="do"     {{ $mahasiswa->status == 'do'     ? 'selected' : '' }}>Drop Out</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label><i class="fas fa-camera mr-1"></i> Foto Profil</label><br>
                                    @if($mahasiswa->foto)
                                        <img src="{{ asset('storage/' . $mahasiswa->foto) }}" width="60" height="60"
                                             style="object-fit:cover; border-radius:50%; margin-bottom:8px;"><br>
                                    @endif
                                    <input type="file" name="foto" class="form-control-file mt-1" accept="image/*">
                                    <small class="text-muted">Kosongkan jika tidak ingin mengubah foto</small>
                                </div>
                            </div>

                            {{-- Row 8 --}}
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label><i class="fas fa-sticky-note mr-1"></i> Catatan</label>
                                    <textarea name="catatan" class="form-control" rows="3" placeholder="Catatan tambahan">{{ $mahasiswa->catatan }}</textarea>
                                </div>
                            </div>

                            <hr>
                            <div class="d-flex justify-content-end">
                                <a href="{{ route('data-mahasiswa') }}" class="btn btn-secondary mr-2">
                                    <i class="fas fa-times"></i> Batal
                                </a>
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-save"></i> Update Data
                                </button>
                            </div>

                        </form>

                    </div>
                </div>

            </div>
            {{-- End Main Content --}}

        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/startbootstrap-sb-admin-2@4.1.4/js/sb-admin-2.min.js"></script>
</body>
</html>
