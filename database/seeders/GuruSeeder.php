<?php

namespace Database\Seeders;

use App\Models\Guru;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class GuruSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pondok = \App\Models\Pondok::first();

        $user = User::create([
            "username"=> "guru1",
            "email"=> "guru1@example.com",
            "password"=> Hash::make("password123"),
            "role"=> "guru",
            "status"=> 1,
        ]);

        Guru::create([
            "user_id"=> $user->id,
            "pondok_id"=> $pondok->id,
            "nip"=> "198001012005011001",
            "nama"=> "Ustadz Ahmad",
            "gelar_awal"=> null,
            "gelar_akhir"=> "S.Pd.I",
            "tempat_lahir"=> "Bandung",
            "tanggal_lahir"=> "1980-01-01",
            "jenis_kelamin"=> "L",
            "status_menikah"=> "Menikah",
            "alamat"=> "Jl. Guru No. 1",
            "no_identitas"=> "3201010101010001",
            "no_telpon"=> "022-9876543",
            "no_handphone"=> "081234567892",
            "email"=> "guru1@example.com",
            "tanggal_kerja"=> "2010-01-01",
            "non_aktif"=> false,
            "keterangan"=> "Guru senior",
        ]);
    }
}
