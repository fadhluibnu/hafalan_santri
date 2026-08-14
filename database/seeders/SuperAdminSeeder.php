<?php

namespace Database\Seeders;

use App\Models\SuperAdmin;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            "username"=> "superadmin",
            "email"=> "superadmin@gmail.com",
            "password"=> Hash::make("password123"),
            "role"=> "super_admin",
            "status"=> 1,
        ]);

        SuperAdmin::create([
            "user_id"=> $user->id,
            "name"=> "Super Admin",
            "phone"=> "081234567890",
            "jabatan"=> "Kepala Pondok",
        ]);
    }
}
