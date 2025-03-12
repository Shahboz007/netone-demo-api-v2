<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RentalPropertyCategory extends Model
{
    //

    protected $fillable = [
        'parent_id',
        'name',
        'is_income',
    ];
}
