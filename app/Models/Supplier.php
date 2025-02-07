<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;


class Supplier extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'telegram',
        'balance',
    ];

    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'paymentable');
    }

    public function receiveProducts(): HasMany
    {
        return $this->hasMany(ReceiveProduct::class, 'supplier_id');
    }
}
