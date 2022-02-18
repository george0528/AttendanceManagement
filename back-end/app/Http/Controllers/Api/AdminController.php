<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ShiftRequest;
use App\Rules\AnyOneMatch;
use Illuminate\Http\Request;
use App\Services\AdminService;
use App\Services\AuthService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use SebastianBergmann\Environment\Console;

class AdminController extends Controller
{
	private $service;

	public function __construct(AdminService $service, AuthService $auth) {
		$this->service = $service;
		$this->auth = $auth;
	}

	// 運営者情報を取得
	public function getAdmin()
	{
		return $this->service->getAdmin();
	}

	// ログイン
	public function login(Request $request)
	{
		$val = Validator::make($request->all(), [
			'email' => ['required', 'email'],
			'password' => ['required'],
		]);

		if($val->fails()) {
			return response()->json('ログインに失敗しました', 400);
		}

		return $this->service->login($val->validated(), $request->ip());
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
			'password' => ['required', 'between:6, 12'],
		]);

		if($val->fails()) {
			return response()->json('ユーザーの追加に失敗しました', 400);
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
			return response()->json('ユーザー情報の変更に失敗しました', 400);
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
			return response()->json('そのユーザーは存在しません', 400);
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
			return response()->json('そのユーザーを復元できませんでした', 400);
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
			return response()->json('ユーザーを完全に削除することが出来ませんでした', 400);
		}

		return $this->service->forceDeleteUser($val->validated()['user_id']);
	}

	// userの給料を取得
	public function getSalary()
	{
		return $this->service->getSalary();
	}

	// userの給料設定を追加
	public function addSalary(Request $request)
	{
		$val = Validator::make($request->all(), [
			'user_id' => ['required', Rule::exists('users', 'id')->whereNull('deleted_at')],
			'salary_type' => ['required', new AnyOneMatch(['hour', 'month'])],
			'hour_salary' => ['required', 'numeric'],
			'month_salary' => ['numeric', 'nullable'],
		]);

		if($val->fails()) {
			return response()->json('給与の設定に失敗しました1', 400);
		}

		if($request->get('salary_type') == 'month_salary') {
			$month_salary = $request->get('month_salary');
			if (empty($month_salary)) {
				return response()->json('給与の設定に失敗しました2', 400);
			}
		}

		return $this->service->addSalary($val->validated());
	}

	// シフトリクエストを取得
	public function getShift()
	{
		return $this->service->getShift();
	}

	// 特定のIDのシフトリクエストを取得
	public function getShiftId($request_id)
	{
		return $this->service->getShiftId($request_id);
	}

	// スケジュールを取得
	public function getSchedule()
	{
		return $this->service->getSchedule();	
	}

	// スケジュールを追加
	public function addSchedule(Request $request)
	{
		$val = Validator::make($request->all(), [
			'shift_request_id' => ['required', Rule::exists('shift_requests', 'id')->whereNull('deleted_at')],
		]);

		if($val->fails()) {
			return response()->json('シフト申請をスケジュールに追加することが出来ませんでした', 400);
		}

		return $this->service->addSchedule($val->validated()['shift_request_id']);
	}

	// 欠勤申請取得
	public function getAbsence()
	{
		return $this->service->getAbsence();
	}

	// 欠勤申請を承諾
	public function putAbsence(Request $request)
	{
		$val = Validator::make($request->all(), [
			'absence_id' => ['required', 'integer', Rule::exists('absence_requests', 'id')->whereNull('deleted_at')],
		]);

		if($val->fails()) {
			return response()->json('欠勤処理を承諾出来ませんでした', 400);
		}

		return $this->service->putAbsence($val->validated()['absence_id']);
	}

	// 就業履歴の確認
	public function getHistory()
	{
		return $this->service->getHistory();
	}

	// 設定の確認
	public function getOption()
	{
		return $this->service->getOption();
	}

	// 設定を編集
	public function putOption(Request $request)
	{
		info($request->all());
		$val = Validator::make($request->all(), [
			'create_payslip' => ['required', 'boolean'],
			'salary_closing_day' => ['required', 'integer', 'between:1,28'],
		]);

		if ($val->fails()) {
			return response()->json('設定の編集に失敗しました', 400);
		}
		info($val->validated());

		return $this->service->putOption($val->validated());
	}
}
