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
    <div class="col-md-6 mb-3">
        <label>Kode Bayar</label>
        <input type="text" name="kode_bayar" value="{{ old('kode_bayar', $item->kode_bayar ?? '') }}" class="form-control" required>
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
    <div class="col-md-6 mb-3">
        <label>Jumlah Bayar</label>
        <input type="number" name="jumlah_bayar" value="{{ old('jumlah_bayar', $item->jumlah_bayar ?? '') }}" class="form-control" min="0" step="0.01" required>
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <label>Metode Bayar</label>
        <select name="metode_bayar" class="form-control" required>
            @foreach(['tunai' => 'Tunai', 'transfer' => 'Transfer', 'va' => 'Virtual Account'] as $value => $label)
                <option value="{{ $value }}" {{ old('metode_bayar', $item->metode_bayar ?? '') == $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4 mb-3">
        <label>Status Bayar</label>
        <select name="status_bayar" class="form-control" required>
            @foreach(['lunas' => 'Lunas', 'belum' => 'Belum', 'cicil' => 'Cicil'] as $value => $label)
                <option value="{{ $value }}" {{ old('status_bayar', $item->status_bayar ?? '') == $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4 mb-3">
        <label>Tanggal Bayar</label>
        <input type="date" name="tanggal_bayar" value="{{ old('tanggal_bayar', $item->tanggal_bayar ?? '') }}" class="form-control">
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label>Bukti Bayar</label>
        @if($item && $item->bukti_bayar)
            <div class="mb-2"><a href="{{ asset('storage/' . $item->bukti_bayar) }}" target="_blank">Lihat bukti saat ini</a></div>
        @endif
        <input type="file" name="bukti_bayar" class="form-control-file" accept="image/*">
    </div>
    <div class="col-md-6 mb-3">
        <label>Keterangan</label>
        <textarea name="keterangan" class="form-control" rows="3">{{ old('keterangan', $item->keterangan ?? '') }}</textarea>
    </div>
</div>

<hr>
<div class="d-flex justify-content-end">
    <a href="{{ route('pembayaran-spp.index') }}" class="btn btn-secondary mr-2"><i class="fas fa-times"></i> Batal</a>
    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
</div>
