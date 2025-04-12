<?php

namespace Database\Seeders;

use App\Models\Interval;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        User::factory(10)->create()
                         ->each(function ($user) {
            Attendance::factory(10)->create(['user_id' => $user->id])
                                   ->each(function ($attendance) {
                    Interval::factory()->count(2)->forAttendance($attendance)->create();
                });
        });
    }
}
