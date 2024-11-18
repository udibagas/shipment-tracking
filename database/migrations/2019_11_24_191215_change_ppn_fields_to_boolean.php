<?php

use App\Models\DomesticDelivery;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangePpnFieldsToBoolean extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // DomesticDelivery::where('delivery_cost_ppn', '>', 0)->update(['delivery_cost_ppn' => 1]);
        // DomesticDelivery::where('packing_cost_ppn', '>', 0)->update(['packing_cost_ppn' => 1]);

        Schema::table('domestic_deliveries', function (Blueprint $table) {
            $table->boolean('delivery_cost_ppn')->default(0)->change();
            $table->boolean('packing_cost_ppn')->default(0)->change();
            $table->boolean('forwarder_cost_ppn')->default(0);
            $table->boolean('additional_cost_ppn')->default(0);
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
            $table->dropColumn(['forwarder_cost_ppn', 'additional_cost_ppn']);
        });
    }
}
