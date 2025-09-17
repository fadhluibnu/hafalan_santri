<?php

namespace Database\Seeders;

use App\Models\SuperAdmin;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // user create
        // 'username',
        // 'email',
        // 'password',
        // 'role',
        // 'status',
        User::create([
            "username"=> "admin",
            "email"=> "admin@example.com",
            "password"=> Hash::make("password123"),
            "role"=> "super_admin",
            "status"=> "1",
        ]);

        SuperAdmin::create([
            "user_id"=> User::first()->id,
            "name"=> "Admin",
            "phone"=> "081234567890",
            "jabatan"=> "Kepala Pondok",
        ]);

    }
}
