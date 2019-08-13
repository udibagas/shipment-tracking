<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DomesticDelivery extends Model
{
    protected $fillable = [
        'customer_id', 'agent_id', 'user_id', 'service_type_id',
        'status_note', 'last_geolocation_coord', 'last_geolocation_timestamp',
        'origin', 'destination', 'vehicle_number', 'delivery_status_id',
        'pick_up_date', 'delivery_date', 'delivered_date', 'spb_number',
        'resi_number', 'receiver', 'volume', 'quantity', 'dimension',
        'ship_name', 'driver_name', 'driver_phone', 'company_id',
        'delivery_type_id', 'etd', 'eta', 'tracking_number',
        'tracking_number', 'delivery_address', 'charge_to'
    ];
}
