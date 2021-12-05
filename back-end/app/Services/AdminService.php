<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;

class AdminService
{
  public function auth($credentials)
  {
    if(Auth::guard('admin')->attempt($credentials)) {
      session()->regenerate();
      return response()->json(['message' => 'success', 'name' => Auth::guard('admin')->user()->name], 200);
    }
    
    return response()->json(['message' => 'fail'], 400);
  }
}