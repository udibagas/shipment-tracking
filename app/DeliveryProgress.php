<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DeliveryProgress extends Model
{
    protected $fillable = ['delivery_id', 'status', 'note', 'user_id', 'image'];
}
