<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;

class UserService
{
  public function login($credentails)
  {
    if(Auth::attempt($credentails)) {
      session()->regenerate();
      return response()->json(['message' => 'success', 'name' => Auth::user()->name()], 200);

      return response()->json(['message' => 'fail'], 400);
    }
  }
}
