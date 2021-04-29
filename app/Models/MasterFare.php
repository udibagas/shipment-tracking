<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterFare extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id', 'customer_id', 'destination', 'vehicle_type_id',
        'fare', 'lead_time', 'minimum', 'user_id', 'ppn'
    ];

    protected $casts = [
        'ppn' => 'boolean'
    ];
}
