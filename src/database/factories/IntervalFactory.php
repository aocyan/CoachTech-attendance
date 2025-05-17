<?php

namespace Database\Factories;

use App\Models\Interval;
use App\Models\Attendance;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class IntervalFactory extends Factory
{
    protected $model = Interval::class;

    public function definition()
    {
        //
    }

    public function forAttendance(Attendance $attendance)
    {
        $clockIn = Carbon::parse($attendance->clock_in_at);
        $clockOut = Carbon::parse($attendance->clock_out_at);

        $intervalStartLimit = (clone $clockIn)->addMinutes(60);
        $intervalEndLimit = (clone $clockOut)->subMinutes(60);

        $intervalCount = rand(1, 3);
        
        $maxIntervalMinutes = 90;     
        $totalIntervalMinutes = 0;
        $intervals = [];

        for ($i = 1; $i <= $intervalCount; $i++) {

            $remaining = $maxIntervalMinutes - $totalIntervalMinutes;
            if ($remaining < 15) break;

            $intervalMinutes = rand(15, min(30, $remaining));
            $possibleStart = $this
                                -> faker  
                                -> dateTimeBetween($intervalStartLimit, $intervalEndLimit -> copy() -> subMinutes($intervalMinutes));

            $intervalIn = Carbon::parse($possibleStart);
            $intervalOut = (clone $intervalIn) -> addMinutes($intervalMinutes);

            $intervals[] = [
                'attendance_id' => $attendance->id,
                'interval_in_at' => $intervalIn,
                'interval_out_at' => $intervalOut,
            ];

            $totalIntervalMinutes += $intervalMinutes;
        }

        return collect($intervals);
    }
}