<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    protected $fillable = [
        'code', 'name', 'phone', 'email',
        'address', 'active', 'contact_person',
        'fax', 'website', 'contact_person_email',
        'contact_person_phone', 'company_id'
    ];
}
