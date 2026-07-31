<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fuzzy_hasil', function (Blueprint $table) {
            $table->id('id_fuzzy_hasil');
            $table->ulid('id_prestasi');
            $table->string('nim', 20);
            $table->float('tingkat_prestasi');
            $table->float('juara');
            $table->integer('jumlah_prestasi');
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

            $table->foreign('id_prestasi')->references('id_prestasi')->on('prestasi')->cascadeOnDelete();
            $table->foreign('nim')->references('nim')->on('mahasiswa')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fuzzy_hasil');
    }
};
