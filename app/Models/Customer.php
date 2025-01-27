<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    /** @use HasFactory<\Database\Factories\CustomerFactory> */
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'telegram',
        'balance',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'customer_id');
    }
}
