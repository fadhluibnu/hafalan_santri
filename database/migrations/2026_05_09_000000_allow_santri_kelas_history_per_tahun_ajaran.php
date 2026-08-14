<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('santri_kelas', function (Blueprint $table) {
            $table->dropUnique('santri_tahun_ajaran_unique');
            $table->index(['santri_id', 'tahun_ajaran_id'], 'santri_tahun_ajaran_index');
        });
    }

    public function down(): void
    {
        Schema::table('santri_kelas', function (Blueprint $table) {
            $table->dropIndex('santri_tahun_ajaran_index');
            $table->unique(['santri_id', 'tahun_ajaran_id'], 'santri_tahun_ajaran_unique');
        });
    }
};
