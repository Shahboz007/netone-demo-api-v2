<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    /** @use HasFactory<\Database\Factories\SupplierFactory> */
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'telegram',
        'balance',
    ];

    // public function receiveRawMaterials(): HasMany
    // {
    //     return $this->hasMany(ReceiveRawMaterial::class, 'supplier_id');
    // }
}
