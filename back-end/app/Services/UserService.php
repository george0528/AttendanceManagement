<?php

namespace App\Services;

use App\Models\AbsenceRequest;
use App\Models\History;
use App\Models\Now;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserService
{
  public function login($credentails)
  {
    if(Auth::attempt($credentails)) {
      session()->regenerate();
      return response()->json(['message' => 'success', 'name' => Auth::user()->name], 200);

      return response()->json(['message' => 'fail'], 400);
    }
  }
  public function logout()
  {
    if(Auth::check()) {
      Auth::logout();
      session()->invalidate();
      session()->regenerateToken();
      return response()->json(['message' => 'success'], 200);
    }

    return response()->json(['message' => 'fail'], 400); 
  }
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
  public function clockIn($data)
  {
    try {
      $res = Now::create($data);
      return response()->json($res, 200);
    } catch (\Exception $e) {
      return response()->json($e, 400);
    }
  }
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
}
