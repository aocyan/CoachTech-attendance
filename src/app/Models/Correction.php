<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Correction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'attendance_id',
        'name',
        'date',
        'clock_in_at',
        'clock_out_at',
        'comment',
        'status',
    ];

    protected $casts = [
        'clock_in_at' => 'datetime',
        'clock_out_at' => 'datetime',
    ];

    public function user()
    {
        return $this -> belongsTo(User::class, 'user_id');
    }

    public function attendance()
    {
        return $this -> belongsTo(Attendance::class, 'attendance_id');
    }

    public function leaves()
    {
        return $this -> hasMany(Leave::class,'correction_id');
    }

    public function store(Request $request, $id)
    {
        $user = Auth::user();

        $startOfDay = Carbon::parse($id)->startOfDay();
        $endOfDay = Carbon::parse($id)->endOfDay();

        $attendance = Attendance::where('user_id', $user->id)
            ->whereBetween('clock_in_at', [$startOfDay, $endOfDay])
            ->first();

        $clockInTime = $request->input('clock_in');
        $clockOutTime = $request->input('clock_out');

        $clockInDate = Carbon::parse($id . ' ' . $clockInTime);
        $clockOutDate = Carbon::parse($id . ' ' . $clockOutTime);

        $correction = new Correction();
        $correction -> user_id = Auth() -> id();
        $correction->attendance_id = $attendance ? $attendance->id : null;
        $correction -> name = $request -> input('name');
        $correction -> date = $id;
        $correction -> clock_in_at = $clockInDate;
        $correction -> clock_out_at = $clockOutDate;
        $correction -> comment = $request -> input('comment');
        $correction -> status = 'unapproved';
        $correction -> save();

        return $correction;
    }
}
