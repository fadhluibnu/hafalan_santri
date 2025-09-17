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
        Schema::create('hafalans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id');
            $table->foreignId('guru_id');
            $table->foreignId('kelas_id');
            $table->date('tanggal_setor');
            $table->integer('juz');
            $table->string('dari_surat');
            $table->integer('dari_ayat');
            $table->string('sampai_surat');
            $table->integer('sampai_ayat');
            $table->string('kategori');
            $table->string('nilai', 5);
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hafalans');
    }
};
