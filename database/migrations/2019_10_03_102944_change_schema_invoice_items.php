<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeSchemaInvoiceItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->renameColumn('domestic_delivery_invoice_id', 'invoice_id');
            $table->json('description');
            $table->dropColumn([
                'domestic_delivery_id',
                'delivery_date', 'delivered_date',
                'spb_number', 'origin', 'destination',
                'service_type'
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
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->renameColumn('invoice_id', 'domestic_delivery_invoice_id');
            $table->dropColumn('description');
            $table->bigInteger('domestic_delivery_id')->nullable();
            $table->date('delivery_date')->nullable();
            $table->date('delivered_date')->nullable();
            $table->string('spb_number')->nullable();
            $table->string('origin')->nullable();
            $table->string('destination')->nullable();
            $table->string('service_type')->nullable();
        });
    }
}
