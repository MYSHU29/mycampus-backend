<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Log Aktivitas</title>
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

        <li class="nav-item active">
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
                <div class="row mb-4">
                    <div class="col-md-8">
                        <h1 class="h3 text-gray-800">Detail Log Aktivitas</h1>
                    </div>
                    <div class="col-md-4 text-right">
                        <a href="{{ route('operator.activity-logs.index') }}" class="btn btn-primary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>

                <div class="row">
                    {{-- Informasi Umum --}}
                    <div class="col-md-6">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Informasi Umum</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-borderless">
                                    <tr>
                                        <th style="width: 150px;">Tanggal & Waktu</th>
                                        <td>{{ $activityLog->created_at->format('Y-m-d H:i:s') }}</td>
                                    </tr>
                                    <tr>
                                        <th>User</th>
                                        <td>{{ $activityLog->user?->name ?? 'Unknown' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Email User</th>
                                        <td>{{ $activityLog->user?->email ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Module</th>
                                        <td><span class="badge badge-info">{{ $activityLog->module }}</span></td>
                                    </tr>
                                    <tr>
                                        <th>Tipe Aktivitas</th>
                                        <td>
                                            @if ($activityLog->activity_type == 'create')
                                                <span class="badge badge-success">{{ $activityLog->activity_type }}</span>
                                            @elseif ($activityLog->activity_type == 'update')
                                                <span class="badge badge-warning">{{ $activityLog->activity_type }}</span>
                                            @elseif ($activityLog->activity_type == 'delete')
                                                <span class="badge badge-danger">{{ $activityLog->activity_type }}</span>
                                            @else
                                                <span class="badge badge-secondary">{{ $activityLog->activity_type }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Deskripsi</th>
                                        <td>{{ $activityLog->description }}</td>
                                    </tr>
                                    <tr>
                                        <th>IP Address</th>
                                        <td><code>{{ $activityLog->ip_address }}</code></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- User Agent --}}
                    <div class="col-md-6">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Informasi Perangkat</h6>
                            </div>
                            <div class="card-body">
                                <p><strong>User Agent:</strong></p>
                                <code style="word-break: break-all; display: block; background: #f8f9fa; padding: 10px; border-radius: 4px;">
                                    {{ $activityLog->user_agent ?? '-' }}
                                </code>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Data Sebelum dan Sesudah --}}
                <div class="row">
                    @if ($activityLog->data_before)
                        <div class="col-md-6">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-danger">Data Sebelumnya</h6>
                                </div>
                                <div class="card-body">
                                    <pre style="background: #f8f9fa; padding: 15px; border-radius: 4px; max-height: 400px; overflow-y: auto;">{{ json_encode($activityLog->data_before, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($activityLog->data_after)
                        <div class="col-md-6">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-success">Data Sesudahnya</h6>
                                </div>
                                <div class="card-body">
                                    <pre style="background: #f8f9fa; padding: 15px; border-radius: 4px; max-height: 400px; overflow-y: auto;">{{ json_encode($activityLog->data_after, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/startbootstrap-sb-admin-2@4.1.4/js/sb-admin-2.min.js"></script>
</body>
</html>
