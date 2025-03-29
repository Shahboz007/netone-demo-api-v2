<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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

    public function transPerms(): BelongsToMany
    {
        return $this->belongsToMany(
            TransPerm::class,
            'depart_trans_perm_property',
            'depart_id',
            'trans_perm_id'
        );
    }
}
