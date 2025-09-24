<?php

namespace Database\Seeders;

use App\Models\OrangTua;
use App\Models\Santri;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrangTuaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $santri = Santri::first();

        // Ayah
        OrangTua::create([
            'santri_id' => $santri->id,
            'tipe' => 'Ayah',
            'nama' => 'Bapak Santri',
            'status' => 'Hidup',
            'status_hubungan' => 'Kandung',
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => '1970-01-01',
            'pendidikan' => 'S1',
            'pekerjaan' => 'Guru',
            'penghasilan' => '5000000',
            'email' => 'ayah.santri@example.com',
            'handphone' => '081234567800',
            'alamat' => 'Jl. Ayah No. 1, Bandung',
        ]);

        // Ibu
        OrangTua::create([
            'santri_id' => $santri->id,
            'tipe' => 'Ibu',
            'nama' => 'Ibu Santri',
            'status' => 'Hidup',
            'status_hubungan' => 'Kandung',
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => '1975-01-01',
            'pendidikan' => 'SMA',
            'pekerjaan' => 'Ibu Rumah Tangga',
            'penghasilan' => '3000000',
            'email' => 'ibu.santri@example.com',
            'handphone' => '081234567801',
            'alamat' => 'Jl. Ibu No. 1, Bandung',
        ]);
    }
}
