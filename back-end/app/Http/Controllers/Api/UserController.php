<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\History;
use App\Models\Now;
use App\Models\Schedule;
use App\Services\AuthService;
use App\Services\UserService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
  private $service;
  public function __construct(UserService $service, AuthService $auth) {
    $this->service = $service;
    $this->auth = $auth;
  }

  // ログイン
  public function login(Request $request)
  {
    $val = Validator::make($request->all(), [
      'login_id' => ['required'],
      'password' => ['required']
    ]);

    if($val->fails()) {
      // $this->auth->loginFailed();
      return new JsonResponse('fail val', 400);
    }
    
    return $this->service->login($val->validated(), $request->ip());
  }

  // ログアウト
  public function logout()
  {
    return $this->service->logout();
  }

  // シフトスケジュール確認
  public function getSchedule()
  {
    if(Auth::check()) {
      $user = Auth::user();
      $schedules = $user->getSchedules();
      return response()->json($schedules, 200);
    }

    return response()->json('fail', 400);
  }
  
  // シフト申請
  public function addSchedule(Request $request)
  {
    $val = Validator::make($request->all(), [
      'schedules' => ['required', 'array'],
      'schedules.*.start_time' => ['required', 'date_format:Y-m-d H:i:s', 'after:tomorrow'],
      'schedules.*.end_time' => ['required', 'date_format:Y-m-d H:i:s', 'after:schedules.*.start_time', ],
    ]);
    
    if($val->fails()) {
      return response()->json($val->errors(), 400);
    }

    return $this->service->addSchedule($val->validated()['schedules']);
  }

  // 就業履歴
  public function getHistory()
  {
    if(Auth::check()) {
      $user = Auth::user();
      $histories = $user->getHistories();
      return response()->json($histories, 200);
    }

    return response()->json('fail', 400);
  }

  // 就業時間の合計を返す
  public function getHistoryTime()
  {
    if(Auth::check()) {
      $user = Auth::user();
      $histories = $user->getHistories();
      $data = History::sum_times($histories);
      return response()->json($data, 200);
    }
  }

  // 欠勤申請
  public function addAbsence(Request $request)
  {
    $val = Validator::make($request->all(), [
      'schedule_id' => ['required', 'integer', Rule::exists('schedules', 'id')->whereNull('deleted_at')],
      'comment' => ['nullable', 'string'],
    ]);

    if($val->fails()) {
      return new JsonResponse('fails', 400);
    }

    $val = array_filter($val->validated());

    return $this->service->addAbsence($val);
  }

  // 出勤処理
  public function clockIn()
  {
    $user = Auth::user();
    $id = $user->id;
    $is_attendance = $user->is_attendance();
    $time = new Carbon('now');

    if($is_attendance) {
      return response()->json('すでに出勤しています', 400);
    }

    return $this->service->clockIn(['user_id' => $id, 'start_time' => $time]);
  }

  // 退勤処理
  public function clockOut()
  {
    $user = Auth::user();
    $id = $user->id;
    $is_attendance = $user->is_attendance();
    $time = new Carbon('now');

    if($is_attendance) {
      return $this->service->clockOut(['user_id' => $id, 'end_time' => $time]);
    }

    return response()->json('まだ出勤していません', 400);
  }
}
