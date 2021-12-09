<?php

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;
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
    $time = new Carbon('now');
    return $time;
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

Route::get('/val', function(Request $request) {
    $data =[
        'users' => [
            0 => [
                'first_name' => 'aaaa',
                'last_name' => 'bbbbb', 
            ],
            1 => [
                // 'first_name' => 'cccc',
                'last_name' => 'ddddddddddd',
            ]
        ]
    ];

    $val = Validator::make($data, [
        'users.*.first_name' => 'required_with:users.*.last_name',
    ]);

    if($val->fails()) {
        return response()->json($val->errors(), 400);
    }
    return response()->json($data, 200);
});

Route::get('/ary', function(Request $request) {
    $data = [
        'users' => [
            1 => [
                'name' => 'taro',
                'admin' => true,
            ],
            2 => [
                'name' => 'jiro',
                'admin' => true,
            ],
        ],
    ];
    $val = Validator::make($data, [
        'users' => ['required', 'array'],
        'users.*.name' => ['required'],
    ]);

    return $val->validate();
});