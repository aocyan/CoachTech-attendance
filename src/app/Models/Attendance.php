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

    public function user()
    {
        return $this -> belongsTo(User::class, 'user_id');
    }

    public function intervals()
    {
        return $this -> hasMany(Interval::class,'attendance_id');
    }

    public static function nowDateTime()
    {
        Carbon::setLocale('ja');
        setlocale(LC_TIME, 'ja_JP.UTF-8');

        return [
            'date' => now() -> isoFormat('YYYY年M月D日（ddd）'),
            'time' => now() -> format('H:i'),
        ];
    }

    public static function defaultSettingAttend()
    {
        $user = Auth::user();
        $user -> status = 'clockIn';
        $user -> save();    

        $attendance = new Attendance();
        $attendance -> user_id = $user->id;
        $attendance -> clock_in_at = null;
        $attendance -> clock_out_at = null; 
        $attendance -> save();

        return $attendance -> only(['clock_in_at', 'clock_out_at']);
    }

    public static function statusAttend()
    {
        $user = Auth::user();

        $attendance = $user -> attendances()
                            -> orderBy('created_at', 'desc')
                            -> first();

        return $attendance;
    }

    public static function dateChanges()
    {
        $user = Auth::user();

        $attendance = $user -> attendances()
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

        $attendance = $user -> attendances()
                            -> whereNull('clock_in_at')
                            -> first();

        $attendance -> clock_in_at = now();
        $attendance -> save();
    }

    public static function clockOutTime()
    {
        $user = Auth::user();
        $user -> status = 'clockOut';
        $user ->save();    

        $attendance = $user -> attendances()
                            -> whereNull('clock_out_at')
                            -> first();

        $attendance -> clock_out_at = now();      
        $attendance -> save();
    }
}
