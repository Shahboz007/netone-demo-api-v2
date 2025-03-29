<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DepartTransPermPropertyDetails extends Model
{
    //

    protected $fillable = [
        'depart_trans_perm_property_id',
        'depart_id'
    ];

    public function departTransPermProperty():BelongsTo
    {
        return $this->belongsTo(DepartTransPermProperty::class, 'depart_trans_perm_property_id');
    }

    public function depart():BelongsTo
    {
        return $this->belongsTo(Depart::class, 'depart_id');
    }
}
