<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AdminService;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
	private $service;

	public function __construct(AdminService $service) {
		$this->service = $service;
	}
	public function login(Request $request)
	{
		Log::alert("login controller");
		$credentials = $request->validate([
			'email' => ['required', 'email'],
			'password' => ['required'],
		]);
		Log::alert("message");
		return $this->service->login($credentials);
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
		$user_data = $request->validate([
			'name' => ['required', 'string'],
			'age' => ['requred', 'integer'],
			'password' => ['between:6, 12'],
		]);

		return $this->service->addUser($user_data);
	}
	public function deleteUser(Request $request)
	{
		$user_id = $request->validate([
			'user_id' => ['required', 'integer', 'exists:users'],
		]);

		return $this->service->deleteUser($user_id);
	}
}
