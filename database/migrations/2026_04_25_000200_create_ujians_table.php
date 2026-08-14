<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ujians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pondok_id')->constrained('pondoks')->onDelete('cascade');
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans')->onDelete('cascade');
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');
            $table->foreignId('ustadz_id')->constrained('ustadzs')->onDelete('cascade');
            $table->foreignId('skema_penilaian_id')->constrained('skema_penilaians')->onDelete('cascade');
            $table->string('nama');
            $table->date('tanggal_ujian');
            $table->json('skema_snapshot');
            $table->enum('status', ['draft', 'selesai'])->default('draft');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index(['pondok_id', 'tahun_ajaran_id', 'kelas_id']);
            $table->index(['kelas_id', 'tanggal_ujian']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ujians');
    }
};
