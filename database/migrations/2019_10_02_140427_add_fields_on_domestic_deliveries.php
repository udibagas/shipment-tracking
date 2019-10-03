<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsOnDomesticDeliveries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('domestic_deliveries', function (Blueprint $table) {
            $table->decimal('packing_volume', 11, 3)->default(0);
            $table->integer('delivery_rate')->default(0);
            $table->integer('packing_rate')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('domestic_deliveries', function (Blueprint $table) {
            $table->dropColumn(['packing_volume', 'delivery_rate', 'packing_rate']);
        });
    }
}
