<?php

namespace App\Models;

use App\Models\User;
use App\Models\Interval;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;

class Admin extends Authenticatable 
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email', 
        'password',
    ];

    public function comments()
    {
        return $this -> hasMany(Comment::class, 'admin_id');
    }

    public static function nowDateTime()
    {
        Carbon::setLocale('ja');
        setlocale(LC_TIME, 'ja_JP.UTF-8');

        return [
            'headerDate' => now() -> isoFormat('YYYY年M月D日'),
            'navDate' => now() -> format('Y年m月d日'),
            'time' => now() -> format('H:i'),
            'year' => now() -> year,
            'month' => now() -> month,
            'day' => now() -> day,
        ];
    }

    public static function indexTime($year = null, $month = null, $day = null)
    {
        if (is_null($year) || is_null($month) || is_null($day)) {

            $nowYearMonthDay = Admin::nowDateTime();
            $year = $nowYearMonthDay['year'];
            $month = $nowYearMonthDay['month'];
            $day = $nowYearMonthDay['day'];
        
        }

        $displayDay = Carbon::createFromDate($year, $month, $day);

        $formatDate = $displayDay -> format('Y/m/d');
        $userDataDate = $displayDay -> format('Y-m-d');

        $prevDate = $displayDay 
            -> copy()
            -> subDay();

        $nextDate = $displayDay 
            -> copy()
            -> addDay();

        return [
            'userDataDate' => $userDataDate,
            'formatDate' => $formatDate,
            'prevDate' => $prevDate,
            'nextDate' => $nextDate,
        ];
    }

    public static function indexToRedirectCorrection($year = null, $month = null, $day = null, $dateData = null)
    {
        if ($dateData) {
            $date = \Carbon\Carbon::parse($dateData);
        } else {
            $date = \Carbon\Carbon::create(
                $year ?? now() -> year,
                $month ?? now() -> month,
                $day ?? now() -> day
            );
        }

        return [
            'year' => $date -> year,
            'month' => $date -> month,
            'day' => $date -> day,
            'carbon' => $date,
        ];
    }

    public static function getMonthClockTime($year, $month, $day)
    {
        $users = User::all();

        $clockInTimes = [];
        $clockOutTimes = [];
        $attendanceIds = [];

        $dateKey = Carbon::create($year, $month, $day) -> format('Y-m-d');

        foreach ($users as $user) {

            $clockInTimes[$dateKey][$user->id] = null;
            $clockOutTimes[$dateKey][$user->id] = null;
            $attendanceIds[$dateKey][$user->id] = null;

            $attendance = Attendance::where('user_id', $user -> id)
                -> whereDate('clock_in_at', Carbon::create($year, $month, $day)
                -> toDateString())
                -> first();

            if ($attendance) {
                $attendanceIds[$dateKey][$user->id] = $attendance -> id;

                $clockInTimes[$dateKey][$user->id] = $attendance
                    -> clock_in_at 
                    -> format('H:i');

                $clockOutTimes[$dateKey][$user->id] = $attendance->clock_out_at ? $attendance
                    ->clock_out_at
                    ->format('H:i') : null;        
            }
        }

        return [
            'attendanceIds' => $attendanceIds,
            'clockInTimes' => $clockInTimes,
            'clockOutTimes' => $clockOutTimes,
        ];
    }

    public static function getMonthIntervalTotalTime($year, $month, $day)
    {
        $users = User::all();

        $intervalTotals = [];

        $dateKey = Carbon::create($year, $month, $day) -> format('Y-m-d');

        foreach ($users as $user) {

            $intervalTotals[$dateKey][$user -> id] = null;

            $attendances = Attendance::where('user_id', $user -> id)
                -> whereDate('clock_in_at', Carbon::create($year, $month, $day)
                -> toDateString())
                -> get();

            $total = 0;
            foreach ($attendances as $attendance) {

                $intervals = $attendance->intervals()
                    -> whereNotNull('interval_in_at')
                    -> whereNotNull('interval_out_at')
                    -> get();

                foreach ($intervals as $interval) {

                    $start = $interval
                        -> interval_in_at
                        -> timestamp;

                    $end = $interval
                        -> interval_out_at
                        -> timestamp;

                    $oneIntervalSecondsTime = $end - $start;
                    $total += $oneIntervalSecondsTime;
                }
            }

            if ($total === 0) {
                $intervalTotals[$dateKey][$user->id] = null;
            } else {
                $hours = floor($total / 3600);
                $minutes = floor(($total % 3600) / 60);
                $intervalTotals[$dateKey][$user -> id] = sprintf('%d:%02d', $hours, $minutes);
            }
        }

        return [
            'intervalTotalTimes' => $intervalTotals
        ];
    }

    public static function workingTotalTime($year, $month, $day)
    {
        $users = User::all();

        $clockTimes = Admin::getMonthClockTime($year, $month, $day);
        $clockInTimes = $clockTimes['clockInTimes'];
        $clockOutTimes = $clockTimes['clockOutTimes'];

        $intervalTotalTimes = Admin::getMonthIntervalTotalTime($year, $month, $day)['intervalTotalTimes'];

        $workingTimes = [];
        $dateKey = Carbon::create($year, $month, $day) -> format('Y-m-d');

        foreach ($users as $user) {
            $userId = $user -> id;

            $workingTimes[$dateKey][$userId] = null;

            $clockInTime = $clockInTimes[$dateKey][$userId] ?? null;
            $clockOutTime = $clockOutTimes[$dateKey][$userId] ?? null;
            $intervalTime = $intervalTotalTimes[$dateKey][$userId] ?? null;

            if (empty($clockInTime) || empty($clockOutTime)) {
                continue;
            }

            $clockInObject = Carbon::createFromFormat('H:i', $clockInTime);
            $clockOutObject = Carbon::createFromFormat('H:i', $clockOutTime);

            $clockStartSeconds = $clockInObject -> timestamp;
            $clockEndSeconds = $clockOutObject -> timestamp;

            $workingSeconds = $clockEndSeconds - $clockStartSeconds;

            $intervalSeconds = 0;
            $h = $m = 0;
            if ( !empty($intervalTime) ) {
                [$h, $m] = explode(':', $intervalTime);
                $intervalSeconds = ($h * 3600) + ($m * 60);
            }

            $actualSeconds = max($workingSeconds - $intervalSeconds, 0);

            $hours = floor($actualSeconds / 3600);
            $minutes = floor(($actualSeconds % 3600) / 60);

            $workingTimes[$dateKey][$userId] = sprintf('%d:%02d', $hours, $minutes);
        }

        return [
            'workingTotalTimes' => $workingTimes
        ];
    }

    public static function updateData($userId, $date, $request)
    {
        $admin = Auth::guard('admin') -> user();

        $attendance = Attendance::where('user_id', $userId)
            -> whereDate('clock_in_at', $date)
            -> first();     

        if ($attendance) {
            $clockIn = $request -> input('clock_in');
            $clockOut = $request -> input('clock_out');

            if (empty($clockIn) && empty($clockOut)) {
                Interval::where('attendance_id', $attendance -> id) -> delete();
                $attendance -> delete(); 

                return;
            }

            Attendance::where('id', $attendance -> id) -> update([
                'clock_in_at' => !empty($clockIn) ? \Carbon\Carbon::parse($date . ' ' . $clockIn) : null,
                'clock_out_at' => !empty($clockOut) ? \Carbon\Carbon::parse($date . ' ' . $clockOut) : null,
            ]);

            Interval::where('attendance_id', $attendance -> id) -> delete();
    
            $intervalIn = $request -> input('interval_in', []);
            $intervalOut = $request -> input('interval_out', []);

            for ($i = 0; $i < count($intervalIn); $i++) {
                $in = $intervalIn[$i] ?? null;
                $out = $intervalOut[$i] ?? null;

                if (!empty($in) && !empty($out)) {
                    Interval::create([
                        'attendance_id' => $attendance->id,
                        'interval_in_at' => \Carbon\Carbon::parse($date . ' ' . $intervalIn[$i]),
                        'interval_out_at' => \Carbon\Carbon::parse($date . ' ' . $intervalOut[$i]),
                    ]);
                }
            }

            Comment::where('attendance_id', $attendance -> id) -> delete();

            $comment = $request -> input('comment');
            $adminComment = new Comment();
            $adminComment -> admin_id = $admin -> id;
            $adminComment -> attendance_id = $attendance -> id;
            $adminComment -> comment = $comment; 
            $adminComment -> save();

            return $attendance;
        }
	}

    public static function newData($userId, $date, $request)
    {
        $admin = Auth::guard('admin') -> user();

        $attendance = Attendance::where('user_id', $userId)
            -> whereDate('clock_in_at', $date)
            -> first();

        if (is_null($attendance)) {
            $clockIn = $request -> input('clock_in');
            $clockOut = $request -> input('clock_out');

            $attendance = Attendance::create([
                'user_id' => $userId,
                'clock_in_at' => \Carbon\Carbon::parse($date . ' ' . $clockIn),
                'clock_out_at' => \Carbon\Carbon::parse($date . ' ' . $clockOut),
            ]);

            $intervalIn = $request -> input('interval_in', []);
            $intervalOut = $request -> input('interval_out', []);

            for ($i = 0; $i < count($intervalIn); $i++) {

                $inTime = $intervalIn[$i] ?? null;
                $outTime = $intervalOut[$i] ?? null;

                if (!empty($inTime) && !empty($outTime)) {
                    Interval::create([
                        'attendance_id' => $attendance -> id,
                        'interval_in_at' => \Carbon\Carbon::parse("$date $inTime"),
                        'interval_out_at' => \Carbon\Carbon::parse("$date $outTime"),
                    ]);
                }
            }
            
            $comment = $request -> input('comment');
            $adminComment = new Comment();
            $adminComment -> admin_id = $admin -> id;
            $adminComment -> attendance_id = $attendance -> id;
            $adminComment -> comment = $comment; 
            $adminComment -> save();

            return $attendance;
        }
    }
}
