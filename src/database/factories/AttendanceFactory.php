<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceFactory extends Factory
{
    protected $model = Attendance::class;

    public function definition()
    {
        $randomDate = $this -> faker
                            -> dateTimeThisYear();
        
        $hour = 9;
        $minute = rand(0, 59);
        $second = rand(0, 59);

        $clockIn = (clone $randomDate)-> setTime($hour, $minute, $second);

        $clockOut = (clone $clockIn)-> modify('+' . rand(9, 10) . ' hours');

        return [
            'user_id' => User::factory(),
            'clock_in_at' => $clockIn,
            'clock_out_at' => $clockOut,
        ];
    }
}
