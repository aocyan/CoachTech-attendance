<?php

namespace Database\Seeders;

use App\Models\Interval;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ClockInTestSeeder extends Seeder
{
    public function run()
    {
        $user = User::create([
            'name' => '山田　太郎',
            'email' => 'user@example.com',
            'password' => bcrypt('1234abcd'),
            'status' => 'clockIn',
        ]);

        $attendance = Attendance::create([
            'user_id' => $user -> id,
            'date' => null,
            'clock_in_at' => null,
            'clock_out_at' => null,
        ]);

        Interval::create([
            'attendance_id' => $attendance -> id,
            'interval_in_at' => null,
            'interval_out_at' => null,
        ]);
    }
}
