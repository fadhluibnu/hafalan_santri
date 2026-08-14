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
        Schema::create('tahun_ajarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pondok_id')->constrained('pondoks')->onDelete('cascade');
            $table->string('nama'); // Format: 2024/2025
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->boolean('is_active')->default(false);
            $table->enum('status', ['aktif', 'selesai'])->default('aktif');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            
            // Index for faster queries
            $table->index(['pondok_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tahun_ajarans');
    }
};
