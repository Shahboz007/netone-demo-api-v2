<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserWallet extends Model
{
    protected $table = 'user_wallet';

    protected $fillable = ['user_id', 'wallet_id'];

    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function wallet():BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }
}
