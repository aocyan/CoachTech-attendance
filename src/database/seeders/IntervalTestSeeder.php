<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Attendance;
use App\Models\Interval;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class IntervalTestSeeder extends Seeder
{
    public function run()
    {
        $user = User::create([
            'name' => '山田　太郎',
            'email' => 'user@example.com',
            'password' => bcrypt('1234abcd'),
            'status' => 'intervalIn',
        ]);

        $clockInTime = Carbon::now() -> setTime(9, 0, 0);
        $intervalInTime = Carbon::now() -> setTime(12,0,0);

        $attendance = Attendance::create([
            'user_id' => $user -> id,
            'date' => $clockInTime -> toDateString(),
            'clock_in_at' => $clockInTime,
            'clock_out_at' => null,
        ]);

        Interval::create([
            'attendance_id' => $attendance -> id,
            'interval_in_at' => $intervalInTime,
            'interval_out_at' => null,
        ]);
    }
}