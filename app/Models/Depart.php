<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Depart extends Model
{
    /** @use HasFactory<\Database\Factories\DepartFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'comment'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
