<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AdminService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
	private $service;

	public function __construct(AdminService $service) {
		$this->service = $service;
	}

	// ログイン
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

	// ログアウト
	public function logout()
	{
		return $this->service->logout();
	}

	// userを取得
	public function getUser()
	{
		return $this->service->getUser();
	}

	// userを追加
	public function addUser(Request $request)
	{
		$val = Validator::make($request->all(),[
			'name' => ['required', 'string'],
			'age' => ['required', 'integer'],
			'password' => ['between:6, 12'],
		]);

		if($val->fails()) {
			return response()->json(['message' => 'fail'], 400);
		}

		return $this->service->addUser($val->validated());
	}

	// user情報の変更
	public function updateUser(Request $request)
	{
		$val = Validator::make($request->all(), [
			'user_id' => ['required', 'integer', Rule::exists('users', 'id')->whereNull('deleted_at')],
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

	// userを論理削除
	public function deleteUser(Request $request)
	{
		$val = Validator::make($request->all(), [
			'user_id' => ['required', 'integer', Rule::exists('users', 'id')->whereNull('deleted_at')],
		]);

		if($val->fails()) {
			return response()->json(['message' => 'fail'], 400);
		}

		return $this->service->deleteUser($val->safe()->only('user_id')['user_id']);
	}


	// 論理削除のuserを取得
	public function getDeleteUser()
	{
		return $this->service->getDeleteUser();
	}

	// 論理削除のuserを復元
	public function restoreDeleteUser(Request $request)
	{
		$val = Validator::make($request->all(), [
			'user_id' => ['required', Rule::exists('users', 'id')->whereNotNull('deleted_at')],
		]);

		if($val->fails()) {
			return response()->json('fail', 400);
		}

		return $this->service->restoreDeleteUser($val->validated()['user_id']);
	}

	// 論理削除のuserを物理削除する
	public function forceDeleteUser(Request $request)
	{
		$val = Validator::make($request->all(), [
			'user_id' => ['required', Rule::exists('users', 'id')->whereNotNull('deleted_at')],
		]);

		if($val->fails()) {
			return response()->json('fail', 400);
		}

		return $this->service->forceDeleteUser($val->validated()['user_id']);
	}

	// シフトリクエストを取得
	public function getShift()
	{
		return $this->service->getShift();
	}


	// 欠勤申請取得
	public function getAbsence()
	{
		return $this->service->getAbsence();
	}

	// 就業履歴の確認
	public function getHistory()
	{
		return $this->service->getHistory();
	}
}
