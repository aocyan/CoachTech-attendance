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

    public static function store(Request $request, $userId, $date)
    {
        $user = User::findOrFail($userId);
        
        $startOfDay = Carbon::parse($date) -> startOfDay();
        $endOfDay = Carbon::parse($date) -> endOfDay();

        $attendance = Attendance::where('user_id', $user->id)
            -> whereBetween('clock_in_at', [$startOfDay, $endOfDay])
            -> first();
        
        if (!$attendance) {
            $attendance = new Attendance();
            $attendance -> user_id = $userId;
            $attendance -> date = $date;
            $attendance -> clock_in_at = null;
            $attendance -> clock_out_at = null;
            $attendance -> save();
        }

        $clockInTime = $request -> input('clock_in');
        $clockOutTime = $request -> input('clock_out');

        $clockInDate = Carbon::parse($date . ' ' . $clockInTime);
        $clockOutDate = Carbon::parse($date . ' ' . $clockOutTime);

        $correction = new Correction();
        $correction -> user_id = $userId;
        $correction -> attendance_id = $attendance->id;
        $correction -> name = $request -> input('name');
        $correction -> date = $date;
        $correction -> clock_in_at = $clockInDate;
        $correction -> clock_out_at = $clockOutDate;
        $correction -> comment = $request -> input('comment');
        $correction -> status = 'unapproved';
        $correction -> save();

        return $correction;
    }

    public function userApply()
    {
        $user = Auth::user();

        return Correction::where('user_id', $user -> id) -> get();
    }

    public function adminApply()
    {
        $usersId = User::pluck('id');
        
        return Correction::whereIn('user_id', $usersId) -> get();
    }

    public static function userSearch($request)
    {
        $user = Auth::user();

        if ($request -> has('status') && $request -> status === 'approved') {
            $corrections = Correction::where('user_id', $user->id)
                                -> where('status', 'approved')
                                -> get();
        } elseif ($request -> has('status') && $request -> status === 'unapproved') {
            $corrections = Correction::where('user_id', $user->id)
                                -> where('status', 'unapproved')
                                -> get();
        } else {
            $corrections = Correction::where('user_id', $user->id) -> get();
        }

        return $corrections;
    }

    public static function adminSearch($request)
    {
        $usersId = User::pluck('id');

        if ($request -> has('status') && $request -> status === 'approved') {
            $corrections = Correction::whereIn('user_id', $usersId)
                                -> where('status', 'approved')
                                -> get();
        } elseif ($request -> has('status') && $request -> status === 'unapproved') {
            $corrections = Correction::whereIn('user_id', $usersId)
                                -> where('status', 'unapproved')
                                -> get();
        } else {
            $corrections = Correction::whereIn('user_id', $usersId) -> get();
        }

        return $corrections;
    }
}