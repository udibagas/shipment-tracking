<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DomesticDelivery extends Model
{

    const STATUS_REGISTERED = 0;

    const STATUS_READY_FOR_DELIVERY = 1;

    const STATUS_ON_DELIVERY = 2;

    const STATUS_DELIVERED = 3;

    const STATUS_STT_RECEIVED = 4;

    const STATUS_INVOICE_SENT = 5;

    const STATUS_INVOICE_PAID = 6;

    protected $fillable = [
        'customer_id', 'agent_id', 'user_id', 'service_type',
        'status_note', 'last_geolocation_coord', 'last_geolocation_timestamp',
        'origin', 'destination', 'vehicle_number', 'delivery_status_id',
        'pick_up_date', 'delivery_date', 'delivered_date', 'spb_number',
        'resi_number', 'receiver', 'volume', 'quantity',
        'ship_name', 'driver_name', 'driver_phone', 'company_id',
        'delivery_type_id', 'etd', 'eta', 'tracking_number',
        'tracking_number', 'delivery_address', 'charge_to', 'weight',
        'service_type', 'vehicle_type_id',
        'packing', 'delivery_cost', 'delivery_cost_ppn',
        'packing_cost', 'packing_cost_ppn', 'total_cost',
        'volume_weight', 'invoice_weight', 'invoice_status',
        'packing_volume', 'delivery_rate', 'packing_rate', 'minimum_weight',
        'created_at', 'updated_at', 'forwarder_cost', 'additional_cost',
        'additional_cost_description', 'forwarder_cost_ppn', 'additional_cost_ppn',
        'stt_received_date'
    ];

    protected $with = ['items', 'vehicleType'];

    protected $appends = ['statusName', 'isDelay', 'isOntime'];

    protected $casts = [
        'delivery_cost_ppn' => 'boolean',
        'packing_cost_ppn' => 'boolean',
        'forwarder_cost_ppn' => 'boolean',
        'additional_cost_ppn' => 'boolean',
    ];

    public function getStatusNameAttribute()
    {
        $statuses = [
            'Terdaftar', 'Siap Kirim',
            'Dalam Pengiriman', 'Terkirim', 'STT Diterima',
            'Invoice Sent', 'Invoice Paid'
        ];

        return $statuses[$this->delivery_status_id];
    }

    public function getIsDelayAttribute()
    {
        return strtotime($this->delivered_date) > strtotime($this->eta);
    }

    public function getIsOntimeAttribute()
    {
        return strtotime($this->delivered_date) <= strtotime($this->eta);
    }

    public function items() {
        return $this->hasMany(DomesticDeliveryItem::class);
    }

    public function customer() {
        return $this->belongsTo(Customer::class);
    }

    public function vehicleType() {
        return $this->belongsTo(VehicleType::class);
    }
}
