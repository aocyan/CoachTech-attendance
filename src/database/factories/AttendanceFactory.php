<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class AttendanceFactory extends Factory
{
    protected $model = Attendance::class;

    public function definition()
    {
        $workDate = $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d');

        $date = $workDate;
        $clockIn = Carbon::parse($workDate . ' 08:55:00')->addMinutes(rand(0, 5));
        $clockOut = Carbon::parse($workDate . ' 18:00:00')->addMinutes(rand(0, 60));

        return [
            'user_id' => User::factory(),
            'clock_in_at' => $clockIn,
            'clock_out_at' => $clockOut,
        ];
    }

    public function forDate(string $date)
    {
        $clockIn = Carbon::parse($date . ' 08:55:00')->addMinutes(rand(0, 5));
        $clockOut = Carbon::parse($date . ' 18:00:00')->addMinutes(rand(0, 60));

        return $this->state([
            'date' => $date,
            'clock_in_at' => $clockIn,
            'clock_out_at' => $clockOut,
        ]);
    }
}
