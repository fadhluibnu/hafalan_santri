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
        Schema::create('santris', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('pondok_id');
            $table->foreignId('kelas_id');
            $table->string('nama');
            $table->string('panggilan')->nullable();
            $table->string('jenis_kelamin');
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->string('status_mukim');
            $table->string('kondisi');
            $table->string('warga_negara');
            $table->string('kode_pos')->nullable();
            $table->text('alamat');
            $table->integer('anak_ke');
            $table->integer('jumlah_saudara');
            $table->string('status_anak');
            $table->integer('saudara_kandung')->default(0);
            $table->integer('saudara_tiri')->default(0);
            $table->decimal('jarak_pondok', 10, 2)->nullable();
            $table->string('telpon')->nullable();
            $table->string('handphone')->nullable();
            $table->string('email')->nullable();
            $table->string('hobi')->nullable();
            $table->string('foto');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('santris');
    }
};
