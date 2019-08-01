<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeliveriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('customer_id');
            $table->integer('forwarder_id');
            $table->integer('user_id')->nullable(); // driver
            $table->string('number');
            $table->tinyInteger('status')->default(0);
            $table->string('status_note')->nullable();
            $table->string('last_geolocation_coord')->nullable();
            $table->dateTime('last_geolocation_timestamp')->nullable();
            $table->string('origin');
            $table->string('destination');
            $table->string('vehicle_number');
            $table->date('date');
            $table->date('due_date');
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
        Schema::dropIfExists('deliveries');
    }
}
