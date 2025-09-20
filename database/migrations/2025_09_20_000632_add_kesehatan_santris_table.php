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
        Schema::table('kesehatan_santris', function (Blueprint $table) {
            $table->foreignId('santri_id')->constrained()->onDelete('cascade');
            $table->string('golongan_darah'); // wajib diisi
            $table->decimal('berat_badan', 5, 2)->nullable();
            $table->decimal('tinggi_badan', 5, 2)->nullable();
            $table->text('riwayat_penyakit')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kesehatan_santris', function (Blueprint $table) {
            $table->dropColumn(['golongan_darah', 'berat_badan', 'tinggi_badan', 'riwayat_penyakit']);
        });
    }
};
