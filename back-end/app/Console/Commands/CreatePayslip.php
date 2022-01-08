<?php

namespace App\Console\Commands;

use App\Models\History;
use App\Models\Payslip;
use App\Models\User;
use Illuminate\Console\Command;

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
  public function handle(Payslip $payslip)
  {
    $users = User::with('histories')->get();
    foreach ($users as $index => $user) {
      $data['user_id'] = $user->id;
      $data['attendance_days'] = count($user->histories);
      $times = History::getTimes($user->histoies);
      $data['sum_time'] = $times['sum_times'];
      $data['midnight_time'] = $times['midnight_times'];
      $payslip->create($data);
    }
    return Command::SUCCESS;
  }
}
