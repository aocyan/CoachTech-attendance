<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIntervalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('intervals', function (Blueprint $table) {
            $table -> id();
            $table -> unsignedBigInteger('attendance_id');
            $table -> timestamp('interval_in_at')->nullable();
            $table -> timestamp('interval_out_at')->nullable();
            $table -> timestamps();

            $table
                -> foreign('attendance_id')
                -> references('id')
                -> on('attendances')
                -> onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('intervals');
    }
}
