<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jenis_prestasi', function (Blueprint $table) {
            $table->id('id_jenis');
            $table->string('nama_jenis', 100);
            $table->timestamps();
        });

        Schema::create('tingkat_prestasi', function (Blueprint $table) {
            $table->id('id_tingkat');
            $table->string('nama_tingkat', 100);
            $table->timestamps();
        });

        Schema::create('admin_prestasi', function (Blueprint $table) {
            $table->id('id_admin');
            $table->string('nama', 100);
            $table->string('email', 100)->unique();
            $table->string('password');
            $table->string('role', 50)->default('admin');
            $table->timestamps();
        });

        Schema::create('prestasi_mahasiswa', function (Blueprint $table) {
            $table->ulid('id_prestasi')->primary();
            $table->string('nim', 20);
            $table->foreignId('id_jenis')->constrained('jenis_prestasi', 'id_jenis')->cascadeOnUpdate();
            $table->foreignId('id_tingkat')->constrained('tingkat_prestasi', 'id_tingkat')->cascadeOnUpdate();
            $table->string('nama_lomba', 150);
            $table->string('penyelenggara', 150);
            $table->date('tanggal');
            $table->string('juara', 100);
            $table->string('sertifikat')->nullable();
            $table->enum('status_verifikasi', ['menunggu', 'diterima', 'ditolak'])->default('menunggu');
            $table->timestamps();

            $table->foreign('nim')->references('nim')->on('mahasiswa')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::create('verifikasi_prestasi', function (Blueprint $table) {
            $table->id('id_verifikasi');
            $table->ulid('id_prestasi');
            $table->foreign('id_prestasi')->references('id_prestasi')->on('prestasi_mahasiswa')->cascadeOnDelete();
            $table->foreignId('id_admin')->constrained('admin_prestasi', 'id_admin')->cascadeOnUpdate();
            $table->date('tanggal_verifikasi');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        DB::table('jenis_prestasi')->insert([
            ['nama_jenis' => 'Akademik', 'created_at' => now(), 'updated_at' => now()],
            ['nama_jenis' => 'Non Akademik', 'created_at' => now(), 'updated_at' => now()],
            ['nama_jenis' => 'Olahraga', 'created_at' => now(), 'updated_at' => now()],
            ['nama_jenis' => 'Seni', 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('tingkat_prestasi')->insert([
            ['nama_tingkat' => 'Kampus', 'created_at' => now(), 'updated_at' => now()],
            ['nama_tingkat' => 'Kota', 'created_at' => now(), 'updated_at' => now()],
            ['nama_tingkat' => 'Provinsi', 'created_at' => now(), 'updated_at' => now()],
            ['nama_tingkat' => 'Nasional', 'created_at' => now(), 'updated_at' => now()],
            ['nama_tingkat' => 'Internasional', 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('admin_prestasi')->insert([
            'nama' => 'Admin Prestasi',
            'email' => 'admin.prestasi@kampus.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('verifikasi_prestasi');
        Schema::dropIfExists('prestasi_mahasiswa');
        Schema::dropIfExists('admin_prestasi');
        Schema::dropIfExists('tingkat_prestasi');
        Schema::dropIfExists('jenis_prestasi');
    }
};
