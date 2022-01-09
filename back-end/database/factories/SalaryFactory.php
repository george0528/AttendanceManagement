<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SalaryFactory extends Factory
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
            'salary_type' => $this->generateSalaryType(),
            'hour_salary' => $this->faker->numberBetween(1000, 2000),
            'month_salary' => $this->faker->numberBetween(200000, 400000),
        ];
    }

    public function generateSalaryType()
    {
        $salary_types = ['hour', 'month'];
        shuffle($salary_types);
        return $salary_types[0];
    }
}
