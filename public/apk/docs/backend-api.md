# Dokumentasi UAS — Logika Fuzzy: Penilaian Kualitas Prestasi Mahasiswa

**Mata Kuliah:** Pemrograman Web Lanjut
**NIM:** 22101001
**Nama:** (Isi Nama Anda)
**Aplikasi:** MyCampus (Sistem Informasi Akademik) — Laravel 13

---

## 1. Implementasi Logika Fuzzy pada Backend

### 1.1 Kasus dan Klasifikasi

Sistem ini menggunakan logika fuzzy untuk menilai **kualitas prestasi mahasiswa** berdasarkan data verifikasi yang sudah diterima. NIM 22101001 termasuk kategori **ganjil**, sehingga menggunakan skema **Prestasi Mahasiswa**.

### 1.2 Variabel Input dan Output

| Jenis | Variabel | Keterangan |
|-------|----------|------------|
| **Input 1** | Tingkat Kompetisi | Level kompetisi lomba yang diikuti mahasiswa |
| **Input 2** | Peringkat / Juara | Posisi juara yang diraih mahasiswa |
| **Input 3** | Jumlah Prestasi | Total prestasi diterima per mahasiswa |
| **Output** | Skor Kualitas Prestasi | Skor 0–100 yang menentukan kategori kelayakan penghargaan |

### 1.3 Himpunan Fuzzy dan Domain

#### Variabel Input 1: Tingkat Kompetisi

| Himpunan Fuzzy | Nilai Numerik | Keterangan |
|----------------|---------------|------------|
| Rendah | 1 (Kampus), 2 (Kota) | Kompetisi lokal/daerah |
| Sedang | 3 (Provinsi) | Komptisi regional |
| Tinggi | 4 (Nasional), 5 (Internasional) | Kompetisi level nasional/internasional |

**Domain/Universe of discourse:** x ∈ [0, 5]

#### Variabel Input 2: Peringkat / Juara

| Himpunan Fuzzy | Nilai Numerik | Keterangan |
|----------------|---------------|------------|
| Juara 1 | 1 | Juara pertama |
| Juara 2 | 2 | Juara kedua |
| Juara 3 ke atas | 3, 4, 5 dst. | Juara ketiga dan seterusnya |

**Catatan:** Field juara di-parse dari teks bebas (misal "Juara 1 Harapan" → 1.5). Jika tidak ada angka, default = 5.

**Domain/Universe of discourse:** x ∈ [1, 5]

#### Variabel Input 3: Jumlah Prestasi

| Himpunan Fuzzy | Kategori | Keterangan |
|----------------|----------|------------|
| Sedikit | 0–2 | Mahasiswa baru memiliki sedikit prestasi |
| Sedang | 2–6 | Jumlah prestasi cukup representatif |
| Banyak | ≥ 4 | Mahasiswa sangat aktif berprestasi |

**Domain/Universe of discourse:** x ∈ [0, 999] (unbounded)

#### Variabel Output: Skor Kualitas Prestasi

| Himpunan Fuzzy | Centroid | Kategori |
|----------------|----------|----------|
| Kurang | 15 | Tidak memenuhi standar |
| Cukup | 35 | Perlu peningkatan |
| Baik | 55 | Cukup memuaskan |
| Sangat Baik | 85 | Layak penghargaan |

### 1.4 Fungsi Keanggotaan (Membership Functions)

#### Fungsi Segitiga (Triangle)

Rumus umum fungsi segitiga `tri(x, a, b, c)`:

```
         1.0 |       /\
             |      /  \
             |     /    \
             |    /      \
           0 |___/________\___
             a    b        c
```

Rumus matematika:

```
μ(x) = 0,              jika x < a atau x ≥ c
μ(x) = (x - a)/(b - a), jika a ≤ x ≤ b
μ(x) = (c - x)/(c - b), jika b < x < c
```

#### Fungsi Trapesium (Trapezoid)

Rumus umum fungsi trapesium `trap(x, a, b, c, d)`:

```
         1.0 |      /-------\
             |     /         \
             |    /           \
             |   /             \
           0 |__/               \___
             a  b               c  d
```

Rumus matematika:

```
μ(x) = 0,              jika x ≤ a atau x > d
μ(x) = (x - a)/(b - a), jika a < x < b
μ(x) = 1.0,             jika b ≤ x ≤ c
μ(x) = (d - x)/(d - c), jika c < x < d
```

#### Penerapan pada Setiap Variabel

| Variabel | Himpunan | Fungsi | Parameter (a, b, c, d) | Kode PHP |
|----------|----------|--------|------------------------|----------|
| **Tingkat** | Rendah | Trapesium | trap(0, 0, 2, 3) | `trapezoid($x, 0, 0, 2, 3)` |
| | Sedang | Segitiga | tri(2, 3, 4) | `triangle($x, 2, 3, 4)` |
| | Tinggi | Trapesium | trap(3, 5, 5, 5) | `trapezoid($x, 3, 5, 5, 5)` |
| **Juara** | Juara 1 | Segitiga | tri(1, 1, 2) | `triangle($x, 1, 1, 2)` |
| | Juara 2 | Segitiga | tri(1.5, 2, 3) | `triangle($x, 1.5, 2, 3)` |
| | Juara 3+ | Trapesium | trap(2.5, 3, 5, 5) | `trapezoid($x, 2.5, 3, 5, 5)` |
| **Jml Prestasi** | Sedikit | Trapesium | trap(0, 0, 1, 3) | `trapezoid($x, 0, 0, 1, 3)` |
| | Sedang | Segitiga | tri(2, 4, 6) | `triangle($x, 2, 4, 6)` |
| | Banyak | Trapesium | trap(4, 6, 999, 999) | `trapezoid($x, 4, 6, 999, 999)` |

### 1.5 Grafik Fungsi Keanggotaan

#### Tingkat Kompetisi

```
μ(x)
1.0 |  Rendah     Sedang      Tinggi
    | ____        /\
    |/    \      /  \        _________
    |      \    /    \      /
    |       \  /      \    /
  0 |________\/________\__/__________
    0    1    2    3    4    5
        Kampus Kota  Prov  Nas  Intl
```

#### Peringkat / Juara

```
μ(x)
1.0 |  Juara 1  Juara 2  Juara 3+
    |  /\        /\       _________
    | /  \      /  \     /
    |/    \    /    \   /
    |      \  /      \ /
  0 |_______\/________\/___________
    1    1.5   2    2.5  3    5
```

#### Jumlah Prestasi

```
μ(x)
1.0 | Sedikit    Sedang      Banyak
    | ___        /\
    |/   \      /  \       _________
    |     \    /    \     /
    |      \  /      \   /
  0 |_______\/________\_/____________
    0    1   2    4    6   ∞
```

---

## 2. Pembentukan Aturan Fuzzy (Rule Base)

### 2.1 Metode yang Digunakan

Sistem ini menggunakan **metode Mamdani** dengan:
- **Operasi AND:** MIN (minimum dari derajat keanggotaan)
- **Agregasi:** MAX (maximum derajat per output)
- **Defuzzifikasi:** Weighted Average (Rata-rata Tertimbang)

### 2.2 Daftar Aturan (27 Rules)

Aturan ditulis dengan format: **JIKA** [Input1] **DAN** [Input2] **DAN** [Input3] **MAKA** [Output]

#### Kelompok Tingkat Tinggi (Nasional/Internasional)

| No | Aturan |
|----|--------|
| R1 | JIKA tingkat **tinggi** DAN juara **1** DAN jumlah prestasi **banyak** MAKA kualitas = **Sangat Baik** |
| R2 | JIKA tingkat **tinggi** DAN juara **1** DAN jumlah prestasi **sedang** MAKA kualitas = **Sangat Baik** |
| R3 | JIKA tingkat **tinggi** DAN juara **1** DAN jumlah prestasi **sedikit** MAKA kualitas = **Baik** |
| R4 | JIKA tingkat **tinggi** DAN juara **2** DAN jumlah prestasi **banyak** MAKA kualitas = **Sangat Baik** |
| R5 | JIKA tingkat **tinggi** DAN juara **2** DAN jumlah prestasi **sedang** MAKA kualitas = **Sangat Baik** |
| R6 | JIKA tingkat **tinggi** DAN juara **2** DAN jumlah prestasi **sedikit** MAKA kualitas = **Baik** |
| R7 | JIKA tingkat **tinggi** DAN juara **3+** DAN jumlah prestasi **banyak** MAKA kualitas = **Baik** |
| R8 | JIKA tingkat **tinggi** DAN juara **3+** DAN jumlah prestasi **sedang** MAKA kualitas = **Cukup** |
| R9 | JIKA tingkat **tinggi** DAN juara **3+** DAN jumlah prestasi **sedikit** MAKA kualitas = **Cukup** |

#### Kelompok Tingkat Sedang (Kota/Provinsi)

| No | Aturan |
|----|--------|
| R10 | JIKA tingkat **sedang** DAN juara **1** DAN jumlah prestasi **banyak** MAKA kualitas = **Sangat Baik** |
| R11 | JIKA tingkat **sedang** DAN juara **1** DAN jumlah prestasi **sedang** MAKA kualitas = **Sangat Baik** |
| R12 | JIKA tingkat **sedang** DAN juara **1** DAN jumlah prestasi **sedikit** MAKA kualitas = **Baik** |
| R13 | JIKA tingkat **sedang** DAN juara **2** DAN jumlah prestasi **banyak** MAKA kualitas = **Baik** |
| R14 | JIKA tingkat **sedang** DAN juara **2** DAN jumlah prestasi **sedang** MAKA kualitas = **Cukup** |
| R15 | JIKA tingkat **sedang** DAN juara **2** DAN jumlah prestasi **sedikit** MAKA kualitas = **Cukup** |
| R16 | JIKA tingkat **sedang** DAN juara **3+** DAN jumlah prestasi **banyak** MAKA kualitas = **Cukup** |
| R17 | JIKA tingkat **sedang** DAN juara **3+** DAN jumlah prestasi **sedang** MAKA kualitas = **Cukup** |
| R18 | JIKA tingkat **sedang** DAN juara **3+** DAN jumlah prestasi **sedikit** MAKA kualitas = **Kurang** |

#### Kelompok Tingkat Rendah (Kampus)

| No | Aturan |
|----|--------|
| R19 | JIKA tingkat **rendah** DAN juara **1** DAN jumlah prestasi **banyak** MAKA kualitas = **Baik** |
| R20 | JIKA tingkat **rendah** DAN juara **1** DAN jumlah prestasi **sedang** MAKA kualitas = **Cukup** |
| R21 | JIKA tingkat **rendah** DAN juara **1** DAN jumlah prestasi **sedikit** MAKA kualitas = **Cukup** |
| R22 | JIKA tingkat **rendah** DAN juara **2** DAN jumlah prestasi **banyak** MAKA kualitas = **Cukup** |
| R23 | JIKA tingkat **rendah** DAN juara **2** DAN jumlah prestasi **sedang** MAKA kualitas = **Cukup** |
| R24 | JIKA tingkat **rendah** DAN juara **2** DAN jumlah prestasi **sedikit** MAKA kualitas = **Kurang** |
| R25 | JIKA tingkat **rendah** DAN juara **3+** DAN jumlah prestasi **banyak** MAKA kualitas = **Cukup** |
| R26 | JIKA tingkat **rendah** DAN juara **3+** DAN jumlah prestasi **sedang** MAKA kualitas = **Kurang** |
| R27 | JIKA tingkat **rendah** DAN juara **3+** DAN jumlah prestasi **sedikit** MAKA kualitas = **Kurang** |

### 2.3 Ringkasan Pola Aturan

| Tingkat | Juara | Jml Prestasi | → Kualitas |
|---------|-------|--------------|------------|
| Tinggi | 1 | Banyak / Sedang | **Sangat Baik** |
| Tinggi | 1 | Sedikit | **Baik** |
| Tinggi | 2 | Banyak / Sedang | **Sangat Baik** |
| Tinggi | 2 | Sedikit | **Baik** |
| Tinggi | 3+ | Banyak | **Baik** |
| Tinggi | 3+ | Sedang / Sedikit | **Cukup** |
| Sedang | 1 | Banyak / Sedang | **Sangat Baik** |
| Sedang | 1 | Sedikit | **Baik** |
| Sedang | 2 | Banyak | **Baik** |
| Sedang | 2 | Sedang / Sedikit | **Cukup** |
| Sedang | 3+ | Banyak / Sedang | **Cukup** |
| Sedang | 3+ | Sedikit | **Kurang** |
| Rendah | 1 | Banyak | **Baik** |
| Rendah | 1 | Sedang / Sedikit | **Cukup** |
| Rendah | 2 | Banyak / Sedang | **Cukup** |
| Rendah | 2 | Sedikit | **Kurang** |
| Rendah | 3+ | Banyak | **Cukup** |
| Rendah | 3+ | Sedang / Sedikit | **Kurang** |

---

## 3. Proses Inferensi dan Defuzzifikasi

### 3.1 Contoh Kasus: Mahasiswa dengan Data Nyata

| Data | Nilai |
|------|-------|
| Nama Lomba | Lomba Programming tingkat Nasional |
| Tingkat Kompetisi | **Nasional** (nilai 4) |
| Juara yang diraih | **Juara 2** (nilai 2.0) |
| Jumlah Prestasi Diterima | **5** prestasi |

### 3.2 Langkah 1: Fuzzifikasi

Konversi nilai input crisp menjadi derajat keanggotaan (0.0 – 1.0).

#### Fuzzifikasi Tingkat = 4 (Nasional)

```
μ_rendah(4)  = trapezoid(4, 0, 0, 2, 3) = 0      (di luar domain)
μ_sedang(4)  = triangle(4, 2, 3, 4)     = 0       (x = c, di luar domain x < c)
μ_tinggi(4)  = trapezoid(4, 3, 5, 5, 5) = (5-4)/(5-5) = error → perlu dicek

Perhitungan manual trapezoid(4, 3, 5, 5, 5):
  a=3, b=5, c=5, d=5
  x=4 → x > a(3) dan x < b(5) → (x-a)/(b-a) = (4-3)/(5-3) = 0.5

μ_tinggi(4) = 0.5
μ_sedang(4) = triangle(4, 2, 3, 4):
  a=2, b=3, c=4 → x=4 = c → x ≥ c → return 0.0
  → μ_sedang(4) = 0.0

μ_rendah(4) = trapezoid(4, 0, 0, 2, 3):
  a=0, b=0, c=2, d=3 → x=4 > d=3 → return 0.0
  → μ_rendah(4) = 0.0
```

**Hasil Fuzzifikasi Tingkat:**

| Himpunan | Derajat Keanggotaan |
|----------|---------------------|
| Rendah | **0.0** |
| Sedang | **0.0** |
| Tinggi | **0.5** |

#### Fuzzifikasi Juara = 2.0

```
μ_juara_1(2) = triangle(2, 1, 1, 2):
  a=1, b=1, c=2 → x=2 = c → x ≥ c → return 0.0
  → μ_juara_1(2) = 0.0

μ_juara_2(2) = triangle(2, 1.5, 2, 3):
  a=1.5, b=2, c=3 → x=2 = b → x ≤ b → (x-a)/(b-a) = (2-1.5)/(2-1.5) = 1.0
  → μ_juara_2(2) = 1.0

μ_juara_3+(2) = trapezoid(2, 2.5, 3, 5, 5):
  a=2.5 → x=2 ≤ a → return 0.0
  → μ_juara_3+(2) = 0.0
```

**Hasil Fuzzifikasi Juara:**

| Himpunan | Derajat Keanggotaan |
|----------|---------------------|
| Juara 1 | **0.0** |
| Juara 2 | **1.0** |
| Juara 3+ | **0.0** |

#### Fuzzifikasi Jumlah Prestasi = 5

```
μ_sedikit(5) = trapezoid(5, 0, 0, 1, 3):
  a=0, b=0, c=1, d=3 → x=5 > d=3 → return 0.0
  → μ_sedikit(5) = 0.0

μ_sedang(5) = triangle(5, 2, 4, 6):
  a=2, b=4, c=6 → x=5 → x > b(4) dan x < c(6) → (c-x)/(c-b) = (6-5)/(6-4) = 0.5
  → μ_sedang(5) = 0.5

μ_banyak(5) = trapezoid(5, 4, 6, 999, 999):
  a=4, b=6 → x=5 → x > a(4) dan x < b(6) → (x-a)/(b-a) = (5-4)/(6-4) = 0.5
  → μ_banyak(5) = 0.5
```

**Hasil Fuzzifikasi Jumlah Prestasi:**

| Himpunan | Derajat Keanggotaan |
|----------|---------------------|
| Sedikit | **0.0** |
| Sedang | **0.5** |
| Banyak | **0.5** |

### 3.3 Langkah 2: Evaluasi Aturan (Rule Evaluation)

Untuk setiap aturan, hitung derajat keanggotaan output dengan operasi **MIN** (AND):

| Aturan | Kombinasi | Perhitungan MIN | Hasil | Output |
|--------|-----------|-----------------|-------|--------|
| R4 | tinggi + juara_2 + banyak | min(0.5, 1.0, 0.5) | **0.5** | Sangat Baik |
| R5 | tinggi + juara_2 + sedang | min(0.5, 1.0, 0.5) | **0.5** | Sangat Baik |

Aturan lainnya menghasilkan degree = 0 karena setidaknya satu input bernilai 0.

### 3.4 Langkah 3: Agregasi (MAX per Output)

Untuk setiap kategori output, ambil **derajat terbesar** dari semua aturan yang aktif:

| Output Kualitas | Aturan Aktif | MAX Degree |
|-----------------|--------------|------------|
| **Sangat Baik** | R4 (0.5), R5 (0.5) | **0.5** |
| Baik | — | 0.0 |
| Cukup | — | 0.0 |
| Kurang | — | 0.0 |

### 3.5 Langkah 4: Defuzzifikasi (Weighted Average)

Rumus Weighted Average:

```
Skor = Σ(centroid × degree) / Σ(degree)
```

Perhitungan:

```
Skor = (85 × 0.5 + 55 × 0.0 + 35 × 0.0 + 15 × 0.0) / (0.5 + 0.0 + 0.0 + 0.0)
Skor = (42.5) / (0.5)
Skor = 85.0
```

### 3.6 Langkah 5: Threshold Kualitas

| Skor | Kategori | Keterangan |
|------|----------|------------|
| ≥ 65 | **Sangat Baik** | Layak penghargaan |
| ≥ 40 | **Baik** | Cukup memuaskan |
| ≥ 20 | **Cukup** | Perlu peningkatan |
| < 20 | **Kurang** | Tidak memenuhi standar |

**Hasil Akhir:**

```
Skor 85.0 ≥ 65 → Kualitas: SANGAT BAIK
```

**Interpretasi:** Mahasiswa yang menang **Juara 2** di lomba **Nasional** dengan **5 prestasi** diterima mendapat predikat **Sangat Baik** dan **layak mendapatkan penghargaan/beasiswa prestasi**.

---

## 4. Rancangan Implementasi di Laravel

### 4.1 Struktur Arsitektur

```
┌─────────────────────────────────────────────────────────┐
│                    PRESENTATION LAYER                     │
│  ┌──────────────────┐  ┌──────────────────────────────┐  │
│  │  form.blade.php   │  │  fuzzy-kualitas.blade.php     │  │
│  │  (Input Prestasi) │  │  (Dashboard + Detail Fuzzy)   │  │
│  └────────┬─────────┘  └──────────────┬───────────────┘  │
│           │                           │                   │
├───────────┼───────────────────────────┼───────────────────┤
│           │        ROUTE LAYER        │                   │
│           │     routes/web.php        │                   │
├───────────┼───────────────────────────┼───────────────────┤
│           │     CONTROLLER LAYER      │                   │
│  ┌────────┴─────────┐  ┌──────────────┴───────────────┐  │
│  │ PrestasiMhs       │  │  FuzzyPrestasiController      │  │
│  │ Controller        │  │  - index() → dashboard fuzzy  │  │
│  │ - store()         │  │  - hitungUlang() → recompute  │  │
│  │ - update()        │  └──────────────┬───────────────┘  │
│  └────────┬─────────┘                  │                   │
│           │                            │                   │
├───────────┼────────────────────────────┼───────────────────┤
│           │       SERVICE LAYER        │                   │
│           │  ┌─────────────────────────┴──────────────┐   │
│           │  │     FuzzyPrestasiService                 │   │
│           │  │  - hitungSkor()                          │   │
│           │  │  - evaluasiSemua()                       │   │
│           │  │  - fuzzifikasiTingkat()                  │   │
│           │  │  - fuzzifikasiJuara()                    │   │
│           │  │  - fuzzifikasiJumlahPrestasi()           │   │
│           │  │  - evaluasiAturan()                      │   │
│           │  │  - defuzzifikasi()                       │   │
│           │  │  - triangle() / trapezoid()              │   │
│           │  └──────────────────┬──────────────────────┘   │
│           │                     │                           │
├───────────┼─────────────────────┼───────────────────────────┤
│           │       MODEL LAYER   │                           │
│  ┌────────┴────────┐  ┌────────┴────────┐  ┌────────────┐  │
│  │ PrestasiMahasiswa│  │  FuzzyHasil     │  │  Mahasiswa  │  │
│  │ (prestasi)       │  │  (fuzzy_hasil)  │  │  (mahasiswa)│  │
│  └────────┬────────┘  └────────┬────────┘  └──────┬─────┘  │
│           │                    │                   │        │
├───────────┼────────────────────┼───────────────────┼────────┤
│           │      DATABASE LAYER│                   │        │
│  ┌────────┴────────┐  ┌───────┴────────┐  ┌──────┴─────┐  │
│  │    prestasi      │  │  fuzzy_hasil    │  │  mahasiswa  │  │
│  └─────────────────┘  └────────────────┘  └────────────┘  │
└─────────────────────────────────────────────────────────────┘
```

### 4.2 ERD (Entity Relationship Diagram)

```
┌──────────────────┐       ┌──────────────────┐
│   mahasiswa       │       │  jenis_prestasi   │
├──────────────────┤       ├──────────────────┤
│ nim (PK)         │       │ id_jenis (PK)    │
│ nama             │       │ nama_jenis       │
│ ...              │       └────────┬─────────┘
└────────┬─────────┘                │
         │                          │
         │ 1:N                      │ N:1
         │                          │
┌────────┴──────────────────────────┴─────────┐
│              prestasi (renamed dari           │
│              prestasi_mahasiswa)              │
├──────────────────────────────────────────────┤
│ id_prestasi (PK, ULID)                      │
│ kode_prestasi (UNIQUE)                       │
│ nim (FK → mahasiswa.nim)                     │
│ id_jenis (FK → jenis_prestasi.id_jenis)      │
│ id_tingkat (FK → tingkat_prestasi.id_tingkat)│
│ nama_lomba                                   │
│ penyelenggara                                │
│ tanggal                                      │
│ juara (teks bebas)                           │
│ sertifikat (file path)                       │
│ status_verifikasi (enum)                     │
│ skor_fuzzy (float) ← hasil fuzzy             │
│ kualitas_fuzzy (string) ← hasil fuzzy        │
│ created_at / updated_at                      │
└────────┬────────────┬────────────────────────┘
         │            │
         │ 1:1        │ 1:1
         │            │
┌────────┴────────┐  ┌┴───────────────────────┐
│ verifikasi_      │  │      fuzzy_hasil        │
│ prestasi         │  ├────────────────────────┤
├──────────────────┤  │ id_fuzzy_hasil (PK)    │
│ id_verifikasi    │  │ id_prestasi (FK, ULID) │
│ id_prestasi (FK) │  │ nim (FK → mahasiswa)   │
│ id_admin (FK)    │  │ tingkat_prestasi       │
│ tanggal_verif.   │  │ juara (float)          │
│ catatan          │  │ jumlah_prestasi        │
└──────────────────┘  │ mf_tingkat_rendah      │
                      │ mf_tingkat_sedang      │
┌──────────────────┐  │ mf_tingkat_tinggi      │
│ tingkat_prestasi  │  │ mf_juara_1             │
├──────────────────┤  │ mf_juara_2             │
│ id_tingkat (PK)  │  │ mf_juara_3_plus        │
│ nama_tingkat     │  │ mf_jml_sedikit         │
└──────────────────┘  │ mf_jml_sedang          │
                      │ mf_jml_banyak          │
┌──────────────────┐  │ aturan_terpakai (JSON) │
│ admin_prestasi    │  │ skor_fuzzy             │
├──────────────────┤  │ kualitas_fuzzy         │
│ id_admin (PK)    │  │ created_at/updated_at  │
│ nama             │  └────────────────────────┘
│ email            │
│ password         │
│ role             │
└──────────────────┘
```

### 4.3 Model dan Relasi

| Model | Tabel | Relasi | Keterangan |
|-------|-------|--------|------------|
| `Mahasiswa` | mahasiswa | hasMany → PrestasiMahasiswa | Satu mahasiswa punya banyak prestasi |
| `PrestasiMahasiswa` | prestasi | belongsTo → Mahasiswa, JenisPrestasi, TingkatPrestasi | Data prestasi mahasiswa |
| | | hasOne → VerifikasiPrestasi | Status verifikasi |
| | | hasOne → FuzzyHasil | Hasil perhitungan fuzzy |
| `FuzzyHasil` | fuzzy_hasil | belongsTo → PrestasiMahasiswa, Mahasiswa | Detail fuzzifikasi + skor |
| `JenisPrestasi` | jenis_prestasi | hasMany → PrestasiMahasiswa | Akademik, Non Akademik, dll |
| `TingkatPrestasi` | tingkat_prestasi | hasMany → PrestasiMahasiswa | Kampus, Kota, Provinsi, dst |
| `VerifikasiPrestasi` | verifikasi_prestasi | belongsTo → PrestasiMahasiswa, AdminPrestasi | Catatan verifikasi admin |

### 4.4 Migration (Schema Tabel `fuzzy_hasil`)

```php
Schema::create('fuzzy_hasil', function (Blueprint $table) {
    $table->id('id_fuzzy_hasil');
    $table->ulid('id_prestasi');
    $table->string('nim', 20);

    // Input fuzzified (nilai numerik)
    $table->float('tingkat_prestasi');
    $table->float('juara');
    $table->integer('jumlah_prestasi');

    // Derajat keanggotaan Tingkat
    $table->float('mf_tingkat_rendah')->default(0);
    $table->float('mf_tingkat_sedang')->default(0);
    $table->float('mf_tingkat_tinggi')->default(0);

    // Derajat keanggotaan Juara
    $table->float('mf_juara_1')->default(0);
    $table->float('mf_juara_2')->default(0);
    $table->float('mf_juara_3_plus')->default(0);

    // Derajat keanggotaan Jumlah Prestasi
    $table->float('mf_jml_sedikit')->default(0);
    $table->float('mf_jml_sedang')->default(0);
    $table->float('mf_jml_banyak')->default(0);

    // Hasil inferensi
    $table->text('aturan_terpakai')->nullable(); // JSON
    $table->float('skor_fuzzy');
    $table->string('kualitas_fuzzy', 50);
    $table->timestamps();

    // Foreign keys
    $table->foreign('id_prestasi')
          ->references('id_prestasi')->on('prestasi')->cascadeOnDelete();
    $table->foreign('nim')
          ->references('nim')->on('mahasiswa')->cascadeOnDelete();
});
```

### 4.5 Alur Logika Program (Flowchart)

```
┌─────────────────────┐
│  User Input Data     │
│  Prestasi Mahasiswa  │
│  (Form: NIM, Tingkat,│
│   Juara, Lomba, dll) │
└─────────┬───────────┘
          │
          ▼
┌─────────────────────┐
│  Simpan ke Tabel     │
│  prestasi            │
│  Status: menunggu    │
└─────────┬───────────┘
          │
          ▼
┌─────────────────────┐     ┌──────────────┐
│  Admin Verifikasi    │────▶│  Status:      │
│  (diterima/ditolak)  │     │  diterima     │
└─────────┬───────────┘     └──────┬───────┘
          │ (hanya diterima)        │
          ▼                         │
┌─────────────────────┐             │
│  Hitung Jumlah       │◀────────────┘
│  Prestasi per NIM    │
│  (count diterima)    │
└─────────┬───────────┘
          │
          ▼
┌─────────────────────────────────────┐
│  FUZZIFICATION (FuzzyPrestasiService)│
│                                      │
│  1. Parse input:                      │
│     - Tingkat → angka (1-5)          │
│     - Juara → angka (parse teks)     │
│     - Jml Prestasi → count           │
│                                      │
│  2. Hitung derajat keanggotaan:      │
│     - fuzzifikasiTingkat(x)         │
│     - fuzzifikasiJuara(x)           │
│     - fuzzifikasiJumlahPrestasi(x)   │
└─────────┬───────────────────────────┘
          │
          ▼
┌─────────────────────────────────────┐
│  RULE EVALUATION                     │
│                                      │
│  Untuk setiap aturan (27 rules):     │
│    degree = MIN(mf_tingkat,          │
│                 mf_juara,            │
│                 mf_jumlah)           │
│    Jika degree > 0 → aturan aktif   │
│                                      │
│  Agregasi: MAX per output kualitas   │
└─────────┬───────────────────────────┘
          │
          ▼
┌─────────────────────────────────────┐
│  DEFUZZIFICATION                     │
│                                      │
│  Skor = Σ(centroid × degree)         │
│         ─────────────────            │
│            Σ(degree)                 │
│                                      │
│  Centroid: Kurang=15, Cukup=35,     │
│            Baik=55, Sangat Baik=85   │
│                                      │
│  Threshold:                          │
│    ≥ 65 → Sangat Baik               │
│    ≥ 40 → Baik                      │
│    ≥ 20 → Cukup                     │
│    < 20 → Kurang                     │
└─────────┬───────────────────────────┘
          │
          ▼
┌─────────────────────────────────────┐
│  SIMPAN HASIL                        │
│                                      │
│  1. Simpan ke fuzzy_hasil            │
│     (semua MF + skor + kualitas)     │
│                                      │
│  2. Update kolom prestasi:           │
│     - skor_fuzzy                     │
│     - kualitas_fuzzy                 │
└─────────┬───────────────────────────┘
          │
          ▼
┌─────────────────────────────────────┐
│  TAMPILKAN HASIL                     │
│  - Dashboard fuzzy-kualitas          │
│  - Chart.js visualizations           │
│  - Tabel detail per prestasi         │
│  - Modal detail fuzzifikasi          │
└─────────────────────────────────────┘
```

### 4.6 Implementasi Kode

#### Service Layer (FuzzyPrestasiService.php)

| Metode | Fungsi | Lokasi |
|--------|--------|--------|
| `hitungSkor()` | Fungsi utama: fuzzifikasi → inferensi → defuzzifikasi | `app/Services/FuzzyPrestasiService.php:26` |
| `evaluasiSemua()` | Evaluasi semua prestasi diterima secara batch | `app/Services/FuzzyPrestasiService.php:79` |
| `parseJuara()` | Parse teks juara menjadi angka | `app/Services/FuzzyPrestasiService.php:111` |
| `fuzzifikasiTingkat()` | Hitung MF untuk tingkat kompetisi | `app/Services/FuzzyPrestasiService.php:124` |
| `fuzzifikasiJuara()` | Hitung MF untuk peringkat juara | `app/Services/FuzzyPrestasiService.php:133` |
| `fuzzifikasiJumlahPrestasi()` | Hitung MF untuk jumlah prestasi | `app/Services/FuzzyPrestasiService.php:142` |
| `evaluasiAturan()` | Evaluasi 27 aturan dengan MIN-MAX | `app/Services/FuzzyPrestasiService.php:151` |
| `defuzzifikasi()` | Weighted average + threshold | `app/Services/FuzzyPrestasiService.php:199` |
| `triangle()` | Fungsi keanggotaan segitiga | `app/Services/FuzzyPrestasiService.php:226` |
| `trapezoid()` | Fungsi keanggotaan trapesium | `app/Services/FuzzyPrestasiService.php:237` |

#### Controller Layer

| Controller | Metode | Fungsi |
|------------|--------|--------|
| `PrestasiMahasiswaController` | `store()` | Simpan data prestasi baru |
| | `update()` | Update data + verifikasi |
| `FuzzyPrestasiController` | `index()` | Dashboard kualitas fuzzy |
| | `hitungUlang()` | Hitung ulang semua skor |

#### Route

```php
// Tampilan dashboard fuzzy
Route::get('prestasi-mahasiswa/fuzzy-kualitas',
    [FuzzyPrestasiController::class, 'index'])
    ->name('prestasi-mahasiswa.fuzzy-kualitas');

// Hitung ulang semua skor fuzzy
Route::post('prestasi-mahasiswa/fuzzy-kualitas/hitung',
    [FuzzyPrestasiController::class, 'hitungUlang'])
    ->name('prestasi-mahasiswa.fuzzy-hitung');
```

---

## 5. Analisis dan Kesimpulan

### 5.1 Mengapa Metode Mamdani Dipilih

Metode **Mamdani** dipilih untuk kasus ini karena:

| Aspek | Mamdani | Tsukamoto |
|-------|---------|-----------|
| **Output** | Himpunan fuzzy (linguistik) | Crisp value (angka) |
| **Interpretasi** | Mudah dipahami (Sangat Baik, Baik, Cukup, Kurang) | Kurang intuitif |
| **Aturan** | IF-THEN dengan output himpunan fuzzy | IF-THEN dengan output fungsi keanggotaan terbalik |
| **Cocok untuk** | Penilaian kualitas berbasis kategori | Optimasi numerik |
| **Kompleksitas** | Lebih sederhana untuk 3 input × 3 himpunan | Lebih kompleks untuk banyak aturan |

**Alasan utama Mamdani dipilih:**
1. **Output berbentuk linguistik** — "Sangat Baik", "Baik", "Cukup", "Kurang" mudah dipahami oleh user (operator/admin).
2. **Jumlah aturan terbatas** — 27 aturan (3³) masih reasonable untuk ditampilkan dan dijelaskan.
3. **Transparansi** — Setiap aturan bisa dijelaskan dalam bahasa manusia ("JIKA tingkat tinggi DAN juara 1... MAKA Sangat Baik").
4. **Audit trail** — Sistem menyimpan semua derajat keanggotaan dan aturan yang terpakai di tabel `fuzzy_hasil`, sehingga proses perhitungan bisa ditelusuri.

### 5.2 Manfaat Logika Fuzzy vs Konvensional (If-Else Biasa)

#### Pendekatan Konvensional (If-Else)

```php
// Contoh pendekatan konvensional
if ($tingkat == 'Nasional' && $juara == 1 && $jumlahPrestasi >= 4) {
    $kualitas = 'Sangat Baik';
} elseif ($tingkat == 'Nasional' && $juara == 1) {
    $kualitas = 'Baik';
} elseif ($tingkat == 'Kota' && $juara == 1 && $jumlahPrestasi >= 4) {
    $kualitas = 'Sangat Baik';
} // ... dst (perlu banyak kombinasi)
```

#### Pendekatan Fuzzy

```php
// Sistem fuzzy menghitung otomatis berdasarkan derajat keanggotaan
$skor = $fuzzyService->hitungSkor($tingkat, $juara, $jumlahPrestasi);
```

#### Perbandingan

| Aspek | If-Else Konvensional | Logika Fuzzy |
|-------|---------------------|--------------|
| **Batas kategori** | Tegas (hard boundary): 3 prestasi ≠ 4 prestasi | Gradual: 3.9 prestasi hampir = 4 prestasi |
| **Kombinasi input** | Perlu if-else manual untuk setiap kombinasi (3×3×3 = 27 kombinasi) | Otomatis via rule base (27 aturan × 3 MF) |
| **Penanganan tepi** | Tidak ada: Juara 1.9 dianggap sama dengan Juara 2 | Juara 1.9 → sebagian "Juara 1" dan sebagian "Juara 2" |
| **Skor output** | Hanya kategori (Sangat Baik / Baik / Cukup / Kurang) | Skor numerik (0-100) + kategori → bisa ranking |
| **Transparansi** | Sulit dipahami jika banyak kondisi | Aturan mudah dibaca dalam bahasa manusia |
| **Maintenance** | Sulit menambah/mengubah aturan | Tambah/ubah aturan tanpa mengubah logika lain |
| **Akurasi** | Kurang presisi untuk nilai di batas kategori | Lebih presisi karena interpolasi antar himpunan |

#### Contoh Nyata Perbedaan

**Kasus:** Mahasiswa Juara 2 di tingkat Nasional dengan 3 prestasi

| Metode | Hasil | Penjelasan |
|--------|-------|------------|
| **If-Else** | "Baik" | Karena aturan hardcode: `if ($juara == 2 && $tingkat == 'Nasional') return 'Baik'` |
| **Fuzzy** | Skor 67.5 → **Sangat Baik** | Karena derajat keanggotaan Juara 2 = 1.0 dan Tinggi = 0.5, yang menghasilkan aturan R4/R5 dengan degree 0.5 → skor weighted average = 85 × 0.5 / 0.5 = 85 |

Fuzzy lebih adil karena mempertimbangkan **semua faktor secara bersamaan** dengan bobot yang proporsional.

### 5.3 Kesimpulan

1. **Logika fuzzy Mamdani** berhasil diimplementasikan dengan 3 variabel input (Tingkat Kompetisi, Peringkat Juara, Jumlah Prestasi) dan 1 variabel output (Skor Kualitas Prestasi).

2. **27 aturan fuzzy** mencakup semua kombinasi kemungkinan input, menghasilkan 4 kategori output: Sangat Baik, Baik, Cukup, Kurang.

3. **Defuzzifikasi Weighted Average** menghasilkan skor numerik (0-100) yang memungkinkan ranking dan perbandingan antar mahasiswa, bukan sekadar kategori.

4. **Implementasi Laravel** menggunakan arsitektur Service Layer (`FuzzyPrestasiService`) yang terpisah dari Controller, sehingga logika fuzzy mudah diuji dan dipelihara.

5. **Tabel `fuzzy_hasil`** menyimpan audit trail lengkap (semua derajat keanggotaan + aturan terpakai), sehingga proses perhitungan transparan dan bisa ditelusuri.

6. **Manfaat utama** fuzzy dibandingkan konvensional: lebih adil untuk nilai di batas kategori, transparan (aturan bisa dibaca manusia), dan mudah dimodifikasi tanpa mengubah logika lain.
