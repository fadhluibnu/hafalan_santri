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
        Schema::create('juz_santris', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained('santris')->onDelete('cascade');
            $table->unsignedTinyInteger('no_juz'); // Efisien untuk angka 1-30
            $table->string('status')->default('belum'); // Opsi: belum, proses, sah, ulang
            $table->date('tanggal_sah')->nullable();
            $table->timestamps();

            // Memastikan tidak ada data duplikat untuk santri dan juz yang sama
            $table->unique(['santri_id', 'no_juz']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('juz_santris');
    }
};
