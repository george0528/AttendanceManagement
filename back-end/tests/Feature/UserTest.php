<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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
    $this->url = '/api/user/login';
  }
  /**
   * A basic feature test example.
   *
   * @return void
   */
  public function test_login_success()
  {
    $user = new User;
    $data = [
      'name' => $this->faker->name(),
      'login_id' => $user->loginIdGenerate(),
      'age' => 20,
      'password' => 'password',
    ];

    $this->assertGuest();

    $this->url = '/api/user/login';
    $response = $this->post($this->url, [
      'login_id' => $this->user->login_id,
      'password' => 'password',
    ]);
    $response->assertStatus(200);
    $this->assertAuthenticated();
  }
  public function test_login_failed()
  {
    $this->assertGuest();
    
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
}
