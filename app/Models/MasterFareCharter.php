<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterFareCharter extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id', 'customer_id', 'destination', 'ppn',
        'fare', 'lead_time', 'user_id', 'vehicle_type_id'
    ];

    protected $casts = [
        'ppn' => 'boolean'
    ];
}
