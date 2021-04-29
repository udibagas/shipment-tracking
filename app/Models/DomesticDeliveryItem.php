<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DomesticDeliveryItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'domestic_delivery_id', 'description', 'weight',
        'remark', 'dimension_p', 'dimension_l', 'dimension_t',
        'volume', 'volume_weight', 'invoice_weight', 'packing',
        'created_at', 'updated_at'
    ];

    protected $casts = [
        'packing' => 'boolean'
    ];
}
