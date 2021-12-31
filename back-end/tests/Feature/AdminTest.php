<?php

namespace Tests\Feature;

use App\Models\AbsenceRequest;
use App\Models\Admin;
use App\Models\History;
use App\Models\Schedule;
use App\Models\ShiftRequest;
use App\Models\ShiftRequestDate;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AdminTest extends TestCase
{
  use DatabaseTransactions;

  // セットアップ
  public function setUp(): void
  {
  parent::setUp();
  $this->admin = Admin::factory()->create();
  $this->url = '/api/admin';   
  }

  // 運営者情報の取得
  public function test_admin_get_success()
  {
    $res = $this->actingAs($this->admin, 'admin')->get($this->url);
    $res->assertOk();
  }
  
  public function test_admin_get_fail()
  {
    $this->assertGuest('admin');
    $res = $this->get($this->url);
    $res->assertStatus(400);
  }

  // ログイン
  public function test_login_success()
  {
    $this->url = $this->url.'/login';

    $data = [
      'email' => $this->admin->email,
      'password' => 'password',
    ];

    $this->assertGuest('admin');
    $res = $this->postJson($this->url, $data);
    $res->assertOk();
    $this->assertAuthenticatedAs($this->admin, 'admin');
  }

  public function test_login_failed()
  {
    $this->url = $this->url.'/login';

    $data = [
      'email' => '',
      'password' => '',
    ];
    $this->login_failed_check($data);

    // ログインロックのテスト
    for ($i=0; $i < 6; $i++) { 
      $response = $this->postJson($this->url, [
        'email' => $this->admin['email'],
        'password' => 'passssss'
      ]);
      if($i == 5) {
        $response->assertStatus(400);
        $response->assertJsonFragment(['ログインの試行回数を超えました']);
        $this->assertGuest('admin');

        // ログインロック解除
        $auth = new AuthService;
        $auth->login_success($this->admin['email'], request()->ip());
      }
    }

    $data['email'] = $this->admin->email;
    $this->login_failed_check($data);

    $data['email'] = '';
    $data['password'] = 'password';
    $this->login_failed_check($data);
  }

  // ログイン失敗チェック関数
  public function login_failed_check($data)
  {
    $this->assertGuest('admin');
    $res = $this->postJson($this->url, $data);
    $res->assertStatus(400);
    $this->assertGuest('admin');
  }

  // ログアウト
  public function test_logout_success()
  {
    $this->url = $this->url.'/logout';

    Auth::guard('admin')->login($this->admin);
    $this->assertAuthenticatedAs($this->admin, 'admin');
    $res = $this->postJson($this->url);
    $res->assertOk();
    $this->assertGuest('admin');
  }

  public function test_logout_failed()
  {
    $this->url = $this->url.'/logout';

    $this->assertGuest('admin');
    $res = $this->postJson($this->url);
    $res->assertStatus(401);
    $res->assertJsonFragment(['Unauthenticated']);
  }

  // user取得
  public function test_user_get()
  {
    $this->url = $this->url.'/user';

    $user_count = User::count();
    $res = $this->actingAs($this->admin, 'admin')->getJson($this->url);
    $res->assertOk();
    
    $json = $res->decodeResponseJson();
    $this->assertEquals($user_count, count($json));
  }

  // user追加
  public function test_user_add()
  {
    $this->url = $this->url.'/user';

    $user_count = User::count();
    $user_count++;

    $data = [
      'name' => 'testuser',
      'age' => 18,
      'password' => 'password',
    ];
    $res = $this->actingAs($this->admin, 'admin')->postJson($this->url, $data);
    $res->assertOk();
    $this->assertEquals(User::count(), $user_count);
  }

  // user編集
  public function test_user_update_success()
  {
    $this->url = $this->url.'/user';

    $user = User::factory()->create();
    $data = [
      'user_id' => $user->id,
      'name' => 'testuser',
    ];

    $res = $this->actingAs($this->admin, 'admin')->putJson($this->url, $data);
    $res->assertOk();
    
    $data['id'] = $data['user_id'];
    unset($data['user_id']);
    $res->assertJsonFragment($data);
  }

  // user削除
  public function test_user_delete()
  {
    $this->url = $this->url.'/user';

    $user = User::factory()->create();

    $user_count = User::count();
    $user_count--;

    $data = [
      'user_id' => $user->id
    ];
    $res = $this->actingAs($this->admin, 'admin')->deleteJson($this->url, $data);
    $res->assertOk();

    $this->assertEquals(User::count(), $user_count);
  }

  // 論理削除のuserを取得
  public function test_user_softdelete_get()
  {
    $this->url = $this->url.'/user/delete';
    
    $user = User::factory()->create();
    $user->delete();

    $delete_user_count = User::onlyTrashed()->count();

    $res = $this->actingAs($this->admin, 'admin')->getJson($this->url);
    $res->assertOk();

    $json = $res->decodeResponseJson();
    $this->assertEquals($delete_user_count, count($json));
  }

  // 論理削除のデータを復元
  public function test_user_softdelete_put()
  {
    $this->url = $this->url.'/user/delete';

    $user = User::factory()->create();
    $user->delete();

    $user_count = User::count();
    $user_count++;

    $delete_user_count = User::onlyTrashed()->count();
    $delete_user_count--;

    $res = $this->actingAs($this->admin, 'admin')->putJson($this->url, ['user_id' => $user->id]);
    $res->assertOk();

    $this->assertEquals($user_count, User::count());
    $this->assertEquals($delete_user_count, User::onlyTrashed()->count());
  }

  // シフト申請を取得
  public function test_shift_request_get()
  {
    $this->url = $this->url.'/shift';

    ShiftRequest::factory()->has(ShiftRequestDate::factory()->count(3), 'shift_request_dates')->create();

    $res = $this->actingAs($this->admin, 'admin')->get($this->url);
    $res->assertOk();
    
    $json = $res->json();

    $index = array_key_last($json);
    $user_id = $json[$index]['user_id'];
    $user = User::find($user_id);

    $this->assertEquals($json[$index]['user']['name'], $user->name);
    $this->assertEquals(count($json), ShiftRequest::count());
  }

  // 特定のIDのシフト申請を取得
  public function test_shift_request_id_get()
  {
    ShiftRequest::factory()->has(ShiftRequestDate::factory()->count(3), 'shift_request_dates')->create();
    $shift_request = ShiftRequest::first();
    $this->url = $this->url."/shift/$shift_request->id";

    $res = $this->actingAs($this->admin, 'admin')->get($this->url);
    $res->assertOk();
  }

  public function test_shift_request_id_get_fail()
  {
    $this->url = $this->url."/shift/10000000000";

    $res = $this->actingAs($this->admin, 'admin')->get($this->url);
    $res->assertStatus(400);
  }

  // スケジュールの追加
  public function test_add_schedule()
  {
    $this->url = $this->url.'/schedule';
    $data = ShiftRequest::factory()->has(ShiftRequestDate::factory()->count(3), 'shift_request_dates')->create();

    $shift_request_count = ShiftRequest::count();
    $shift_request_count--;

    $schedule_count = Schedule::count();
    $schedule_count += 3;

    $res = $this->actingAs($this->admin, 'admin')->post($this->url, [
      'shift_request_id' => $data->id,
    ]);
    $res->assertOk();

    $this->assertEquals($shift_request_count, ShiftRequest::count());
    $this->assertEquals($schedule_count, Schedule::count());
  }

  // 欠勤申請取得
  public function test_absence_request_get()
  {
    $this->url = $this->url.'/absence';

    Schedule::factory()->has(AbsenceRequest::factory()->count(3), 'absence_requests')->create();

    $res = $this->actingAs($this->admin, 'admin')->get($this->url);
    $res->assertOk();

    $this->assertEquals(count($res->json()), AbsenceRequest::count());
  }

  // 欠勤申請を承諾
  public function test_absence_request_put()
  {
    $this->url = $this->url.'/absence';

    $schedule =  Schedule::factory()->has(AbsenceRequest::factory()->count(3), 'absence_requests')->create();
    $data = AbsenceRequest::where('schedule_id', $schedule->id)->first();

    $res = $this->actingAs($this->admin, 'admin')->putJson($this->url, ['absence_id' => $data->id]);
    $res->assertOk();
  }

  // 就業履歴の取得
  public function test_hitory_get()
  {
    $this->url = $this->url.'/history';

    History::factory(5)->create();

    $res = $this->actingAs($this->admin, 'admin')->get($this->url);
    $res->assertOk();

    $this->assertEquals(count($res->json()), History::count());
  }
}
