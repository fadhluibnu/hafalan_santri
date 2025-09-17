<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'role',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function superAdmin()
    {
        return $this->hasOne(SuperAdmin::class);
    }

    /**
     * Relasi ke AdminCabang.
     */
    public function adminCabang()
    {
        return $this->hasOne(AdminCabang::class);
    }

    /**
     * Relasi ke Guru.
     */
    public function guru()
    {
        return $this->hasOne(Guru::class);
    }

    /**
     * Relasi ke Santri.
     */
    public function santri()
    {
        return $this->hasOne(Santri::class);
    }
}
