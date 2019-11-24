<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class ReplaceHariToEmptyOnMasterTarif extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::update('UPDATE master_fares SET lead_time = REPLACE(lead_time, " hari", "")');
        DB::update('UPDATE master_fares SET lead_time = REPLACE(lead_time, " Hari", "")');
        DB::update('UPDATE master_fares SET lead_time = REPLACE(lead_time, " HARI", "")');
        DB::update('UPDATE master_fares SET lead_time = REPLACE(lead_time, "hari", "")');
        DB::update('UPDATE master_fares SET lead_time = REPLACE(lead_time, "Hari", "")');
        DB::update('UPDATE master_fares SET lead_time = REPLACE(lead_time, "HARI", "")');
        DB::update('UPDATE master_fare_charters SET lead_time = REPLACE(lead_time, " hari", "")');
        DB::update('UPDATE master_fare_charters SET lead_time = REPLACE(lead_time, " Hari", "")');
        DB::update('UPDATE master_fare_charters SET lead_time = REPLACE(lead_time, " HARI", "")');
        DB::update('UPDATE master_fare_charters SET lead_time = REPLACE(lead_time, "hari", "")');
        DB::update('UPDATE master_fare_charters SET lead_time = REPLACE(lead_time, "Hari", "")');
        DB::update('UPDATE master_fare_charters SET lead_time = REPLACE(lead_time, "HARI", "")');
        DB::update('UPDATE master_fares SET lead_time = SUBSTRING(lead_time, -1, 1)');
        DB::update('UPDATE master_fare_charters SET lead_time = SUBSTRING(lead_time, -1, 1)');

        Schema::table('master_fares', function (Blueprint $table) {
            $table->smallInteger('lead_time')->default(0)->change();
        });

        Schema::table('master_fare_charters', function (Blueprint $table) {
            $table->smallInteger('lead_time')->default(0)->change();
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
            $table->string('lead_time')->nullable()->change();
        });

        Schema::table('master_fare_charters', function (Blueprint $table) {
            $table->string('lead_time')->nullable()->change();
        });
    }
}
