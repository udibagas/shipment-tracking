<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DomesticDeliveryInvoiceItem extends Model
{
    protected $fillable = [
        'domestic_delivery_invoice_id', 'domestic_delivery_id', 'delivery_date',
        'delivered_date', 'spb_number', 'quantity', 'unit', 'fare', 'price',
        'tax', 'total'
    ];
}
