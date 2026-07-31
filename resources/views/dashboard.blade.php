@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<style>
    .dash-section-title {
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #484f58;
        margin-bottom: 1rem;
        padding-left: 2px;
    }
    .chart-card {
        border: 1px solid #30363d;
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.3s cubic-bezier(.4, 0, .2, 1);
        animation: fadeInUp 0.5s ease both;
        background: #161b22;
    }
    .chart-card:nth-child(1) { animation-delay: 0.25s; }
    .chart-card:nth-child(2) { animation-delay: 0.32s; }
    .chart-card:nth-child(3) { animation-delay: 0.39s; }
    .chart-card:nth-child(4) { animation-delay: 0.46s; }
    .chart-card:hover {
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.4), 0 0 20px rgba(0, 240, 255, 0.06);
        transform: translateY(-3px);
        border-color: rgba(0, 240, 255, 0.12);
    }
    .chart-card .card-header {
        border-bottom: none;
        padding: 1rem 1.25rem;
    }
    .chart-card .card-body {
        padding: 1rem 1.25rem 1.25rem;
        background: #161b22;
    }
    .chart-card h6 {
        font-weight: 700;
        font-size: 0.88rem;
        letter-spacing: 0.01em;
    }
    .chart-card h6 i {
        opacity: 0.85;
        margin-right: 6px;
    }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(14px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<div class="dashboard-wrap">
    <div class="row">
        <div class="col-xl col-md-4 col-sm-6 mb-4">
            <a href="{{ auth()->user()?->hasPermission('mahasiswa.view') ? route('data-mahasiswa') : route('dashboard') }}" class="dashboard-stat-card">
                <span class="stat-icon stat-blue"><i class="fas fa-users"></i></span>
                <strong>{{ $jumlahMahasiswa }}</strong>
                <small>Data Mahasiswa</small>
            </a>
        </div>
        <div class="col-xl col-md-4 col-sm-6 mb-4">
            <a href="{{ auth()->user()?->hasPermission('prestasi.report') ? route('prestasi-mahasiswa.fuzzy-kualitas') : (auth()->user()?->hasPermission('prestasi.view') ? route('prestasi-mahasiswa.index') : route('dashboard')) }}" class="dashboard-stat-card">
                <span class="stat-icon stat-orange"><i class="fas fa-brain"></i></span>
                <strong>{{ $jumlahCapaian }}</strong>
                <small>Kualitas Fuzzy</small>
            </a>
        </div>
        <div class="col-xl col-md-4 col-sm-6 mb-4">
            <a href="{{ auth()->user()?->hasPermission('prestasi.view') ? route('prestasi-mahasiswa.index') : route('dashboard') }}" class="dashboard-stat-card">
                <span class="stat-icon stat-indigo"><i class="fas fa-clipboard-list"></i></span>
                <strong>{{ $jumlahPendaftaran }}</strong>
                <small>Pendaftaran Prestasi</small>
            </a>
        </div>
        <div class="col-xl col-md-4 col-sm-6 mb-4">
            <a href="{{ auth()->user()?->hasPermission('prestasi.report') ? route('laporan-prestasi') : (auth()->user()?->hasPermission('prestasi.view') ? route('prestasi-mahasiswa.index') : route('dashboard')) }}" class="dashboard-stat-card">
                <span class="stat-icon stat-red"><i class="fas fa-award"></i></span>
                <strong>{{ $jumlahCapaian }}</strong>
                <small>Capaian Prestasi</small>
            </a>
        </div>
    </div>

    <div class="dash-section-title">Visualisasi Data</div>
    <div class="row mb-4">
        <div class="col-lg-6 mb-4">
            <div class="card chart-card h-100">
                <div class="card-header bg-gradient-primary">
                    <h6 class="m-0 text-neon-cyan"><i class="fas fa-chart-pie"></i>Status Verifikasi Prestasi</h6>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center" style="min-height: 280px;">
                    <canvas id="chartStatusVerifikasi" style="max-height:260px;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card chart-card h-100">
                <div class="card-header bg-gradient-primary">
                    <h6 class="m-0 text-neon-cyan"><i class="fas fa-chart-bar"></i>Prestasi per Jenis Kejuaraan</h6>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center" style="min-height: 280px;">
                    <canvas id="chartPrestasiJenis" style="max-height:260px;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card chart-card h-100">
                <div class="card-header bg-gradient-info">
                    <h6 class="m-0" style="color: #38bdf8;"><i class="fas fa-chart-bar"></i>Prestasi per Tingkat Kompetisi</h6>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center" style="min-height: 280px;">
                    <canvas id="chartPrestasiTingkat" style="max-height:260px;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card chart-card h-100">
                <div class="card-header bg-gradient-success">
                    <h6 class="m-0" style="color: #34d399;"><i class="fas fa-chart-line"></i>Tren Pendaftaran Prestasi</h6>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center" style="min-height: 280px;">
                    <canvas id="chartTrenBulanan" style="max-height:260px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="dash-section-title">Akses Cepat</div>
    <div class="row">
        <div class="col-lg-4 mb-4">
            <a href="{{ auth()->user()?->hasPermission('prestasi.view') ? route('prestasi-mahasiswa.index') : route('dashboard') }}" class="dashboard-action-card">
                <span><i class="fas fa-trophy"></i></span>
                <div>
                    <strong>Referensi Kejuaraan</strong>
                    <p>Kelola nama kejuaraan dan bobot poin sebagai dasar penilaian prestasi.</p>
                </div>
            </a>
        </div>
        <div class="col-lg-4 mb-4">
            <a href="{{ auth()->user()?->hasPermission('prestasi.create') ? route('prestasi-mahasiswa.create') : (auth()->user()?->hasPermission('prestasi.view') ? route('prestasi-mahasiswa.index') : route('dashboard')) }}" class="dashboard-action-card">
                <span><i class="fas fa-clipboard-check"></i></span>
                <div>
                    <strong>Pendaftaran Prestasi</strong>
                    <p>Catat mahasiswa, kegiatan, dan kategori kejuaraan yang diikuti.</p>
                </div>
            </a>
        </div>
        <div class="col-lg-4 mb-4">
            <a href="{{ auth()->user()?->hasPermission('prestasi.report') ? route('laporan-prestasi') : (auth()->user()?->hasPermission('prestasi.view') ? route('prestasi-mahasiswa.index') : route('dashboard')) }}" class="dashboard-action-card">
                <span><i class="fas fa-award"></i></span>
                <div>
                    <strong>Capaian Prestasi</strong>
                    <p>Simpan hasil capaian, peringkat, dosen pembimbing, dan file bukti.</p>
                </div>
            </a>
        </div>
        <div class="col-lg-4 mb-4">
            <a href="{{ route('info-terkait') }}" class="dashboard-action-card">
                <span><i class="fas fa-circle-info"></i></span>
                <div>
                    <strong>Info Terkait</strong>
                    <p>Lihat panduan singkat role pengguna, alur verifikasi, dan informasi penggunaan sistem.</p>
                </div>
            </a>
        </div>

        @if(auth()->user()?->hasRole('operator'))
            <div class="col-lg-4 mb-4">
                <a href="{{ route('operator.activity-logs.index') }}" class="dashboard-action-card">
                    <span><i class="fas fa-history"></i></span>
                    <div>
                        <strong>Log Aktivitas</strong>
                        <p>Pantau dan analisis semua aktivitas pengguna di sistem dengan filter detail.</p>
                    </div>
                </a>
            </div>

            <div class="col-lg-4 mb-4">
                <a href="{{ route('operator.users.index') }}" class="dashboard-action-card">
                    <span><i class="fas fa-users"></i></span>
                    <div>
                        <strong>Manajemen User</strong>
                        <p>Kelola akun pengguna, role, dan hak akses dalam sistem.</p>
                    </div>
                </a>
            </div>

            <div class="col-lg-4 mb-4">
                <a href="{{ route('data-role') }}" class="dashboard-action-card">
                    <span><i class="fas fa-user-shield"></i></span>
                    <div>
                        <strong>Manajemen Role</strong>
                        <p>Atur peran dan izin pengguna untuk mengontrol akses sistem.</p>
                    </div>
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    Chart.defaults.color = '#8b949e';
    Chart.defaults.borderColor = '#21262d';

    var statusLabels = ['Menunggu', 'Diterima', 'Ditolak'];
    var statusData = @json($statusVerifikasi);
    var statusColors = ['#f59e0b', '#34d399', '#f87171'];

    new Chart(document.getElementById('chartStatusVerifikasi'), {
        type: 'doughnut',
        data: {
            labels: statusLabels,
            datasets: [{
                data: statusData,
                backgroundColor: statusColors,
                borderWidth: 2,
                borderColor: '#161b22',
                hoverBorderWidth: 3,
                hoverOffset: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { padding: 16, usePointStyle: true, font: { family: 'Inter', size: 12 } } }
            },
            animation: { animateScale: true, animateRotate: true }
        }
    });

    var jenisLabels = @json(array_keys($prestasiPerJenis));
    var jenisData = @json(array_values($prestasiPerJenis));

    new Chart(document.getElementById('chartPrestasiJenis'), {
        type: 'bar',
        data: {
            labels: jenisLabels,
            datasets: [{
                label: 'Jumlah Prestasi',
                data: jenisData,
                backgroundColor: ['#00f0ff', '#a855f7', '#38bdf8', '#f59e0b'],
                borderRadius: 8,
                barThickness: 36
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: '#21262d' } },
                x: { grid: { display: false } }
            },
            animation: { duration: 800, easing: 'easeOutQuart' }
        }
    });

    var tingkatOrder = ['Kampus', 'Kota', 'Provinsi', 'Nasional', 'Internasional'];
    var tingkatRaw = @json($prestasiPerTingkat);
    var tingkatLabels = tingkatOrder.filter(function(t) { return tingkatRaw[t] !== undefined; });
    var tingkatData = tingkatLabels.map(function(t) { return tingkatRaw[t]; });

    new Chart(document.getElementById('chartPrestasiTingkat'), {
        type: 'bar',
        data: {
            labels: tingkatLabels,
            datasets: [{
                label: 'Jumlah Prestasi',
                data: tingkatData,
                backgroundColor: ['#00f0ff', '#38bdf8', '#f59e0b', '#f87171', '#34d399'],
                borderRadius: 8,
                barThickness: 36
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: '#21262d' } },
                x: { grid: { display: false } }
            },
            animation: { duration: 800, easing: 'easeOutQuart' }
        }
    });

    var bulanLabels = @json(array_keys($trenBulanan));
    var bulanData = @json(array_values($trenBulanan));
    var bulanDisplay = bulanLabels.map(function(b) {
        var parts = b.split('-');
        var months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        return months[parseInt(parts[1], 10) - 1] + ' ' + parts[0];
    });

    new Chart(document.getElementById('chartTrenBulanan'), {
        type: 'line',
        data: {
            labels: bulanDisplay,
            datasets: [{
                label: 'Pendaftaran',
                data: bulanData,
                borderColor: '#00f0ff',
                backgroundColor: function(ctx) {
                    var chart = ctx.chart;
                    var area = chart.ctx.createLinearGradient(0, 0, 0, chart.height);
                    area.addColorStop(0, 'rgba(0, 240, 255, 0.2)');
                    area.addColorStop(1, 'rgba(0, 240, 255, 0.01)');
                    return area;
                },
                fill: true,
                tension: 0.4,
                pointRadius: 5,
                pointBackgroundColor: '#00f0ff',
                pointBorderColor: '#161b22',
                pointBorderWidth: 2,
                pointHoverRadius: 7,
                pointHoverBackgroundColor: '#00f0ff',
                pointHoverBorderColor: '#161b22',
                pointHoverBorderWidth: 3,
                borderWidth: 2.5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: '#21262d' } },
                x: { grid: { display: false } }
            },
            animation: { duration: 1000, easing: 'easeOutQuart' }
        }
    });
})();
</script>
@endpush
