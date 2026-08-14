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
        Schema::create('ujian_nilais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ujian_id')->constrained('ujians')->onDelete('cascade');
            $table->foreignId('santri_id')->constrained('santris')->onDelete('cascade');
            $table->decimal('nilai_angka', 10, 2)->nullable();
            $table->string('nilai_label')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique(['ujian_id', 'santri_id']);
            $table->index(['santri_id', 'ujian_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ujian_nilais');
    }
};
