<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class RentalProperty extends Model
{
    protected $fillable = [
        "name",
        "price",
        "comment",
    ];
}
