<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MasterFarePacking extends Model
{
    protected $fillable = ['company_id', 'customer_id', 'user_id', 'fare', 'ppn'];

    protected $casts = [
        'ppn' => 'boolean'
    ];
}
