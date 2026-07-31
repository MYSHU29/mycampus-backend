<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'MyCampus')</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/startbootstrap-sb-admin-2@4.1.4/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/mycampus-theme.css') }}" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <style>
        .topbar .navbar-nav .nav-item .nav-link {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 0.5rem 0.85rem;
            border-radius: 8px;
            transition: all 0.2s cubic-bezier(.4, 0, .2, 1);
            color: var(--mycampus-muted);
            font-weight: 500;
            font-size: 0.88rem;
        }

        .topbar .navbar-nav .nav-item .nav-link:hover {
            background: rgba(0, 240, 255, 0.06);
            color: var(--mycampus-text);
        }

        .user-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: linear-gradient(135deg, rgba(0, 240, 255, 0.08), rgba(168, 85, 247, 0.06));
            border: 1px solid rgba(0, 240, 255, 0.12);
            border-radius: 999px;
            padding: 4px 12px 4px 8px;
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--mycampus-primary);
            transition: all 0.25s cubic-bezier(.4, 0, .2, 1);
        }

        .user-badge:hover {
            background: linear-gradient(135deg, rgba(0, 240, 255, 0.12), rgba(168, 85, 247, 0.10));
            box-shadow: 0 2px 12px rgba(0, 240, 255, 0.12);
        }

        .user-badge .user-avatar {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: linear-gradient(135deg, #00f0ff, #a855f7);
            color: #0a0a1a;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.72rem;
            font-weight: 700;
            box-shadow: 0 2px 8px rgba(0, 240, 255, 0.3);
        }

        .user-badge .user-role {
            color: var(--mycampus-muted);
            font-size: 0.72rem;
            font-weight: 500;
        }

        .logout-btn {
            background: linear-gradient(135deg, #f87171, #dc2626);
            border: none;
            border-radius: 8px;
            padding: 0.35rem 0.85rem;
            font-size: 0.8rem;
            font-weight: 600;
            color: #fff;
            transition: all 0.25s cubic-bezier(.4, 0, .2, 1);
            box-shadow: 0 2px 6px rgba(248, 113, 113, 0.2);
            margin-left: 10px;
        }

        .logout-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(248, 113, 113, 0.3);
            background: linear-gradient(135deg, #fca5a5, #dc2626);
        }

        .logout-btn:active {
            transform: translateY(0) scale(0.97);
        }

        .logout-btn i {
            margin-right: 4px;
        }

        #sidebarToggleTop {
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            transition: all 0.2s ease;
            color: var(--mycampus-muted);
        }

        #sidebarToggleTop:hover {
            background: rgba(0, 240, 255, 0.08);
            color: var(--mycampus-primary);
        }

        .alert-success {
            border-left: 4px solid #34d399;
            border-radius: 8px;
            animation: fadeInUp 0.4s ease both;
        }

        .alert-danger {
            border-left: 4px solid #f87171;
            border-radius: 8px;
            animation: fadeInUp 0.4s ease both;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body id="page-top">
<div id="wrapper">
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ auth()->user()?->hasPermission('mahasiswa.view') ? route('data-mahasiswa') : route('dashboard') }}">
            <div class="sidebar-brand-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" fill="none" width="28" height="28">
                    <defs>
                        <linearGradient id="sideNeonGrad" x1="0" y1="0" x2="64" y2="64" gradientUnits="userSpaceOnUse">
                            <stop offset="0%" stop-color="#00f0ff"/>
                            <stop offset="100%" stop-color="#a855f7"/>
                        </linearGradient>
                    </defs>
                    <polygon points="32,8 56,22 32,30 8,22" fill="url(#sideNeonGrad)" opacity="0.9"/>
                    <line x1="48" y1="16" x2="54" y2="28" stroke="#00f0ff" stroke-width="1.5" stroke-linecap="round" opacity="0.7"/>
                    <circle cx="54" cy="29" r="2" fill="#00f0ff" opacity="0.8"/>
                    <polygon points="18,24 32,32 46,24 46,28 32,36 18,28" fill="url(#sideNeonGrad)" opacity="0.7"/>
                    <rect x="20" y="36" width="24" height="16" rx="2" fill="none" stroke="url(#sideNeonGrad)" stroke-width="1.5" opacity="0.6"/>
                    <line x1="32" y1="36" x2="32" y2="52" stroke="url(#sideNeonGrad)" stroke-width="1" opacity="0.4"/>
                    <circle cx="8" cy="22" r="1.5" fill="#00f0ff" opacity="0.6"/>
                    <circle cx="56" cy="22" r="1.5" fill="#a855f7" opacity="0.6"/>
                </svg>
            </div>
            <div class="sidebar-brand-text mx-3">MyCampus</div>
        </a>
        <hr class="sidebar-divider my-0">

        <li class="nav-item {{ request()->is('dashboard') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('dashboard') }}">
                <i class="fas fa-fw fa-home"></i><span>Dashboard</span>
            </a>
        </li>

        <li class="nav-item {{ request()->is('info-terkait') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('info-terkait') }}">
                <i class="fas fa-fw fa-circle-info"></i><span>Info Terkait</span>
            </a>
        </li>

        @if(auth()->user()?->hasPermission('mahasiswa.view'))
            <li class="nav-item {{ request()->is('data-mahasiswa') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('data-mahasiswa') }}">
                    <i class="fas fa-fw fa-users"></i><span>Data Mahasiswa</span>
                </a>
            </li>
        @endif
        @if(auth()->user()?->hasPermission('mahasiswa.manage'))
            <li class="nav-item {{ request()->is('create-mahasiswa') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('create-mahasiswa') }}">
                    <i class="fas fa-fw fa-user-plus"></i><span>Tambah Mahasiswa</span>
                </a>
            </li>
        @endif

        <hr class="sidebar-divider">
        <div class="sidebar-heading text-white px-3 mb-1" style="font-size:11px;">AKADEMIK</div>

        @if(auth()->user()?->hasPermission('pembayaran.view'))
            <li class="nav-item {{ request()->is('pembayaran-spp*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('pembayaran-spp.index') }}">
                    <i class="fas fa-fw fa-money-bill"></i><span>Pembayaran SPP</span>
                </a>
            </li>
        @endif
        @if(auth()->user()?->hasPermission('matakuliah.view'))
            <li class="nav-item {{ request()->is('pengambilan-matakuliah*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('pengambilan-matakuliah.index') }}">
                    <i class="fas fa-fw fa-book-open"></i><span>Matakuliah</span>
                </a>
            </li>
        @endif
        @if(auth()->user()?->hasPermission('buku.view'))
            <li class="nav-item {{ request()->is('peminjaman-buku*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('peminjaman-buku.index') }}">
                    <i class="fas fa-fw fa-book"></i><span>Peminjaman Buku</span>
                </a>
            </li>
        @endif

        @if(auth()->user()?->hasPermission('prestasi.view'))
            <li class="nav-item {{ request()->is('prestasi-mahasiswa*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('prestasi-mahasiswa.index') }}">
                    <i class="fas fa-fw fa-trophy"></i><span>Prestasi Mahasiswa</span>
                </a>
            </li>
        @endif

        @if(auth()->user()?->hasPermission('prestasi.report'))
            <li class="nav-item {{ request()->is('laporan-prestasi') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('laporan-prestasi') }}">
                    <i class="fas fa-fw fa-file-alt"></i><span>Laporan Prestasi</span>
                </a>
            </li>
        @endif

        @if(auth()->user()?->hasPermission('prestasi.view'))
            <li class="nav-item {{ request()->is('prestasi-mahasiswa/fuzzy-kualitas*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('prestasi-mahasiswa.fuzzy-kualitas') }}">
                    <i class="fas fa-fw fa-brain"></i><span>Kualitas Fuzzy</span>
                </a>
            </li>
        @endif

        @if(auth()->user()?->hasPermission('role.manage'))
            <hr class="sidebar-divider">
            <div class="sidebar-heading text-white px-3 mb-1" style="font-size:11px;">MANAJEMEN AKSES</div>
            <li class="nav-item {{ request()->is('data-role') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('data-role') }}">
                    <i class="fas fa-fw fa-user-shield"></i><span>Hak Akses</span>
                </a>
            </li>
        @endif

        @if(auth()->user()?->hasRole('operator'))
            <hr class="sidebar-divider">
            <div class="sidebar-heading text-white px-3 mb-1" style="font-size:11px;">OPERATOR</div>
            
            <li class="nav-item {{ request()->is('operator/activity-logs*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('operator.activity-logs.index') }}">
                    <i class="fas fa-fw fa-history"></i><span>Log Aktivitas</span>
                </a>
            </li>
            
            <li class="nav-item {{ request()->is('operator/users*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('operator.users.index') }}">
                    <i class="fas fa-fw fa-users"></i><span>Manajemen User</span>
                </a>
            </li>
        @endif
    </ul>

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <nav class="navbar navbar-expand navbar-dark topbar topbar-dark mb-4 static-top">
                <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                    <i class="fa fa-bars"></i>
                </button>
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item d-flex align-items-center">
                        <span class="nav-link">
                            <span class="user-badge">
                                <span class="user-avatar">
                                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                                </span>
                                {{ auth()->user()->name ?? 'Admin' }}
                                <span class="user-role">({{ auth()->user()->role->nama_role ?? '-' }})</span>
                            </span>
                        </span>
                        <form action="{{ route('logout') }}" method="POST" class="mb-0">
                            @csrf
                            <button type="submit" class="btn logout-btn">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </nav>

            <div class="container-fluid">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger">
                        <strong>Data belum bisa disimpan.</strong>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>
</div>

@stack('modals')

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/startbootstrap-sb-admin-2@4.1.4/js/sb-admin-2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    @stack('scripts')
</body>
</html>
