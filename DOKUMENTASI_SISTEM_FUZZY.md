# Dokumentasi Sistem Fuzzy Kategorisasi Kualitas Prestasi Mahasiswa

## 1. Pendahuluan

Sistem ini menerapkan **Fuzzy Inference System (FIS) tipe Mamdani** untuk mengategorikan kualitas prestasi mahasiswa secara otomatis. Input berupa **tingkat prestasi** (kampus hingga internasional), **peringkat juara**, dan **jumlah prestasi** per mahasiswa, diolah melalui aturan fuzzy menghasilkan **skor kualitas 0–100** yang dikategorikan menjadi: **Sangat Baik**, **Baik**, **Cukup**, atau **Kurang**.

Tujuan utama: memberikan penilaian kualitas yang lebih halus dan realistis dibandingkan pendekatan krisis (hard threshold), karena kombinasi tingkat + juara + jumlah prestasi tidak selalu linier.

---

## 2. Arsitektur Sistem

```
┌─────────────────────────────────────────────────────────────────────┐
│                        INPUT (3 Variabel)                           │
│  ┌──────────────────┐  ┌──────────────────┐  ┌──────────────────┐  │
│  │ Tingkat Prestasi │  │     Juara        │  │ Jumlah Prestasi  │  │
│  │ (1–5)            │  │ (float 1.0–5.0)  │  │ (integer 0–N)    │  │
│  └────────┬─────────┘  └────────┬─────────┘  └────────┬─────────┘  │
└───────────┼──────────────────────┼──────────────────────┼───────────┘
            │                      │                      │
            ▼                      ▼                      ▼
┌─────────────────────────────────────────────────────────────────────┐
│                         FUZZIFIKASI                                 │
│  ┌──────────────────┐  ┌──────────────────┐  ┌──────────────────┐  │
│  │ rendah, sedang,  │  │ juara_1, juara_2,│  │ sedikit, sedang, │  │
│  │ tinggi           │  │ juara_3_plus     │  │ banyak           │  │
│  └────────┬─────────┘  └────────┬─────────┘  └────────┬─────────┘  │
└───────────┼──────────────────────┼──────────────────────┼───────────┘
            │                      │                      │
            ▼                      ▼                      ▼
┌─────────────────────────────────────────────────────────────────────┐
│                   EVALUASI ATURAN (27 Rules)                        │
│                                                                      │
│  IF tingkat = X AND juara = Y AND jumlah = Z THEN kualitas = W    │
│  Derajat keanggotaan = min(tingkat_MF, juara_MF, jumlah_MF)        │
└───────────────────────────────────┬─────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────┐
│                        DEFUZZIFIKASI                                 │
│          Weighted Average (centroid × degree)                       │
│                                                                      │
│           Σ(centroid_i × degree_i)                                   │
│  Skor = ────────────────────────────                                 │
│                Σ(degree_i)                                           │
└───────────────────────────────────┬─────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────┐
│                         OUTPUT                                       │
│  ┌─────────┬──────┬──────┬───────────┐                              │
│  │ Sangat  │ Baik │Cukup │  Kurang   │                              │
│  │  Baik   │      │      │           │                              │
│  │ 65–100  │40–64 │20–39 │   0–19    │                              │
│  └─────────┴──────┴──────┴───────────┘                              │
└─────────────────────────────────────────────────────────────────────┘
```

---

## 3. Variabel Input

### 3.1 Tingkat Prestasi

Berasal dari tabel `tingkat_prestasi`. Di-mapping ke nilai numerik:

| nama_tingkat   | Nilai |
|----------------|-------|
| Kampus         | 1     |
| Kota           | 2     |
| Provinsi       | 3     |
| Nasional       | 4     |
| Internasional  | 5     |

### 3.2 Juara

Berasal dari field `juara` pada tabel `prestasi_mahasiswa`. Karena field ini berisi teks bebas (misal: `"Juara 1"`, `"Harapan 2"`, `"3"`), dilakukan parsing:

```
"Juara 1"   → 1.0
"Harapan 2" → 2.5   (Harapan = angka + 0.5)
"3"         → 3.0
"Tidak ada" → 5.0   (fallback)
```

**Regex:** `/(\d+)/` untuk mengekstrak angka, kemudian cek kata `"harapan"` (case-insensitive) untuk menambahkan +0.5.

### 3.3 Jumlah Prestasi (Variabel Baru)

Berasal dari agregat `COUNT(prestasi_mahasiswa WHERE nim = X AND status_verifikasi = 'diterima')`.

| Linguistik | Fungsi    | Parameter       | Bentuk     |
|------------|-----------|-----------------|------------|
| Sedikit    | trapezoid | (0, 0, 1, 3)    | Trapesium  |
| Sedang     | triangle  | (2, 4, 6)       | Segitiga   |
| Banyak     | trapezoid | (4, 6, 999, 999) | Trapesium |

**Grafik Keanggotaan Jumlah Prestasi:**

```
μ(x)
1.0 ┤████████╲                  ╱████████████████████
    │█████████╲                ╱
    │ Sedikit ╲______________╱   Banyak
    │          ╲   Sedang  ╱
    │           ╲         ╱
0.0 ┤____________╲_______╱_____________
    └──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──→
       0  1  2  3  4  5  6  7  8  9  10
                  Jumlah Prestasi
```

**Detail Membership Value:**

| Jumlah | Sedikit | Sedang | Banyak |
|--------|---------|--------|--------|
| 0      | 1.0     | 0.0    | 0.0    |
| 1      | 1.0     | 0.0    | 0.0    |
| 2      | 0.5     | 0.0    | 0.0    |
| 3      | 0.0     | 0.5    | 0.0    |
| 4      | 0.0     | 1.0    | 0.0    |
| 5      | 0.0     | 0.5    | 0.5    |
| 6      | 0.0     | 0.0    | 1.0    |
| 7+     | 0.0     | 0.0    | 1.0    |

---

## 4. Membership Functions

### 4.1 Fungsi Keanggotaan Triangle

```
        μ(x)
        1.0 │      ╱╲
            │     ╱  ╲
            │    ╱    ╲
            │   ╱      ╲
        0.0 └──a────b────c──→ x
```

**Formula:**

```
         ⎧ 0                    , x ≤ a atau x ≥ c
μ(x) =   ⎨ (x - a) / (b - a)   , a < x ≤ b
         ⎩ (c - x) / (c - b)   , b < x < c
```

Ketika `a == b` (puncak di titik kiri), fungsi mengembalikan **1.0** saat `x == a == b`.

### 4.2 Fungsi Keanggotaan Trapezoid

```
        μ(x)
        1.0 │      ┌────┐
            │     ╱│    │╲
            │    ╱ │    │ ╲
            │   ╱  │    │  ╲
        0.0 └──a───b────c───d──→ x
```

**Formula:**

```
         ⎧ 0                        , x ≤ a atau x > d
μ(x) =   ⎨ 1                        , b ≤ x ≤ c
         ⎨ (x - a) / (b - a)        , a < x < b
         ⎩ (d - x) / (d - c)        , c < x ≤ d
```

### 4.3 Parameter Input

#### Tingkat Prestasi

| Linguistik | Fungsi    | Parameter       | Bentuk     |
|------------|-----------|-----------------|------------|
| Rendah     | trapezoid | (0, 0, 2, 3)   | Trapesium  |
| Sedang     | triangle  | (2, 3, 4)       | Segitiga   |
| Tinggi     | trapezoid | (3, 5, 5, 5)   | Trapesium  |

**Grafik Keanggotaan Tingkat:**

```
μ(x)
1.0 ┤ ██████████████
    │ █ Rendah █████╲              ╱█ Tinggi ██████████
    │ ████╱─────────╲────────────╱──█████████████████
    │    ╱  Sedang   ╲──────────╱
    │   ╱             ╲────────╱
0.0 ┤──╱───────────────╲──────╱──
    └──┬──┬──┬──┬──┬──┬──┬──┬──┬──→
       0  1  2  3  4  5  6
          Tingkat Prestasi
```

**Detail Membership Value:**

| Nilai | Rendah | Sedang | Tinggi |
|-------|--------|--------|--------|
| 1 (Kampus) | 1.0 | 0.0 | 0.0 |
| 2 (Kota) | 1.0 | 0.0 | 0.0 |
| 3 (Provinsi) | 0.0 | 1.0 | 0.0 |
| 4 (Nasional) | 0.0 | 0.0 | 1.0 |
| 5 (Internasional) | 0.0 | 0.0 | 1.0 |

#### Juara

| Linguistik   | Fungsi    | Parameter         | Bentuk     |
|--------------|-----------|-------------------|------------|
| Juara 1      | triangle  | (1, 1, 2)         | Segitiga   |
| Juara 2      | triangle  | (1.5, 2, 3)       | Segitiga   |
| Juara 3+     | trapezoid | (2.5, 3, 5, 5)   | Trapesium  |

**Grafik Keanggotaan Juara:**

```
μ(x)
1.0 ┤██          ████              ████████████████
    │██ Juara 1  ████ Juara 2      ██ Juara 3+ █████
    │██╱─────────████──────────────████────────████
    │ ╱          ╲███╲            ╱
    │╱            ╲███╲──────────╱
0.0 ┤──────────────╲─────────────
    └──┬──┬──┬──┬──┬──┬──┬──┬──┬──→
       1  1.5 2  2.5 3  3.5 4  5
               Juara (float)
```

**Detail Membership Value:**

| Nilai | Juara 1 | Juara 2 | Juara 3+ |
|-------|---------|---------|----------|
| 1.0 (Juara 1) | 1.0 | 0.0 | 0.0 |
| 1.5 | 0.0 | 0.0 | 0.0 |
| 2.0 (Juara 2) | 0.0 | 1.0 | 0.0 |
| 2.5 | 0.0 | 0.5 | 0.0 |
| 3.0 (Juara 3+) | 0.0 | 0.0 | 1.0 |
| 4.0 | 0.0 | 0.0 | 1.0 |
| 5.0 | 0.0 | 0.0 | 1.0 |

#### Jumlah Prestasi (Baru)

| Linguistik | Fungsi    | Parameter         | Bentuk     |
|------------|-----------|-------------------|------------|
| Sedikit    | trapezoid | (0, 0, 1, 3)     | Trapesium  |
| Sedang     | triangle  | (2, 4, 6)         | Segitiga   |
| Banyak     | trapezoid | (4, 6, 999, 999)  | Trapesium  |

**Detail Membership Value:**

| Jumlah | Sedikit | Sedang | Banyak |
|--------|---------|--------|--------|
| 0      | 1.0     | 0.0    | 0.0    |
| 1      | 1.0     | 0.0    | 0.0    |
| 2      | 0.5     | 0.0    | 0.0    |
| 3      | 0.0     | 0.5    | 0.0    |
| 4      | 0.0     | 1.0    | 0.0    |
| 5      | 0.0     | 0.5    | 0.5    |
| 6      | 0.0     | 0.0    | 1.0    |
| 7+     | 0.0     | 0.0    | 1.0    |

---

## 5. Output — Kualitas

| Linguistik   | Fungsi    | Parameter                 | Centroid |
|--------------|-----------|---------------------------|----------|
| Kurang       | trapezoid | (0, 0, 20, 35)           | 15       |
| Cukup        | triangle  | (25, 35, 55)              | 35       |
| Baik         | triangle  | (40, 55, 75)              | 55       |
| Sangat Baik  | trapezoid | (65, 80, 100, 100)        | 85       |

**Rentang Kategori Akhir:**

| Kualitas    | Rentang Skor | Badge  |
|-------------|-------------|--------|
| Sangat Baik | 65 – 100    | Hijau  |
| Baik        | 40 – 64     | Biru   |
| Cukup       | 20 – 39     | Kuning |
| Kurang      | 0 – 19      | Merah  |

---

## 6. Tabel Aturan Fuzzy (Rule Base)

Sistem menggunakan **27 aturan Mamdani** dengan operator **AND = min** (3-way min):

| # | IF Tingkat | AND Juara | AND Jml Prestasi | THEN Kualitas | Centroid |
|---|------------|-----------|------------------|---------------|----------|
| 1 | Tinggi     | Juara 1   | Banyak           | Sangat Baik   | 85       |
| 2 | Tinggi     | Juara 1   | Sedang           | Sangat Baik   | 85       |
| 3 | Tinggi     | Juara 1   | Sedikit          | Baik          | 55       |
| 4 | Tinggi     | Juara 2   | Banyak           | Sangat Baik   | 85       |
| 5 | Tinggi     | Juara 2   | Sedang           | Sangat Baik   | 85       |
| 6 | Tinggi     | Juara 2   | Sedikit          | Baik          | 55       |
| 7 | Tinggi     | Juara 3+  | Banyak           | Baik          | 55       |
| 8 | Tinggi     | Juara 3+  | Sedang           | Cukup         | 35       |
| 9 | Tinggi     | Juara 3+  | Sedikit          | Cukup         | 35       |
| 10| Sedang     | Juara 1   | Banyak           | Sangat Baik   | 85       |
| 11| Sedang     | Juara 1   | Sedang           | Sangat Baik   | 85       |
| 12| Sedang     | Juara 1   | Sedikit          | Baik          | 55       |
| 13| Sedang     | Juara 2   | Banyak           | Baik          | 55       |
| 14| Sedang     | Juara 2   | Sedang           | Cukup         | 35       |
| 15| Sedang     | Juara 2   | Sedikit          | Cukup         | 35       |
| 16| Sedang     | Juara 3+  | Banyak           | Cukup         | 35       |
| 17| Sedang     | Juara 3+  | Sedang           | Cukup         | 35       |
| 18| Sedang     | Juara 3+  | Sedikit          | Kurang        | 15       |
| 19| Rendah     | Juara 1   | Banyak           | Baik          | 55       |
| 20| Rendah     | Juara 1   | Sedang           | Cukup         | 35       |
| 21| Rendah     | Juara 1   | Sedikit          | Cukup         | 35       |
| 22| Rendah     | Juara 2   | Banyak           | Cukup         | 35       |
| 23| Rendah     | Juara 2   | Sedang           | Cukup         | 35       |
| 24| Rendah     | Juara 2   | Sedikit          | Kurang        | 15       |
| 25| Rendah     | Juara 3+  | Banyak           | Cukup         | 35       |
| 26| Rendah     | Juara 3+  | Sedang           | Kurang        | 15       |
| 27| Rendah     | Juara 3+  | Sedikit          | Kurang        | 15       |

**Interpretasi Aturan:**
- Aturan 1–9: Prestasi tingkat tinggi, juara 1–2 + banyak/sedang prestasi = sangat baik; juara 3+ + sedikit prestasi = cukup
- Aturan 10–18: Prestasi tingkat sedang, juara 1 + banyak/sedang = sangat baik; juara 3+ + sedikit = kurang
- Aturan 19–27: Prestasi tingkat rendah hanya bisa baik jika juara 1 + banyak prestasi; sisanya cukup/kurang

**Aggregasi:** Jika beberapa aturan menghasilkan output yang sama, diambil **derajat keanggotaan maksimum** (`max`) untuk output tersebut.

---

## 7. Defuzzifikasi

Menggunakan metode **Weighted Average (Centroid)**:

```
        Σ (centroid_i × degree_i)
Skor = ────────────────────────────
             Σ (degree_i)

dimana:
  centroid_i = nilai centroid output kualitas ke-i
  degree_i   = derajat keanggotaan output kualitas ke-i (hasil agregasi)
```

**Proses:**
1. Kumpulkan semua aturan yang ter-fire (degree > 0)
2. Untuk setiap output kualitas, hitung: `centroid × degree`
3. Jumlahkan semua `centroid × degree` (numerator)
4. Jumlahkan semua `degree` (denominator)
5. Bagi numerator / denominator = **skor fuzzy**
6. Konversi skor ke label kualitas berdasarkan rentang

---

## 8. Contoh Perhitungan Manual

### Studi Kasus 1: Data Nyata NIM 2455201110030 — Internasional Juara 1

**Input:**
- NIM: `2455201110030`
- Tingkat: Internasional → 5
- Juara: "juara 1" → 1.0
- Jumlah prestasi diterima: 2
- Data transaksi lain mahasiswa: Nasional, Juara 2

Data ini diambil dari tabel transaksi prestasi. Mahasiswa dengan NIM tersebut memiliki dua prestasi dengan status **diterima**. Perhitungan menggunakan transaksi Internasional Juara 1, sedangkan jumlah prestasi menggunakan total dua prestasi diterima milik mahasiswa tersebut.

**Fuzzifikasi Tingkat (5):**
- Rendah: trapezoid(5, 0, 0, 2, 3) = 0
- Sedang: triangle(5, 2, 3, 4) = 0
- Tinggi: trapezoid(5, 3, 5, 5, 5) = **1.0**

**Fuzzifikasi Juara (1.0):**
- Juara 1: triangle(1, 1, 1, 2) = **1.0**
- Juara 2: triangle(1, 1.5, 2, 3) = 0
- Juara 3+: trapezoid(1, 2.5, 3, 5, 5) = 0

**Fuzzifikasi Jumlah Prestasi (2):**
- Sedikit: trapezoid(2, 0, 0, 1, 3) = **0.5**
- Sedang: triangle(2, 2, 4, 6) = 0
- Banyak: trapezoid(2, 4, 6, 999, 999) = 0

**Evaluasi Aturan (hanya yang degree > 0):**

| Aturan | Tingkat | Juara | Jml Prestasi | Min | Output |
|--------|---------|-------|-------------|-----|--------|
| 3 | tinggi=1.0 | juara_1=1.0 | sedikit=0.5 | **0.5** | Baik |

**Agregasi:**
- Baik = **0.5**

**Defuzzifikasi:**
```
Skor = (55 × 0.5) / 0.5 = 55
```

**Output: Skor 55 → Baik**

---

### Studi Kasus 2: Provinsi Juara 3 dengan 1 Prestasi

**Input:**
- Tingkat: Provinsi → 3
- Juara: "Juara 3" → 3.0
- Jumlah Prestasi: 1

**Fuzzifikasi Tingkat (3):**
- Rendah: trapezoid(3, 0, 0, 2, 3) = 0
- Sedang: triangle(3, 2, 3, 4) = **1.0**
- Tinggi: trapezoid(3, 3, 5, 5, 5) = 0

**Fuzzifikasi Juara (3.0):**
- Juara 1: triangle(3, 1, 1, 2) = 0
- Juara 2: triangle(3, 1.5, 2, 3) = 0
- Juara 3+: trapezoid(3, 2.5, 3, 5, 5) = **1.0**

**Fuzzifikasi Jumlah Prestasi (1):**
- Sedikit: trapezoid(1, 0, 0, 1, 3) = **1.0**
- Sedang: triangle(1, 2, 4, 6) = 0
- Banyak: trapezoid(1, 4, 6, 999, 999) = 0

**Evaluasi Aturan:**
| Aturan | Tingkat | Juara | Jml Prestasi | Min | Output |
|--------|---------|-------|-------------|-----|--------|
| 16 | sedang=1.0 | juara_3+=1.0 | banyak=0 | 0 | — |
| 17 | sedang=1.0 | juara_3+=1.0 | sedang=0 | 0 | — |
| 18 | sedang=1.0 | juara_3+=1.0 | sedikit=1.0 | **1.0** | Kurang |

**Agregasi:** Kurang = 1.0

**Defuzzifikasi:**
```
Skor = (15 × 1.0) / 1.0 = 15
```

**Output: Skor 15 → Kurang**

---

### Studi Kasus 3: Kota Juara Harapan 1 dengan 4 Prestasi

**Input:**
- Tingkat: Kota → 2
- Juara: "Harapan 1" → 1.5 (Harapan = 1 + 0.5)
- Jumlah Prestasi: 4

**Fuzzifikasi Tingkat (2):**
- Rendah: trapezoid(2, 0, 0, 2, 3) = **1.0**
- Sedang: triangle(2, 2, 3, 4) = 0
- Tinggi: trapezoid(2, 3, 5, 5, 5) = 0

**Fuzzifikasi Juara (1.5):**
- Juara 1: triangle(1.5, 1, 1, 2) = **0.5**
- Juara 2: triangle(1.5, 1.5, 2, 3) = **0.0** (tepi kiri)
- Juara 3+: trapezoid(1.5, 2.5, 3, 5, 5) = 0

**Fuzzifikasi Jumlah Prestasi (4):**
- Sedikit: trapezoid(4, 0, 0, 1, 3) = 0
- Sedang: triangle(4, 2, 4, 6) = **1.0**
- Banyak: trapezoid(4, 4, 6, 999, 999) = **0.0**

**Evaluasi Aturan:**
| Aturan | Tingkat | Juara | Jml Prestasi | Min | Output |
|--------|---------|-------|-------------|-----|--------|
| 20 | rendah=1.0 | juara_1=0.5 | sedang=1.0 | **0.5** | Cukup |
| 21 | rendah=1.0 | juara_1=0.5 | sedikit=0 | 0 | — |

**Agregasi:** Cukup = 0.5

**Defuzzifikasi:**
```
Skor = (35 × 0.5) / 0.5 = 35
```

**Output: Skor 35 → Cukup**

---

## 9. Implementasi Teknis

### Struktur File

```
app/
├── Services/
│   └── FuzzyPrestasiService.php    ← FIS engine (3 variabel)
├── Http/Controllers/
│   └── FuzzyPrestasiController.php ← controller
└── Models/
    ├── PrestasiMahasiswa.php       ← model (skor_fuzzy, kualitas_fuzzy)
    └── FuzzyHasil.php              ← model detail hitungan fuzzy

resources/views/prestasi-mahasiswa/
└── fuzzy-kualitas.blade.php        ← halaman hasil

database/migrations/
├── 2026_07_12_100000_add_fuzzy_columns_to_prestasi_mahasiswa_table.php
└── 2026_07_21_100000_create_fuzzy_hasil_table.php  ← tabel baru
```

### Tabel Database

#### Tabel `fuzzy_hasil` (Baru)

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id_fuzzy_hasil` | bigint (PK) | Auto increment |
| `id_prestasi` | ulid (FK) | Relasi ke prestasi_mahasiswa |
| `nim` | string(20) | NIM mahasiswa |
| `tingkat_prestasi` | float | Nilai numerik tingkat (1–5) |
| `juara` | float | Nilai numerik juara (1.0–5.0) |
| `jumlah_prestasi` | integer | Total prestasi mahasiswa |
| `mf_tingkat_*` | float | Membership degree tingkat |
| `mf_juara_*` | float | Membership degree juara |
| `mf_jml_*` | float | Membership degree jumlah prestasi |
| `aturan_terpakai` | text (JSON) | Daftar aturan yang ter-fire |
| `skor_fuzzy` | float | Hasil defuzzifikasi 0–100 |
| `kualitas_fuzzy` | string(50) | Label kualitas |
| timestamps | | created_at, updated_at |

#### Kolom tambahan di `prestasi_mahasiswa` (Sudah ada)

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `skor_fuzzy` | float, nullable | Skor 0–100 hasil FIS |
| `kualitas_fuzzy` | string(50), nullable | Label |

### Route

| Method | URI | Nama | Keterangan |
|--------|-----|------|------------|
| GET | `/prestasi-mahasiswa/fuzzy-kualitas` | `prestasi-mahasiswa.fuzzy-kualitas` | Halaman hasil fuzzy |
| POST | `/prestasi-mahasiswa/fuzzy-kualitas/hitung` | `prestasi-mahasiswa.fuzzy-hitung` | Hitung ulang semua |

**Middleware:** `permission:prestasi.report`

### Cara Penggunaan

1. Login dengan role yang memiliki permission `prestasi.report`
2. Buka sidebar → **Kualitas Fuzzy** (atau dari halaman Laporan Prestasi → tombol "Kualitas Fuzzy")
3. Sistem otomatis menghitung jika ada data yang belum punya skor
4. Klik **"Hitung Ulang"** untuk memperbarui semua skor
5. Hasil ditampilkan berurutan dari Sangat Baik ke Kurang
6. Klik **"Detail"** untuk melihat detail fuzzifikasi per variabel dan aturan yang terpakai

### Flow Otomatis

```
User buka halaman fuzzy
        │
        ▼
┌─ Ada skor_fuzzy yang NULL? ─┐
│                              │
│  YA                          │  TIDAK
│  ▼                           ▼
│  Jalankan evaluasiSemua()    Tampilkan data dari DB
│  Untuk setiap prestasi:      termasuk fuzzy_hasil
│  1. Hitung jumlah prestasi  │
│     per mahasiswa (COUNT)   │
│  2. Fuzzifikasi 3 variabel  │
│  3. Evaluasi 27 aturan      │
│  4. Defuzzifikasi WA        │
│  5. Simpan ke DB            │
│     (prestasi_mahasiswa +   │
│      fuzzy_hasil)           │
│  ▼                           │
│  Tampilkan hasil             │
│                              │
└──────────────────────────────┘
```

---

## 10. Analisis dan Kesimpulan

### 10.1 Mengapa Metode Mamdani?

Metode **Mamdani** dipilih karena:

1. **Intuitif dan mudah dipahami**: Aturan IF-THEN menggunakan bahasa alami (rendah, sedang, tinggi) yang mudah diinterpretasikan oleh pengguna non-teknis
2. **Output fuzzy**: Menghasilkan himpunan fuzzy yang bisa didefuzzifikasi menjadi nilai crisp, cocok untuk skoring 0–100
3. **Cocok untuk sistem rekomendasi/penilaian**: Banyak digunakan dalam sistem pengambilan keputusan seperti penilaian kinerja, kelayakan beasiswa, dll.
4. **Fleksibel**: Aturan mudah ditambah/dimodifikasi tanpa mengubah struktur inti

### 10.2 Manfaat Logika Fuzzy vs Konvensional (If-Else)

| Aspek | Konvensional (If-Else) | Fuzzy Logic |
|-------|------------------------|-------------|
| **Threshold** | Kaku (Nasional=baik, Lokal=kurang) | Halus (sebagian Nasional, sebagian Lokal) |
| **Penanganan overlap** | Tidak bisa (x harus di satu kategori) | Bisa (x bisa di 2 himpunan sekaligus) |
| **Kombinasi variabel** | Kombinasi eksplosif (3×3×3=27 cabang if) | Ringkas dengan derajat keanggotaan |
| **Presisi** | Lonjakan nilai di batas threshold | Transisi gradual |
| **Contoh**: Mahasiswa A (Nasional, Juara 1, 1 prestasi) vs B (Kota, Juara 2, 10 prestasi) | If-else: A = baik, B = kurang (tidak adil) | Fuzzy: A = 85 (sangat baik), B = 40 (cukup) — lebih adil karena jumlah prestasi diperhitungkan |

**Kesimpulan:** Logika fuzzy memberikan penilaian yang lebih **adil, realistis, dan informatif** dibandingkan sistem threshold kaku, terutama ketika mengevaluasi prestasi mahasiswa yang memiliki kombinasi beragam dari tingkat, peringkat, dan kuantitas prestasi.

---

## 11. Optimasi Parameter Fuzzy

### 11.1 Masalah yang Didentifikasi

Pada konfigurasi awal, kategori **"Sangat Baik"** sangat sulit dicapai karena:

1. **Threshold terlalu tinggi (≥70):** Weighted average defuzzification membuat skor jarang mencapai 70 ketika beberapa aturan fire bersamaan
2. **Centroid "baik" terlalu tinggi (60):** Saat aturan "baik" dan "sangat_baik" keduanya fire, rata-rata tertarik ke bawah
3. **Aturan terbatas:** Hanya 3 kombinasi yang menghasilkan "sangat_baik"

### 11.2 Perubahan yang Dilakukan

#### A. Penyesuaian Centroid

| Kualitas   | Centroid Lama | Centroid Baru | Alasan |
|------------|---------------|---------------|--------|
| Kurang     | 15            | 15            | Tidak berubah |
| Cukup      | 40            | **35**        | Menurunkan bobot agar skor lebih mudah naik |
| Baik       | 60            | **55**        | Menurunkan bobot agar tidak mendominasi weighted average |
| Sangat Baik| 85            | 85            | Tidak berubah |

#### B. Penyesuaian Threshold Klasifikasi

| Kualitas   | Threshold Lama | Threshold Baru | Alasan |
|------------|----------------|----------------|--------|
| Sangat Baik| ≥ 70           | **≥ 65**       | 65 = 76% dari 85 (centroid SB), masih ketat |
| Baik       | ≥ 45           | **≥ 40**       | 40 = 73% dari 55 (centroid B) |
| Cukup      | ≥ 25           | **≥ 20**       | 20 = 57% dari 35 (centroid C) |
| Kurang     | < 25           | **< 20**       | Proporsional |

#### C. Penambahan Aturan "Sangat Baik"

| Aturan Lama | Kombinasi | Output Lama | Output Baru |
|-------------|-----------|-------------|-------------|
| Aturan 5 | Tinggi + Juara 2 + Sedang | Baik | **Sangat Baik** |
| Aturan 11 | Sedang + Juara 1 + Sedang | Baik | **Sangat Baik** |

**Alasan:** Kombinasi ini menunjukkan prestasi yang cukup baik (tingkat tinggi/sedang + peringkat 1-2 + jumlah prestasi sedang) dan layak masuk kategori "Sangat Baik".

### 11.3 Analisis Skenario Perubahan

| Skenario | Skor Lama | Kategori Lama | Skor Baru | Kategori Baru |
|----------|-----------|---------------|-----------|---------------|
| Nasional + Juara 1 + 3 prestasi | 85 | Sangat Baik ✓ | 85 | Sangat Baik ✓ |
| Nasional + Juara 2 + 3 prestasi | 60 | Baik | 67.5 | **Sangat Baik ✓** |
| Provinsi + Juara 1 + 3 prestasi | 60 | Baik | 60 | Baik (tetap) |
| Kota + Juara 1 + 3 prestasi | 40 | Cukup | 40 | **Baik ✓** |
| Nasional + Juara 1 + 2 prestasi | 60 | Baik | 76 | **Sangat Baik ✓** |

### 11.4 Contoh Perhitungan: Nasional Juara 2 + 3 Prestasi

**Input:**
- Tingkat: Nasional → 4
- Juara: "Juara 2" → 2.0
- Jumlah Prestasi: 3

**Fuzzifikasi:**
- Tingkat: tinggi=0.5, sedang=0.0, rendah=0.0
- Juara: juara_1=0.0, juara_2=1.0, juara_3+=0.0
- Jml: sedikit=0.0, sedang=0.5, banyak=0.0

**Evaluasi Aturan (baru):**
| Aturan | Kombinasi | Min | Output |
|--------|-----------|-----|--------|
| 5 | tinggi(0.5) + juara_2(1.0) + sedang(0.5) | 0.5 | **Sangat Baik** |

**Defuzzifikasi:**
```
Skor = (85 × 0.5) / 0.5 = 85
→ Threshold ≥ 65 → Sangat Baik ✓
```

**Sebelum perubahan:** Aturan 5 menghasilkan "baik" (centroid 60), skor = 60 → "Baik"

### 11.5 Kesimpulan Optimasi

Perubahan parameter membuat distribusi "Sangat Baik" lebih representatif terhadap data yang memang memiliki kombinasi tingkat kompetisi tinggi, peringkat juara baik, dan jumlah prestasi memadai. Threshold 65 masih menjaga kategori "Sangat Baik" tetap eksklusif (hanya 24% lebih rendah dari centroid 85).

---

## 12. Referensi

- **Metode:** Mamdani Fuzzy Inference System
- **Operator AND:** Minimum (min)
- **Agregasi:** Maximum (max) per output
- **Defuzzifikasi:** Weighted Average (centroid)
- **Jumlah Aturan:** 27 rules (3 × 3 × 3 kombinasi input)
- **Jumlah Variabel Input:** 3 (Tingkat, Juara, Jumlah Prestasi)
- **Jumlah Variabel Output:** 1 (Kualitas)
- **Library:** Implementasi pure PHP, tanpa dependency eksternal
- **Lokasi kode:** `app/Services/FuzzyPrestasiService.php`
