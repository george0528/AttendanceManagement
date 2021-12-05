<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AdminService;

class AdminController extends Controller
{
	private $service;

	public function __construct(AdminService $service) {
		$this->service = $service;
	}
	public function login(Request $request)
	{
		$credentials = $request->validate([
			'email' => ['required', 'email'],
			'password' => ['required'],
		]);

		return $this->service->auth($credentials);
	}
}
