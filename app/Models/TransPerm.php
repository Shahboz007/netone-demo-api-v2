<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TransPerm extends Model
{

    protected $fillable = [
        'name',
        'code'
    ];

    public function departs(): BelongsToMany
    {
        return $this->belongsToMany(
            Depart::class,
            'depart_trans_perm_property',
            'trans_perm_id',
            'depart_id'
        );
    }
}
