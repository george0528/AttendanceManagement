<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class History extends Model
{
  use HasFactory, SoftDeletes;
  protected $guarded = ['id'];
  public $timestamps = false;

  public function midnight_time() {
    $start_time = new Carbon($this->start_time);
    $end_time = new Carbon($this->end_time);

    // 出勤時間と退勤時間が日付をまたいでいなかったら
    if($start_time->month == $end_time->month && $start_time->day == $end_time->day) {
      $start_time = $this->change_minute($start_time);
      $end_time = $this->change_minute($end_time);
    } else {
      $start_time = $this->change_minute($start_time);
      $end_time = $this->change_minute($end_time) + 24 * 60;
    }

    // 出勤時間が日付をまたいでいないかつ5時より前に出勤している場合
    if($start_time <= 5 * 60) {
      $start_time += 24 * 60;
      $end_time += 24 * 60;
    }

    $night_start_time = 22 * 60;
    $night_end_time = 5 * 60 + 24 * 60;
    
    // 出勤時間が22時以降かつ5時前なら
    if($night_start_time <= $start_time) {
      info('1 true');
      
      // 退勤時間が5時を過ぎる場合
      if($night_end_time <= $end_time) {
        return $night_end_time - $start_time;
      }

      // 退勤時間が5時より前の場合 
      return $end_time - $start_time;
    }

    // 出勤時間が22時前かつ退勤時間の時間が22時より後なら
    if($night_start_time - $start_time < $end_time - $start_time) {
      info('2 true');
      if($night_end_time <= $end_time) {
        return $night_end_time - $night_start_time;
      }
      return $end_time - $start_time;
    }

    info('3 true');
    // 深夜時間ではなかったら
    return 0;
  }

  // 分にする
  public function change_minute($time)
  {
    return $time->hour * 60 + $time->minute;
  }

  public function test()
  {
    return $this->midnight_time();
  }
}
