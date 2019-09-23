<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MasterFare extends Model
{
    protected $fillable = [
        'company_id', 'customer_id', 'destination',
        'fare', 'lead_time', 'minimum', 'user_id', 'ppn'
    ];
}
