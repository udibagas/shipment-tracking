<?php

use App\Models\City;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cities', function (Blueprint $table) {
            City::insert([
                ['name' => 'ADARO'],
                ['name' => 'SAMPIT'],
                ['name' => 'SANGATTA'],
                ['name' => 'TANJUNG ENIM'],
                ['name' => 'TANJUNG PANDAN'],
                ['name' => 'CIBUTUNG'],
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cities', function (Blueprint $table) {
            //
        });
    }
}
