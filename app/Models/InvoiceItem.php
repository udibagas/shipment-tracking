<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'description', 'quantity', 'unit',
        'fare', 'price', 'tax', 'total'
    ];

    protected $casts = ['description' => 'array'];
}
