<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RentalPropertyAction extends Model
{
    //

    protected $fillable = [
        'rental_property_id',
        'user_id',
        'customer_id',
        'user_wallet_id',
        'price',
    ];

    public function rentalProperty(): BelongsTo
    {
        return $this->belongsTo(RentalProperty::class, 'rental_property_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function userWallet(): BelongsTo
    {
        return $this->belongsTo(UserWallet::class, 'user_wallet_id');
    }
}
