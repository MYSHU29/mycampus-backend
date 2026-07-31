@php
    $item = $item ?? null;
@endphp

<div class="row">
    <div class="col-md-6 mb-3">
        <label>Mahasiswa</label>
        <select name="nim" class="form-control" required>
            <option value="">-- Pilih Mahasiswa --</option>
            @foreach($mahasiswa as $mhs)
                <option value="{{ $mhs->nim }}" {{ old('nim', $item->nim ?? '') == $mhs->nim ? 'selected' : '' }}>
                    {{ $mhs->nim }} - {{ $mhs->nama }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3 mb-3">
        <label>Kode Matakuliah</label>
        <input type="text" name="kode_matkul" value="{{ old('kode_matkul', $item->kode_matkul ?? '') }}" class="form-control" required>
    </div>
    <div class="col-md-3 mb-3">
        <label>SKS</label>
        <input type="number" name="sks" value="{{ old('sks', $item->sks ?? '') }}" class="form-control" min="1" max="6" required>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label>Nama Matakuliah</label>
        <input type="text" name="nama_matkul" value="{{ old('nama_matkul', $item->nama_matkul ?? '') }}" class="form-control" required>
    </div>
    <div class="col-md-6 mb-3">
        <label>Dosen</label>
        <input type="text" name="dosen" value="{{ old('dosen', $item->dosen ?? '') }}" class="form-control" required>
    </div>
</div>

<div class="row">
    <div class="col-md-3 mb-3">
        <label>Semester</label>
        <input type="number" name="semester" value="{{ old('semester', $item->semester ?? '') }}" class="form-control" min="1" max="14" required>
    </div>
    <div class="col-md-3 mb-3">
        <label>Tahun Akademik</label>
        <input type="number" name="tahun_akademik" value="{{ old('tahun_akademik', $item->tahun_akademik ?? '') }}" class="form-control" min="2000" max="2099" required>
    </div>
    <div class="col-md-3 mb-3">
        <label>Status</label>
        <select name="status" class="form-control" required>
            @foreach(['aktif' => 'Aktif', 'mengulang' => 'Mengulang', 'lulus' => 'Lulus'] as $value => $label)
                <option value="{{ $value }}" {{ old('status', $item->status ?? '') == $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3 mb-3">
        <label>Grade</label>
        <select name="grade" class="form-control">
            <option value="">-- Kosong --</option>
            @foreach(['A', 'B+', 'B', 'C+', 'C', 'D', 'E'] as $grade)
                <option value="{{ $grade }}" {{ old('grade', $item->grade ?? '') == $grade ? 'selected' : '' }}>{{ $grade }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <label>Nilai Akhir</label>
        <input type="number" name="nilai_akhir" value="{{ old('nilai_akhir', $item->nilai_akhir ?? '') }}" class="form-control" min="0" max="100" step="0.01">
    </div>
</div>

<hr>
<div class="d-flex justify-content-end">
    <a href="{{ route('pengambilan-matakuliah.index') }}" class="btn btn-secondary mr-2"><i class="fas fa-times"></i> Batal</a>
    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
</div>
