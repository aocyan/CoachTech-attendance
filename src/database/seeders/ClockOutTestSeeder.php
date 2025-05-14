<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Attendance;
use App\Models\Interval;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ClockOutTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'name' => '山田　太郎',
            'email' => 'user@example.com',
            'password' => bcrypt('1234abcd'),
            'status' => 'clockOut',
        ]);

        $clockInTime = Carbon::now() -> setTime(9, 0, 0);
        $clockOutTime = Carbon::now() -> setTime(18, 0, 0);

        $attendance = Attendance::create([
            'user_id' => $user -> id,
            'date' => $clockInTime -> toDateString(),
            'clock_in_at' => $clockInTime,
            'clock_out_at' => $clockOutTime,
        ]);

        Interval::create([
            'attendance_id' => $attendance -> id,
            'interval_in_at' => null,
            'interval_out_at' => null,
        ]);
    }
}