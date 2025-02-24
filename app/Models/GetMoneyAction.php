<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class GetMoneyAction extends Model
{
    protected $fillable = [
        'get_money_id',
        'user_wallet_id',
        'user_id',
        'sum_amount',
    ];

    public function getMoney(): BelongsTo
    {
        return $this->belongsTo(GetMoney::class, 'get_money_id');
    }

    public function userWallet(): BelongsTo
    {
        return $this->belongsTo(UserWallet::class, 'user_wallet_id')
            ->with(['user']);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'paymentable');
    }
}
