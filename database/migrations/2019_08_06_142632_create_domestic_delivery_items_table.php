<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDomesticDeliveryItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('domestic_delivery_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('domestic_delivery_id');
            $table->string('description');
            $table->string('coli');
            $table->integer('weight');
            $table->integer('item');
            $table->string('reference')->nullable();
            $table->string('remark')->nullable();
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
        Schema::dropIfExists('domestic_delivery_items');
    }
}
