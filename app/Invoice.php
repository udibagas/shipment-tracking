<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'company_id', 'customer_id', 'user_id',
        'date', 'number', 'total', 'status',
        'type', 'total_said', 'service_type',
        'faktur'
    ];

    protected $with = ['items'];

    public function items() {
        return $this->hasMany(InvoiceItem::class);
    }

    public function groupedItems()
    {
        $items = [];

        foreach ($this->items as $item) {
            $items[$item->description['spb_number']][] = $item;
        }

        return $items;
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
