<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'company_id', 'customer_id', 'user_id',
        'date', 'number', 'total', 'status'
    ];

    protected $with = ['items'];

    public function items() {
        return $this->hasMany(DomesticDeliveryInvoiceItem::class);
    }

    public function customer() {
        return $this->belongsTo(Customer::class);
    }

    public function company() {
        return $this->belongsTo(Company::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
