<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'trade_name',
        'document',
        'email',
        'phone',
        'state_registration',
        'city_registration',
        'zipcode',
        'address',
        'number',
        'district',
        'city',
        'state',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
