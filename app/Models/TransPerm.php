<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransPerm extends Model
{
    /** @use HasFactory<\Database\Factories\TransPermFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'code'
    ];
}
