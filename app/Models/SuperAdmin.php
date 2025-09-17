<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuperAdmin extends Model
{
    /** @use HasFactory<\Database\Factories\SuperAdminFactory> */
    use HasFactory;

    protected $fillable = [
        "user_id",
        "name",
        "phone",
        "jabatan",
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
