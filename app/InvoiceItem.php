<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    protected $fillable = [
        'description','quantity', 'unit',
        'fare', 'price', 'tax', 'total'
    ];

    protected $casts = ['description' => 'array'];
}
