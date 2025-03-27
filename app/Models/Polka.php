<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Polka extends Model
{
    /** @use HasFactory<\Database\Factories\PolkaFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'parent_id'
    ];

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->with('children');
    }
}
