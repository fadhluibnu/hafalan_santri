<?php

namespace Database\Seeders;

use App\Models\AdminCabang;
use App\Models\Pondok;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminCabangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pondok = Pondok::first();

        $user = User::create([
            "username"=> "admincabang",
            "email"=> "admincabang@example.com",
            "password"=> Hash::make("password123"),
            "role"=> "admin_cabang",
            "status"=> 1,
        ]);

        AdminCabang::create([
            "user_id"=> $user->id,
            "pondok_id"=> $pondok->id,
            "name"=> "Admin Cabang 1",
            "phone"=> "081234567891",
            "jabatan"=> "Kepala Cabang",
        ]);
    }
}
