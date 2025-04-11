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
        return $this->belongsTo(User::class, 'user_id');
    }

    public function intervals()
    {
        return $this->hasMany(Interval::class,'attendance_id');
    }

    public static function nowDateTime()
    {
        Carbon::setLocale('ja');
        setlocale(LC_TIME, 'ja_JP.UTF-8');

        return [
            'date' => now()->isoFormat('YYYY年M月D日（ddd）'),
            'time' => now()->format('H:i'),
        ];
    }

    public static function defaultSettingAttend()
    {
        $userStatus = 'clockIn';

        $user = Auth::user();

        $attendance = new Attendance();
        $attendance->user_id = $user->id;
        $attendance->clock_in_at = null;
        $attendance->clock_out_at = null;

        $attendance->save();

        return compact('userStatus') + $attendance->only(['clock_in_at', 'clock_out_at']);
    }

    public static function statusAttend()
    {
        $user = Auth::user();

        $attendance = $user->attendances()->orderBy('created_at', 'desc')->first();

        return $attendance;
    }

    public static function clockInTime()
    {
        $userStatus = 'working';
        session(['userStatus' => $userStatus]);

        $user = Auth::user();
        $attendance = $user->attendances()->whereNull('clock_in_at')->first();
     
        $attendance-> clock_in_at = now();
        $attendance-> save();

        return compact('userStatus');
    }

    public static function clockOutTime()
    {
        $userStatus = 'clockOut';
        session(['userStatus' => $userStatus]);

        $user = Auth::user();

        $attendance = $user->attendances()->whereNull('clock_out_at')->first();

        $attendance -> clock_out_at = now();
        $attendance-> save();

        return compact('userStatus');
    }
}
