<?php

namespace App\Console\Commands;

use App\Models\History;
use App\Models\Option;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreatePayslip extends Command
{
  // クラス変数
  // 給与明細を作る日は給料締め日の何日後か
  private $payslip_create_salary_closing_any_ago_day = 2;

  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'payslip:create';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = '給与明細の作成';

  /**
   * Create a new command instance.
   *
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * Execute the console command.
   *
   * @return int
   */
  public function handle()
  {
    $option = Option::firstOrcreate();
    $now = Carbon::now();
    $users = User::with(['salary'])->get();
    // 締め日の二日後に作成
    $payslip_create_day = $option->salary_closing_day + $this->payslip_create_salary_closing_any_ago_day;
    $year = $now->year;

    // 月をまたいでいる可能性があるため
    $now->subDays($this->payslip_create_salary_closing_any_ago_day);
    $month = $now->month;
    $now->addDays($this->payslip_create_salary_closing_any_ago_day);
    
    $day = $option->salary_closing_day;
    $salary_end_date = Carbon::createMidnightDate($year, $month, $day);
    
    if(!$option->create_payslip) {
      throw new Exception("給与設定の自動作成が許可されていません");
    }
    
    if($payslip_create_day != $now->day) {
      throw new Exception($now.":給与明細を作成する日ではありません");
    }
    DB::beginTransaction();
    try {
      $create_datas = [];
      foreach ($users as $user) {
        if($user->salary == null) {
          throw new Exception($user->name."さんの給料が設定されていません");
        }
        // データを初期化
        $payslip_create_data = [];
        
        $payslip_create_data['user_id'] = $user->id;
        
        // 25日締め日なら$salary_end_dateは26日0時0分にする
        $salary_first_date = clone $salary_end_date;
        $salary_first_date->subMonth();
        $salary_end_date->addDay();
        // 就業履歴確認
        $user->histories = History::where('user_id', $user->id)->where('start_time', '>=', $salary_first_date)->where('start_time', '<', $salary_end_date)->get();
        $payslip_create_data['attendance_days'] = count($user->histories);
        $time_data = History::getTimes($user->histories);
        $payslip_create_data['sum_time'] = $time_data['sum_times'];
        $payslip_create_data['midnight_time'] = $time_data['midnight_times'];
        $salary_data = $user->salary;
        $payslip_create_data['salary_type'] = $salary_data['salary_type'];
        $payslip_create_data['hour_salary'] = $salary_data['hour_salary'];
        $payslip_create_data['month_salary'] = $salary_data['month_salary'];
        $now = Carbon::now();
        $payslip_create_data['created_at'] = $now;
        $payslip_create_data['updated_at'] = $now;
        
        // 作成予定データを配列に追加
        $create_datas[] = $payslip_create_data;
      }
      // 複数データをインサート
      DB::table('payslips')->insert($create_datas);
      DB::commit();
    } catch (\Exception $e) {
      DB::rollBack();
      logger()->error($e);
      return Command::SUCCESS;
    }
    return Command::SUCCESS;
  }
}
