<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMasterFareChartersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_fare_charters', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('company_id');
            $table->bigInteger('customer_id');
            $table->bigInteger('vehicle_type_id');
            $table->string('destination');
            $table->string('lead_time');
            $table->bigInteger('fare');
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
        Schema::dropIfExists('master_fare_charters');
    }
}
