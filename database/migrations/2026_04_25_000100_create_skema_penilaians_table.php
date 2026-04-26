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
        Schema::create('skema_penilaians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pondok_id')->constrained('pondoks')->onDelete('cascade');
            $table->string('nama');
            $table->enum('tipe', ['label', 'numeric'])->default('label');
            $table->decimal('numeric_min', 10, 2)->nullable();
            $table->decimal('numeric_max', 10, 2)->nullable();
            $table->decimal('numeric_step', 10, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            // Satu pondok hanya punya satu skema aktif untuk v1.
            $table->unique('pondok_id');
            $table->index(['pondok_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skema_penilaians');
    }
};
