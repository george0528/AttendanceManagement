<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScheduleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::first()->id,
            'start_time' => $this->generateDate(),
            'end_time' => $this->generateDate(),
        ];
    }

    public function generateDate()
    {
        return $this->faker->dateTimeBetween('now', '+2 week')->format('Y-m-d H:i:s');
    }
    
}
