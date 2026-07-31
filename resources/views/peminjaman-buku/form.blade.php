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
        <label>Kode Buku</label>
        <input type="text" name="kode_buku" value="{{ old('kode_buku', $item->kode_buku ?? '') }}" class="form-control" required>
    </div>
    <div class="col-md-3 mb-3">
        <label>Status</label>
        <select name="status" class="form-control" required>
            @foreach(['dipinjam' => 'Dipinjam', 'dikembalikan' => 'Dikembalikan', 'terlambat' => 'Terlambat'] as $value => $label)
                <option value="{{ $value }}" {{ old('status', $item->status ?? '') == $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label>Judul Buku</label>
        <input type="text" name="judul_buku" value="{{ old('judul_buku', $item->judul_buku ?? '') }}" class="form-control" required>
    </div>
    <div class="col-md-6 mb-3">
        <label>Pengarang</label>
        <input type="text" name="pengarang" value="{{ old('pengarang', $item->pengarang ?? '') }}" class="form-control" required>
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <label>Tanggal Pinjam</label>
        <input type="date" name="tanggal_pinjam" value="{{ old('tanggal_pinjam', $item->tanggal_pinjam ?? '') }}" class="form-control" required>
    </div>
    <div class="col-md-4 mb-3">
        <label>Tanggal Kembali Rencana</label>
        <input type="date" name="tanggal_kembali_rencana" value="{{ old('tanggal_kembali_rencana', $item->tanggal_kembali_rencana ?? '') }}" class="form-control" required>
    </div>
    <div class="col-md-4 mb-3">
        <label>Tanggal Kembali Aktual</label>
        <input type="date" name="tanggal_kembali_aktual" value="{{ old('tanggal_kembali_aktual', $item->tanggal_kembali_aktual ?? '') }}" class="form-control">
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <label>Denda</label>
        <input type="number" name="denda" value="{{ old('denda', $item->denda ?? 0) }}" class="form-control" min="0">
    </div>
    <div class="col-md-8 mb-3">
        <label>Keterangan</label>
        <textarea name="keterangan" class="form-control" rows="3">{{ old('keterangan', $item->keterangan ?? '') }}</textarea>
    </div>
</div>

<hr>
<div class="d-flex justify-content-end">
    <a href="{{ route('peminjaman-buku.index') }}" class="btn btn-secondary mr-2"><i class="fas fa-times"></i> Batal</a>
    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
</div>
