<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
  private $service;
  public function __construct(UserService $service) {
    $this->service = $service;
  }

  public function login(Request $request)
  {
    $val = Validator::make($request->all(), [
      'login_id' => ['required'],
      'password' => ['required']
    ]);

    if($val->fails()) {
      return new JsonResponse('fail val', 400);
    }
    
    return $this->service->login($val->validated());
  }
  public function logout()
  {
    return $this->service->logout();
  }
  public function getSchedule()
  {
    if(Auth::check()) {
      $user = Auth::user();
      $schedules = $user->getSchedules();
      return response()->json($schedules, 200);
    }

    return response()->json('fail', 400);
  }
  public function getHistory()
  {
    if(Auth::check()) {
      $user = Auth::user();
      $histories = $user->getHistories();
      return response()->json($histories, 200);
    }

    return response()->json('fail', 400);
  }
  public function addAbsence(Request $request)
  {
    $val = Validator::make($request->all(), [
      'schedule_id' => ['required', 'integer', 'exists:schedules,id'],
      'comment' => ['nullable', 'string'],
    ]);

    if($val->fails()) {
      return new JsonResponse('fails', 400);
    }

    $val = array_filter($val->validated());

    return $this->service->addAbsence($val);
  }
}
