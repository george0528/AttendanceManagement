<?php

namespace App\Services;

use App\Models\AbsenceRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

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
}
