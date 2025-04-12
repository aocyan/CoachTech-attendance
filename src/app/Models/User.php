<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
    ];

    public function attendances()
    {
        return $this -> hasMany(Attendance::class, 'user_id');
    }

    public static function store()
    {
        $user = request() -> all();
        $user['password'] = Hash::make($user['password']);
        $user['status'] = 'clockIn';

        return User::create($user);
    }
}
