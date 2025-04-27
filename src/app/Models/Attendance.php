<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'clock_in_at',
        'clock_out_at',
    ];

    protected $casts = [
        'clock_in_at' => 'datetime',
        'clock_out_at' => 'datetime',
    ];

    public function user()
    {
        return $this -> belongsTo(User::class, 'user_id');
    }

    public function intervals()
    {
        return $this -> hasMany(Interval::class,'attendance_id');
    }

    public function correction()
    {
        return $this -> belongsTo(Correction::class,'attendance_id');
    }

    public function comment()
    {
        return $this -> belongsTo(Comment::class,'attendance_id');
    }

    public static function nowDateTime()
    {
        Carbon::setLocale('ja');
        setlocale(LC_TIME, 'ja_JP.UTF-8');

        return [
            'date' => now() -> isoFormat('YYYY年M月D日（ddd）'),
            'time' => now() -> format('H:i'),
            'year' => now() -> year,
            'month' => now() -> month,
        ];
    }

    public static function defaultSettingAttend()
    {
        $user = Auth::user();
        $user -> status = 'clockIn';
        $user -> save();    

        $attendance = new Attendance();
        $attendance -> user_id = $user->id;
        $attendance -> date = null;
        $attendance -> clock_in_at = null;
        $attendance -> clock_out_at = null; 
        $attendance -> save();

        return $attendance -> only(['clock_in_at', 'clock_out_at']);
    }

    public static function statusAttend()
    {
        $user = Auth::user();

        $attendance = $user 
            -> attendances()
            -> orderBy('created_at', 'desc')
            -> first();

        return $attendance;
    }

    public static function dateChanges()
    {
        $user = Auth::user();

        $attendance = $user 
            -> attendances()
            -> whereNull('clock_in_at')
            -> first();

        Attendance::defaultSettingAttend();
        Interval::defaultSettingInterval();
    }

    public static function clockInTime()
    {
        $user = Auth::user();
        $user -> status = 'working';
        $user -> save();    

        $attendance = $user 
            -> attendances()
            -> whereNull('clock_in_at')
            -> first();

        if (!$attendance) {
            $attendance = new Attendance();
            $attendance -> user_id = $user -> id;
        }

        $attendance -> date = now() -> toDateString();
        $attendance -> clock_in_at = now();
        $attendance -> save();
    }

    public static function clockOutTime()
    {
        $user = Auth::user();
        $user -> status = 'clockOut';
        $user ->save();    

        $attendance = $user 
            -> attendances()
            -> whereNull('clock_out_at')
            -> first();

        $attendance -> clock_out_at = now();      
        $attendance -> save();
    }

    public static function indexTime($year = null, $month = null)
    {
        if (is_null($year) || is_null($month)) {

            $nowYearMonth = Attendance::nowDateTime();
            $year = $nowYearMonth['year'];
            $month = $nowYearMonth['month'];
        
        }

        $displayMonth = Carbon::createFromDate($year, $month, 1);

        $prevDate = $displayMonth 
            -> copy()
            -> subMonth();

        $nextDate = $displayMonth 
            -> copy()
            -> addMonth();

        $daysInMonth = $displayMonth -> daysInMonth;

        $dates = [];
        foreach (range(1, $daysInMonth) as $day) {
            $date = Carbon::createFromDate($year, $month, $day);
            $dates[] = [
                'date' => $date,
                'dayWeek' => $date->isoFormat('ddd'),
            ];
        }

        return [
            'displayMonth' => $displayMonth,
            'prevDate' => $prevDate,
            'nextDate' => $nextDate,
            'dates' => $dates,
        ];
    }

    public static function getMonthClockTime($user, $year, $month)
    {
        $attendances = self::where('user_id', $user->id)
            -> whereYear('clock_in_at', $year)
            -> whereMonth('clock_in_at', $month)
            -> get();

        $clockInTimes = [];
        $clockOutTimes = [];
        $attendanceIds = [];

        foreach ($attendances as $attendance) {

            $dateKey = $attendance
                -> clock_in_at
                -> format('Y-m-d');

            $attendanceIds[$dateKey] = $attendance->id;

            $clockInTimes[$dateKey] = $attendance
                -> clock_in_at
                -> format('H:i');

            if ($attendance -> clock_out_at) {

                $clockOutTimes[$dateKey] = $attendance 
                    -> clock_out_at
                    -> format('H:i');

            } else {
                $clockOutTimes[$dateKey] = null;
            }
        }   

        return [
            'attendanceIds' => $attendanceIds,
            'clockInTimes' => $clockInTimes,
            'clockOutTimes' => $clockOutTimes,
        ];
    }

    public static function workingTotalTime($user, $year, $month)
    {
        $clockTimes = Attendance::getMonthClockTime($user, $year, $month);
        $clockInTimes = $clockTimes['clockInTimes'];
        $clockOutTimes = $clockTimes['clockOutTimes'];

        $intervalTotalTimes = Interval::getMonthIntervalTotalTime($user, $year, $month)['intervalTotalTimes'];

        $workingTimes = [];

        foreach ($clockInTimes as $date => $clockInTime) {

            if (empty($clockInTime) || empty($clockOutTimes[$date])) {
                continue;
            }

            $clockInObject = Carbon::parse($clockInTime);
            $clockOutObject = Carbon::parse($clockOutTimes[$date]);

            $clockStartSeconds = $clockInObject -> timestamp;
            $clockEndSeconds = $clockOutObject -> timestamp;

            $workingSeconds = $clockEndSeconds - $clockStartSeconds;

            $intervalSeconds = 0;
            $h = $m = 0;
            if (!empty($intervalTotalTimes[$date])) {
                list($h, $m) = explode(':', $intervalTotalTimes[$date]);
                $intervalSeconds = ($h * 3600) + ($m * 60);
            } else{
                $intervalSeconds = 0;
            }

            $actualSeconds = max($workingSeconds - $intervalSeconds, 0);

            $hours = floor($actualSeconds / 3600);
            $minutes = floor(($actualSeconds % 3600) / 60);

            $workingTimes[$date] = sprintf('%d:%02d', $hours, $minutes);
        }

        return [
            'workingTotalTimes' => $workingTimes
        ];
    }

    public static function detailData($userId)
    {
        $user = User::findOrFail($userId);

        $targetDate = request()
            -> query('date') ?? now()
            -> format('Y-m-d');

        $attendance = Attendance::where('user_id', $user->id)
            -> whereDate('clock_in_at', $targetDate)
            -> first();

        $correction = Correction::where('user_id', $user -> id)
            -> whereDate('clock_in_at', $targetDate)
            -> latest()
            -> first();

        if (Auth::guard('admin') -> check() && $attendance) {
            return [
                'user' => $user,
                'attendance' => $attendance,
                'intervals' => $attendance->intervals,
                'correction' => optional($correction) -> status,
                'comment' => optional($correction) -> comment,
                'targetDate' => $targetDate,
            ];
        }

        if ($correction) {
            $attendance = new Attendance();
            $attendance -> clock_in_at = $correction -> clock_in_at;
            $attendance -> clock_out_at = $correction -> clock_out_at;
            $attendance -> exists = false;

            $intervals = Leave::where('correction_id', $correction -> id) -> get();

            return [
                'user' => $user,
                'attendance' => $attendance,
                'intervals' => $intervals,
                'correction' => optional($correction) -> status,
                'comment' => optional($correction) -> comment,
                'targetDate' => $targetDate,
            ];
        }

        if (is_null($attendance)) {
            $attendance = new Attendance([
                    'clock_in_at' => null,
                    'clock_out_at' => null,
            ]);
            
            $intervals = collect();
        } else {
            $intervals = $attendance -> intervals;
        }

        return [
            'user' => $user,
            'attendance' => $attendance,
            'intervals' => $intervals,
            'correction' => optional($correction) -> status,
            'comment' => optional($correction) -> comment,
            'targetDate' => $targetDate,
        ];
    }
}
