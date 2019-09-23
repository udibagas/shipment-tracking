<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeSchemaDomesticDeliveriesAddServiceType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('domestic_deliveries', function (Blueprint $table) {
            $table->dropColumn(['service_type_id', 'dimension']);
            $table->string('service_type')->nullable();
            $table->bigInteger('vehicle_type_id')->nullable();
            $table->boolean('packing')->default(0);
            $table->bigInteger('delivery_cost')->default(0);
            $table->bigInteger('delivery_cost_ppn')->default(0);
            $table->bigInteger('packing_cost')->default(0);
            $table->bigInteger('packing_cost_ppn')->default(0);
            $table->bigInteger('total_cost')->default(0);
            $table->decimal('volume', 10, 4)->default(0)->change();
            $table->integer('volume_weight')->default(0);
            $table->integer('invoice_weight')->default(0);
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
            $table->string('dimension')->nullable();
            $table->bigInteger('service_type_id')->nullable();
            $table->dropColumn([
                'service_type', 'vehicle_type_id',
                'packing', 'delivery_cost', 'delivery_cost_ppn',
                'packing_cost', 'packing_cost_ppn', 'total_cost',
                'volume_weight', 'invoice_weight'
            ]);
        });
    }
}
