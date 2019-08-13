<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DomesticDeliveryItem extends Model
{
    protected $fillable = [
        'domestic_delivery_id', 'description',
        'coli', 'weight', 'item', 'reference',
        'remark'
    ];
}
