<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShiftRequestDatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shift_request_dates', function (Blueprint $table) {
            $table->id();
            $table->integer('request_id');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->foreign('request_id')->references('id')->on('shift_requests');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shift_request_dates');
    }
}
