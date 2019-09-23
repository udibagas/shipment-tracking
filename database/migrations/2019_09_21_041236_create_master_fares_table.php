<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMasterFaresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_fares', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('company_id');
            $table->bigInteger('customer_id');
            $table->string('destination');
            $table->bigInteger('fare');
            $table->string('lead_time');
            $table->integer('minimum');
            $table->bigInteger('user_id');
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
        Schema::dropIfExists('master_fares');
    }
}
