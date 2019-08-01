<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DeliveryStatus extends Model
{
    protected $fillable = ['code', 'name', 'description'];
}
