<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Rename table gurus to ustadzs and update related columns.
     */
    public function up(): void
    {
        // Rename table gurus to ustadzs
        Schema::rename('gurus', 'ustadzs');

        // Update role in users table from 'guru' to 'ustadz'
        DB::table('users')->where('role', 'guru')->update(['role' => 'ustadz']);

        // If hafalans table has guru_id, rename it to ustadz_id
        if (Schema::hasColumn('hafalans', 'guru_id')) {
            Schema::table('hafalans', function (Blueprint $table) {
                $table->renameColumn('guru_id', 'ustadz_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rename back ustadzs to gurus
        Schema::rename('ustadzs', 'gurus');

        // Update role back from 'ustadz' to 'guru'
        DB::table('users')->where('role', 'ustadz')->update(['role' => 'guru']);

        // Rename ustadz_id back to guru_id
        if (Schema::hasColumn('hafalans', 'ustadz_id')) {
            Schema::table('hafalans', function (Blueprint $table) {
                $table->renameColumn('ustadz_id', 'guru_id');
            });
        }
    }
};
