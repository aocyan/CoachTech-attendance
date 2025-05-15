<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Interval;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(int $userCount = 6, int $attendanceDays = 250): void
    {
        Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('1234abcd'),
        ]);

        User::factory($userCount) 
            -> create() 
            -> each(function ($user) use ($attendanceDays) {

            $usedDates = collect();

            for ($i = 0; $i < $attendanceDays; $i++) {
                do {
                    $date = now() 
                        -> subDays(rand(1, 365)) 
                        -> format('Y-m-d');
                } while ($usedDates -> contains($date));

                $usedDates -> push($date);

                $attendance = Attendance::factory()
                                -> for($user)
                                -> forDate($date)
                                -> create();

                $intervals = Interval::factory() -> forAttendance($attendance);
                foreach ($intervals as $interval) {
                    Interval::create($interval);
                }
            }
        });
    }
}
