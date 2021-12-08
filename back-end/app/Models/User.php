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

  public function loginIdGenerate()
  {
    $loop_flag = true;
    $login_id = '';
    while($loop_flag) {
      $login_id = Str::random(8);
      $already_login_id =  $this->where('login_id', $login_id)->first();
      if(!$already_login_id) {
        $loop_flag = false;
        return $login_id;
      }
    }
  }
  public function schedules()
  {
    return $this->hasMany(Schedule::class);
  }
  public function getSchedules()
  {
    $id = $this->id;
    return Schedule::where('user_id', $id)->get();
  }
  public function histories()
  {
    return $this->hasMany(History::class);
  }
  public function getHistories()
  {
    $id = $this->id;
    return History::where('user_id', $id)->get();
  }
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
