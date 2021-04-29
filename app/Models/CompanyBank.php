<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyBank extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id',
        'bank_name',
        'bank_branch',
        'account_number',
        'account_holder',
        'active'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
