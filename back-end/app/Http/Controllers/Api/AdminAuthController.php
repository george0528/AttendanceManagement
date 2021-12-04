<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
        if(Auth::guard('admin')->attempt($credentials)) {
            return response()->json(['message' => 'success', 'name' => Auth::guard('admin')->user()->name], 200);
        }
        return response()->json(['message' => 'fail'], 400);
    }
}
