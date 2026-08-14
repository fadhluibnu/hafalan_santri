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
        Schema::create('skema_penilaian_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skema_penilaian_id')
                ->constrained('skema_penilaians')
                ->onDelete('cascade');
            $table->string('label');
            $table->unsignedInteger('urutan')->default(0);
            $table->timestamps();

            $table->index(['skema_penilaian_id', 'urutan']);
            $table->unique(['skema_penilaian_id', 'label']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skema_penilaian_items');
    }
};
