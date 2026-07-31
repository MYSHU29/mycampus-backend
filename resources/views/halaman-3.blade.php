<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data Mahasiswa</title>
</head>
<body>

    <h2>Tambah Data Mahasiswa</h2>

    <form action="/simpan-mahasiswa" method="POST">
        @csrf

        <div>
            <label>Nama:</label><br>
            <input type="text" id="nama" name="nama" placeholder="Nama Mahasiswa">
        </div>

        <br>

        <div>
            <label>Alamat:</label><br>
            <input type="text" id="alamat" name="alamat" placeholder="Alamat Mahasiswa">
        </div>

        <br>

        <div>
            <button type="submit">Simpan</button>
        </div>

    </form>
    <br><br>
    <a href="/halaman-1">kembali ke halaman 1</a>

</body>
</html>