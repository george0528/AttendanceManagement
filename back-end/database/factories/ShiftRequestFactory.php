<?php

namespace Database\Factories;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShiftRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $date = [
            'user_id' => User::first()->id,
            'request_check' => false,
            'created_at' => new Carbon('now'),
        ];
        return $date;
    }
}
