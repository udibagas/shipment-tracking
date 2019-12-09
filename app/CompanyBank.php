<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanyBank extends Model
{
    protected $fillable = [
        'company_id',
        'bank_name',
        'bank_branch',
        'account_number',
        'account_holder',
        'active'
    ];

}
