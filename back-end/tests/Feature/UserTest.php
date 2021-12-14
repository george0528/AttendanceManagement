<?php

namespace Tests\Feature;

use App\Models\AbsenceRequest;
use App\Models\History;
use App\Models\Now;
use App\Models\Schedule;
use App\Models\ShiftRequest;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserTest extends TestCase
{
  use DatabaseTransactions, WithFaker;

  // セットアップ
  public function setUp(): void
  {
    parent::setUp();
    $this->user = User::factory()->create();
    $this->url = '/api/user';
  }

  // ログイン
  public function test_login_success()
  {
    $this->assertGuest('user');

    $this->url = $this->url.'/login';
    $response = $this->post($this->url, [
      'login_id' => $this->user->login_id,
      'password' => 'password',
    ]);
    $response->assertStatus(200);
    $this->assertAuthenticated('user');
  }

  public function test_login_failed()
  {
    $this->assertGuest('user');

    $this->url = $this->url.'/login';
    
    $response = $this->postJson($this->url, []);
    $response->assertStatus(400);

    // ログインロックチェックテスト
    for ($i=0; $i < 6; $i++) { 
      $response = $this->postJson($this->url, [
        'login_id' => $this->user['login_id'],
        'password' => 'passssss'
      ]);
      if($i == 5) {
        $response->assertStatus(400);
        $response->assertJsonFragment(['ログインの試行回数を超えました']);
        $this->assertGuest('user');

        // ログインロック解除
        $auth = new AuthService;
        $auth->login_success($this->user['login_id'], request()->ip());
      }
    }

    $response = $this->postJson($this->url, [
      'login_id' => 'fajdlfjaljdlfadddddd',
      'password' => 'password',
    ]);
    $response->assertStatus(400);

    $response = $this->postJson($this->url, [
      'login_id' => $this->user->login_id
    ]);
    $response->assertStatus(400);

    $response = $this->postJson($this->url, [
      'password' => 'password'
    ]);
    $response->assertStatus(400);

    $response = $this->postJson($this->url, [
      'login_id' => $this->user->login_id,
      'password' => 'lfajdflkj'
    ]);
    $response->assertStatus(400);
  }

  // ログアウト
  public function test_logout_success()
  {
    $this->url = $this->url.'/logout';

    $this->assertGuest('user');
    Auth::login($this->user);
    $this->assertAuthenticatedAs($this->user, 'user');
    $response = $this->postJson($this->url);
    $response->assertOk();
    $this->assertGuest('user');
  }

  public function test_logout_failed()
  {
    $this->url = $this->url.'/logout';

    $this->assertGuest('user');
    $response = $this->postJson($this->url);
    $response->assertStatus(401);
    $response->assertJsonFragment(['Unauthenticated']);
  }

  // 出勤
  public function test_clockIn_success()
  {
    $this->url = $this->url.'/clockin';

    $count = Now::count();
    $in_count = $count + 1;
    $response = $this->actingAs($this->user, 'user')->postJson($this->url);
    $response->assertOk();
    $this->assertEquals(Now::count(), $in_count);
  }

  public function test_clockIn_failed()
  {
    $this->url = $this->url.'/clockin';

    Now::create(['user_id' => $this->user->id, 'start_time' => Carbon::now()]);
    $count = Now::count();
    $response = $this->actingAs($this->user, 'user')->postJson($this->url);
    $response->assertStatus(400);
    $response->assertJsonFragment(['すでに出勤しています']);
    $this->assertEquals(Now::count(), $count);
  }

  // 退勤
  public function test_clockOut_success()
  {
    $this->url = $this->url.'/clockout';
    
    $time = Carbon::now();
    $time->subHour(5);
    Now::create(['user_id' => $this->user->id, 'start_time' => $time]);

    $history_count = History::count();
    $history_count++;

    $now_count = Now::count();
    $now_count--;

    $response = $this->actingAs($this->user, 'user')->postJson($this->url);
    $response->assertOk();
    
    $this->assertEquals(Now::count(), $now_count);
    $this->assertEquals(History::count(), $history_count);
  }

  public function test_clockOut_failed()
  {
    $this->url = $this->url.'/clockout';

    $history_count = History::count();

    $response = $this->actingAs($this->user, 'user')->postJson($this->url);
    $response->assertStatus(400);

    $this->assertEquals(History::count(), $history_count);
  }

  // スケジュール取得
  public function test_schedule_success()
  {
    $this->url = $this->url.'/schedule';

    Schedule::factory(3)->create(['user_id' => $this->user->id]);

    $response = $this->actingAs($this->user, 'user')->getJson($this->url);
    $response->assertOk();

    $json = $response->json();
    $this->assertEquals(Schedule::count(), count($json));
  }

  // 就業履歴取得
  public function test_history_success()
  {
    $this->url = $this->url.'/history';

    History::factory(3)->create(['user_id' => $this->user->id]);

    $history_count = History::count();

    $response = $this->actingAs($this->user, 'user')->getJson($this->url);
    $response->assertOk();

    $json = $response->decodeResponseJson();
    $this->assertEquals($history_count, count($json)); 
  }

  // 欠勤申請
  public function test_absence_success()
  {
    $this->url = $this->url.'/absence';

    $schedule = Schedule::factory()->create(['user_id' => $this->user->id]);
    
    $absence_count = AbsenceRequest::count();
    $absence_count++;

    $response = $this->actingAs($this->user, 'user')->postJson($this->url, [
      'schedule_id' => $schedule->id,
    ]);
    $response->assertOk();

    $this->assertEquals(AbsenceRequest::count(), $absence_count);
  }

  public function test_absence_comment_success()
  {
    $this->url = $this->url.'/absence';

    $schedule = Schedule::factory()->create(['user_id' => $this->user->id]);

    $absence_count = AbsenceRequest::count();
    $absence_count++;

    $comment = '体調不良のため';

    $response = $this->actingAs($this->user, 'user')->postJson($this->url, [
      'schedule_id' => $schedule->id,
      'comment' => $comment, 
    ]);
    $response->assertOk();
    $response->assertJsonFragment(['comment' => $comment]);

    $this->assertEquals(AbsenceRequest::count(), $absence_count);
  }

  // シフト申請
  public function test_shift_success()
  {
    $this->url = $this->url.'/schedule';

    $shift_request_count = ShiftRequest::count();
    $shift_request_count++;

    $data = [];

    for ($i=0; $i < 3; $i++) { 
      $data['schedules'][] = $this->schedule_data_generate();
    }

    $res = $this->actingAs($this->user, 'user')->postJson($this->url, $data);
    $res->assertOk();

    $this->assertEquals($shift_request_count, ShiftRequest::count());
  }
  
  public function test_shift_failed()
  {
    $this->url = $this->url.'/schedule';

    $shift_request_count = ShiftRequest::count();

    $data = [];

    for ($i=0; $i < 20; $i++) { 
      if($i == 10) {
        $schedule = $this->schedule_data_generate();
        $schedule['end_time'] = $schedule['start_time'];
        $data['schedules'][] = $schedule;
      } else {
        $data['schedules'][] = $this->schedule_data_generate();
      }
    }

    $res = $this->actingAs($this->user, 'user')->postJson($this->url, $data);
    $res->assertStatus(400);

    $this->assertEquals($shift_request_count, ShiftRequest::count());
  }

  // シフト申請のテストデータ生成関数
  public function schedule_data_generate()
  {
    $start_time = $this->faker->dateTimeBetween('tomorrow', '+2 week');
    $start_time = new Carbon($start_time);
    $end_time = new Carbon($start_time);
    $end_time->addHour();
    $schedule_data = [
      'start_time' =>  $start_time->__toString(),
      'end_time' => $end_time->__toString(),
    ];
    return $schedule_data;
  }
}