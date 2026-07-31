<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Role</title>
    <link href="https://cdn.jsdelivr.net/npm/startbootstrap-sb-admin-2@4.1.4/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/mycampus-theme.css') }}" rel="stylesheet">
</head>
<body id="page-top">
<div id="wrapper">

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
        <li class="nav-item">
            <a class="nav-link" href="{{ route('data-mahasiswa') }}">
                <i class="fas fa-fw fa-table"></i>
                <span>Data Mahasiswa</span>
            </a>
        </li>
        <li class="nav-item active">
            <a class="nav-link" href="{{ route('data-role') }}">
                <i class="fas fa-fw fa-user-shield"></i>
                <span>Hak Akses</span>
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
    </ul>

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">

            <nav class="navbar navbar-expand navbar-dark topbar topbar-dark mb-4 static-top">
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

            <div class="container-fluid">
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 mb-0 text-gray-800">Tambah Role</h1>
                    <div>
                        <a href="{{ route('prestasi-mahasiswa.index') }}" class="btn btn-dark btn-sm mr-2 mb-2 mb-sm-0">
                            <i class="fas fa-trophy fa-sm"></i> Prestasi Mahasiswa
                        </a>
                        <a href="{{ route('data-role') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>

                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-gradient-primary">
                        <h6 class="m-0 font-weight-bold text-white">
                            <i class="fas fa-user-shield mr-2"></i> Form Tambah Role
                        </h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('simpan-role') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label><i class="fas fa-user-tag mr-1"></i> Nama Role</label>
                                <select name="nama_role" class="form-control">
                                    <option value="">-- Pilih Role --</option>
                                    <option value="admin">Admin</option>
                                    <option value="operator">Operator</option>
                                    <option value="dosen">Dosen</option>
                                    <option value="mahasiswa">Mahasiswa</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label><i class="fas fa-info-circle mr-1"></i> Deskripsi</label>
                                <textarea name="deskripsi" class="form-control" rows="3"
                                    placeholder="Jelaskan tanggung jawab role ini"></textarea>
                            </div>
                            <div class="mb-3">
                                <label><i class="fas fa-list-check mr-1"></i> Hak Akses</label>
                                <div class="row">
                                    @foreach($permissions as $modul => $items)
                                        <div class="col-md-6 mb-3">
                                            <div class="border rounded p-3 h-100">
                                                <h6 class="font-weight-bold text-primary mb-3">{{ $modul ?? 'Lainnya' }}</h6>
                                                @foreach($items as $permission)
                                                    <div class="custom-control custom-checkbox mb-2">
                                                        <input type="checkbox"
                                                               name="permissions[]"
                                                               value="{{ $permission->id }}"
                                                               id="permission-{{ $permission->id }}"
                                                               class="custom-control-input"
                                                               {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                                        <label class="custom-control-label" for="permission-{{ $permission->id }}">
                                                            {{ $permission->aksi }}
                                                            <small class="text-muted d-block">{{ $permission->nama_permission }}</small>
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-end">
                                <a href="{{ route('data-role') }}" class="btn btn-secondary mr-2">
                                    <i class="fas fa-times"></i> Batal
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan
                                </button>
                            </div>
                        </form>
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
