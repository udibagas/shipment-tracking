<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code', 'name', 'phone', 'email',
        'address', 'active', 'contact_person',
        'fax', 'website', 'contact_person_email',
        'contact_person_phone', 'company_id'
    ];
}
