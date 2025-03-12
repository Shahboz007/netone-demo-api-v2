<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RentalPropertyCategory extends Model
{
    //

    protected $fillable = [
        'parent_id',
        'name',
        'is_income',
    ];

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')
            ->with('children');
    }
}
