# Dokumentasi UTS — Implementasi Logika Fuzzy Sistem Rekomendasi Prestasi Mahasiswa

**Mata Kuliah:** Pemrograman Web Lanjut  
**NIM:** 22101001  
**Nama:** (Isi Nama Anda)  
**Aplikasi:** MyCampus (Sistem Informasi Akademik) — Laravel 13 + MySQL

---

## 1. Identifikasi Masalah dan Pemilihan Metode

### 1.1 Rumusan Masalah

Sistem akademik (MyCampus) memiliki data prestasi mahasiswa yang meliputi tingkat kompetisi, peringkat juara, dan jumlah prestasi yang diraih. Penilaian kelayakan penghargaan/beasiswa prestasi saat ini dilakukan secara manual dengan if-else konvensional, sehingga menghasilkan keputusan kaku (*crisp*) tanpa mempertimbangkan gradasi antar kategori.

### 1.2 Pemilihan Metode: Mamdani

Metode **Mamdani** dipilih dengan pertimbangan:

- **Interpretabilitas tinggi:** Setiap aturan IF-THEN mudah dipahami oleh manusia (misal: "JIKA tingkat Nasional DAN juara 1 DAN banyak prestasi MAKA kualitas Sangat Baik").
- **Cocok untuk sistem keputusan berbasis aturan:** Input berupa data kualitatif (tingkat kompetisi) dan kuantitatif (juara, jumlah prestasi) yang cocok dengan pendekatan rule-based.
- **Output linguistik:** Mamdani menghasilkan output berupa label kualitas (Sangat Baik, Baik, Cukup, Kurang) yang mudah diinterpretasikan.
- **Input multi-variabel:** Mamdani mendukung kombinasi banyak input dengan operasi MIN (AND) dan MAX (OR).

---

## 2. Definisi Variabel Fuzzy

### 2.1 Variabel Input (3 variabel)

#### Input 1: Tingkat Kompetisi ($x_1$)

| Aspek | Keterangan |
|-------|-----------|
| Nama variabel | Tingkat Prestasi |
| Domain/Semesta pembicaraan | $[1, 5]$ |
| Pemetaan | Kampus=1, Kota=2, Provinsi=3, Nasional=4, Internasional=5 |
| Himpunan fuzzy | Rendah, Sedang, Tinggi |

**Fungsi Keanggotaan:**

| Himpunan | Tipe | Parameter | Rumus |
|----------|------|-----------|-------|
| Rendah | Trapezoid | (0, 0, 2, 3) | $\mu_{Rendah}(x) = \begin{cases} 0 & x \leq 0 \\ 1 & 0 < x \leq 2 \\ \frac{3-x}{3-2} & 2 < x \leq 3 \\ 0 & x > 3 \end{cases}$ |
| Sedang | Segitiga | (2, 3, 4) | $\mu_{Sedang}(x) = \begin{cases} 0 & x \leq 2 \\ \frac{x-2}{3-2} & 2 < x \leq 3 \\ \frac{4-x}{4-3} & 3 < x < 4 \\ 0 & x \geq 4 \end{cases}$ |
| Tinggi | Trapezoid | (3, 5, 5, 5) | $\mu_{Tinggi}(x) = \begin{cases} 0 & x \leq 3 \\ \frac{x-3}{5-3} & 3 < x < 5 \\ 1 & x \geq 5 \end{cases}$ |

**Grafik Fungsi Keanggotaan:**

```
μ(x)
1.0 ┤        ╱‾‾‾‾‾‾‾‾‾‾‾‾‾‾╲
    │       ╱                  ╲
    │      ╱    Sedang          ╲
    │     ╱       ╱╲             ╲
    │    ╱       ╱  ╲             ╲
0.5 ┤   ╱       ╱    ╲             ╲
    │  ╱ Rendah╱      ╲    Tinggi   ╲
    │ ╱       ╱        ╲             ╲
    │╱       ╱          ╲             ╲
0.0 ┼───────┼────┼───────┼──────┼─────┤
    0       1    2       3      4     5
                Domain (Tingkat)
```

#### Input 2: Peringkat Juara ($x_2$)

| Aspek | Keterangan |
|-------|-----------|
| Nama variabel | Juara |
| Domain/Semesta pembicaraan | $[1, 5]$ |
| Parsing | Digit diekstrak dari string; "Harapan" menambah +0.5 |
| Himpunan fuzzy | Juara 1, Juara 2, Juara 3+ |

**Fungsi Keanggotaan:**

| Himpunan | Tipe | Parameter | Rumus |
|----------|------|-----------|-------|
| Juara 1 | Segitiga | (1, 1, 2) | $\mu_{Juara1}(x) = \begin{cases} 0 & x < 1 \\ 1 & x = 1 \\ \frac{2-x}{2-1} & 1 < x < 2 \\ 0 & x \geq 2 \end{cases}$ |
| Juara 2 | Segitiga | (1.5, 2, 3) | $\mu_{Juara2}(x) = \begin{cases} 0 & x \leq 1.5 \\ \frac{x-1.5}{2-1.5} & 1.5 < x \leq 2 \\ \frac{3-x}{3-2} & 2 < x < 3 \\ 0 & x \geq 3 \end{cases}$ |
| Juara 3+ | Trapezoid | (2.5, 3, 5, 5) | $\mu_{Juara3+}(x) = \begin{cases} 0 & x \leq 2.5 \\ \frac{x-2.5}{3-2.5} & 2.5 < x < 3 \\ 1 & x \geq 3 \end{cases}$ |

**Grafik Fungsi Keanggotaan:**

```
μ(x)
1.0 ┤╲      ╱╲        ╱‾‾‾‾‾‾‾‾‾
    │ ╲    ╱  ╲      ╱
    │  ╲  ╱    ╲    ╱
    │   ╲╱      ╲  ╱
0.5 ┤   Juara1   ╲╱    Juara 3+
    │          Juara2
    │
0.0 ┼────┼────┼────┼────┼────┤
    1   1.5   2   2.5  3    4    5
              Domain (Juara)
```

#### Input 3: Jumlah Prestasi ($x_3$)

| Aspek | Keterangan |
|-------|-----------|
| Nama variabel | Jumlah Prestasi (per mahasiswa, status diterima) |
| Domain/Semesta pembicaraan | $[0, \infty)$ (batas praktis: 0–20) |
| Himpunan fuzzy | Sedikit, Sedang, Banyak |

**Fungsi Keanggotaan:**

| Himpunan | Tipe | Parameter | Rumus |
|----------|------|-----------|-------|
| Sedikit | Trapezoid | (0, 0, 1, 3) | $\mu_{Sedikit}(x) = \begin{cases} 1 & 0 \leq x \leq 1 \\ \frac{3-x}{3-1} & 1 < x \leq 3 \\ 0 & x > 3 \end{cases}$ |
| Sedang | Segitiga | (2, 4, 6) | $\mu_{Sedang}(x) = \begin{cases} 0 & x \leq 2 \\ \frac{x-2}{4-2} & 2 < x \leq 4 \\ \frac{6-x}{6-4} & 4 < x < 6 \\ 0 & x \geq 6 \end{cases}$ |
| Banyak | Trapezoid | (4, 6, 999, 999) | $\mu_{Banyak}(x) = \begin{cases} 0 & x \leq 4 \\ \frac{x-4}{6-4} & 4 < x < 6 \\ 1 & x \geq 6 \end{cases}$ |

**Grafik Fungsi Keanggotaan:**

```
μ(x)
1.0 ┤‾‾‾‾‾╲           ╱‾‾‾‾‾‾‾‾‾‾
    │       ╲         ╱
    │        ╲       ╱
    │    Sedikit╲   ╱ Banyak
0.5 ┤         Sedang
    │
0.0 ┼──┼──┼──┼──┼──┼──┼──┼──┤
    0  1  2  3  4  5  6  7
         Domain (Jumlah Prestasi)
```

### 2.2 Variabel Output (1 variabel)

#### Output: Skor Kelayakan Penghargaan ($y$)

| Aspek | Keterangan |
|-------|-----------|
| Nama variabel | Skor Kualitas Prestasi |
| Domain/Semesta pembicaraan | $[0, 100]$ |
| Himpunan fuzzy | Kurang, Cukup, Baik, Sangat Baik |
| Centroid per himpunan | Kurang=15, Cukup=40, Baik=60, Sangat Baik=85 |
| Label akhir | Skor ≥ 70 → Sangat Baik; ≥ 45 → Baik; ≥ 25 → Cukup; < 25 → Kurang |

---

## 3. Rule Base (Aturan Fuzzy)

### 3.1 Tabel Aturan (27 rules — kombinasi 3×3×3)

| No | IF | | | THEN |
|----|-----|---|---|------|
| | Tingkat | Juara | Jumlah Prestasi | Kualitas |
| 1 | Tinggi | 1 | Banyak | **Sangat Baik** |
| 2 | Tinggi | 1 | Sedang | **Sangat Baik** |
| 3 | Tinggi | 1 | Sedikit | **Baik** |
| 4 | Tinggi | 2 | Banyak | **Sangat Baik** |
| 5 | Tinggi | 2 | Sedang | **Baik** |
| 6 | Tinggi | 2 | Sedikit | **Baik** |
| 7 | Tinggi | 3+ | Banyak | **Baik** |
| 8 | Tinggi | 3+ | Sedang | **Cukup** |
| 9 | Tinggi | 3+ | Sedikit | **Cukup** |
| 10 | Sedang | 1 | Banyak | **Sangat Baik** |
| 11 | Sedang | 1 | Sedang | **Baik** |
| 12 | Sedang | 1 | Sedikit | **Baik** |
| 13 | Sedang | 2 | Banyak | **Baik** |
| 14 | Sedang | 2 | Sedang | **Cukup** |
| 15 | Sedang | 2 | Sedikit | **Cukup** |
| 16 | Sedang | 3+ | Banyak | **Cukup** |
| 17 | Sedang | 3+ | Sedang | **Cukup** |
| 18 | Sedang | 3+ | Sedikit | **Kurang** |
| 19 | Rendah | 1 | Banyak | **Baik** |
| 20 | Rendah | 1 | Sedang | **Cukup** |
| 21 | Rendah | 1 | Sedikit | **Cukup** |
| 22 | Rendah | 2 | Banyak | **Cukup** |
| 23 | Rendah | 2 | Sedang | **Cukup** |
| 24 | Rendah | 2 | Sedikit | **Kurang** |
| 25 | Rendah | 3+ | Banyak | **Cukup** |
| 26 | Rendah | 3+ | Sedang | **Kurang** |
| 27 | Rendah | 3+ | Sedikit | **Kurang** |

### 3.2 Metode Inferensi: Mamdani (Min-Max)

- **Antecedent (IF part):** Operator AND = **MIN** (minimum dari semua derajat keanggotaan input)
- **Implication (THEN part):** Derajat output = nilai MIN dari antecedent
- **Aggregation:** **MAX** (maksimum dari semua aturan yang menghasilkan output yang sama)

---

## 4. Contoh Perhitungan Manual

### 4.1 Data Kasus Mahasiswa

| Field | Nilai |
|-------|-------|
| NIM | 22101001 (ganjil) |
| Nama | Ahmad Rizki Pratama |
| Tingkat Kompetisi | Provinsi → nilai $x_1 = 3$ |
| Juara | "2" → parsed $x_2 = 2$ |
| Jumlah Prestasi (diterima) | 5 → $x_3 = 5$ |

### 4.2 Tahap 1: Fuzzifikasi

#### Fuzzifikasi Tingkat ($x_1 = 3$)

- $\mu_{Rendah}(3)$: trapezoid(3, 0, 0, 2, 3)
  - $3 \leq 0$? Tidak. $3 > 3$? Tidak. $3 \geq 0 \land 3 \leq 2$? Tidak. $3 < 0$? Tidak.
  - $\frac{3-3}{3-2} = \frac{0}{1} = 0$

- $\mu_{Sedang}(3)$: triangle(3, 2, 3, 4)
  - $3 < 2$? Tidak. $3 \geq 4$? Tidak. $3 \leq 3$? Ya.
  - $\frac{3-2}{3-2} = \frac{1}{1} = \mathbf{1.0}$

- $\mu_{Tinggi}(3)$: trapezoid(3, 3, 5, 5, 5)
  - $3 \leq 3$? Ya. → **0**

**Hasil:** Rendah = 0, **Sedang = 1.0**, Tinggi = 0

#### Fuzzifikasi Juara ($x_2 = 2$)

- $\mu_{Juara1}(2)$: triangle(2, 1, 1, 2)
  - $2 < 1$? Tidak. $2 \geq 2$? Ya. → **0**

- $\mu_{Juara2}(2)$: triangle(2, 1.5, 2, 3)
  - $2 < 1.5$? Tidak. $2 \geq 3$? Tidak. $2 \leq 2$? Ya.
  - $\frac{2-1.5}{2-1.5} = \frac{0.5}{0.5} = \mathbf{1.0}$

- $\mu_{Juara3+}(2)$: trapezoid(2, 2.5, 3, 5, 5)
  - $2 \leq 2.5$? Ya. → **0**

**Hasil:** Juara 1 = 0, **Juara 2 = 1.0**, Juara 3+ = 0

#### Fuzzifikasi Jumlah Prestasi ($x_3 = 5$)

- $\mu_{Sedikit}(5)$: trapezoid(5, 0, 0, 1, 3)
  - $5 > 3$? Ya. → **0**

- $\mu_{Sedang}(5)$: triangle(5, 2, 4, 6)
  - $5 < 2$? Tidak. $5 \geq 6$? Tidak. $5 \leq 4$? Tidak.
  - $\frac{6-5}{6-4} = \frac{1}{2} = \mathbf{0.5}$

- $\mu_{Banyak}(5)$: trapezoid(5, 4, 6, 999, 999)
  - $5 \leq 4$? Tidak. $5 > 999$? Tidak. $5 \geq 6$? Tidak. $5 < 6$? Ya.
  - $\frac{5-4}{6-4} = \frac{1}{2} = \mathbf{0.5}$

**Hasil:** Sedikit = 0, **Sedang = 0.5**, **Banyak = 0.5**

### 4.3 Tahap 2: Evaluasi Rule Base (Inferensi)

Hanya aturan yang memiliki kombinasi input dengan derajat > 0 yang akan terbakar (*fired*):

| No | Aturan | Tingkat | Juara | Jml | MIN | Output |
|----|--------|---------|-------|-----|-----|--------|
| 13 | IF Sedang AND Juara 2 AND Banyak | 1.0 | 1.0 | 0.5 | **0.5** | **Baik** |
| 14 | IF Sedang AND Juara 2 AND Sedang | 1.0 | 1.0 | 0.5 | **0.5** | **Cukup** |

Aturan lain tidak terbakar karena salah satu input bernilai 0.

**Aggregasi (MAX per output):**
- $\mu_{Baik} = 0.5$ (dari aturan 13)
- $\mu_{Cukup} = 0.5$ (dari aturan 14)

### 4.4 Tahap 3: Defuzzifikasi (Weighted Average)

$$\text{Skor} = \frac{\sum_{i} (\text{centroid}_i \times \mu_i)}{\sum_{i} \mu_i}$$

$$\text{Skor} = \frac{(60 \times 0.5) + (40 \times 0.5)}{0.5 + 0.5} = \frac{30 + 20}{1.0} = \mathbf{50.0}$$

### 4.5 Hasil Akhir

| Aspek | Nilai |
|-------|-------|
| Skor Fuzzy | **50.0** |
| Kategori Kualitas | **Baik** (45 ≤ 50 < 70) |
| Kelayakan | Layak penghargaan (skor ≥ 45) |

### 4.6 Ringkasan Alur Perhitungan

```
Input: Tingkat=3 (Provinsi), Juara=2, JmlPrestasi=5
                    │
        ┌───────────┼───────────┐
        ▼           ▼           ▼
   Fuzzifikasi  Fuzzifikasi  Fuzzifikasi
   Tingkat:     Juara:       JmlPrestasi:
   Sedang=1.0   Juara2=1.0   Sedang=0.5, Banyak=0.5
        │           │           │
        └───────────┼───────────┘
                    ▼
          ┌─────────────────┐
          │  Rule Evaluation │
          │  (MIN per rule)  │
          ├─────────────────┤
          │ R13: 0.5 → Baik │
          │ R14: 0.5 → Cukup│
          └────────┬────────┘
                   ▼
          ┌─────────────────┐
          │  Aggregation     │
          │  (MAX per output)│
          ├─────────────────┤
          │ Baik: 0.5        │
          │ Cukup: 0.5       │
          └────────┬────────┘
                   ▼
          ┌─────────────────┐
          │  Defuzzifikasi   │
          │  Weighted Average│
          ├─────────────────┤
          │ (60×0.5+40×0.5) │
          │   / (0.5+0.5)   │
          │   = 50.0         │
          └────────┬────────┘
                   ▼
          Output: Skor=50.0
                  Kualitas=Baik
```

---

## 5. ERD (Entity Relationship Diagram)

### 5.1 Diagram ERD

```
┌──────────────────┐       ┌────────────────────┐       ┌──────────────────┐
│   mahasiswa       │       │      prestasi       │       │  jenis_prestasi  │
├──────────────────┤       ├────────────────────┤       ├──────────────────┤
│ nim (PK)         │──┐    │ id_prestasi (PK)   │   ┌──│ id_jenis (PK)   │
│ nama             │  └───▶│ nim (FK)           │   │  │ nama_jenis      │
│ email            │       │ id_jenis (FK)      │◀──┘  └──────────────────┘
│ no_telp          │       │ id_tingkat (FK)    │──┐
│ tanggal_lahir    │       │ nama_lomba         │  │  ┌──────────────────┐
│ ...              │       │ penyelenggara      │  └─▶│ tingkat_prestasi │
└──────────────────┘       │ tanggal            │     ├──────────────────┤
                           │ juara              │     │ id_tingkat (PK) │
                           │ sertifikat         │     │ nama_tingkat    │
                           │ status_verifikasi  │     └──────────────────┘
                           │ skor_fuzzy         │
                           │ kualitas_fuzzy     │
                           │ kode_prestasi      │
                           └─────────┬──────────┘
                                     │
                                     │ 1:1
                                     ▼
                           ┌────────────────────┐
                           │   fuzzy_hasil       │
                           ├────────────────────┤
                           │ id_fuzzy_hasil(PK) │
                           │ id_prestasi (FK)   │
                           │ nim (FK)           │
                           │ tingkat_prestasi   │
                           │ juara              │
                           │ jumlah_prestasi    │
                           │ mf_tingkat_rendah  │
                           │ mf_tingkat_sedang  │
                           │ mf_tingkat_tinggi  │
                           │ mf_juara_1         │
                           │ mf_juara_2         │
                           │ mf_juara_3_plus    │
                           │ mf_jml_sedikit     │
                           │ mf_jml_sedang      │
                           │ mf_jml_banyak      │
                           │ aturan_terpakai    │
                           │ skor_fuzzy         │
                           │ kualitas_fuzzy     │
                           └────────────────────┘
```

### 5.2 Tabel Baru: fuzzy_hasil

| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| id_fuzzy_hasil | bigint (PK, auto) | Primary key |
| id_prestasi | ULID (FK → prestasi) | Referensi ke prestasi |
| nim | string (FK → mahasiswa) | NIM mahasiswa |
| tingkat_prestasi | float | Nilai numerik tingkat (1-5) |
| juara | float | Nilai parsed juara |
| jumlah_prestasi | int | Total prestasi diterima |
| mf_tingkat_rendah | float | Derajat keanggotaan himpunan Rendah |
| mf_tingkat_sedang | float | Derajat keanggotaan himpunan Sedang |
| mf_tingkat_tinggi | float | Derajat keanggotaan himpunan Tinggi |
| mf_juara_1 | float | Derajat keanggotaan Juara 1 |
| mf_juara_2 | float | Derajat keanggotaan Juara 2 |
| mf_juara_3_plus | float | Derajat keanggotaan Juara 3+ |
| mf_jml_sedikit | float | Derajat keanggotaan Sedikit |
| mf_jml_sedang | float | Derajat keanggotaan Sedang |
| mf_jml_banyak | float | Derajat keanggotaan Banyak |
| aturan_terpakai | text (JSON) | Aturan yang terbakar + derajat |
| skor_fuzzy | float | Hasil defuzzifikasi (0-100) |
| kualitas_fuzzy | string(50) | Label kualitas akhir |

---

## 6. Arsitektur Laravel (Model, Migration, Controller, View)

### 6.1 Struktur File

```
app/
├── Models/
│   ├── PrestasiMahasiswa.php    # Model utama prestasi
│   └── FuzzyHasil.php           # Model hasil perhitungan fuzzy
├── Services/
│   └── FuzzyPrestasiService.php # Service fuzzy (fuzzifikasi, inferensi, defuzzifikasi)
└── Http/Controllers/
    └── FuzzyPrestasiController.php  # Controller halaman fuzzy

database/
└── migrations/
    ├── 2026_07_12_100000_add_fuzzy_columns_to_prestasi_mahasiswa_table.php
    └── 2026_07_21_100000_create_fuzzy_hasil_table.php

resources/views/prestasi-mahasiswa/
└── fuzzy-kualitas.blade.php      # View halaman fuzzy
```

### 6.2 Flowchart Alur Program

```
┌─────────────────────────────┐
│   User mengakses halaman    │
│   Kualitas Fuzzy            │
└─────────────┬───────────────┘
              ▼
┌─────────────────────────────┐
│   FuzzyPrestasiController   │
│   -> index()                │
└─────────────┬───────────────┘
              ▼
┌─────────────────────────────┐
│   Load semua prestasi       │
│   dengan status diterima    │
│   (eager load relasi)       │
└─────────────┬───────────────┘
              ▼
┌─────────────────────────────┐
│   Cek apakah ada prestasi   │
│   yang belum memiliki       │
│   skor_fuzzy (null)         │
└──────────┬──────────────────┘
     Ya    │    Tidak
     ▼     │    ▼
┌──────────┴───────┐  ┌──────────────────┐
│ Hitung fuzzy     │  │  Langsung tampil  │
│ untuk semua data │  │  hasil fuzzy     │
│ (evaluasiSemua)  │  └──────────────────┘
└──────────┬───────┘
           ▼
┌─────────────────────────────┐
│   Untuk SETIAP prestasi:    │
│                             │
│  1. Ambil: tingkat, juara,  │
│     jumlah_prestasi         │
│                             │
│  2. Fuzzifikasi:            │
│     - tingkat → MF tingkat  │
│     - juara → MF juara      │
│     - jml → MF jumlah       │
│                             │
│  3. Evaluasi 27 aturan:     │
│     - MIN per antecedent    │
│     - MAX per output        │
│                             │
│  4. Defuzzifikasi:          │
│     Weighted Average        │
│                             │
│  5. Simpan ke:              │
│     - fuzzy_hasil           │
│     - prestasi.skor_fuzzy   │
└──────────┬──────────────────┘
           ▼
┌─────────────────────────────┐
│   Tampilkan hasil di view   │
│   (tabel + modal detail +   │
│    grafik distribusi)       │
└─────────────────────────────┘
```

### 6.3 Kode Program Utama

**FuzzyPrestasiService.php** — 3 komponen utama:

1. **Fuzzifikasi:** Menghitung derajat keanggotaan untuk setiap input menggunakan fungsi `triangle()` dan `trapezoid()`.

2. **Evaluasi Aturan:** 27 aturan IF-THEN dengan operator MIN (antecedent) dan MAX (aggregasi per output).

3. **Defuzzifikasi:** Weighted Average — `skor = Σ(centroid × degree) / Σ(degree)`

**FuzzyPrestasiController.php:**
- `index()` — Memuat data, menjalankan fuzzy otomatis jika ada data yang belum dihitung, menyiapkan data untuk view dan grafik.
- `hitungUlang()` — Menjalankan ulang evaluasi fuzzy untuk semua data.

---

## 7. Analisis dan Kesimpulan

### 7.1 Mengapa Mamdani Dipilih

| Aspek | Mamdani | Tsukamoto |
|-------|---------|-----------|
| Output | Himpunan fuzzy (linguistik) | Fungsi keanggotaan terbalik → crisp |
| Aturan | Menghasilkan output berupa label kualitas | Menghasilkan output berupa nilai numerik langsung |
| Interpretabilitas | **Tinggi** — aturan mudah dibaca | Sedang — perlu invers fungsi |
| Kasus ini | **Cocok** — output berupa kategori kualitas | Kurang cocok — ingin label + skor |

**Kesimpulan:** Mamdani cocok karena sistem ini membutuhkan output berupa label linguistik (Sangat Baik, Baik, Cukup, Kurang) yang mudah dipahami pengguna, serta skor numerik (0-100) dari defuzzifikasi weighted average.

### 7.2 Keunggulan Fuzzy vs Konvensional (if-else)

| Aspek | If-Else Konvensional | Logika Fuzzy |
|-------|---------------------|--------------|
| Keputusan | Kaku (true/false) | Gradasi (0 s/d 1) |
| Batas kategori | Tegas (misal: ≥ 70 = Sangat Baik) | Fleksibel dengan derajat keanggotaan |
| Multi-variabel | Bersarang, kompleks | Kombinasi alami dengan aturan |
| Kasus tepi | Sering salah klasifikasi | Menangani batas kategori dengan tepat |
| Transparansi | Sulit dimengerti jika banyak | Aturan IF-THEN mudah dibaca |

**Contoh kasus tepi:** Mahasiswa dengan juara 2 di tingkat Nasional dan 4 prestasi. Sistem if-else mungkin langsung mengklasifikasikan sebagai "Baik" secara kaku, sedangkan fuzzy mempertimbangkan kombinasi semua input dan menghasilkan skor 50.0 (Baik) dengan derajat keanggotaan yang jelas.

### 7.3 Manfaat Penerapan

1. **Keputusan lebih adil:** Mahasiswa di batas kategori tidak dirugikan oleh ketegasan batas if-else.
2. **Transparansi:** Setiap mahasiswa dapat melihat detail fuzzifikasi (derajat keanggotaan) dan aturan mana yang terbakar.
3. **Fleksibilitas:** Parameter fungsi keanggotaan dapat disesuaikan tanpa mengubah logika program.
4. **Skor komposit:** Menggabungkan 3 dimensi penilaian (tingkat, juara, jumlah) menjadi 1 skor yang utuh.
5. **Auditabilitas:** Tabel `fuzzy_hasil` menyimpan seluruh proses perhitungan untuk audit.

---

## 8. Konfigurasi Environment (.env)

```env
APP_NAME=MyCampus
APP_ENV=local
APP_KEY=base64:leGvhobbeAc+VcGcDIdD5yy7PaSUxl5ahKmgD4nPinY=
APP_DEBUG=true
APP_URL=http://localhost

APP_LOCALE=id
APP_FALLBACK_LOCALE=id
APP_FAKER_LOCALE=id_ID

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=PROJECT1
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
CACHE_STORE=database
```

**Catatan:**
- `APP_LOCALE=id` — Locale diatur ke Bahasa Indonesia untuk format tanggal, angka, dan pesan error bawaan Laravel.
- `DB_DATABASE=PROJECT1` — Database MySQL yang digunakan untuk menyimpan data prestasi dan hasil fuzzy.
- `SESSION_DRIVER=database` — Sesi disimpan di database untuk kompatibilitas dengan Laragon.
- `APP_DEBUG=true` — Aktifkan di environment development untuk debugging.
