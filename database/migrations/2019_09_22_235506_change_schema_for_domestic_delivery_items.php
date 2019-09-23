<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeSchemaForDomesticDeliveryItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('domestic_delivery_items', function (Blueprint $table) {
            $table->integer('dimension_p')->default(0);
            $table->integer('dimension_l')->default(0);
            $table->integer('dimension_t')->default(0);
            $table->decimal('volume', 10, 4)->default(0);
            $table->integer('volume_weight')->default(0);
            $table->integer('invoice_weight')->default(0);
            $table->dropColumn(['item', 'reference', 'coli']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('domestic_delivery_items', function (Blueprint $table) {
            $table->dropColumn([
                'dimension_p', 'dimension_l', 'dimension_t',
                'volume', 'volume_weight', 'invoice_weight'
            ]);
            $table->integer('item')->nullable();
            $table->string('reference')->nullable();
            $table->integer('coli')->nullable();
        });
    }
}
