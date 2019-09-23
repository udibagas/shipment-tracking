<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DomesticDeliveryItem extends Model
{
    protected $fillable = [
        'domestic_delivery_id', 'description', 'weight',
        'remark', 'dimension_p', 'dimension_l', 'dimension_t',
        'volume', 'volume_weight', 'invoice_weight'
    ];
}
