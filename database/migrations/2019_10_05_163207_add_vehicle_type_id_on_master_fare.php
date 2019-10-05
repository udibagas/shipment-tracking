<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVehicleTypeIdOnMasterFare extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('master_fares', function (Blueprint $table) {
            $table->bigInteger('vehicle_type_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('master_fares', function (Blueprint $table) {
            $table->dropColumn(['vehicle_type_id']);
        });
    }
}
