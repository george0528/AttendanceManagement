<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayslipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payslips', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->enum('salary_type', ['hour', 'month']);
            $table->integer('hour_salary');
            $table->integer('month_salary')->nullable();
            $table->integer('sum_time');
            $table->integer('midnight_time');
            $table->integer('attendance_days');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payslips');
    }
}
