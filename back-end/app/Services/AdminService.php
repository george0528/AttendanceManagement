<?php

namespace App\Services;

use App\Models\AbsenceRequest;
use App\Models\History;
use App\Models\ShiftRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminService extends AuthService
{
  // 運営者情報を取得
  public function getAdmin()
  {
    if(Auth::guard('admin')->check()) {
      return response()->json(Auth::guard('admin')->user(), 200);
    }

    return response()->json('ログインしていません', 400);
  }
  // ログイン
  public function login($credentials, $ip_address)
  {
    if($this->is_login_lock($credentials['email'], $ip_address)) {
      return response()->json('ログインの試行回数を超えました', 400);
    }

    if(Auth::guard('admin')->attempt($credentials)) {
      $this->login_success($credentials['email'], $ip_address);
      session()->regenerate();
      return response()->json(['message' => 'success', 'name' => Auth::guard('admin')->user()->name], 200);
    }

    $this->login_failed($credentials['email'], $ip_address);
    return response()->json('ログインに失敗しました', 400);
  }

  // ログアウト
  public function logout()
  {
    if(Auth::guard('admin')->check()) {
      Auth::guard('admin')->logout();
      session()->invalidate();
      session()->regenerateToken();
      return response()->json('ログアウトに成功しました', 200);
    }

    return response()->json('ログアウトに失敗しました', 400); 
  }

  // user取得
  public function getUser()
  {
    $users = User::all();
    return response()->json($users, 200);
  }

  // user追加
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

  // user削除
  public function deleteUser($user_id)
  {
    try {
      User::destroy($user_id);
      return response()->json('ユーザーを削除しました', 200);
    } catch (\Exception $e) {
     return response()->json($e, 400);
    }
  }

  // user情報の編集
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
        throw new Exception("戻す事が出来ませんでした");
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
        throw new Exception("完全に削除する事が出来ませんでした");
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
      $shift_requests = ShiftRequest::with('user')->get();
      return response()->json($shift_requests, 200);
    } catch (\Exception $e) {
      return response()->json($e, 400);
    }
  }

  // 特定のIDのシフトリクエストを取得
  public function getShiftId($request_id)
  {
    try {
      $shift_request_dates = ShiftRequest::with('shift_request_dates')->find($request_id);

      if(!$shift_request_dates) {
        throw new Exception("そのIDのシフトリクエストはありません");
      }

      return response()->json($shift_request_dates, 200);
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

  // 欠勤申請を承諾
  public function putAbsence($absence_id)
  {
    try {
      $absence = AbsenceRequest::find($absence_id);
      $absence->request_check = true;
      $absence->save();
      return response()->json('欠勤処理に成功しました', 200);
    } catch (\Exception $e) {
      return response()->json($e, 400);
    }
  }
  
  // 就業履歴取得
  public function getHistory()
  {
    $histories = History::all();
    return response()->json($histories, 200);
  }
}