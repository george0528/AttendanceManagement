<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $token = $request->user()->createToken('token-name');
            return response()->json(['api_token' => $token->plainTextToken], 200);
        }

        return response()->json(['api_token' => null], 401);
    }
    public function session(Request $request)
    {
        $credentials = $request->validate([
            'user_id' => ['required'],
            'password' => ['required']
        ]);
        if(Auth::attempt($credentials)) {
            return response()->json(['message' => 'success', 'name' => Auth::user()->name], 200);
        }
        return response()->json(['message' => 'failed'], 401);
    }
    public function login(Request $request)
    {
        if(Auth::check()) {
            $request->session()->regenerate();
            return response()->json(['message' => 'logined'], 200);
        }
        return response()->json(['message' => 'notlogined'], 400);
    }
}
