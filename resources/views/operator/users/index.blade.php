<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen User</title>
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

        <hr class="sidebar-divider">
        <div class="sidebar-heading text-white px-3 mb-1" style="font-size:11px;">OPERATOR</div>

        <li class="nav-item">
            <a class="nav-link" href="{{ route('operator.activity-logs.index') }}">
                <i class="fas fa-fw fa-history"></i>
                <span>Log Aktivitas</span>
            </a>
        </li>

        <li class="nav-item active">
            <a class="nav-link" href="{{ route('operator.users.index') }}">
                <i class="fas fa-fw fa-users"></i>
                <span>Manajemen User</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="{{ route('data-role') }}">
                <i class="fas fa-fw fa-user-shield"></i>
                <span>Manajemen Role</span>
            </a>
        </li>

        <hr class="sidebar-divider d-none d-md-block">
        <div class="text-center d-none d-md-inline">
            <button class="rounded-circle border-0" id="sidebarToggle"></button>
        </div>
    </ul>

    {{-- Content Wrapper --}}
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            {{-- Topbar --}}
            <nav class="navbar navbar-expand navbar-dark topbar topbar-dark mb-4 static-top">
                <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                    <i class="fa fa-bars"></i>
                </button>

                <ul class="navbar-nav ml-auto">
                    <li class="nav-item dropdown no-arrow">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown">
                            <span class="mr-2 d-none d-lg-inline text-gray-600 small">{{ auth()->user()->name }}</span>
                            <img class="img-profile rounded-circle" src="https://undraw.co/api/illustration/user_-_25/5">
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </li>
                </ul>
            </nav>

            {{-- Page Content --}}
            <div class="container-fluid">
                <div class="row mb-4">
                    <div class="col-md-8">
                        <h1 class="h3 mb-2 text-gray-800">Manajemen User</h1>
                        <p class="mb-0">Kelola akun pengguna sistem</p>
                    </div>
                    <div class="col-md-4 text-right">
                        <a href="{{ route('operator.users.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Tambah User
                        </a>
                    </div>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Error!</strong>
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                        <button type="button" class="close" data-dismiss="alert">
                            <span>&times;</span>
                        </button>
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert">
                            <span>&times;</span>
                        </button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert">
                            <span>&times;</span>
                        </button>
                    </div>
                @endif

                {{-- Filter Section --}}
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Filter</h6>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('operator.users.index') }}" class="form-inline">
                            <div class="form-group mr-2 mb-2">
                                <label for="search" class="mr-2">Cari:</label>
                                <input type="text" name="search" id="search" class="form-control form-control-sm" 
                                       placeholder="Nama atau Email" value="{{ request('search') }}">
                            </div>

                            <div class="form-group mr-2 mb-2">
                                <label for="role_id" class="mr-2">Role:</label>
                                <select name="role_id" id="role_id" class="form-control form-control-sm">
                                    <option value="">Semua</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>{{ $role->nama_role }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary btn-sm mb-2">
                                <i class="fas fa-search"></i> Filter
                            </button>
                            <a href="{{ route('operator.users.index') }}" class="btn btn-secondary btn-sm mb-2 ml-2">
                                <i class="fas fa-redo"></i> Reset
                            </a>
                        </form>
                    </div>
                </div>

                {{-- Data Table --}}
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Daftar User</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nama</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Dibuat Pada</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($users as $user)
                                        <tr>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>
                                                <span class="badge badge-info">{{ $user->role?->nama_role ?? 'No Role' }}</span>
                                            </td>
                                            <td>{{ $user->created_at->format('Y-m-d H:i') }}</td>
                                            <td>
                                                <a href="{{ route('operator.users.edit', $user) }}" class="btn btn-sm btn-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="{{ route('operator.users.activity-logs', $user) }}" class="btn btn-sm btn-info" title="Lihat Activity Log">
                                                    <i class="fas fa-history"></i>
                                                </a>
                                                @if ($user->id !== auth()->id())
                                                    <form action="{{ route('operator.users.destroy', $user) }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm('Yakin ingin menghapus?')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">Tidak ada data</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                Total: {{ $users->total() }} data
                            </div>
                            <div>
                                {{ $users->render() }}
                            </div>
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
