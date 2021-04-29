<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeliveryProgress extends Model
{
    use SoftDeletes;

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

        if ($this->status === null) {
            return 'DRAFT';
        }

        return $status[$this->status];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
