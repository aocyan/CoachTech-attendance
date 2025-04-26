<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;

    public static function staffIndex()
    {
        $staffs = User::select('name', 'email') -> get();

        $staffs;

        return $staffs;
    }
}
