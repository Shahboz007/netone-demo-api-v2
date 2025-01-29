<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Wallet extends Model
{
    protected $fillable = [
        'currency_id',
        'name',
        'is_active'
    ];

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function payments():BelongsToMany
    {
        return $this->belongsToMany(Payment::class);
    }
}
