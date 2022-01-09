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

  // 配列データの就業時間の合計
  public static function getTimes($histories): array
  {
    $sum_times = 0;
    $midnight_times = 0;

    if(isset($histories)) {
      foreach ($histories as $history) {
        $sum_times += $history->sumTime();
        $midnight_times += $history->midnightTime();
      }
    }

    $data = [
      'sum_times' => $sum_times,
      'midnight_times' => $midnight_times,
    ];

    return $data;
  }

  // 就業時間の合計を算出
  public function sumTime(): int
  {
    $start_time = new Carbon($this->start_time);
    $end_time = new Carbon($this->end_time);

    $diff_time = $start_time->diffInMinutes($end_time);
    return $diff_time;
  }

  // 深夜時間を算出
  public function midnightTime(): int 
  {
    $start_time = new Carbon($this->start_time);
    $end_time = new Carbon($this->end_time);

    // 出勤時間と退勤時間が日付をまたいでいなかったら
    if($start_time->month == $end_time->month && $start_time->day == $end_time->day) {
      $start_time = $this->changeMinute($start_time);
      $end_time = $this->changeMinute($end_time);
    } else {
      $start_time = $this->changeMinute($start_time);
      $end_time = $this->changeMinute($end_time) + 24 * 60;
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
      
      // 退勤時間が5時を過ぎる場合
      if($night_end_time <= $end_time) {
        return $night_end_time - $start_time;
      }

      // 退勤時間が5時より前の場合 
      return $end_time - $start_time;
    }

    // 出勤時間が22時前かつ退勤時間の時間が22時より後なら
    if($night_start_time - $start_time < $end_time - $start_time) {

      // 退勤時間が5時を超えていたら
      if($night_end_time <= $end_time) {
        return $night_end_time - $night_start_time;
      }

      return $end_time - $start_time;
    }

    // 深夜時間ではなかったら
    return 0;
  }

  // 分にする
  public function changeMinute($time)
  {
    return $time->hour * 60 + $time->minute;
  }
}
