<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
  public function __construct(UserService $service) {
    $this->service = $service;
  }

  public function login(Request $request)
  {
    $credentails = $request->validate([
      'login_id' => ['required'],
      'password' => ['required']
    ]);
    
    return $this->service->login($credentails);
  }
}
