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
        Schema::table('skema_penilaian_items', function (Blueprint $table) {
            if (!Schema::hasColumn('skema_penilaian_items', 'batas_bawah')) {
                $table->decimal('batas_bawah', 10, 2)->nullable()->after('singkatan');
            }
            if (!Schema::hasColumn('skema_penilaian_items', 'batas_atas')) {
                $table->decimal('batas_atas', 10, 2)->nullable()->after('batas_bawah');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('skema_penilaian_items', function (Blueprint $table) {
            if (Schema::hasColumn('skema_penilaian_items', 'batas_atas')) {
                $table->dropColumn('batas_atas');
            }
            if (Schema::hasColumn('skema_penilaian_items', 'batas_bawah')) {
                $table->dropColumn('batas_bawah');
            }
        });
    }
};
