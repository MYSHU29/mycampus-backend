<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Aktivitas</title>
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

        <li class="nav-item">
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
                <h1 class="h3 mb-2 text-gray-800">Log Aktivitas</h1>
                <p class="mb-4">Pantau semua aktivitas sistem di sini</p>

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

                {{-- Filter Section --}}
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Filter</h6>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('operator.activity-logs.index') }}" class="form-inline">
                            <div class="form-group mr-2 mb-2">
                                <label for="module" class="mr-2">Module:</label>
                                <select name="module" id="module" class="form-control form-control-sm">
                                    <option value="">Semua</option>
                                    @foreach ($modules as $mod)
                                        <option value="{{ $mod }}" {{ request('module') == $mod ? 'selected' : '' }}>{{ $mod }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mr-2 mb-2">
                                <label for="activity_type" class="mr-2">Tipe Aktivitas:</label>
                                <select name="activity_type" id="activity_type" class="form-control form-control-sm">
                                    <option value="">Semua</option>
                                    @foreach ($activityTypes as $type)
                                        <option value="{{ $type }}" {{ request('activity_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mr-2 mb-2">
                                <label for="user_id" class="mr-2">User:</label>
                                <select name="user_id" id="user_id" class="form-control form-control-sm">
                                    <option value="">Semua</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mr-2 mb-2">
                                <label for="start_date" class="mr-2">Dari Tanggal:</label>
                                <input type="date" name="start_date" id="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}">
                            </div>

                            <div class="form-group mr-2 mb-2">
                                <label for="end_date" class="mr-2">Sampai Tanggal:</label>
                                <input type="date" name="end_date" id="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}">
                            </div>

                            <button type="submit" class="btn btn-primary btn-sm mb-2">
                                <i class="fas fa-search"></i> Filter
                            </button>
                            <a href="{{ route('operator.activity-logs.index') }}" class="btn btn-secondary btn-sm mb-2 ml-2">
                                <i class="fas fa-redo"></i> Reset
                            </a>
                        </form>
                    </div>
                </div>

                {{-- Data Table --}}
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Daftar Log Aktivitas</h6>
                        <form action="{{ route('operator.activity-logs.export') }}" method="GET" class="form-inline">
                            <input type="hidden" name="module" value="{{ request('module') }}">
                            <input type="hidden" name="activity_type" value="{{ request('activity_type') }}">
                            <input type="hidden" name="user_id" value="{{ request('user_id') }}">
                            <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                            <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                            <button type="submit" class="btn btn-sm btn-info">
                                <i class="fas fa-download"></i> Export CSV
                            </button>
                        </form>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tanggal & Waktu</th>
                                        <th>User</th>
                                        <th>Module</th>
                                        <th>Tipe Aktivitas</th>
                                        <th>Deskripsi</th>
                                        <th>IP Address</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($activityLogs as $log)
                                        <tr>
                                            <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                                            <td>{{ $log->user?->name ?? 'Unknown' }}</td>
                                            <td>
                                                <span class="badge badge-info">{{ $log->module }}</span>
                                            </td>
                                            <td>
                                                @if ($log->activity_type == 'create')
                                                    <span class="badge badge-success">{{ $log->activity_type }}</span>
                                                @elseif ($log->activity_type == 'update')
                                                    <span class="badge badge-warning">{{ $log->activity_type }}</span>
                                                @elseif ($log->activity_type == 'delete')
                                                    <span class="badge badge-danger">{{ $log->activity_type }}</span>
                                                @else
                                                    <span class="badge badge-secondary">{{ $log->activity_type }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $log->description }}</td>
                                            <td><small>{{ $log->ip_address }}</small></td>
                                            <td>
                                                <a href="{{ route('operator.activity-logs.show', $log) }}" class="btn btn-sm btn-info" title="Lihat Detail">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">Tidak ada data</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                Total: {{ $activityLogs->total() }} data
                            </div>
                            <div>
                                {{ $activityLogs->render() }}
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
