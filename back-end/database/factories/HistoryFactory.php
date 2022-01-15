<?php

namespace Database\Factories;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class HistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $data = [
            'user_id' => User::first()->id,
            'start_time' => $this->generateDate(),
            'end_time' => $this->generateDate(),
        ];
        $start_time = new Carbon($data['start_time']);
        $end_time = $start_time->addHours(mt_rand(1, 10));
        $data['end_time'] = $end_time;
        return $data;
    }

    public function generateDate()
    {
        return $this->faker->dateTimeBetween('-2 week', 'now');
    }
}
