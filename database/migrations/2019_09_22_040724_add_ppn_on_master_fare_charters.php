<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPpnOnMasterFareCharters extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('master_fare_charters', function (Blueprint $table) {
            $table->boolean('ppn')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('master_fare_charters', function (Blueprint $table) {
            $table->dropColumn(['ppn']);
        });
    }
}
