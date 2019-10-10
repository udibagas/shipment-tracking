<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForwarderCostAndAdditionalCost extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('domestic_deliveries', function (Blueprint $table) {
            $table->integer('forwarder_cost')->default(0);
            $table->integer('additional_cost')->default(0);
            $table->string('additional_cost_description')->nullable();
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
            $table->dropColumn(['forwarder_cost', 'additional_cost', 'additional_cost_description']);
        });
    }
}
