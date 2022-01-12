<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::create('options', function (Blueprint $table) {
            $table->id();
            // $table->bigInteger('user_id')->unsigned();
            $table->boolean('create_payslip')->default(true);
            $table->integer('create_payslip_day')->default('25');
            $table->softDeletes();
            $table->timestamps();

            // 外部キー
            // $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('options');
    }
}
