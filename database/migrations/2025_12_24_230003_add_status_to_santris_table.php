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
        Schema::table('santris', function (Blueprint $table) {
            $table->enum('status_santri', ['aktif', 'lulus', 'alumni', 'keluar', 'cuti'])->default('aktif')->after('foto');
            $table->date('tanggal_masuk_pondok')->nullable()->after('status_santri');
            $table->date('tanggal_lulus')->nullable()->after('tanggal_masuk_pondok');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('santris', function (Blueprint $table) {
            $table->dropColumn(['status_santri', 'tanggal_masuk_pondok', 'tanggal_lulus']);
        });
    }
};
