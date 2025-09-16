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
        Schema::create('gurus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('pondok_id')->nullable();
            $table->string('nip')->unique();
            $table->string('nama');
            $table->string('gelar_awal')->nullable();
            $table->string('gelar_akhir')->nullable();
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->string('jenis_kelamin');
            $table->string('status_menikah');
            $table->text('alamat');
            $table->string('no_identitas');
            $table->string('no_telpon');
            $table->string('no_handphone');
            $table->string('email');
            $table->date('tanggal_kerja');
            $table->boolean('non_aktif')->default(false);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gurus');
    }
};
