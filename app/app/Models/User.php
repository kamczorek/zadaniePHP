<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'password', 'role'];

    protected $hidden = ['password'];

    public function isAdmin()
    {
        return $this->role === 'admin';
    }
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

}
