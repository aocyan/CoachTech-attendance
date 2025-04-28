<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table -> id();
            $table -> unsignedBigInteger('user_id');
            $table -> string('date')->nullable();;
            $table -> timestamp('clock_in_at')->nullable();
            $table -> timestamp('clock_out_at')->nullable();
            $table -> timestamps();

            $table
                -> foreign('user_id')
                -> references('id')
                -> on('users')
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
        Schema::dropIfExists('attendances');
    }
}
