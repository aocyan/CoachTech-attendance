<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interval extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_id',
        'interval_in_at',
        'interval_out_at',
    ];

    public function attendances()
    {
        return $this->belongsTo(Attendance::class, 'attendance_id');
    }

    public static function defaultSettingInterval()
    {
        $user = Auth::user();

        $attendance = $user
            -> attendances()
            -> orderBy('created_at', 'desc')
            -> first();

        $interval = new Interval();
        $interval -> attendance_id = $attendance->id;
        $interval -> interval_in_at = null;
        $interval -> interval_out_at = null;

        $interval->save();

        return $attendance->only(['interval_in_at', 'interval_out_at']);
    }

    public static function statusInterval()
    {
        $user = Auth::user();

        $attendance = $user
            -> attendances() 
            -> orderBy('created_at', 'desc')
            -> first();

        $interval = $attendance
            -> intervals()
            -> orderBy('created_at', 'desc')
            -> first();

        return $interval;
    }

    public static function intervalInTime()
    {
        $user = Auth::user();
        $user -> status = 'intervalIn';
        $user->save();    

        $attendance = Auth::user()
            -> attendances()
            -> whereDate('created_at',now())
            -> whereNotNull('clock_in_at')
            -> latest()
            -> first();

        $interval = $attendance
            -> intervals()
            -> latest()
            -> first();
        
        if( ($interval->interval_in_at) !== null) {
            $interval = new Interval();
            $interval->attendance_id = $attendance->id;
            $interval->interval_in_at = now();
        } else{
            $interval -> interval_in_at = now();
        } 
      
        $interval -> save();
    }

    public static function intervalOutTime()
    {
        $user = Auth::user();
        $user -> status = 'working';
        $user->save();    

        $attendance = Auth::user()
            -> attendances()
            -> whereDate('created_at',now())
            -> whereNotNull('clock_in_at')
            -> latest()
            -> first();

        $interval = $attendance
            -> intervals()
            -> latest()
            -> first();

        if( ($interval->interval_out_at) !== null) {
            $interval = new Interval();
            $interval->attendance_id = $attendance->id;
            $interval->interval_out_at = now();
        } else{
            $interval -> interval_out_at = now();
        } 

        $interval -> save();
    }
}
