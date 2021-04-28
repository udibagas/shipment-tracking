<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code', 'name', 'phone', 'email',
        'address', 'active', 'contact_person',
        'fax', 'website', 'contact_person_email',
        'contact_person_phone', 'logo', 'director_name',
        'smtp_host', 'smtp_port', 'smtp_encryption', 'smtp_username', 'smtp_password',
    ];

    public function banks()
    {
        return $this->hasMany(CompanyBank::class);
    }
}
