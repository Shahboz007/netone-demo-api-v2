<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Expense extends Model
{
    protected $fillable = [
        'parent_id',
        'name',
        'amount',
        'comment'
    ];

    public function children(): HasMany
    {
        return $this->hasMany(Expense::class, 'parent_id');
    }
}
