@php
    $item = $item ?? null;
    $canEditData = ! $item || auth()->user()?->hasPermission('prestasi.manage');
@endphp

<div class="row">
    <div class="col-md-6 mb-3">
        <label>Mahasiswa</label>
        <select name="nim" class="form-control" required {{ $canEditData ? '' : 'disabled' }}>
            <option value="">-- Pilih Mahasiswa --</option>
            @foreach($mahasiswa as $mhs)
                <option value="{{ $mhs->nim }}" {{ old('nim', $item->nim ?? '') == $mhs->nim ? 'selected' : '' }}>
                    {{ $mhs->nim }} - {{ $mhs->nama }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3 mb-3">
        <label>Jenis Prestasi</label>
        <select name="id_jenis" class="form-control" required {{ $canEditData ? '' : 'disabled' }}>
            <option value="">-- Pilih Jenis --</option>
            @foreach($jenisPrestasi as $jenis)
                <option value="{{ $jenis->id_jenis }}" {{ old('id_jenis', $item->id_jenis ?? '') == $jenis->id_jenis ? 'selected' : '' }}>
                    {{ $jenis->nama_jenis }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3 mb-3">
        <label>Tingkat Prestasi</label>
        <select name="id_tingkat" class="form-control" required {{ $canEditData ? '' : 'disabled' }}>
            <option value="">-- Pilih Tingkat --</option>
            @foreach($tingkatPrestasi as $tingkat)
                <option value="{{ $tingkat->id_tingkat }}" {{ old('id_tingkat', $item->id_tingkat ?? '') == $tingkat->id_tingkat ? 'selected' : '' }}>
                    {{ $tingkat->nama_tingkat }}
                </option>
            @endforeach
        </select>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label>Nama Lomba/Kegiatan</label>
        <input type="text" name="nama_lomba" value="{{ old('nama_lomba', $item->nama_lomba ?? '') }}" class="form-control" required {{ $canEditData ? '' : 'disabled' }}>
    </div>
    <div class="col-md-6 mb-3">
        <label>Penyelenggara</label>
        <input type="text" name="penyelenggara" value="{{ old('penyelenggara', $item->penyelenggara ?? '') }}" class="form-control" required {{ $canEditData ? '' : 'disabled' }}>
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <label>Tanggal</label>
        <input type="date" name="tanggal" value="{{ old('tanggal', $item->tanggal ?? '') }}" class="form-control" required {{ $canEditData ? '' : 'disabled' }}>
    </div>
    <div class="col-md-4 mb-3">
        <label>Juara</label>
        <input type="text" name="juara" value="{{ old('juara', $item->juara ?? '') }}" class="form-control" placeholder="Juara 1 / Finalis / Peserta" required {{ $canEditData ? '' : 'disabled' }}>
    </div>
    <div class="col-md-4 mb-3">
        @if($item)
            <label>Status Saat Ini</label>
            <input type="hidden" name="status_verifikasi" value="{{ $item->status_verifikasi }}">
            <input type="text" value="{{ ucfirst($item->status_verifikasi) }}" class="form-control" disabled>
        @else
            <label>Status Verifikasi</label>
            @if(auth()->user()?->hasPermission('prestasi.verify'))
                <select name="status_verifikasi" class="form-control" required>
                    @foreach(['menunggu' => 'Menunggu', 'diterima' => 'Diterima', 'ditolak' => 'Ditolak'] as $value => $label)
                        <option value="{{ $value }}" {{ old('status_verifikasi', $item->status_verifikasi ?? 'menunggu') == $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            @else
                <input type="hidden" name="status_verifikasi" value="menunggu">
                <input type="text" value="Menunggu" class="form-control" disabled>
            @endif
        @endif
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label>Sertifikat</label>
        @if($item && $item->sertifikat)
            <div class="mb-2"><a href="{{ asset('storage/' . $item->sertifikat) }}" target="_blank">Lihat sertifikat saat ini</a></div>
        @endif
        @if($canEditData)
            <input type="file" name="sertifikat" class="form-control-file" accept=".pdf,image/*">
            <small class="text-muted">Format: PDF/JPG/PNG, maksimal 2 MB.</small>
        @endif
    </div>
</div>

@if(! $item)
    <hr>
    <div class="d-flex justify-content-end">
        <a href="{{ route('prestasi-mahasiswa.index') }}" class="btn btn-secondary mr-2"><i class="fas fa-times"></i> Batal</a>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
    </div>
@endif
