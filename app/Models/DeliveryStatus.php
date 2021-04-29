<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeliveryStatus extends Model
{
    use SoftDeletes;

    protected $fillable = ['code', 'name', 'description'];
}
