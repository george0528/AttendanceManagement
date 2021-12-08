<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\User;
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
    $this->assertDatabaseCount('users', $user_count);
  }
}
