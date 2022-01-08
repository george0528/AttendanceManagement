<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
  use HasApiTokens, HasFactory, Notifiable, SoftDeletes;
  protected $dates = ['deleted_at'];

  /**
   * The attributes that are mass assignable.
   *
   * @var string[]
   */
  protected $guarded = [
    'id'
  ];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var array
   */
  protected $hidden = [
    'password',
    'remember_token',
  ];

  /**
   * The attributes that should be cast.
   *
   * @var array
   */
  protected $casts = [
    'email_verified_at' => 'datetime',
  ];

  // ログインID生成
  public function loginIdGenerate()
  {
    $loop_flag = true;
    $login_id = '';
    while($loop_flag) {
      $login_id = Str::random(8);
      $already_login_id =  $this->withTrashed()->where('login_id', $login_id)->first();
      if(!$already_login_id) {
        $loop_flag = false;
        return $login_id;
      }
    }
  }

  // スケジュールのhasmany
  public function schedules()
  {
    return $this->hasMany(Schedule::class);
  }

  // ユーザーのスケジュール取得 
  public function getSchedules()
  {
    $id = $this->id;
    return Schedule::with('absence_request')->where('user_id', $id)->get();
  }

  // 就業履歴のhasmany
  public function histories()
  {
    return $this->hasMany(History::class);
  }

  // 就業履歴の取得 
  public function getHistories()
  {
    $id = $this->id;
    return History::where('user_id', $id)->get();
  }

  // 給料のhasone
  public function salary()
  {
    return $this->hasOne(Salary::class);
  }

  // 給料が設定されているか
  public function is_set_salary()
  {
    $id = $this->id;
    User::find(1);
    $res = Salary::where('user_id', $id)->first();
    if (isset($res)) {
      return true;
    }
    return false;
  }
  // 現在出勤しているか
  public function is_attendance()
  {
    $id = $this->id;
    $is_attendance = Now::where('user_id', $id)->first();
    if($is_attendance) {
      return true;
    } else {
      return false;
    }
  }
}
