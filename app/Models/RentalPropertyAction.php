<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class RentalPropertyAction extends Model
{
    //

    protected $fillable = [
        'rental_property_id',
        'rental_property_category_id',
        'is_income'
    ];

    public function rentalProperty(): BelongsTo
    {
        return  $this->belongsTo(RentalProperty::class, 'rental_property_id');
    }

    public function rentalPropertyCategory(): BelongsTo
    {
        return $this->belongsTo(RentalPropertyCategory::class, 'rental_property_category_id');
    }

    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'paymentable');
    }
}
