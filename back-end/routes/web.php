<?php

use App\Models\User;
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
    $user = User::find(1);
    return $user->getSchedules();
    return view('welcome');
});

Route::get('/test', function(Request $request) {
    $v = Validator::make($request->all(), [
        'name' => ['required']
    ]);
    if($v->fails()) {
        info('info', [$v]);
    }
    info($v->validated());
    return new JsonResponse(['message' => 'fails'], 400);
    return view('welcome');
});

Route::get('/fill', function(User $user) {
    $user = $user::first();
    $user->fill(['age' => 100]);
    $user->save();
    info('user', [$user]);
    return $user;
});