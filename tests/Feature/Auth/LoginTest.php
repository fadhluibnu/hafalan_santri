<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginTest extends TestCase
{
    /**
     * A basic feature test example.
     */

    use RefreshDatabase;

    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        $response = $this->post('api/login', [
            'email'    => 'nonexistent@example.com',
            'password' => 'invalidpassword',
        ]);

        // Pastikan session memiliki error
        $response->assertStatus(401);
        // $response->assertJson(['message' => 'Invalid credentials']);

        // Pastikan user tetap guest
        $this->assertGuest();
    }

    public function test_user_success_login_with_valid_credentials(): void
    {
        $user = User::where('email', 'admin@example.com')->first();
        $response = $this->post('api/login', [
            'email'    => 'admin@example.com',
            'password' => 'password123',
        ]);

        // dd($response);

        // Pastikan session memiliki error
        $response->assertStatus(200);
        // $response->assertJson(['message' => 'Invalid credentials']);

        // Pastikan user ter auth
        // $this->assertAuthenticated($user);
    }
}
