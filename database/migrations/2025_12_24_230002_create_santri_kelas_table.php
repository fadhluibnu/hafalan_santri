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
        Schema::create('santri_kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained('santris')->onDelete('cascade');
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans')->onDelete('cascade');
            $table->enum('status', ['aktif', 'naik_kelas', 'lulus', 'keluar', 'pindah'])->default('aktif');
            $table->date('tanggal_masuk');
            $table->date('tanggal_keluar')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
            
            // Unique constraint: satu santri hanya bisa di satu kelas per tahun ajaran
            $table->unique(['santri_id', 'tahun_ajaran_id'], 'santri_tahun_ajaran_unique');
            
            // Index for faster queries
            $table->index(['kelas_id', 'tahun_ajaran_id']);
            $table->index(['santri_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('santri_kelas');
    }
};
