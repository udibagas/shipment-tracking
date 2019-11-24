<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MasterFareCharter extends Model
{
    protected $fillable = [
        'company_id', 'customer_id', 'destination', 'ppn',
        'fare', 'lead_time', 'user_id', 'vehicle_type_id'
    ];

    protected $casts = [
        'ppn' => 'boolean'
    ];
}
