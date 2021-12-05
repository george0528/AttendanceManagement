<?php

use App\Http\Controllers\Api\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/admin/user', function(Request $request) {
    if(Auth::guard('admin')->check()) {
        return Auth::guard('admin')->user();
    }
    return response()->json(['message' => 'fail'], 400);
})->middleware('auth:admin');
Route::get('/test', function () {
    setcookie('name', 'jota');
    return response()->json(['message' => 'success']);
});
Route::get('/session/delete', function(Request $request) {
    $request->session()->flush();
});

// 真面目に
Route::post('/user/login', [UserController::class, 'login']);
Route::post('/admin/login', [AdminController::class, 'login']);