<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RentalPropertyCategory extends Model
{
    //

    protected $fillable = [
        'name',
        'is_income',
    ];
}
