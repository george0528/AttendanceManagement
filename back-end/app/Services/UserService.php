<?php

namespace App\Services;

use App\Models\AbsenceRequest;
use App\Models\History;
use App\Models\Now;
use App\Models\ShiftRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserService
{

  // ログイン処理
  public function login($credentails)
  {
    if(Auth::attempt($credentails)) {
      session()->regenerate();
      return response()->json(['message' => 'success', 'name' => Auth::user()->name], 200);
    }
    return response()->json(['message' => 'fail'], 400);
  }

  // ログアウト処理
  public function logout()
  {
    if(Auth::check()) {
      Auth::guard('user')->logout();
      session()->invalidate();
      session()->regenerateToken();
      return response()->json(['message' => 'success'], 200);
    }

    return response()->json(['message' => 'fail'], 400); 
  }

  // 欠勤申請処理
  public function addAbsence($data)
  {
    try {
      $res = AbsenceRequest::create($data);
      // 通知処理ロジック
      return new JsonResponse($res, 200);
    } catch (\Exception $e) {
      return new JsonResponse($e, 400);
    }
  }

  // 出勤処理
  public function clockIn($data)
  {
    try {
      $res = Now::create($data);
      return response()->json($res, 200);
    } catch (\Exception $e) {
      return response()->json($e, 400);
    }
  }

  // 退勤処理
  public function clockOut($data)
  {
    DB::beginTransaction();
    try {
      $now = Now::where('user_id', $data['user_id'])->first();
      $data['start_time'] = $now->start_time;
      $res = History::create($data);
      $now->delete();
      DB::commit();
      return response()->json($res, 200);
    } catch (\Exception $e) {
      DB::rollBack();
      return response()->json($e, 400);
    }
  }

  // シフト申請
  public function addSchedule($schedules)
  {
    DB::beginTransaction();
    try {
      $shift_request = ShiftRequest::create(['user_id' => Auth::id()]);
      $new_schedules = [];
      foreach ($schedules as $schedule) {
        $schedule['request_id'] = $shift_request->id;
        $new_schedules[] = $schedule;
      }
      info('array', $new_schedules);
      DB::table('shift_request_dates')->insert($new_schedules);
      DB::commit();
      return response()->json($shift_request->with('shift_request_dates')->find($shift_request->id), 200);
    } catch (\Exception $e) {
      DB::rollBack();
      return response()->json($e, 400);
    }
  }

}
