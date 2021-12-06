<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminService
{
  public function login($credentials)
  {
    if(Auth::guard('admin')->attempt($credentials)) {
      session()->regenerate();
      return response()->json(['message' => 'success', 'name' => Auth::guard('admin')->user()->name], 200);
    }

    return response()->json(['message' => 'fail'], 400);
  }
  public function logout()
  {
    if(Auth::guard('admin')->check()) {
      Auth::guard('admin')->logout();
      session()->invalidate();
      session()->regenerateToken();
      return response()->json(['message' => 'success'], 200);
    }

    return response()->json(['message' => 'fail'], 400); 
  }
  public function getUser()
  {
    $users = User::all();
    return response()->json($users, 200);
  }
  public function addUser($data)
  {
    try {
      $user = new User;
      $login_id = $user->loginIdGenerate();
      $data['login_id'] = $login_id;
      $data['password'] = Hash::make($data['password']);
      $data['remember_token'] = Str::random(10);
      User::create($data);
      return response()->json($data, 200);
    } catch (\Exception $e) {
      return response()->json($e, 400);
    }
  }
  public function deleteUser($user_id)
  {
    try {
      User::destroy($user_id);
      return response()->json(['message' => 'success'], 200);
    } catch (\Exception $e) {
     return response()->json($e, 400);
    }
  }
  public function updateUser($data)
  {
    try {
      $user = User::find($data['user_id']);
      unset($data['user_id']);
      $user->fill($data)->save();
      return response()->json($user, 200);
    } catch (\Exception $e) {
      return response()->json($e, 400);
    }
  }
}