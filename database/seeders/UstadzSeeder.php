<?php

namespace Database\Seeders;

use App\Models\Ustadz;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UstadzSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pondok = \App\Models\Pondok::first();

        // Ustadz 1
        $user1 = User::create([
            "username"=> "ustadz1",
            "email"=> "ustadz1@gmail.com",
            "password"=> Hash::make("password123"),
            "role"=> "ustadz",
            "status"=> 1,
        ]);

        Ustadz::create([
            "user_id"=> $user1->id,
            "pondok_id"=> $pondok->id,
            "nip"=> "198001012005011001",
            "nama"=> "Ustadz Ahmad",
            "gelar_awal"=> null,
            "gelar_akhir"=> "S.Pd.I",
            "tempat_lahir"=> "Bandung",
            "tanggal_lahir"=> "1980-01-01",
            "jenis_kelamin"=> "L",
            "status_menikah"=> "Menikah",
            "alamat"=> "Jl. Ustadz No. 1",
            "no_identitas"=> "3201010101010001",
            "no_telpon"=> "022-9876543",
            "no_handphone"=> "081234567892",
            "email"=> "ustadz1@example.com",
            "tanggal_kerja"=> "2010-01-01",
            "non_aktif"=> false,
            "keterangan"=> "Ustadz senior",
        ]);

        // Ustadz 2
        $user2 = User::create([
            "username"=> "ustadz2",
            "email"=> "ustadz2@gmail.com",
            "password"=> Hash::make("password123"),
            "role"=> "ustadz",
            "status"=> 1,
        ]);

        Ustadz::create([
            "user_id"=> $user2->id,
            "pondok_id"=> $pondok->id,
            "nip"=> "198502022010011002",
            "nama"=> "Ustadzah Siti",
            "gelar_awal"=> null,
            "gelar_akhir"=> "M.Pd",
            "tempat_lahir"=> "Jakarta",
            "tanggal_lahir"=> "1985-02-02",
            "jenis_kelamin"=> "P",
            "status_menikah"=> "Menikah",
            "alamat"=> "Jl. Ustadz No. 2",
            "no_identitas"=> "3202020202020002",
            "no_telpon"=> "021-1234567",
            "no_handphone"=> "081234567893",
            "email"=> "ustadz2@example.com",
            "tanggal_kerja"=> "2012-05-01",
            "non_aktif"=> false,
            "keterangan"=> "Ustadz pengajar utama",
        ]);
    }
}
