<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AdminFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => $this -> faker->name(),
            'email' => $this -> faker->unique()
                             -> safeEmail(),
            'password' => bcrypt('1234abcd'),
        ];
    }
}
