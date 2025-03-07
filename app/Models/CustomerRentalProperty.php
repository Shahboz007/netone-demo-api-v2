<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerRentalProperty extends Model
{
    //

    protected $fillable = [
        'user_id',
        'rental_property_id',
        'customer_id',
        'price',
        'customer_id'
    ];

    public function user():BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function rentalProperty():BelongsTo
    {
        return $this->belongsTo(RentalProperty::class, 'rental_property_id');
    }

    public function customer():BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
