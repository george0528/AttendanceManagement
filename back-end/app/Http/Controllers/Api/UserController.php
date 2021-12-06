<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
  private $service;
  public function __construct(UserService $service) {
    $this->service = $service;
  }

  public function login(Request $request)
  {
    $val = Validator::make($request->all(), [
      'login_id' => ['required'],
      'password' => ['required']
    ]);

    if($val->fails()) {
      return new JsonResponse('fail val', 400);
    }
    
    return $this->service->login($val->validated());
  }
}
