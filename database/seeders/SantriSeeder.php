<?php

namespace Database\Seeders;

use App\Models\Santri;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SantriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pondok = \App\Models\Pondok::first();

        $user = User::create([
            "username"=> "santri1",
            "email"=> "santri1@example.com",
            "password"=> Hash::make("password123"),
            "role"=> "santri",
            "status"=> 1,
        ]);

        Santri::create([
            "user_id"=> $user->id,
            "pondok_id"=> $pondok->id,
            "kelas_id"=> null, // abaikan kelas sesuai instruksi
            "nama"=> "Santri Pertama",
            "panggilan"=> "Santri",
            "jenis_kelamin"=> "L",
            "tempat_lahir"=> "Bandung",
            "tanggal_lahir"=> "2005-01-01",
            "status_mukim"=> "Mukim",
            "kondisi"=> "Sehat",
            "warga_negara"=> "Indonesia",
            "kode_pos"=> "40123",
            "alamat"=> "Jl. Santri No. 1",
            "anak_ke"=> 1,
            "jumlah_saudara"=> 2,
            "status_anak"=> "Kandung",
            "saudara_kandung"=> 2,
            "saudara_tiri"=> 0,
            "jarak_pondok"=> 2.5,
            "telpon"=> "022-1234568",
            "handphone"=> "081234567893",
            "email"=> "santri1@example.com",
            "hobi"=> "Membaca",
            "foto"=> "santri_foto/hZgb0NPxgcoLpForDlT77M3VHlm2kp2CEtLqjlVU.jpg",
        ]);
    }
}
