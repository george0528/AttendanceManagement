<?php

namespace App\Services;

use App\Models\AbsenceRequest;
use App\Models\ShiftRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminService
{
  public function login($credentials)
  {
    if(Auth::guard('admin')->attempt($credentials)) {
      session()->regenerate();
      return response()->json(['message' => 'success', 'name' => Auth::guard('admin')->user()->name], 200);
    }

    return response()->json(['message' => 'fail'], 400);
  }
  public function logout()
  {
    if(Auth::guard('admin')->check()) {
      Auth::guard('admin')->logout();
      session()->invalidate();
      session()->regenerateToken();
      return response()->json(['message' => 'success'], 200);
    }

    return response()->json(['message' => 'fail'], 400); 
  }
  public function getUser()
  {
    $users = User::all();
    return response()->json($users, 200);
  }
  public function addUser($data)
  {
    try {
      $user = new User;
      $login_id = $user->loginIdGenerate();
      $data['login_id'] = $login_id;
      $data['password'] = Hash::make($data['password']);
      $data['remember_token'] = Str::random(10);
      User::create($data);
      return response()->json($data, 200);
    } catch (\Exception $e) {
      return response()->json($e, 400);
    }
  }
  public function deleteUser($user_id)
  {
    try {
      User::destroy($user_id);
      return response()->json(['message' => 'success'], 200);
    } catch (\Exception $e) {
     return response()->json($e, 400);
    }
  }
  public function updateUser($data)
  {
    try {
      $user = User::find($data['user_id']);
      unset($data['user_id']);
      $user->fill($data)->save();
      return response()->json($user, 200);
    } catch (\Exception $e) {
      return response()->json($e, 400);
    }
  }

  // 論理削除したuserの取得
  public function getDeleteUser()
  {
    $users = User::onlyTrashed()->get();
    return response()->json($users, 200);
  }

  // 論理削除したuserを復元
  public function restoreDeleteUser($user_id)
  {
    try {
      $user = User::onlyTrashed()->find($user_id);
      $res = $user->restore();
      if(!$res) {
        throw new Exception("戻す事が出来ませんでした", 1);
      }
      return response()->json('success', 200);
    } catch (\Exception $e) {
      return response()->json($e, 400);
    }
  }

  // 論理削除したuserを物理削除する
  public function forceDeleteUser($user_id)
  {
    try {
      $user = User::onlyTrashed()->find($user_id);
      $res = $user->forceDelete();
      if(!$res) {
        throw new Exception("完全に削除する事が出来ませんでした", 1);
      }
      return response()->json('success', 200);
    } catch (\Exception $e) {
      return response()->json($e, 400);
    }
  }

  // シフトリクエストを取得
  public function getShift()
  {
    try {
      $shift_requests = ShiftRequest::with('shift_request_dates')->get();
      return response()->json($shift_requests, 200);
    } catch (\Exception $e) {
      return response()->json($e, 400);
    }
  }

  // 欠勤申請取得
  public function getAbsence()
  {
    $absence_requests = AbsenceRequest::all();
    return response()->json($absence_requests, 200);
  }
}