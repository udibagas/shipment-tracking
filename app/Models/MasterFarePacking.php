<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterFarePacking extends Model
{
    use SoftDeletes;

    protected $fillable = ['company_id', 'customer_id', 'user_id', 'fare', 'ppn'];

    protected $casts = [
        'ppn' => 'boolean'
    ];
}
