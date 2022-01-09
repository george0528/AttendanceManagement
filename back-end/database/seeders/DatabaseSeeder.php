<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\User::factory(10)->create();
        \App\Models\Admin::factory(3)->create();
        \App\Models\History::factory(3)->create();
        \App\Models\Schedule::factory(3)->create();
        \App\Models\ShiftRequest::factory(3)->create();
        \App\Models\ShiftRequestDate::factory(3)->create();
        \App\Models\AbsenceRequest::factory(3)->create();
        \App\Models\Salary::factory(1)->create();
    }
}
