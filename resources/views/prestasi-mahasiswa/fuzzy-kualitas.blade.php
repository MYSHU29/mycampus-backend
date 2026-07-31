@extends('layouts.app')

@section('title', 'Kualitas Fuzzy Prestasi')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Kategorisasi Kualitas Prestasi (Fuzzy)</h1>
    <div>
        <form action="{{ route('prestasi-mahasiswa.fuzzy-hitung') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fas fa-sync-alt fa-sm"></i> Hitung Ulang
            </button>
        </form>
        <a href="{{ route('laporan-prestasi') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left fa-sm"></i> Kembali
        </a>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Jumlah Prestasi</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $jumlahPrestasi }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card border-left-secondary shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Rata-rata Skor Fuzzy</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($rataRataSkor, 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card border-left-dark shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">Peringkat Terbaik</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $peringkatTerbaik }} <span class="text-muted" style="font-size: 0.8em;">({{ $frekuensiTerbaik }}x)</span></div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6 mb-3">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Prestasi Layak Penghargaan</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $layakCount }}                 <span class="text-muted" style="font-size: 0.8em;">(skor ≥ 65)</span></div>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-3">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Prestasi Tidak Layak</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $tidakLayakCount }}                 <span class="text-muted" style="font-size: 0.8em;">(skor < 40)</span></div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-12 mb-3">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Tingkat Kompetisi</div>
                <div class="mt-2">
                    @forelse($tingkatBreakdown as $tingkat => $count)
                        <span class="badge badge-primary mr-2 mb-1" style="font-size: 0.9em;">
                            {{ $tingkat }}: {{ $count }}
                        </span>
                    @empty
                        <span class="text-muted">Tidak ada data</span>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Sangat Baik</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $rekapKualitas['Sangat Baik'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Baik</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $rekapKualitas['Baik'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Cukup</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $rekapKualitas['Cukup'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Kurang</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $rekapKualitas['Kurang'] }}</div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-lg-4 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 bg-gradient-primary">
                <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-chart-pie mr-2"></i>Distribusi Kualitas Fuzzy</h6>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center" style="min-height: 300px;">
                <canvas id="chartDistribusiKualitas" style="max-height:280px;"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 bg-gradient-info">
                <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-chart-bar mr-2"></i>Rata-rata Skor per Tingkat</h6>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center" style="min-height: 300px;">
                <canvas id="chartSkorTingkat" style="max-height:280px;"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 bg-gradient-success">
                <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-chart-bar mr-2"></i>Rata-rata Skor per Jenis</h6>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center" style="min-height: 300px;">
                <canvas id="chartSkorJenis" style="max-height:280px;"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3 bg-gradient-primary">
        <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-brain mr-2"></i>Hasil Kategorisasi Fuzzy</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%" id="fuzzyTable">
                <thead class="thead-dark">
                    <tr>
                        <th>Kode</th>
                        <th>Mahasiswa</th>
                        <th>Lomba</th>
                        <th>Jenis</th>
                        <th>Tingkat</th>
                        <th>Juara</th>
                        <th>Jml Prestasi</th>
                        <th>Skor Fuzzy</th>
                        <th>Kualitas</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($prestasiMahasiswa as $item)
                        <tr>
                            <td>{{ $item->kode_prestasi }}</td>
                            <td>{{ $item->mahasiswa->nama ?? '-' }}</td>
                            <td>{{ $item->nama_lomba }}</td>
                            <td>{{ $item->jenisPrestasi->nama_jenis ?? '-' }}</td>
                            <td>{{ $item->tingkatPrestasi->nama_tingkat ?? '-' }}</td>
                            <td>{{ $item->juara }}</td>
                            <td>{{ $item->fuzzyHasil?->jumlah_prestasi ?? '-' }}</td>
                            <td>
                                @if($item->skor_fuzzy !== null)
                                    <strong>{{ $item->skor_fuzzy }}</strong>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @switch($item->kualitas_fuzzy)
                                    @case('Sangat Baik')
                                        <span class="badge badge-success px-2 py-1">Sangat Baik</span>
                                        @break
                                    @case('Baik')
                                        <span class="badge badge-primary px-2 py-1">Baik</span>
                                        @break
                                    @case('Cukup')
                                        <span class="badge badge-warning px-2 py-1">Cukup</span>
                                        @break
                                    @case('Kurang')
                                        <span class="badge badge-danger px-2 py-1">Kurang</span>
                                        @break
                                    @default
                                        <span class="badge badge-secondary px-2 py-1">Belum dihitung</span>
                                @endswitch
                            </td>
                            <td>
                                <button type="button" class="btn btn-info btn-sm"
                                    onclick="showDetail(this)"
                                    data-kode="{{ $item->kode_prestasi }}"
                                    data-nim="{{ $item->mahasiswa->nim ?? '-' }}"
                                    data-nama="{{ $item->mahasiswa->nama ?? '-' }}"
                                    data-lomba="{{ $item->nama_lomba }}"
                                    data-penyelenggara="{{ $item->penyelenggara ?? '-' }}"
                                    data-jenis="{{ $item->jenisPrestasi->nama_jenis ?? '-' }}"
                                    data-tingkat="{{ $item->tingkatPrestasi->nama_tingkat ?? '-' }}"
                                    data-juara="{{ $item->juara }}"
                                    data-tanggal="{{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y') : '-' }}"
                                    data-skor="{{ $item->skor_fuzzy ?? '-' }}"
                                    data-kualitas="{{ $item->kualitas_fuzzy ?? '-' }}"
                                    data-jml-prestasi="{{ $item->fuzzyHasil?->jumlah_prestasi ?? '-' }}"
                                    data-mf-tingkat-rendah="{{ $item->fuzzyHasil?->mf_tingkat_rendah ?? '0' }}"
                                    data-mf-tingkat-sedang="{{ $item->fuzzyHasil?->mf_tingkat_sedang ?? '0' }}"
                                    data-mf-tingkat-tinggi="{{ $item->fuzzyHasil?->mf_tingkat_tinggi ?? '0' }}"
                                    data-mf-juara-1="{{ $item->fuzzyHasil?->mf_juara_1 ?? '0' }}"
                                    data-mf-juara-2="{{ $item->fuzzyHasil?->mf_juara_2 ?? '0' }}"
                                    data-mf-juara-3-plus="{{ $item->fuzzyHasil?->mf_juara_3_plus ?? '0' }}"
                                    data-mf-jml-sedikit="{{ $item->fuzzyHasil?->mf_jml_sedikit ?? '0' }}"
                                    data-mf-jml-sedang="{{ $item->fuzzyHasil?->mf_jml_sedang ?? '0' }}"
                                    data-mf-jml-banyak="{{ $item->fuzzyHasil?->mf_jml_banyak ?? '0' }}"
                                    data-aturan-terpakai="{{ $item->fuzzyHasil?->aturan_terpakai ?? '[]' }}">
                                    <i class="fas fa-eye fa-sm"></i> Detail
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="10" class="text-center text-muted">Tidak ada prestasi dengan status diterima.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-lg-12 mb-3">
        <div class="card shadow h-100">
            <div class="card-header py-3 bg-gradient-info">
                <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-info-circle mr-2"></i>Metode Fuzzy — Penjelasan Lengkap</h6>
            </div>
            <div class="card-body">

                {{-- INPUT --}}
                <p class="mb-2"><strong>1. Input (3 Variabel):</strong></p>
                <div class="table-responsive mb-3">
                    <table class="table table-sm table-bordered" width="100%">
                        <thead class="thead-dark">
                            <tr>
                                <th>Variabel</th>
                                <th>Kategori</th>
                                <th>Nilai / Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td rowspan="3"><strong>Tingkat Prestasi</strong></td>
                                <td>Rendah</td>
                                <td>Kampus (nilai 1)</td>
                            </tr>
                            <tr>
                                <td>Sedang</td>
                                <td>Kota (2), Provinsi (3)</td>
                            </tr>
                            <tr>
                                <td>Tinggi</td>
                                <td>Nasional (4), Internasional (5)</td>
                            </tr>
                            <tr>
                                <td rowspan="3"><strong>Juara</strong></td>
                                <td>Juara 1</td>
                                <td>Juara 1 (nilai 1)</td>
                            </tr>
                            <tr>
                                <td>Juara 2</td>
                                <td>Juara 2 (nilai 2)</td>
                            </tr>
                            <tr>
                                <td>Juara 3+</td>
                                <td>Juara 3 ke atas (nilai 3 dst.). "Harapan" dianggap +0.5. Jika tidak ada angka, default = 5</td>
                            </tr>
                            <tr>
                                <td rowspan="3"><strong>Jumlah Prestasi</strong></td>
                                <td>Sedikit</td>
                                <td>0 – 2 prestasi diterima</td>
                            </tr>
                            <tr>
                                <td>Sedang</td>
                                <td>2 – 6 prestasi diterima</td>
                            </tr>
                            <tr>
                                <td>Banyak</td>
                                <td>4 atau lebih prestasi diterima</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- ATURAN 27 RULES --}}
                <p class="mb-2"><strong>2. Aturan Fuzzy (27 Rules):</strong></p>
                <p class="small text-muted mb-2">Format: JIKA [Tingkat] DAN [Juara] DAN [Jumlah Prestasi] MAKA [Kualitas]</p>

                <p class="small mb-1"><strong>Kelompok Tingkat Tinggi (Nasional / Internasional):</strong></p>
                <div class="table-responsive mb-2">
                    <table class="table table-sm table-bordered" width="100%">
                        <thead class="thead-dark">
                            <tr><th width="35">#</th><th>Tingkat</th><th>Juara</th><th>Jml Prestasi</th><th>Kualitas</th></tr>
                        </thead>
                        <tbody>
                            <tr><td>1</td><td>Tinggi</td><td>Juara 1</td><td>Banyak</td><td><span class="badge badge-success">Sangat Baik</span></td></tr>
                            <tr><td>2</td><td>Tinggi</td><td>Juara 1</td><td>Sedang</td><td><span class="badge badge-success">Sangat Baik</span></td></tr>
                            <tr><td>3</td><td>Tinggi</td><td>Juara 1</td><td>Sedikit</td><td><span class="badge badge-primary">Baik</span></td></tr>
                            <tr><td>4</td><td>Tinggi</td><td>Juara 2</td><td>Banyak</td><td><span class="badge badge-success">Sangat Baik</span></td></tr>
                            <tr><td>5</td><td>Tinggi</td><td>Juara 2</td><td>Sedang</td><td><span class="badge badge-success">Sangat Baik</span></td></tr>
                            <tr><td>6</td><td>Tinggi</td><td>Juara 2</td><td>Sedikit</td><td><span class="badge badge-primary">Baik</span></td></tr>
                            <tr><td>7</td><td>Tinggi</td><td>Juara 3+</td><td>Banyak</td><td><span class="badge badge-primary">Baik</span></td></tr>
                            <tr><td>8</td><td>Tinggi</td><td>Juara 3+</td><td>Sedang</td><td><span class="badge badge-warning text-dark">Cukup</span></td></tr>
                            <tr><td>9</td><td>Tinggi</td><td>Juara 3+</td><td>Sedikit</td><td><span class="badge badge-warning text-dark">Cukup</span></td></tr>
                        </tbody>
                    </table>
                </div>

                <p class="small mb-1"><strong>Kelompok Tingkat Sedang (Kota / Provinsi):</strong></p>
                <div class="table-responsive mb-2">
                    <table class="table table-sm table-bordered" width="100%">
                        <thead class="thead-dark">
                            <tr><th width="35">#</th><th>Tingkat</th><th>Juara</th><th>Jml Prestasi</th><th>Kualitas</th></tr>
                        </thead>
                        <tbody>
                            <tr><td>10</td><td>Sedang</td><td>Juara 1</td><td>Banyak</td><td><span class="badge badge-success">Sangat Baik</span></td></tr>
                            <tr><td>11</td><td>Sedang</td><td>Juara 1</td><td>Sedang</td><td><span class="badge badge-success">Sangat Baik</span></td></tr>
                            <tr><td>12</td><td>Sedang</td><td>Juara 1</td><td>Sedikit</td><td><span class="badge badge-primary">Baik</span></td></tr>
                            <tr><td>13</td><td>Sedang</td><td>Juara 2</td><td>Banyak</td><td><span class="badge badge-primary">Baik</span></td></tr>
                            <tr><td>14</td><td>Sedang</td><td>Juara 2</td><td>Sedang</td><td><span class="badge badge-warning text-dark">Cukup</span></td></tr>
                            <tr><td>15</td><td>Sedang</td><td>Juara 2</td><td>Sedikit</td><td><span class="badge badge-warning text-dark">Cukup</span></td></tr>
                            <tr><td>16</td><td>Sedang</td><td>Juara 3+</td><td>Banyak</td><td><span class="badge badge-warning text-dark">Cukup</span></td></tr>
                            <tr><td>17</td><td>Sedang</td><td>Juara 3+</td><td>Sedang</td><td><span class="badge badge-warning text-dark">Cukup</span></td></tr>
                            <tr><td>18</td><td>Sedang</td><td>Juara 3+</td><td>Sedikit</td><td><span class="badge badge-danger">Kurang</span></td></tr>
                        </tbody>
                    </table>
                </div>

                <p class="small mb-1"><strong>Kelompok Tingkat Rendah (Kampus):</strong></p>
                <div class="table-responsive mb-3">
                    <table class="table table-sm table-bordered" width="100%">
                        <thead class="thead-dark">
                            <tr><th width="35">#</th><th>Tingkat</th><th>Juara</th><th>Jml Prestasi</th><th>Kualitas</th></tr>
                        </thead>
                        <tbody>
                            <tr><td>19</td><td>Rendah</td><td>Juara 1</td><td>Banyak</td><td><span class="badge badge-primary">Baik</span></td></tr>
                            <tr><td>20</td><td>Rendah</td><td>Juara 1</td><td>Sedang</td><td><span class="badge badge-warning text-dark">Cukup</span></td></tr>
                            <tr><td>21</td><td>Rendah</td><td>Juara 1</td><td>Sedikit</td><td><span class="badge badge-warning text-dark">Cukup</span></td></tr>
                            <tr><td>22</td><td>Rendah</td><td>Juara 2</td><td>Banyak</td><td><span class="badge badge-warning text-dark">Cukup</span></td></tr>
                            <tr><td>23</td><td>Rendah</td><td>Juara 2</td><td>Sedang</td><td><span class="badge badge-warning text-dark">Cukup</span></td></tr>
                            <tr><td>24</td><td>Rendah</td><td>Juara 2</td><td>Sedikit</td><td><span class="badge badge-danger">Kurang</span></td></tr>
                            <tr><td>25</td><td>Rendah</td><td>Juara 3+</td><td>Banyak</td><td><span class="badge badge-warning text-dark">Cukup</span></td></tr>
                            <tr><td>26</td><td>Rendah</td><td>Juara 3+</td><td>Sedang</td><td><span class="badge badge-danger">Kurang</span></td></tr>
                            <tr><td>27</td><td>Rendah</td><td>Juara 3+</td><td>Sedikit</td><td><span class="badge badge-danger">Kurang</span></td></tr>
                        </tbody>
                    </table>
                </div>

                {{-- RINGKASAN POLA --}}
                <p class="mb-2"><strong>3. Ringkasan Pola Aturan:</strong></p>
                <div class="table-responsive mb-3">
                    <table class="table table-sm table-bordered" width="100%">
                        <thead class="thead-dark">
                            <tr><th>Tingkat</th><th>Juara</th><th>Jml Prestasi</th><th>Kualitas</th></tr>
                        </thead>
                        <tbody>
                            <tr><td>Tinggi</td><td>Juara 1</td><td>Banyak / Sedang</td><td><span class="badge badge-success">Sangat Baik</span></td></tr>
                            <tr><td>Tinggi</td><td>Juara 1</td><td>Sedikit</td><td><span class="badge badge-primary">Baik</span></td></tr>
                            <tr><td>Tinggi</td><td>Juara 2</td><td>Banyak / Sedang</td><td><span class="badge badge-success">Sangat Baik</span></td></tr>
                            <tr><td>Tinggi</td><td>Juara 2</td><td>Sedikit</td><td><span class="badge badge-primary">Baik</span></td></tr>
                            <tr><td>Tinggi</td><td>Juara 3+</td><td>Banyak</td><td><span class="badge badge-primary">Baik</span></td></tr>
                            <tr><td>Tinggi</td><td>Juara 3+</td><td>Sedang / Sedikit</td><td><span class="badge badge-warning text-dark">Cukup</span></td></tr>
                            <tr><td>Sedang</td><td>Juara 1</td><td>Banyak / Sedang</td><td><span class="badge badge-success">Sangat Baik</span></td></tr>
                            <tr><td>Sedang</td><td>Juara 1</td><td>Sedikit</td><td><span class="badge badge-primary">Baik</span></td></tr>
                            <tr><td>Sedang</td><td>Juara 2</td><td>Banyak</td><td><span class="badge badge-primary">Baik</span></td></tr>
                            <tr><td>Sedang</td><td>Juara 2</td><td>Sedang / Sedikit</td><td><span class="badge badge-warning text-dark">Cukup</span></td></tr>
                            <tr><td>Sedang</td><td>Juara 3+</td><td>Banyak / Sedang</td><td><span class="badge badge-warning text-dark">Cukup</span></td></tr>
                            <tr><td>Sedang</td><td>Juara 3+</td><td>Sedikit</td><td><span class="badge badge-danger">Kurang</span></td></tr>
                            <tr><td>Rendah</td><td>Juara 1</td><td>Banyak</td><td><span class="badge badge-primary">Baik</span></td></tr>
                            <tr><td>Rendah</td><td>Juara 1</td><td>Sedang / Sedikit</td><td><span class="badge badge-warning text-dark">Cukup</span></td></tr>
                            <tr><td>Rendah</td><td>Juara 2</td><td>Banyak / Sedang</td><td><span class="badge badge-warning text-dark">Cukup</span></td></tr>
                            <tr><td>Rendah</td><td>Juara 2</td><td>Sedikit</td><td><span class="badge badge-danger">Kurang</span></td></tr>
                            <tr><td>Rendah</td><td>Juara 3+</td><td>Banyak</td><td><span class="badge badge-warning text-dark">Cukup</span></td></tr>
                            <tr><td>Rendah</td><td>Juara 3+</td><td>Sedang / Sedikit</td><td><span class="badge badge-danger">Kurang</span></td></tr>
                        </tbody>
                    </table>
                </div>

                {{-- CONTOH PERHITUNGAN --}}
                <p class="mb-2"><strong>4. Contoh Perhitungan:</strong></p>
                <div class="p-3 rounded mb-3" style="background:#1a1a2e; border:1px solid #16213e; color:#ffffff;">
                    <p class="mb-1" style="color:#ffffff;"><strong>Kasus:</strong> Mahasiswa menang Juara 1 di Lomba Nasional, jumlah prestasi diterima = 5</p>
                    <hr style="border-color:#3a3a5c;">
                    <p class="mb-1" style="color:#ffffff;"><strong>Langkah 1 — Fuzzifikasi Input:</strong></p>
                    <ul class="small mb-2" style="color:#e0e0e0;">
                        <li>Tingkat Nasional (4) → Tinggi = 0.5, Sedang = 0.5, Rendah = 0</li>
                        <li>Juara 1 → Juara 1 = 1.0, Juara 2 = 0, Juara 3+ = 0</li>
                        <li>Jumlah Prestasi 5 → Banyak = 0.5, Sedang = 0.5, Sedikit = 0</li>
                    </ul>
                    <p class="mb-1" style="color:#ffffff;"><strong>Langkah 2 — Evaluasi Aturan (MIN dari 3 input):</strong></p>
                    <ul class="small mb-2" style="color:#e0e0e0;">
                        <li>Aturan 1: MIN(Tinggi=0.5, Juara1=1.0, Banyak=0.5) = <strong>0.5</strong> → Sangat Baik</li>
                        <li>Aturan 2: MIN(Tinggi=0.5, Juara1=1.0, Sedang=0.5) = <strong>0.5</strong> → Sangat Baik</li>
                        <li>Aturan 4: MIN(Sedang=0.5, Juara1=1.0, Banyak=0.5) = <strong>0.5</strong> → Sangat Baik</li>
                        <li>Aturan 5: MIN(Sedang=0.5, Juara1=1.0, Sedang=0.5) = <strong>0.5</strong> → Sangat Baik</li>
                    </ul>
                    <p class="mb-1" style="color:#ffffff;"><strong>Langkah 3 — Agregasi (MAX per output):</strong></p>
                    <ul class="small mb-2" style="color:#e0e0e0;">
                        <li>Sangat Baik = MAX(0.5, 0.5, 0.5, 0.5) = <strong>0.5</strong></li>
                    </ul>
                    <p class="mb-1" style="color:#ffffff;"><strong>Langkah 4 — Defuzzifikasi (Weighted Average):</strong></p>
                    <ul class="small mb-2" style="color:#e0e0e0;">
                        <li>Skor = (85 × 0.5) / 0.5 = <strong>85</strong></li>
                    </ul>
                    <p class="mb-0" style="color:#ffffff;"><strong>Langkah 5 — Threshold Kualitas:</strong></p>
                    <ul class="small mb-0" style="color:#e0e0e0;">
                        <li>Skor 85 ≥ 65 → <span class="badge badge-success">Sangat Baik</span></li>
                    </ul>
                </div>

                {{-- THRESHOLD --}}
                <p class="mb-2"><strong>5. Threshold Kualitas Akhir:</strong></p>
                <div class="table-responsive mb-3">
                    <table class="table table-sm table-bordered" width="100%">
                        <thead class="thead-dark">
                            <tr><th>Skor</th><th>Kategori</th><th>Keterangan</th></tr>
                        </thead>
                        <tbody>
                            <tr><td>≥ 65</td><td><span class="badge badge-success">Sangat Baik</span></td><td>Layak penghargaan</td></tr>
                            <tr><td>≥ 40</td><td><span class="badge badge-primary">Baik</span></td><td>Cukup memuaskan</td></tr>
                            <tr><td>≥ 20</td><td><span class="badge badge-warning text-dark">Cukup</span></td><td>Perlu peningkatan</td></tr>
                            <tr><td>&lt; 20</td><td><span class="badge badge-danger">Kurang</span></td><td>Tidak memenuhi standar</td></tr>
                        </tbody>
                    </table>
                </div>

                {{-- CARA KERJA --}}
                <p class="mb-2"><strong>6. Cara Kerja Secara Singkat:</strong></p>
                <ol class="small mb-0">
                    <li class="mb-1"><strong>Fuzzifikasi:</strong> Setiap input dikonversi menjadi derajat keanggotaan (0.0 – 1.0) berdasarkan fungsi keanggotaan.</li>
                    <li class="mb-1"><strong>Evaluasi Aturan:</strong> Untuk setiap aturan, ambil <strong>nilai terkecil</strong> (MIN) dari ketiga derajat keanggotaan. Jika hasilnya &gt; 0, aturan aktif.</li>
                    <li class="mb-1"><strong>Agregasi:</strong> Untuk setiap kategori output, ambil <strong>derajat terbesar</strong> (MAX) dari semua aturan yang aktif untuk kategori tersebut.</li>
                    <li class="mb-1"><strong>Defuzzifikasi:</strong> Hitung skor akhir = Σ(centroid × degree) / Σ(degree).</li>
                    <li><strong>Threshold:</strong> Skor akhir dikategorikan berdasarkan tabel threshold di atas.</li>
                </ol>

            </div>
        </div>
    </div>
</div>
@endsection

@push('modals')
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="background:#161b22; border:1px solid #30363d;">
            <div class="modal-header bg-gradient-primary py-3">
                <h6 class="modal-title font-weight-bold text-white" id="detailModalLabel">
                    <i class="fas fa-award mr-2"></i>Detail Prestasi & Fuzzy
                </h6>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Kode Prestasi:</strong></p>
                        <p class="mb-3" id="modal-kode">-</p>

                        <p class="mb-1"><strong>Mahasiswa:</strong></p>
                        <p class="mb-3" id="modal-mahasiswa">-</p>

                        <p class="mb-1"><strong>NIM:</strong></p>
                        <p class="mb-3" id="modal-nim">-</p>

                        <p class="mb-1"><strong>Nama Lomba:</strong></p>
                        <p class="mb-3" id="modal-lomba">-</p>

                        <p class="mb-1"><strong>Penyelenggara:</strong></p>
                        <p class="mb-3" id="modal-penyelenggara">-</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Jenis Prestasi:</strong></p>
                        <p class="mb-3" id="modal-jenis">-</p>

                        <p class="mb-1"><strong>Tingkat Kompetisi:</strong></p>
                        <p class="mb-3" id="modal-tingkat">-</p>

                        <p class="mb-1"><strong>Peringkat / Juara:</strong></p>
                        <p class="mb-3" id="modal-juara">-</p>

                        <p class="mb-1"><strong>Tanggal:</strong></p>
                        <p class="mb-3" id="modal-tanggal">-</p>

                        <p class="mb-1"><strong>Jumlah Prestasi Mahasiswa:</strong></p>
                        <p class="mb-3" id="modal-jml-prestasi">-</p>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-6 text-center">
                        <div class="p-3 rounded" style="background:#0d1117; border:1px solid #30363d;">
                            <div class="text-xs font-weight-bold text-uppercase mb-1" style="color:#8b949e;">Skor Fuzzy</div>
                            <div class="h4 font-weight-bold" style="color:#e6edf3;" id="modal-skor">-</div>
                        </div>
                    </div>
                    <div class="col-md-6 text-center">
                        <div class="p-3 rounded" style="background:#0d1117; border:1px solid #30363d;">
                            <div class="text-xs font-weight-bold text-uppercase mb-1" style="color:#8b949e;">Kualitas</div>
                            <div id="modal-kualitas">-</div>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="card" style="background:#0d1117; border:1px solid #30363d;">
                            <div class="card-body py-2">
                                <h6 class="font-weight-bold mb-2">Detail Fuzzifikasi</h6>
                                <div class="row small">
                                    <div class="col-md-4">
                                        <strong>Tingkat:</strong><br>
                                        Rendah: <span id="modal-mf-tingkat-rendah">0</span><br>
                                        Sedang: <span id="modal-mf-tingkat-sedang">0</span><br>
                                        Tinggi: <span id="modal-mf-tingkat-tinggi">0</span>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Juara:</strong><br>
                                        Juara 1: <span id="modal-mf-juara-1">0</span><br>
                                        Juara 2: <span id="modal-mf-juara-2">0</span><br>
                                        Juara 3+: <span id="modal-mf-juara-3+">0</span>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Jml Prestasi:</strong><br>
                                        Sedikit: <span id="modal-mf-jml-sedikit">0</span><br>
                                        Sedang: <span id="modal-mf-jml-sedang">0</span><br>
                                        Banyak: <span id="modal-mf-jml-banyak">0</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-12">
                        <p class="mb-1 small"><strong>Aturan Terpakai:</strong></p>
                        <pre id="modal-aturan-terpakai" class="small p-2 rounded" style="max-height:100px; overflow-y:auto; font-size: 0.8em; background:#0d1117; border:1px solid #30363d; color:#e6edf3;">-</pre>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script>
(function() {
    var kualitasLabels = ['Sangat Baik', 'Baik', 'Cukup', 'Kurang'];
    var kualitasData = [
        {{ $rekapKualitas['Sangat Baik'] }},
        {{ $rekapKualitas['Baik'] }},
        {{ $rekapKualitas['Cukup'] }},
        {{ $rekapKualitas['Kurang'] }}
    ];
    var kualitasColors = ['#1cc88a', '#4e73df', '#f6c23e', '#e74a3b'];

    new Chart(document.getElementById('chartDistribusiKualitas'), {
        type: 'doughnut',
        data: {
            labels: kualitasLabels,
            datasets: [{
                data: kualitasData,
                backgroundColor: kualitasColors,
                borderWidth: 2,
                borderColor: '#161b22'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { padding: 14, usePointStyle: true } }
            }
        }
    });

    var tingkatLabels = @json(array_keys($rataRataPerTingkat));
    var tingkatData = @json(array_values($rataRataPerTingkat));

    new Chart(document.getElementById('chartSkorTingkat'), {
        type: 'bar',
        data: {
            labels: tingkatLabels,
            datasets: [{
                label: 'Rata-rata Skor',
                data: tingkatData,
                backgroundColor: ['#6c63ff', '#36b9cc', '#f6c23e', '#e74a3b', '#1cc88a'],
                borderRadius: 6,
                barThickness: 36
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, max: 100, ticks: { stepSize: 20 } }
            }
        }
    });

    var jenisLabels = @json(array_keys($rataRataPerJenis));
    var jenisData = @json(array_values($rataRataPerJenis));

    new Chart(document.getElementById('chartSkorJenis'), {
        type: 'bar',
        data: {
            labels: jenisLabels,
            datasets: [{
                label: 'Rata-rata Skor',
                data: jenisData,
                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e'],
                borderRadius: 6,
                barThickness: 36
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, max: 100, ticks: { stepSize: 20 } }
            }
        }
    });
})();
function showDetail(btn) {
    document.getElementById('modal-kode').textContent = btn.getAttribute('data-kode') || '-';
    document.getElementById('modal-nim').textContent = btn.getAttribute('data-nim') || '-';
    document.getElementById('modal-mahasiswa').textContent = btn.getAttribute('data-nama') || '-';
    document.getElementById('modal-lomba').textContent = btn.getAttribute('data-lomba') || '-';
    document.getElementById('modal-penyelenggara').textContent = btn.getAttribute('data-penyelenggara') || '-';
    document.getElementById('modal-jenis').textContent = btn.getAttribute('data-jenis') || '-';
    document.getElementById('modal-tingkat').textContent = btn.getAttribute('data-tingkat') || '-';
    document.getElementById('modal-juara').textContent = btn.getAttribute('data-juara') || '-';
    document.getElementById('modal-tanggal').textContent = btn.getAttribute('data-tanggal') || '-';
    document.getElementById('modal-skor').textContent = btn.getAttribute('data-skor') || '-';
    document.getElementById('modal-jml-prestasi').textContent = btn.getAttribute('data-jml-prestasi') || '-';

    document.getElementById('modal-mf-tingkat-rendah').textContent = btn.getAttribute('data-mf-tingkat-rendah') || '0';
    document.getElementById('modal-mf-tingkat-sedang').textContent = btn.getAttribute('data-mf-tingkat-sedang') || '0';
    document.getElementById('modal-mf-tingkat-tinggi').textContent = btn.getAttribute('data-mf-tingkat-tinggi') || '0';
    document.getElementById('modal-mf-juara-1').textContent = btn.getAttribute('data-mf-juara-1') || '0';
    document.getElementById('modal-mf-juara-2').textContent = btn.getAttribute('data-mf-juara-2') || '0';
    document.getElementById('modal-mf-juara-3+').textContent = btn.getAttribute('data-mf-juara-3-plus') || '0';
    document.getElementById('modal-mf-jml-sedikit').textContent = btn.getAttribute('data-mf-jml-sedikit') || '0';
    document.getElementById('modal-mf-jml-sedang').textContent = btn.getAttribute('data-mf-jml-sedang') || '0';
    document.getElementById('modal-mf-jml-banyak').textContent = btn.getAttribute('data-mf-jml-banyak') || '0';

    var aturanRaw = btn.getAttribute('data-aturan-terpakai');
    if (aturanRaw && aturanRaw !== '[]' && aturanRaw !== '') {
        try {
            var aturan = JSON.parse(aturanRaw);
            var html = '';
            aturan.forEach(function(a) {
                html += a.output + ' (degree: ' + a.degree + ')\n';
            });
            document.getElementById('modal-aturan-terpakai').textContent = html || 'Tidak ada aturan terpakai';
        } catch(e) {
            document.getElementById('modal-aturan-terpakai').textContent = aturanRaw;
        }
    } else {
        document.getElementById('modal-aturan-terpakai').textContent = 'Tidak ada aturan terpakai';
    }

    var kualitas = btn.getAttribute('data-kualitas');
    var badge = '';
    switch(kualitas) {
        case 'Sangat Baik': badge = '<span class="badge badge-success px-3 py-2" style="font-size:1em;">Sangat Baik</span>'; break;
        case 'Baik': badge = '<span class="badge badge-primary px-3 py-2" style="font-size:1em;">Baik</span>'; break;
        case 'Cukup': badge = '<span class="badge badge-warning px-3 py-2" style="font-size:1em;">Cukup</span>'; break;
        case 'Kurang': badge = '<span class="badge badge-danger px-3 py-2" style="font-size:1em;">Kurang</span>'; break;
        default: badge = '<span class="badge badge-secondary px-3 py-2" style="font-size:1em;">Belum dihitung</span>';
    }
    document.getElementById('modal-kualitas').innerHTML = badge;

    $('#detailModal').modal('show');
}

$(document).ready(function() {
    $('#fuzzyTable').DataTable({
        order: [[6, 'desc']],
        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
            emptyTable: "Tidak ada data",
        }
    });
});
</script>
@endpush
