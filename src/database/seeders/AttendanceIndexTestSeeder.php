<?php

namespace Database\Seeders;

use App\Models\Interval;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AttendanceIndexTestSeeder extends Seeder
{
    public function run()
    {
        $user = User::create([
            'name' => '山田　太郎',
            'email' => 'user@example.com',
            'password' => bcrypt('1234abcd'),
            'status' => 'clockIn',
        ]);

        $clockInFirstTime = Carbon::create(2025, 4, 1, 9, 0, 0);
        $clockOutFirstTime = Carbon::create(2025, 4, 1, 18, 0, 0);

        $clockInSecondTime = Carbon::create(2025, 4, 10, 9, 0, 0);
        $clockOutSecondTime = Carbon::create(2025, 4, 10, 18, 0, 0);

        $clockInThirdTime = Carbon::create(2025, 4, 20, 9, 0, 0);
        $clockOutThirdTime = Carbon::create(2025, 4, 20, 18, 0, 0);

        $clockInFourthTime = Carbon::create(2025, 4, 30, 9, 0, 0);
        $clockOutFourthTime = Carbon::create(2025, 4, 30, 18, 0, 0);

        $intervalInFirstTime = Carbon::create(2025, 4, 1, 12, 0, 0);
        $intervalOutFirstTime = Carbon::create(2025, 4, 1, 13, 0, 0);

        $intervalInSecondTime = Carbon::create(2025, 4, 10, 12, 0, 0);
        $intervalOutSecondTime = Carbon::create(2025, 4, 10, 13, 0, 0);

        $intervalInThirdTime = Carbon::create(2025, 4, 20, 12, 0, 0);
        $intervalOutThirdTime = Carbon::create(2025, 4, 20, 13, 0, 0);

        $intervalInFourthTime = Carbon::create(2025, 4, 30, 12, 0, 0);
        $intervalOutFourthTime = Carbon::create(2025, 4, 30, 13, 0, 0);

        $firstAttendance = Attendance::create([
            'user_id' => $user -> id,
            'date' => $clockInFirstTime -> toDateString(),
            'clock_in_at' => $clockInFirstTime,
            'clock_out_at' => $clockOutFirstTime,
        ]);

        Interval::create([
            'attendance_id' => $firstAttendance -> id,
            'interval_in_at' => $intervalInFirstTime,
            'interval_out_at' => $intervalOutFirstTime,
        ]);

        $secondAttendance = Attendance::create([
            'user_id' => $user -> id,
            'date' => $clockInSecondTime -> toDateString(),
            'clock_in_at' => $clockInSecondTime,
            'clock_out_at' => $clockOutSecondTime,
        ]);

        Interval::create([
            'attendance_id' => $secondAttendance -> id,
            'interval_in_at' => $intervalInSecondTime,
            'interval_out_at' => $intervalOutSecondTime,
        ]);

        $thirdAttendance = Attendance::create([
            'user_id' => $user -> id,
            'date' => $clockInThirdTime -> toDateString(),
            'clock_in_at' => $clockInThirdTime,
            'clock_out_at' => $clockOutThirdTime,
        ]);

        Interval::create([
            'attendance_id' => $thirdAttendance -> id,
            'interval_in_at' => $intervalInThirdTime,
            'interval_out_at' => $intervalOutThirdTime,
        ]);

        $fourthAttendance = Attendance::create([
            'user_id' => $user -> id,
            'date' => $clockInFourthTime -> toDateString(),
            'clock_in_at' => $clockInFourthTime,
            'clock_out_at' => $clockOutFourthTime,
        ]);

        Interval::create([
            'attendance_id' => $fourthAttendance -> id,
            'interval_in_at' => $intervalInFourthTime,
            'interval_out_at' => $intervalOutFourthTime,
        ]);
    }
}
