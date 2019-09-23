<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('domestic_delivery_invoice_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('domestic_delivery_invoice_id');
            $table->bigInteger('domestic_delivery_id');
            $table->date('delivery_date');
            $table->date('delivered_date')->nullable();
            $table->string('spb_number');
            $table->string('origin');
            $table->string('destination');
            $table->string('service_type'); // bisa charter, reguler, packing
            $table->decimal('quantity', 10, 3);
            $table->string('unit');
            $table->integer('fare');
            $table->bigInteger('price');
            $table->bigInteger('tax');
            $table->bigInteger('total');
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
        Schema::dropIfExists('domestic_delivery_invoice_items');
    }
}
