<?php

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test', function(Request $request) {
    $v = Validator::make($request->all(), [
        'name' => ['required']
    ]);
    if($v->fails()) {
        Log::alert("失敗");
    }
    return new JsonResponse(['message' => 'fails'], 400);
    return view('welcome');
});