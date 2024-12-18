<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens, SoftDeletes;

    const ROLE_SUPERADMIN = 11;

    const ROLE_ADMIN = 21;

    const ROLE_OPERATOR = 31;

    const ROLE_CUSTOMER = 41;

    const ROLE_AGENT = 51;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'active',
        'phone',
        'customer_id',
        'company_id',
        'agent_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = ['role_name'];

    protected $with = ['company'];

    public function scopeSuperAdmin($q)
    {
        return $q->where('role', self::ROLE_SUPERADMIN);
    }

    public function scopeAdmin($q)
    {
        return $q->where('role', self::ROLE_ADMIN);
    }

    public function scopeOperator($q)
    {
        return $q->where('role', self::ROLE_OPERATOR);
    }

    public function scopeCustomer($q)
    {
        return $q->where('role', self::ROLE_CUSTOMER);
    }

    public function scopeAgent($q)
    {
        return $q->where('role', self::ROLE_AGENT);
    }

    public static function roleList()
    {
        $list = [
            self::ROLE_ADMIN => 'ADMIN',
            self::ROLE_OPERATOR => 'OPERATOR',
            self::ROLE_CUSTOMER => 'CUSTOMER',
            self::ROLE_AGENT => 'AGENT'
        ];

        // if (auth()->user()->role == self::ROLE_SUPERADMIN) {
        $list[self::ROLE_SUPERADMIN] = 'SUPER ADMIN';
        // }

        return $list;
    }

    public function getRoleNameAttribute()
    {
        return self::roleList()[$this->role];
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }
}
