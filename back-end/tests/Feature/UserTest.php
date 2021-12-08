<?php

namespace Tests\Feature;

use App\Models\AbsenceRequest;
use App\Models\History;
use App\Models\Now;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserTest extends TestCase
{
  use DatabaseTransactions, WithFaker;

  // setup
  public function setUp(): void
  {
    parent::setUp();
    $this->user = User::factory()->create();
    $this->url = '/api/user';
  }
  /**
   * A basic feature test example.
   *
   * @return void
   */

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
    $response->assertJsonFragment(['fail val']);

    $response = $this->postJson($this->url, [
      'login_id' => 'fajdlfjaljdlfadddddd',
      'password' => 'password',
    ]);
    $response->assertStatus(400);
    $response->assertJsonFragment(['fail val']);

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
    $this->assertDatabaseCount('nows', $in_count);
  }

  public function test_clockIn_failed()
  {
    $this->url = $this->url.'/clockin';

    Now::create(['user_id' => $this->user->id, 'start_time' => Carbon::now()]);
    $count = Now::count();
    $response = $this->actingAs($this->user, 'user')->postJson($this->url);
    $response->assertStatus(400);
    $response->assertJsonFragment(['すでに出勤しています']);
    $this->assertDatabaseCount('nows', $count);
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
    
    $this->assertDatabaseCount('nows', $now_count);
    $this->assertDatabaseCount('histories', $history_count);
  }

  public function test_clockOut_failed()
  {
    $this->url = $this->url.'/clockout';

    $history_count = History::count();

    $response = $this->actingAs($this->user, 'user')->postJson($this->url);
    $response->assertStatus(400);

    $this->assertDatabaseCount('histories', $history_count);
  }

  // スケジュール取得
  public function test_schedule_success()
  {
    $this->url = $this->url.'/schedule';

    Schedule::factory(3)->create(['user_id' => $this->user->id]);

    $schedule_count = Schedule::count();

    $response = $this->actingAs($this->user, 'user')->getJson($this->url);
    $response->assertOk();

    $json = $response->decodeResponseJson();
    $this->assertEquals($schedule_count, count($json));
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

    $this->assertDatabaseCount('absence_requests', $absence_count);
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

    $this->assertDatabaseCount('absence_requests', $absence_count);
  }
}