<?php

namespace App\Console\Commands;

use App\Models\History;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreatePayslip extends Command
{
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
    $users = User::with(['histories', 'salary'])->get();
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
