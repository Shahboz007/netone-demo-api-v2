<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Pivot;

class DepartTransPermProperty extends Pivot
{
    //

    protected $fillable = [
        'trans_perm_id',
        'depart_id',
    ];

    public function details():HasMany
    {
        return $this->hasMany(DepartTransPermPropertyDetails::class, 'depart_trans_perm_property_id');
    }


}
