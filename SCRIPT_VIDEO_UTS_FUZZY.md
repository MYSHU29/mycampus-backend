# SCRIPT VIDEO UTS — Implementasi Logika Fuzzy pada Sistem Penilaian Kualitas Prestasi Mahasiswa

## Informasi Umum

- **Mata Kuliah**: Sistem Informasi / Rekayasa Perangkat Lunak
- **Kasus**: NIM Ganjil — Prestasi Mahasiswa
- **Metode**: Mamdani Fuzzy Inference System (FIS)
- **Aplikasi**: MyCampus (Laravel 13, PHP 8.3, Tailwind CSS v4)
- **URL**: `http://localhost:8000/prestasi-mahasiswa/fuzzy-kualitas`

---

---

# BAGIAN 1: LOGIKA FUZZY — Definisi Variabel, Himpunan Fuzzy, dan Fungsi Keanggotaan

---

### NARASI (Teks yang Diucapkan)

> "Assalamualaikum warahmatullahi wabarakatuh. Pada kesempatan kali ini, saya akan menjelaskan implementasi logika fuzzy pada sistem informasi akademik MyCampus, khususnya untuk menilai kualitas prestasi mahasiswa.
>
> Logika fuzzy dipilih karena penilaian prestasi mahasiswa bersifat gradasi — tidak ada batas tegas antara prestasi yang 'baik' dan 'sangat baik'. Dengan fuzzy, kita bisa memetakan data numerik seperti tingkat kompetisi, peringkat juara, dan jumlah prestasi menjadi skor kualitas yang lebih nuanced dan adil.
>
> Sistem ini menggunakan metode **Mamdani** dengan **3 variabel input** dan **1 variabel output**."

---

### PANDUAN LAYAR

1. **Buka browser** → navigasi ke `http://localhost:8000/prestasi-mahasiswa/fuzzy-kualitas`
2. **Login** sebagai admin (email: `admin@mycampus.test`, password: `password`)
3. **Tampilkan halaman Dashboard Fuzzy** — tunjukkan bagian dokumentasi di bawah tabel data
4. **Scroll ke bagian "Metodologi"** di halaman fuzzy-kualitas (ada penjelasan lengkap di view)
5. **Tampilkan slide/diagram** berikut yang dibuat secara visual:

---

### MATERI PENDUKUNG

#### 1.1 Variabel Input

| No | Variabel Input | Simbol | Domain (Universe of Discourse) | Satuan |
|----|---------------|--------|-------------------------------|--------|
| 1 | Tingkat Prestasi | X₁ | [1, 5] | Level kompetisi |
| 2 | Juara (Peringkat) | X₂ | [1, 5] | Peringkat diraih |
| 3 | Jumlah Prestasi | X₃ | [1, ∞) | Jumlah prestasi diterima |

**Keterangan domain Tingkat Prestasi:**
| Nilai | Keterangan |
|-------|-----------|
| 1 | Kampus |
| 2 | Kota |
| 3 | Provinsi |
| 4 | Nasional |
| 5 | Internasional |

**Keterangan domain Juara:**
- Angka diambil dari string juara (misal "Juara 2" → 2)
- Jika mengandung kata "Harapan", ditambah 0.5 (misal "Juara Harapan 1" → 1.5)
- Jika tidak ada angka (misal "Finalis"), default = 5.0

**Keterangan domain Jumlah Prestasi:**
- Diambil dari total jumlah prestasi mahasiswa yang berstatus "diterima" di sistem

#### 1.2 Variabel Output

| No | Variabel Output | Simbol | Domain | Satuan |
|----|----------------|--------|--------|--------|
| 1 | Skor Kualitas Prestasi | Y | [0, 100] | Skor 0-100 |

| Label Output | Rentang Skor | Keterangan |
|-------------|-------------|-----------|
| Sangat Baik | ≥ 65 | Prestasi sangat memenuhi kriteria |
| Baik | 40 – 64 | Prestasi memenuhi kriteria |
| Cukup | 20 – 39 | Prestasi cukup memenuhi kriteria |
| Kurang | < 20 | Prestasi kurang memenuhi kriteria |

#### 1.3 Himpunan Fuzzy dan Fungsi Keanggotaan

---

##### Variabel 1: Tingkat Prestasi (X₁) — Domain [1, 5]

| Himpunan | Jenis Fungsi | Parameter (a, b, c, d) | rumus |
|----------|-------------|----------------------|-------|
| Rendah | Trapesium | (0, 0, 2, 3) | turun dari 1→0 pada x=[2,3] |
| Sedang | Segitiga | (2, 3, 4) | naik dari 0→1 pada x=[2,3], turun 1→0 pada x=[3,4] |
| Tinggi | Trapesium | (3, 5, 5, 5) | naik dari 0→1 pada x=[3,5], penuh pada x≥5 |

**Rumus Fungsi Keanggotaan:**

**Trapesium** `trap(x; a, b, c, d)`:

```
         1 ┌──────────┐
           │          │
         0 ┘          └───────
           a    b    c    d
```

```
         0                    , jika x ≤ a
         (x - a) / (b - a)   , jika a < x < b
μ(x)  =  1                    , jika b ≤ x ≤ c
         (d - x) / (d - c)   , jika c < x < d
         0                    , jika x ≥ d
```

**Segitiga** `tri(x; a, b, c)`:

```
              /\
             /  \
           0/    \0
           a  b  c
```

```
         0                    , jika x < a atau x ≥ c
         (x - a) / (b - a)   , jika a ≤ x ≤ b
μ(x)  =
         (c - x) / (c - b)   , jika b < x < c
```

**Contoh perhitungan Tingkat = Provinsi (x = 3):**
- μ_rendah(3) = trap(3; 0, 0, 2, 3) = (3-3)/(3-2) = **0**
- μ_sedang(3) = tri(3; 2, 3, 4) = (3-2)/(3-2) = **1.0**
- μ_tinggi(3) = trap(3; 3, 5, 5, 5) = 3 ≤ 3 → **0**

---

##### Variabel 2: Juara / Peringkat (X₂) — Domain [1, 5]

| Himpunan | Jenis Fungsi | Parameter (a, b, c) / (a, b, c, d) | Keterangan |
|----------|-------------|-------------------------------------|-----------|
| Juara 1 | Segitiga | (1, 1, 2) | puncak di x=1 |
| Juara 2 | Segitiga | (1.5, 2, 3) | puncak di x=2 |
| Juara 3+ | Trapesium | (2.5, 3, 5, 5) | penuh dari x≥3 |

**Contoh perhitungan Juara = 1 (x = 1):**
- μ_juara_1(1) = tri(1; 1, 1, 2) → a=b=1, return **1.0** (kasus degenerate)
- μ_juara_2(1) = tri(1; 1.5, 2, 3) → 1 < 1.5 → **0**
- μ_juara_3_plus(1) = trap(1; 2.5, 3, 5, 5) → 1 ≤ 2.5 → **0**

---

##### Variabel 3: Jumlah Prestasi (X₃) — Domain [1, ∞)

| Himpunan | Jenis Fungsi | Parameter (a, b, c, d) | Keterangan |
|----------|-------------|----------------------|-----------|
| Sedikit | Trapesium | (0, 0, 1, 3) | penuh ≤ 1, turun ke 0 di x=3 |
| Sedang | Segitiga | (2, 4, 6) | puncak di x=4 |
| Banyak | Trapesium | (4, 6, 999, 999) | naik dari x=4, penuh dari x≥6 |

**Contoh perhitungan Jumlah Prestasi = 3 (x = 3):**
- μ_sedikit(3) = trap(3; 0, 0, 1, 3) = (3-3)/(3-1) = **0**
- μ_sedang(3) = tri(3; 2, 4, 6) = (3-2)/(4-2) = **0.5**
- μ_banyak(3) = trap(3; 4, 6, 999, 999) → 3 ≤ 4 → **0**

---

##### Grafik Fungsi Keanggotaan (Ilustrasi Teks)

```
TINGKAT PRESTASI:
μ(x)
1.0 ┤ ████████                          ████████████
    ┤ ████Rendah████                    ████Tinggi████
    ┤        ████                    ████
0.5 ┤          ██ Sedang ████████████
    ┤           ████        ████
    ┤             ██████████
0.0 ┼──────┬──────┬──────┬──────┬──────┬───→ x
    0      1      2      3      4      5
          Kampus  Kota  Prov  Nas    Int

JUARA / PERINGKAT:
μ(x)
1.0 ┤ ██                                ████████████
    ┤ █Juara1█                         ██Juara 3+██
    ┤ ████                              ████████████
0.5 ┤   ██    ████████
    ┤    ██  ██Juara2██
    ┤     ████  ████
0.0 ┼──────┬──────┬──────┬──────┬──────┬───→ x
    0      1     1.5     2      3      5

JUMLAH PRESTASI:
μ(x)
1.0 ┤ ████████
    ┤ █Sedikit█                         ████████████
    ┤ ████                             ██ Banyak ██
0.5 ┤       ██████████                 ████████████
    ┤        ██Sedang██
    ┤         ██████████
0.0 ┼──────┬──────┬──────┬──────┬──────┬──────→ x
    0      1      2      4      6      8+
```

---

---

# BAGIAN 2: RULE BASE — Aturan Fuzzy IF-THEN (Mamdani)

---

### NARASI

> "Setelah menentukan variabel input dan output beserta fungsi keanggotaannya, langkah selanjutnya adalah membentuk rule base atau basis aturan fuzzy.
>
> Karena kita memiliki 3 variabel input masing-masing dengan 3 himpunan fuzzy, maka total kombinasi aturan adalah 3 × 3 × 3 = **27 aturan**. Setiap aturan berbentuk IF-THEN menggunakan operator AND (minimum) untuk menggabungkan kondisi input.
>
> Berikut adalah seluruh 27 aturan yang telah dirancang berdasarkan logika domain penilaian prestasi mahasiswa."

---

### PANDUAN LAYAR

1. **Di halaman fuzzy-kualitas**, scroll ke bagian **dokumentasi** di bawah tabel data
2. Tampilkan bagian **"Pola Aturan"** yang sudah ada di view
3. **Atau tampilkan tabel berikut** sebagai overlay/slide

---

### MATERI PENDUKUNG

#### 2.1 Tabel Lengkap 27 Aturan IF-THEN

| No | IF Tingkat | AND Juara | AND Jumlah | THEN Output |
|----|-----------|-----------|------------|-------------|
| 1 | Tinggi | Juara 1 | Banyak | **Sangat Baik** |
| 2 | Tinggi | Juara 1 | Sedang | **Sangat Baik** |
| 3 | Tinggi | Juara 1 | Sedikit | **Baik** |
| 4 | Tinggi | Juara 2 | Banyak | **Sangat Baik** |
| 5 | Tinggi | Juara 2 | Sedang | **Sangat Baik** |
| 6 | Tinggi | Juara 2 | Sedikit | **Baik** |
| 7 | Tinggi | Juara 3+ | Banyak | **Baik** |
| 8 | Tinggi | Juara 3+ | Sedang | **Cukup** |
| 9 | Tinggi | Juara 3+ | Sedikit | **Cukup** |
| 10 | Sedang | Juara 1 | Banyak | **Sangat Baik** |
| 11 | Sedang | Juara 1 | Sedang | **Sangat Baik** |
| 12 | Sedang | Juara 1 | Sedikit | **Baik** |
| 13 | Sedang | Juara 2 | Banyak | **Baik** |
| 14 | Sedang | Juara 2 | Sedang | **Cukup** |
| 15 | Sedang | Juara 2 | Sedikit | **Cukup** |
| 16 | Sedang | Juara 3+ | Banyak | **Cukup** |
| 17 | Sedang | Juara 3+ | Sedang | **Cukup** |
| 18 | Sedang | Juara 3+ | Sedikit | **Kurang** |
| 19 | Rendah | Juara 1 | Banyak | **Baik** |
| 20 | Rendah | Juara 1 | Sedang | **Cukup** |
| 21 | Rendah | Juara 1 | Sedikit | **Cukup** |
| 22 | Rendah | Juara 2 | Banyak | **Cukup** |
| 23 | Rendah | Juara 2 | Sedang | **Cukup** |
| 24 | Rendah | Juara 2 | Sedikit | **Kurang** |
| 25 | Rendah | Juara 3+ | Banyak | **Cukup** |
| 26 | Rendah | Juara 3+ | Sedang | **Kurang** |
| 27 | Rendah | Juara 3+ | Sedikit | **Kurang** |

#### 2.2 Pola Penalaran Aturan

| Pola | Kondisi | Output | Rationale |
|------|---------|--------|-----------|
| **Skor Maksimal** | Tingkat Tinggi + Juara 1 + Banyak/Sedang | Sangat Baik | Kombinasi terbaik di semua aspek |
| **Skor Tinggi** | Tingkat Tinggi + Juara 1/2 + Sedikit, atau Tinggi + Juara 2 + Banyak | Baik | Prestasi tinggi tapi jumlah masih sedikit |
| **Skor Menengah** | Variasi kombinasi sedang-rendah | Cukup | Keseimbangan antar variabel |
| **Skor Rendah** | Rendah + Juara 3+ + Sedikit/Sedang | Kurang | Semua aspek masih rendah |

#### 2.3 Kode Rule Base dalam PHP (FuzzyPrestasiService.php)

```php
$rules = [
    // Tinggi (1-9)
    ['tingkat' => 'tinggi', 'juara' => 'juara_1',      'jml' => 'banyak',   'output' => 'sangat_baik'],
    ['tingkat' => 'tinggi', 'juara' => 'juara_1',      'jml' => 'sedang',   'output' => 'sangat_baik'],
    ['tingkat' => 'tinggi', 'juara' => 'juara_1',      'jml' => 'sedikit',  'output' => 'baik'],
    ['tingkat' => 'tinggi', 'juara' => 'juara_2',      'jml' => 'banyak',   'output' => 'sangat_baik'],
    ['tingkat' => 'tinggi', 'juara' => 'juara_2',      'jml' => 'sedang',   'output' => 'sangat_baik'],
    ['tingkat' => 'tinggi', 'juara' => 'juara_2',      'jml' => 'sedikit',  'output' => 'baik'],
    ['tingkat' => 'tinggi', 'juara' => 'juara_3_plus', 'jml' => 'banyak',   'output' => 'baik'],
    ['tingkat' => 'tinggi', 'juara' => 'juara_3_plus', 'jml' => 'sedang',   'output' => 'cukup'],
    ['tingkat' => 'tinggi', 'juara' => 'juara_3_plus', 'jml' => 'sedikit',  'output' => 'cukup'],
    // Sedang (10-18)
    ['tingkat' => 'sedang', 'juara' => 'juara_1',      'jml' => 'banyak',   'output' => 'sangat_baik'],
    ['tingkat' => 'sedang', 'juara' => 'juara_1',      'jml' => 'sedang',   'output' => 'sangat_baik'],
    ['tingkat' => 'sedang', 'juara' => 'juara_1',      'jml' => 'sedikit',  'output' => 'baik'],
    ['tingkat' => 'sedang', 'juara' => 'juara_2',      'jml' => 'banyak',   'output' => 'baik'],
    ['tingkat' => 'sedang', 'juara' => 'juara_2',      'jml' => 'sedang',   'output' => 'cukup'],
    ['tingkat' => 'sedang', 'juara' => 'juara_2',      'jml' => 'sedikit',  'output' => 'cukup'],
    ['tingkat' => 'sedang', 'juara' => 'juara_3_plus', 'jml' => 'banyak',   'output' => 'cukup'],
    ['tingkat' => 'sedang', 'juara' => 'juara_3_plus', 'jml' => 'sedang',   'output' => 'cukup'],
    ['tingkat' => 'sedang', 'juara' => 'juara_3_plus', 'jml' => 'sedikit',  'output' => 'kurang'],
    // Rendah (19-27)
    ['tingkat' => 'rendah', 'juara' => 'juara_1',      'jml' => 'banyak',   'output' => 'baik'],
    ['tingkat' => 'rendah', 'juara' => 'juara_1',      'jml' => 'sedang',   'output' => 'cukup'],
    ['tingkat' => 'rendah', 'juara' => 'juara_1',      'jml' => 'sedikit',  'output' => 'cukup'],
    ['tingkat' => 'rendah', 'juara' => 'juara_2',      'jml' => 'banyak',   'output' => 'cukup'],
    ['tingkat' => 'rendah', 'juara' => 'juara_2',      'jml' => 'sedang',   'output' => 'cukup'],
    ['tingkat' => 'rendah', 'juara' => 'juara_2',      'jml' => 'sedikit',  'output' => 'kurang'],
    ['tingkat' => 'rendah', 'juara' => 'juara_3_plus', 'jml' => 'banyak',   'output' => 'cukup'],
    ['tingkat' => 'rendah', 'juara' => 'juara_3_plus', 'jml' => 'sedang',   'output' => 'kurang'],
    ['tingkat' => 'rendah', 'juara' => 'juara_3_plus', 'jml' => 'sedikit',  'output' => 'kurang'],
];
```

---

---

# BAGIAN 3: PROSES INFERENSI DAN DEFUZZIFIKASI — Perhitungan Manual

---

### NARASI

> "Sekarang saya akan mendemonstrasikan perhitungan manual fuzzy menggunakan data mahasiswa nyata dari database.
>
> Kasus yang digunakan adalah **Muhammad Syafiq Husin**, NIM 2455201110013, dengan data prestasi: Lomba Business Plan Provinsi, Juara 1, dan memiliki total 3 prestasi yang diterima di sistem.
>
> Mari kita hitung langkah demi langkah: fuzzifikasi, evaluasi aturan, agregasi, dan defuzzifikasi."

---

### PANDUAN LAYAR

1. **Di halaman fuzzy-kualitas**, tunjukkan baris data untuk **Muhammad Syafiq Husin** (PRESM-011)
2. **Klik tombol "Detail"** pada baris tersebut → muncul modal yang menampilkan:
   - Data prestasi: Lomba Business Plan Provinsi
   - Tingkat: Provinsi (nilai: 3)
   - Juara: Juara 1 (nilai: 1)
   - Jumlah Prestasi: 3
   - Semua 9 nilai membership function
   - Aturan terpakai (JSON)
   - Skor fuzzy: 85
   - Kualitas: Sangat Baik
3. **Tutup modal**, lalu tampilkan slide perhitungan manual berikut

---

### MATERI PENDUKUNG

#### 3.1 Data Input Kasus

| Data | Nilai |
|------|-------|
| Nama Mahasiswa | Muhammad Syafiq Husin |
| NIM | 2455201110013 |
| Kode Prestasi | PRESM-011 |
| Nama Lomba | Lomba Business Plan Provinsi |
| Tingkat Prestasi | Provinsi → **X₁ = 3** |
| Juara | Juara 1 → **X₂ = 1** |
| Jumlah Prestasi (diterima) | 3 → **X₃ = 3** |

---

#### 3.2 STEP 1 — Fuzzifikasi

Menghitung derajat keanggotaan (membership degree) untuk setiap input pada setiap himpunan fuzzy.

**Fuzzifikasi X₁ = 3 (Tingkat Provinsi):**

```
μ_rendah(3)  = trap(3; 0, 0, 2, 3)
             = (3 - 3) / (3 - 2) = 0/1 = 0

μ_sedang(3)  = tri(3; 2, 3, 4)
             = (3 - 2) / (3 - 2) = 1/1 = 1.0

μ_tinggi(3)  = trap(3; 3, 5, 5, 5)
             = x ≤ a → 0
```

| Himpunan | Derajat Keanggotaan |
|----------|-------------------|
| Rendah | **0** |
| Sedang | **1.0** |
| Tinggi | **0** |

---

**Fuzzifikasi X₂ = 1 (Juara 1):**

```
μ_juara_1(1)      = tri(1; 1, 1, 2)
                  = a = b → return 1.0 (degenerate triangle)

μ_juara_2(1)      = tri(1; 1.5, 2, 3)
                  = x < a → 0

μ_juara_3_plus(1) = trap(1; 2.5, 3, 5, 5)
                  = x ≤ a → 0
```

| Himpunan | Derajat Keanggotaan |
|----------|-------------------|
| Juara 1 | **1.0** |
| Juara 2 | **0** |
| Juara 3+ | **0** |

---

**Fuzzifikasi X₃ = 3 (Jumlah Prestasi):**

```
μ_sedikit(3) = trap(3; 0, 0, 1, 3)
             = (3 - 3) / (3 - 1) = 0/2 = 0

μ_sedang(3)  = tri(3; 2, 4, 6)
             = (3 - 2) / (4 - 2) = 1/2 = 0.5

μ_banyak(3)  = trap(3; 4, 6, 999, 999)
             = x ≤ a → 0
```

| Himpunan | Derajat Keanggotaan |
|----------|-------------------|
| Sedikit | **0** |
| Sedang | **0.5** |
| Banyak | **0** |

---

#### 3.3 STEP 2 — Evaluasi Aturan (Inferensi Mamdani)

Menerapkan 27 aturan IF-THEN. Firing strength dihitung dengan operator **MIN** (AND).

**Hanya aturan dengan firing strength > 0 yang akan "fire" (aktif):**

```
Aturan ke-11:
  IF Tingkat = Sedang (μ = 1.0)
  AND Juara = Juara 1 (μ = 1.0)
  AND Jumlah = Sedang (μ = 0.5)
  THEN Output = Sangat Baik

  Firing Strength = min(1.0, 1.0, 0.5) = 0.5
```

**Pengecekan aturan lain (contoh beberapa aturan yang TIDAK fire):**

| Aturan | Kondisi | μ Input | Min (Firing Strength) | Fire? |
|--------|---------|---------|----------------------|-------|
| #1 | Tinggi, Juara 1, Banyak | min(0, 1.0, 0) | 0 | Tidak |
| #3 | Tinggi, Juara 1, Sedikit | min(0, 1.0, 0) | 0 | Tidak |
| #10 | Sedang, Juara 1, Banyak | min(1.0, 1.0, 0) | 0 | Tidak |
| #11 | **Sedang, Juara 1, Sedang** | **min(1.0, 1.0, 0.5)** | **0.5** | **YA** |
| #12 | Sedang, Juara 1, Sedikit | min(1.0, 1.0, 0) | 0 | Tidak |
| #18 | Sedang, Juara 3+, Sedikit | min(1.0, 0, 0) | 0 | Tidak |
| #27 | Rendah, Juara 3+, Sedikit | min(0, 0, 0) | 0 | Tidak |

> **Hanya 1 aturan yang fire:** Aturan #11 → Output = **Sangat Baik**, Degree = **0.5**

---

#### 3.4 STEP 3 — Agregasi

Operator **MAX** diambil untuk setiap kategori output dari semua aturan yang fire.

| Kategori Output | Degree dari Aturan yang Fire | Hasil Agregasi (MAX) |
|----------------|----------------------------|---------------------|
| Sangat Baik | 0.5 (dari aturan #11) | **0.5** |
| Baik | — | **0** |
| Cukup | — | **0** |
| Kurang | — | **0** |

---

#### 3.5 STEP 4 — Defuzzifikasi (Weighted Average / Centroid)

Menggunakan metode **Weighted Average** dengan centroid tetap per kategori:

| Kategori Output | Centroid (Titik Tengah) |
|----------------|------------------------|
| Kurang | 15 |
| Cukup | 35 |
| Baik | 55 |
| Sangat Baik | 85 |

**Rumus:**

```
         Σ (degree_i × centroid_i)     0.5 × 85     42.5
Skor = ─────────────────────────── = ────────── = ────── = 85.0
              Σ (degree_i)                0.5        0.5
```

**Hasil Defuzzifikasi:**

| Parameter | Nilai |
|-----------|-------|
| Skor Fuzzy | **85.0** |
| Kualitas | **Sangat Baik** (karena 85 ≥ 65) |

---

#### 3.6 Verifikasi dengan Data di Sistem

| Perhitungan Manual | Hasil di Database (fuzzy_hasil) | Cocok? |
|-------------------|-------------------------------|--------|
| μ_tingkat_sedang = 1.0 | mf_tingkat_sedang = 1 | ✓ |
| μ_juara_1 = 1.0 | mf_juara_1 = 1 | ✓ |
| μ_jml_sedang = 0.5 | mf_jml_sedang = 0.5 | ✓ |
| Firing strength = 0.5 | aturan_terpakai: [{"output":"sangat_baik","degree":0.5}] | ✓ |
| Skor = 85.0 | skor_fuzzy = 85 | ✓ |
| Kualitas = Sangat Baik | kualitas_fuzzy = Sangat Baik | ✓ |

> **Hasil perhitungan manual 100% cocok dengan hasil sistem!**

---

---

# BAGIAN 4: RANCANGAN IMPLEMENTASI DI LARAVEL

---

### NARASI

> "Berikut adalah rancangan implementasi logika fuzzy di dalam framework Laravel 13. Sistem ini dirancang dengan pola **Service Layer** untuk memisahkan logika bisnis dari controller.
>
> Arsitektur terdiri dari Model Eloquent, Migration untuk database schema, Service class untuk logika fuzzy, Controller untuk HTTP handling, dan Blade View untuk tampilan dashboard."

---

### PANDUAN LAYAR

1. **Buka IDE/code editor** → tampilkan struktur folder project:
   - `app/Services/FuzzyPrestasiService.php`
   - `app/Http/Controllers/FuzzyPrestasiController.php`
   - `app/Models/FuzzyHasil.php`
   - `app/Models/PrestasiMahasiswa.php`
   - `resources/views/prestasi-mahasiswa/fuzzy-kualitas.blade.php`
   - `database/migrations/2026_07_21_100000_create_fuzzy_hasil_table.php`
2. **Buka file FuzzyPrestasiService.php** → tunjukkan class dan method utama
3. **Buka file FuzzyPrestasiController.php** → tunjukkan method index() dan hitungUlang()
4. **Buka file fuzzy-kualitas.blade.php** → tunjukkan chart dan tabel
5. **Tampilkan ERD** dan flowchart

---

### MATERI PENDUKUNG

#### 4.1 ERD (Entity Relationship Diagram)

```
┌──────────────────┐     ┌──────────────────────┐     ┌──────────────────┐
│   jenis_prestasi │     │      prestasi         │     │ tingkat_prestasi │
├──────────────────┤     ├──────────────────────┤     ├──────────────────┤
│ id_jenis (PK)    │◄────│ id_jenis (FK)         │────►│ id_tingkat (PK)  │
│ nama_jenis       │     │ id_prestasi (PK,ULID) │     │ nama_tingkat     │
└──────────────────┘     │ kode_prestasi (UNIQUE)│     └──────────────────┘
                         │ nim (FK)              │
┌──────────────────┐     │ nama_lomba            │     ┌──────────────────┐
│    mahasiswa      │     │ penyelenggara         │     │  admin_prestasi  │
├──────────────────┤     │ tanggal               │     ├──────────────────┤
│ nim (PK)         │◄────│ juara                 │────►│ id_admin (PK)    │
│ nama             │     │ sertifikat            │     │ nama             │
│ email            │     │ status_verifikasi     │     │ email            │
│ ...              │     │ skor_fuzzy ◄─ tambahan│     │ password         │
└──────────────────┘     │ kualitas_fuzzy ◄─────│     └──────────────────┘
         │               └──────────┬───────────┘               │
         │                          │                           │
         │                          │ 1:1                       │
         │                          ▼                           │
         │               ┌──────────────────────┐               │
         │               │     fuzzy_hasil       │               │
         │               ├──────────────────────┤               │
         │               │ id_fuzzy_hasil (PK)   │               │
         └──────────────►│ nim (FK)              │               │
                         │ id_prestasi (FK)      │◄──────────────┘
                         │ tingkat_prestasi (float)   verifikasi_prestasi
                         │ juara (float)               ┌──────────────────┐
                         │ jumlah_prestasi (int)        ├──────────────────┤
                         │ mf_tingkat_rendah (float)    │ id_verifikasi(PK)│
                         │ mf_tingkat_sedang (float)    │ id_prestasi (FK) │
                         │ mf_tingkat_tinggi (float)    │ id_admin (FK)    │
                         │ mf_juara_1 (float)           │ tgl_verifikasi   │
                         │ mf_juara_2 (float)           │ catatan          │
                         │ mf_juara_3_plus (float)      └──────────────────┘
                         │ mf_jml_sedikit (float)
                         │ mf_jml_sedang (float)
                         │ mf_jml_banyak (float)
                         │ aturan_terpakai (text/JSON)
                         │ skor_fuzzy (float)
                         │ kualitas_fuzzy (string)
                         └──────────────────────┘
```

#### 4.2 Struktur File dan Tugas

| File | Tipe | Tugas |
|------|------|-------|
| `app/Services/FuzzyPrestasiService.php` | Service | Core fuzzy engine: fuzzifikasi, 27 aturan, defuzzifikasi, persistensi |
| `app/Http/Controllers/FuzzyPrestasiController.php` | Controller | HTTP handler: dashboard view, hitung ulang |
| `app/Models/FuzzyHasil.php` | Model | Eloquent model untuk tabel `fuzzy_hasil` |
| `app/Models/PrestasiMahasiswa.php` | Model | Eloquent model untuk tabel `prestasi` (+ relasi fuzzyHasil) |
| `resources/views/prestasi-mahasiswa/fuzzy-kualitas.blade.php` | View | Dashboard: tabel, chart (Chart.js), detail modal |
| `database/migrations/..._create_fuzzy_hasil_table.php` | Migration | Schema tabel `fuzzy_hasil` (17 kolom) |
| `routes/web.php` | Routes | 2 rute: GET dashboard + POST hitung ulang |

#### 4.3 Model — FuzzyHasil.php

```php
class FuzzyHasil extends Model
{
    protected $table = 'fuzzy_hasil';
    protected $primaryKey = 'id_fuzzy_hasil';

    protected $fillable = [
        'id_prestasi', 'nim',
        'tingkat_prestasi', 'juara', 'jumlah_prestasi',
        'mf_tingkat_rendah', 'mf_tingkat_sedang', 'mf_tingkat_tinggi',
        'mf_juara_1', 'mf_juara_2', 'mf_juara_3_plus',
        'mf_jml_sedikit', 'mf_jml_sedang', 'mf_jml_banyak',
        'aturan_terpakai', 'skor_fuzzy', 'kualitas_fuzzy',
    ];

    protected $casts = [
        'mf_tingkat_rendah' => 'float',
        'mf_tingkat_sedang' => 'float',
        'mf_tingkat_tinggi' => 'float',
        'mf_juara_1' => 'float',
        'mf_juara_2' => 'float',
        'mf_juara_3_plus' => 'float',
        'mf_jml_sedikit' => 'float',
        'mf_jml_sedang' => 'float',
        'mf_jml_banyak' => 'float',
        'skor_fuzzy' => 'float',
    ];
}
```

#### 4.4 Migration — fuzzy_hasil Table

```php
Schema::create('fuzzy_hasil', function (Blueprint $table) {
    $table->id('id_fuzzy_hasil');
    $table->ulid('id_prestasi');
    $table->string('nim', 20);
    $table->float('tingkat_prestasi');
    $table->float('juara');
    $table->integer('jumlah_prestasi');
    // 9 kolom membership degree
    $table->float('mf_tingkat_rendah')->default(0);
    $table->float('mf_tingkat_sedang')->default(0);
    $table->float('mf_tingkat_tinggi')->default(0);
    $table->float('mf_juara_1')->default(0);
    $table->float('mf_juara_2')->default(0);
    $table->float('mf_juara_3_plus')->default(0);
    $table->float('mf_jml_sedikit')->default(0);
    $table->float('mf_jml_sedang')->default(0);
    $table->float('mf_jml_banyak')->default(0);
    $table->text('aturan_terpakai')->nullable();
    $table->float('skor_fuzzy');
    $table->string('kualitas_fuzzy', 50);
    $table->timestamps();

    $table->foreign('id_prestasi')->references('id_prestasi')
          ->on('prestasi')->cascadeOnDelete();
    $table->foreign('nim')->references('nim')
          ->on('mahasiswa')->cascadeOnDelete();
});
```

#### 4.5 Controller — FuzzyPrestasiController.php

```php
class FuzzyPrestasiController extends Controller
{
    public function index()
    {
        // Ambil semua prestasi yang sudah diterima
        $prestasi = PrestasiMahasiswa::where('status_verifikasi', 'diterima')
            ->with(['mahasiswa', 'tingkatPrestasi', 'fuzzyHasil'])
            ->get();

        // Auto-evaluate jika ada yang belum dihitung
        if ($prestasi->contains(fn($p) => $p->skor_fuzzy === null)) {
            $fuzzy = new FuzzyPrestasiService();
            $fuzzy->evaluasiSemua();
            $prestasi = PrestasiMahasiswa::where('status_verifikasi', 'diterima')
                ->with(['mahasiswa', 'tingkatPrestasi', 'fuzzyHasil'])
                ->get();
        }

        // Statistik dashboard
        $rekapKualitas = $prestasi->groupBy('kualitas_fuzzy')
            ->map(fn($g) => $g->count());

        return view('prestasi-mahasiswa.fuzzy-kualitas', compact('prestasi', 'rekapKualitas'));
    }

    public function hitungUlang()
    {
        $fuzzy = new FuzzyPrestasiService();
        $fuzzy->evaluasiSemua();

        return redirect()->route('prestasi-mahasiswa.fuzzy-kualitas')
            ->with('success', 'Perhitungan fuzzy berhasil diperbarui.');
    }
}
```

#### 4.6 Service — FuzzyPrestasiService.php (Alur Metode Utama)

```
hitungSkor(tingkat, juara, jumlahPrestasi, idPrestasi, nim)
  │
  ├── parseJuara(string) → float
  │     (ekstrak angka dari string "Juara 2", handle "Harapan")
  │
  ├── fuzzifikasiTingkat(float) → {rendah, sedang, tinggi}
  │     (trapesium & segitiga)
  │
  ├── fuzzifikasiJuara(float) → {juara_1, juara_2, juara_3_plus}
  │     (segitiga & trapesium)
  │
  ├── fuzzifikasiJumlahPrestasi(int) → {sedikit, sedang, banyak}
  │     (trapesium & segitiga)
  │
  ├── evaluasiAturan(tingkatMF, juaraMF, jmlMF) → {output: degree}
  │     (27 aturan IF-THEN, operator MIN + MAX agregasi)
  │
  ├── defuzzifikasi(firedRules) → {skor, kualitas}
  │     (weighted average dengan centroid tetap)
  │
  └── FuzzyHasil::updateOrCreate(...)
        (simpan audit trail lengkap ke database)
```

#### 4.7 Flowchart — Alur Program dari Input hingga Output

```
┌─────────────┐
│   START      │
└──────┬──────┘
       │
       ▼
┌──────────────────────────────┐
│  Admin menginput/mengelola   │
│  data prestasi mahasiswa     │
│  (PrestasiMahasiswaController│
│   → store/update)            │
└──────────────┬───────────────┘
               │
               ▼
┌──────────────────────────────┐
│  Admin memverifikasi prestasi│
│  status_verifikasi =         │
│  "diterima" / "ditolak"      │
└──────────────┬───────────────┘
               │
               ▼
┌──────────────────────────────┐
│  User membuka halaman        │
│  /prestasi-mahasiswa/        │
│  fuzzy-kualitas              │
└──────────────┬───────────────┘
               │
               ▼
┌──────────────────────────────┐
│  Controller mendeteksi       │
│  ada skor_fuzzy == null?     │
│  ── YA → Hitung fuzzy        │
│  ── TIDAK → Tampilkan data   │
└──────────────┬───────────────┘
               │
               ▼
┌──────────────────────────────┐
│  FuzzyPrestasiService::      │
│  evaluasiSemua()             │
│  Untuk SETIAP prestasi       │
│  berstatus "diterima":       │
│  ┌────────────────────────┐  │
│  │ 1. Hitung jumlah       │  │
│  │    prestasi per NIM    │  │
│  │                        │  │
│  │ 2. Fuzzifikasi         │  │
│  │    - tingkat MF        │  │
│  │    - juara MF          │  │
│  │    - jumlah MF         │  │
│  │                        │  │
│  │ 3. Evaluasi 27 aturan  │  │
│  │    (MIN per aturan,    │  │
│  │     MAX per output)    │  │
│  │                        │  │
│  │ 4. Defuzzifikasi       │  │
│  │    Weighted Average     │  │
│  │    → skor crisp        │  │
│  │                        │  │
│  │ 5. Simpan ke           │  │
│  │    fuzzy_hasil + update│  │
│  │    prestasi.skor_fuzzy │  │
│  └────────────────────────┘  │
└──────────────┬───────────────┘
               │
               ▼
┌──────────────────────────────┐
│  Tampilkan Dashboard:        │
│  - Tabel data + skor/kualitas│
│  - Chart (doughnut, bar)     │
│  - Ringkasan statistik       │
│  - Modal detail per baris    │
│  - Tombol "Hitung Ulang"     │
└──────────────┬───────────────┘
               │
               ▼
┌─────────────┐
│    END       │
└─────────────┘
```

#### 4.8 Routes (web.php)

```php
Route::middleware('permission:prestasi.report')->group(function () {
    // Dashboard fuzzy kualitas
    Route::get('prestasi-mahasiswa/fuzzy-kualitas',
        [FuzzyPrestasiController::class, 'index'])
        ->name('prestasi-mahasiswa.fuzzy-kualitas');

    // Hitung ulang semua skor fuzzy
    Route::post('prestasi-mahasiswa/fuzzy-kualitas/hitung',
        [FuzzyPrestasiController::class, 'hitungUlang'])
        ->name('prestasi-mahasiswa.fuzzy-hitung');
});
```

---

---

# BAGIAN 5: ANALISIS DAN KESIMPULAN

---

### NARASI

> "Sebagai penutup, saya akan menjelaskan mengapa metode Mamdani fuzzy dipilih dan manfaatnya dibandingkan sistem penilaian konvensional.
>
> Metode Mamdani cocok untuk kasus ini karena menghasilkan output berupa label linguistik — Sangat Baik, Baik, Cukup, Kurang — yang mudah dipahami oleh pengguna sistem (admin, dosen, operator). Berbeda dengan Tsukamoto yang menghasilkan output crisp langsung, Mamdani memberikan interpretabilitas yang lebih baik melalui proses fuzzifikasi hingga defuzzifikasi yang transparan.
>
> Keunggulan fuzzy dibandingkan if-else biasa: fuzzy menangani ambiguitas dan gradasi. Dalam sistem konvensional, perbedaan antara Juara 1 dan Juara 2 diperlakukan sama tegasnya. Dalam fuzzy, peringkat 1.5 (Juara Harapan 1) bisa terhubung ke kedua kategori dengan derajat keanggotaan tertentu, sehingga penilaian lebih adil dan realistis.
>
> Untuk sistem akademik, ini sangat bermanfaat untuk rekomendasi beasiswa dan penghargaan — sistem bisa memberikan skor kualitas yang proporsional berdasarkan kombinasi beberapa faktor, bukan hanya satu kriteria."

---

### PANDUAN LAYAR

1. **Di halaman fuzzy-kualitas**, tunjukkan **4 kartu ringkasan** di bagian atas:
   - Jumlah Prestasi
   - Rata-rata Skor Fuzzy
   - Prestasi Layak Penghargaan (skor ≥ 70)
   - Prestasi Tidak Layak (skor < 50)
2. **Tunjukkan Chart Doughnut** → distribusi kualitas (Sangat Baik, Baik, Cukup, Kurang)
3. **Tunjukkan Chart Bar** → rata-rata skor per tingkat kompetisi
4. **Tunjukkan Chart Bar** → rata-rata skor per jenis prestasi
5. **Tampilkan slide analisis dan kesimpulan** berikut

---

### MATERI PENDUKUNG

#### 5.1 Mengapa Metode Mamdani?

| Aspek | Mamdani | Tsukamoto |
|-------|---------|-----------|
| **Output** | Label linguistik + centroid | Crisp value langsung |
| **Interpretabilitas** | Tinggi (transparan) | Rendah (black-box) |
| **Kompleksitas** | Sedang | Lebih tinggi |
| **Cocok untuk** | Sistem penilaian/klasifikasi | Sistem kontrol presisi |
| **Implementasi di PHP** | Mudah (native, tanpa library) | Lebih kompleks |
| **Audit trail** | Mudah (simpan semua MF degree) | Sulit |

**Kesimpulan**: Mamdani dipilih karena:
1. Menghasilkan label kualitas yang mudah dipahami
2. Proses inference transparan dan bisa diaudit
3. Implementasi native PHP tanpa library eksternal
4. Cocok untuk sistem keputusan/klasifikasi (bukan kontrol)

#### 5.2 Perbandingan: Fuzzy vs If-Else Konvensional

**Sistem If-Else Konvensional:**
```php
// Contoh pendekatan if-else biasa
if ($tingkat == 'Internasional' && $juara == 1 && $jmlPrestasi >= 3) {
    return 'Sangat Baik';
} elseif ($tingkat == 'Nasional' && $juara <= 2) {
    return 'Baik';
} elseif ($tingkat == 'Kota' || $juara >= 3) {
    return 'Kurang';
} else {
    return 'Cukup';
}
```

**Masalah if-else:**
- Batas tegas (hard boundary) — Juara 2 = "Baik", Juara 3 = "Kurang"
- Tidak mempertimbangkan gradasi
- Kombinasi input yang ambigu tidak ditangani
- Sulit mempertimbangkan banyak variabel secara bersamaan
- Tidak ada audit trail / jejak perhitungan

**Keunggulan Pendekatan Fuzzy:**

| Aspek | If-Else Konvensional | Logika Fuzzy |
|-------|---------------------|-------------|
| **Batas keputusan** | Tegas (hard boundary) | Gradasi (soft boundary) |
| **Kombinasi variabel** | Terbatas (nesting kompleks) | Eksplisit (rule base) |
| **Ambiguitas** | Tidak ditangani | Ditangani dengan MF |
| **Audit Trail** | Tidak ada | Tersimpan lengkap (9 MF degree + rules) |
| **Konsistensi** | Bergantung pada urutan if-else | Konsisten (deterministic) |
| **Skalabilitas** | Sulit tambah variabel | Mudah tambah aturan |
| **Transparansi** | Rendah | Tinggi (semua langkah terekam) |

#### 5.3 Manfaat untuk Sistem Akademik

1. **Rekomendasi Beasiswa Otomatis**: Skor fuzzy bisa langsung digunakan untuk memfilter mahasiswa yang layak menerima penghargaan/beasiswa prestasi
2. **Keadilan Penilaian**: Mahasiswa dengan kombinasi prestasi berbeda mendapat penilaian proporsional
3. **Transparansi**: Setiap perhitungan bisa diaudit — administrator bisa melihat exact membership degree dan aturan yang fire
4. **Fleksibilitas**: Parameter fuzzy (bentuk fungsi keanggotaan, aturan) bisa disesuaikan tanpa mengubah struktur database
5. **Rekam Jejak**: Tabel `fuzzy_hasil` menyimpan seluruh jejak perhitungan untuk setiap prestasi

#### 5.4 Statistik Sistem (Data Aktual dari Database)

| Metrik | Nilai |
|--------|-------|
| Total Mahasiswa | 30+ |
| Total Prestasi Diterima | 25+ |
| Kategori Sangat Baik | 8 (skor ≥ 65) |
| Kategori Baik | 5 (skor 40-64) |
| Kategori Cukup | 8 (skor 20-39) |
| Kategori Kurang | 4 (skor < 20) |
| Total Aturan Fuzzy | 27 |
| Variabel Input | 3 (Tingkat, Juara, Jumlah) |
| Variabel Output | 1 (Skor Kualitas 0-100) |

#### 5.5 Penutup

> "Demikian penjelasan implementasi logika fuzzy pada sistem MyCampus. Metode Mamdani terbukti efektif untuk menilai kualitas prestasi mahasiswa secara objektif, transparan, dan adil. Seluruh perhitungan tersimpan sebagai audit trail di database, sehingga setiap keputusan bisa diverifikasi.
>
> Terima kasih. Wassalamualaikum warahmatullahi wabarakatuh."

---

---

# LAMPIRAN: Ringkasan Komponen Sistem

| Komponen | File Lokasi | Jumlah Baris |
|----------|------------|-------------|
| Core Fuzzy Engine | `app/Services/FuzzyPrestasiService.php` | 250 |
| Controller | `app/Http/Controllers/FuzzyPrestasiController.php` | 90 |
| Model (FuzzyHasil) | `app/Models/FuzzyHasil.php` | 54 |
| Model (PrestasiMahasiswa) | `app/Models/PrestasiMahasiswa.php` | 54 |
| Dashboard View | `resources/views/prestasi-mahasiswa/fuzzy-kualitas.blade.php` | 722 |
| Migration (fuzzy_hasil) | `database/migrations/..._create_fuzzy_hasil_table.php` | 41 |
| Migration (fuzzy columns) | `database/migrations/..._add_fuzzy_columns_...php` | 23 |
| Routes | `routes/web.php` | 2 rute fuzzy |
| **Total** | | **~1.256 baris kode** |
