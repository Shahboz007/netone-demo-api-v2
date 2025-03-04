<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GetMoney extends Model
{
    protected $fillable = [
        'parent_id',
        'name',
        'amount',
        'comment',
    ];

    public function children(): HasMany
    {
        return $this->hasMany(Expense::class, 'parent_id');
    }
}
