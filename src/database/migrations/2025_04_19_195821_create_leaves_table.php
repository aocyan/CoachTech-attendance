<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeavesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leaves', function (Blueprint $table) {
            $table -> id();
            $table -> unsignedBigInteger('correction_id');
            $table -> timestamp('interval_in_at')->nullable();
            $table -> timestamp('interval_out_at')->nullable();
            $table -> timestamps();

            $table
                -> foreign('correction_id')
                -> references('id')
                -> on('corrections')
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
        Schema::dropIfExists('leaves');
    }
}
