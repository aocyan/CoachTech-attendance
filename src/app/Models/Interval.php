<?php

namespace App\Models;

use App\Models\Attendance;
use Carbon\Carbon;
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

    protected $casts = [
        'interval_in_at' => 'datetime',
        'interval_out_at' => 'datetime',
    ];

    public function attendance()
    {
        return $this -> belongsTo(Attendance::class, 'attendance_id');
    }

    public static function defaultSettingInterval()
    {
        $user = Auth::user();

        $attendance = $user 
            -> attendances()
            -> orderBy('created_at', 'desc')
            -> first();

        $interval = new Interval();
        $interval -> attendance_id = $attendance -> id;
        $interval -> interval_in_at = null;
        $interval -> interval_out_at = null;

        $interval -> save();

        return $attendance -> only(['interval_in_at', 'interval_out_at']);
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
        $user -> save();    

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
            $interval -> attendance_id = $attendance->id;
            $interval -> interval_in_at = now();
        } else{
            $interval -> interval_in_at = now();
        } 
      
        $interval -> save();
    }

    public static function intervalOutTime()
    {
        $user = Auth::user();
        $user -> status = 'working';
        $user -> save();    

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
            $interval -> attendance_id = $attendance -> id;
            $interval -> interval_out_at = now();
        
        } else{
            $interval -> interval_out_at = now();
        } 

        $interval -> save();
    }

    public static function getMonthIntervalTotalTime($user, $year, $month)
    {   
        $attendances = Attendance::where('user_id', $user->id) 
            -> whereYear('clock_in_at', $year)
            -> whereMonth('clock_in_at', $month)
            -> get();
        
        foreach ($attendances as $attendance) {
            $attendance->intervals = $attendance
                -> intervals()
                -> whereNotNull('interval_out_at')
                -> get();
        }
        
        $intervalTotals = [];

        foreach ($attendances as $attendance) {
            $date = $attendance
                -> clock_in_at
                -> format('Y-m-d');

            if ( !empty($attendance -> intervals) ) {
                $total = 0;

                foreach ($attendance -> intervals as $interval) {

                    $start = $interval 
                        -> interval_in_at 
                        -> timestamp;
                    
                    $end = $interval 
                        -> interval_out_at 
                        -> timestamp;
                    
                    $oneIntervalSecondsTime = $end - $start;

                    $total += $oneIntervalSecondsTime;
                }

                $intervalTotals[$date] = $total;
            
            } else {
                $intervalTotals[$date] = null;
            }
        }

        $formattedTotals = [];

        foreach ($intervalTotals as $date => $totalSeconds) {
            if ($totalSeconds === 0) {
                $formattedTotals[$date] = null;
            } else {
                $hours = floor($totalSeconds / 3600);
                $minutes = floor(($totalSeconds % 3600) / 60);
                $formattedTotals[$date] = sprintf('%d:%02d', $hours, $minutes);
            }
        }

        return [
            'intervalTotalTimes' => $formattedTotals
        ];
    }
}
