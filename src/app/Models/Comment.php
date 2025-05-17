<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'attendance_id',
        'comment',
    ];

    public function admin()
    {
        return $this -> belongsTo(Admin::class, 'admin_id');
    }

    public function attendance()
    {
        return $this -> belongsTo(Attendance::class,'attendance_id');
    }
}
