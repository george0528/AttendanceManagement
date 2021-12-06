<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AdminService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
	private $service;

	public function __construct(AdminService $service) {
		$this->service = $service;
	}
	public function login(Request $request)
	{
		$val = Validator::make($request->all(), [
			'email' => ['required', 'email'],
			'password' => ['required'],
		]);
		if($val->fails()) {
			return response()->json(['message' => 'val fail'], 400);
		}
		return $this->service->login($val->validated());
	}
	public function logout()
	{
		return $this->logout();
	}
	public function getUser()
	{
		return $this->service->getUser();
	}
	public function addUser(Request $request)
	{
		$val = Validate::make($request->all(),[
			'name' => ['required', 'string'],
			'age' => ['requred', 'integer'],
			'password' => ['between:6, 12'],
		]);

		if($val->fails()) {
			return response()->json(['message' => 'fail'], 400);
		}

		return $this->service->addUser($val->validated());
	}
	public function deleteUser(Request $request)
	{
		$val = Validator::make($request->all(), [
			'user_id' => ['required', 'integer', 'exists:users,id'],
		]);

		if($val->fails()) {
			return response()->json(['message' => 'fail'], 400);
		}

		return $this->service->deleteUser($val->safe()->only('user_id')['user_id']);
	}
	public function updateUser(Request $request)
	{
		$val = Validator::make($request->all(), [
			'user_id' => ['required', 'integer', 'exists:users,id'],
			'login_id' => ['nullable', 'string', 'min:6', 'unique:users,login_id'],
			'password' => ['nullable', 'string', 'between:6, 12'],
			'name' => ['nullable', 'string'],
			'age' => ['nullable', 'integer'],
		]);


		if($val->fails()) {
			return response()->json(['message' => 'fail'], 400);
		}

		// 空文字,null,0削除
		$val = array_filter($val->validated());
		
		return $this->service->updateUser($val);
	}
}
