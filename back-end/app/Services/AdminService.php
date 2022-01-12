<?php

namespace App\Services;

use App\Models\AbsenceRequest;
use App\Models\History;
use App\Models\Option;
use App\Models\Salary;
use App\Models\Schedule;
use App\Models\ShiftRequest;
use App\Models\ShiftRequestDate;
use App\Models\User;
use Exception;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
    $users = User::with('salary')->get();
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

  // userの給与を取得
  public function getSalary()
  {
    $salaries = Salary::all();
    if(empty($salaries)) {
      return response()->json('給料の設定がありません', 400);
    }
    return response()->json($salaries, 200);
  }

  // userの給与設定を追加
  public function addSalary($data)
  {
    DB::beginTransaction();
    try {
      $salary = Salary::where('user_id', $data['user_id'])->first();
      if (isset($salary)) {
        $delete_res = $salary->delete();
        if(empty($delete_res)) {
          throw new Exception("userのすでに設定済みの給与設定の削除に失敗しました");
        }
      }
      $create_res = Salary::create($data);
      if(empty($create_res)) {
        throw new Exception("userの給与設定の作成に失敗しました");
      }
      DB::commit();
      return response()->json('給与設定を追加しました', 200);
    } catch (\Exception $e) {
      DB::rollBack();
      logger()->error($e);
      return response()->json('給与設定の追加に失敗しました', 400);
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
        throw new \Exception("そのIDのシフトリクエストはありません");
      }

      return response()->json($shift_request_dates, 200);
    } catch (\Exception $e) {
      return response()->json($e, 400);
    }
  }

  // スケジュールを取得
  public function getSchedule()
  {
    $schedules = Schedule::with(['user', 'absence_request'])->get();
    return response()->json($schedules, 200);
  }

  // スケジュールを追加
  public function addSchedule($request_id)
  {
    DB::beginTransaction();
    try {
      $shift_request_dates = ShiftRequestDate::where('request_id', $request_id)->get();
      $shift_request = ShiftRequest::find($request_id);
      $user_id = $shift_request->user_id;
      $res = $shift_request->delete();
      foreach ($shift_request_dates as $index => $date) {
        $shift_request_dates[$index]['user_id'] = $user_id;
        unset($shift_request_dates[$index]['id']);
        unset($shift_request_dates[$index]['request_id']);
      }
      DB::table('schedules')->insert($shift_request_dates->toArray());
      if($res != 1) {
        throw new \Exception('失敗しました');
      }
      DB::commit();
      return response()->json('スケジュールの追加に成功しました', 200);
    } catch (\Exception $e) {
      DB::rollBack();
      return response()->json('スケジュールの追加に失敗しました', 400);
    }
  }

  // 欠勤申請取得
  public function getAbsence()
  {
    $absence_requests = AbsenceRequest::with('schedule.user')->get();
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

  // 設定の取得
  public function getOption()
  {
    $option = Option::first();
    return response()->json($option, 200);
  }
}