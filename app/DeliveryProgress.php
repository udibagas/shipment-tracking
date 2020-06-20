<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DeliveryProgress extends Model
{
    protected $fillable = ['delivery_id', 'status', 'note', 'user_id', 'image'];

    protected $appends = ['status_name'];

    public function getStatusNameAttribute()
    {
        $status = [
            'TERDAFTAR',
            'SIAP KIRIM',
            'DALAM PENGIRIMAN',
            'TERKIRIM',
            'STT DITERIMA'
        ];

        return $status[$this->status];
    }
}
