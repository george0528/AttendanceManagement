<?php

namespace Database\Factories;

use App\Models\ShiftRequest;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShiftRequestDateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $data = [
            'request_id' => ShiftRequest::first()->id,
            'start_time' => $this->generateStartDate(),
        ];
        $data['end_time'] = $this->generateEndDate($data['start_time']);

        return $data;
    }

    public function generateStartDate()
    {
        return $this->faker->dateTimeBetween('now', '+2 week')->format('Y-m-d H:i:s');
    }

    public function generateEndDate($start_time)
    {
        $end_time = new Carbon($start_time);
        $end_time->addHour();
        return $end_time->__toString();
    }
}
