<?php

namespace App\Services;

use App\Models\Schedule;

class AuthenticatedService
{
  public function getSchedule()
  {
    try {
      $schedules = Schedule::all();
      return response()->json($schedules, 200);
    } catch (\Exception $e) {
      return response()->json($e, 400);
    }
  }
}
