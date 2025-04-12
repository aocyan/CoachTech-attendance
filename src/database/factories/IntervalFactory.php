<?php

namespace Database\Factories;

use Carbon\Carbon;
use App\Models\Interval;
use App\Models\Attendance;
use Illuminate\Database\Eloquent\Factories\Factory;

class IntervalFactory extends Factory
{
    protected $model = \App\Models\Interval::class;

    public function definition()
    {
        return [
            'attendance_id' => Attendance::factory(),
            'interval_in_at' => now(),
            'interval_out_at' => now(),
        ];
    }

    public function forAttendance(Attendance $attendance)
    {
        $clockIn = Carbon::parse($attendance -> clock_in_at);
        $clockOut = Carbon::parse($attendance -> clock_out_at);

        $intervalIn = $this -> faker
                            ->dateTimeBetween($clockIn, $clockOut);                           
        $intervalOut = Carbon::parse($intervalIn) -> addMinutes(rand(15, 90))
                                                  ->min($clockOut);

        return $this->state([
            'attendance_id' => $attendance->id,
            'interval_in_at' => $intervalIn,
            'interval_out_at' => $intervalOut,
        ]);
    }
}
