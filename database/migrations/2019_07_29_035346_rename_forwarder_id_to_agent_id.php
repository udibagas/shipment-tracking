<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameForwarderIdToAgentId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('domestic_deliveries', function (Blueprint $table) {
            $table->renameColumn('forwarder_id', 'agent_id');
            $table->bigInteger('customer_id')->change();
        });

        Schema::table('domestic_deliveries', function(Blueprint $table) {
            $table->bigInteger('agent_id')->change();
            $table->bigInteger('user_id')->change();
            $table->dropColumn(['status', 'date', 'due_date', 'number']);
            $table->bigInteger('delivery_status_id');
            $table->bigInteger('service_type_id');
            $table->date('pick_up_date')->nullable();
            $table->date('delivery_date')->nullable();
            $table->date('delivered_date')->nullable();
            $table->string('spb_number');
            $table->string('resi_number');
            $table->string('receiver')->nullable();
            $table->integer('volume')->nullable();
            $table->integer('quantity')->nullable();
            $table->string('dimension')->nullable();
            $table->string('ship_name')->nullable();
            $table->string('driver_name')->nullable();
            $table->string('driver_phone')->nullable();
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
            $table->renameColumn('agent_id', 'forwarder_id');
            $table->date('date')->nullable();
            $table->date('due_date')->nullable();
            $table->tinyInteger('status');
            $table->string('number');
            $table->dropColumn([
                'delivery_status_id', 'service_type_id',
                'pick_up_date', 'delivery_date', 'delivered_date',
                'spb_number', 'resi_number', 'receiver',
                'volume', 'quantity', 'dimension', 'ship_name',
                'driver_name', 'driver_phone'
            ]);
        });
    }
}
