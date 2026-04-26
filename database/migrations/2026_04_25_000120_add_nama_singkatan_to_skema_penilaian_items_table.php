<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('skema_penilaian_items', function (Blueprint $table) {
            if (!Schema::hasColumn('skema_penilaian_items', 'nama')) {
                $table->string('nama')->nullable()->after('skema_penilaian_id');
            }

            if (!Schema::hasColumn('skema_penilaian_items', 'singkatan')) {
                $table->string('singkatan', 20)->nullable()->after('nama');
            }
        });

        DB::table('skema_penilaian_items')
            ->orderBy('id')
            ->chunkById(200, function ($rows) {
                foreach ($rows as $row) {
                    $nama = trim((string) ($row->nama ?? ''));
                    $singkatan = trim((string) ($row->singkatan ?? ''));
                    $label = trim((string) ($row->label ?? ''));

                    if ($nama === '') {
                        $nama = $label !== '' ? $label : 'Item ' . $row->id;
                    }

                    if ($singkatan === '') {
                        $singkatan = $label !== '' ? $label : $nama;
                    }

                    DB::table('skema_penilaian_items')
                        ->where('id', $row->id)
                        ->update([
                            'nama' => $nama,
                            'singkatan' => Str::upper(Str::limit($singkatan, 20, '')),
                        ]);
                }
            });

        Schema::table('skema_penilaian_items', function (Blueprint $table) {
            if (!Schema::hasColumn('skema_penilaian_items', 'singkatan')) {
                return;
            }

            $table->index(['skema_penilaian_id', 'singkatan'], 'spi_skema_singkatan_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('skema_penilaian_items', function (Blueprint $table) {
            try {
                $table->dropIndex('spi_skema_singkatan_index');
            } catch (\Throwable $e) {
                // no-op
            }

            if (Schema::hasColumn('skema_penilaian_items', 'singkatan')) {
                $table->dropColumn('singkatan');
            }

            if (Schema::hasColumn('skema_penilaian_items', 'nama')) {
                $table->dropColumn('nama');
            }
        });
    }
};
