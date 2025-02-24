<?php

namespace App\Services\Finance;

use App\Models\GetMoney;
use Illuminate\Database\Eloquent\Collection;

class GetMoneyService
{
    public function findAll(): Collection
    {
        return GetMoney::with('children')->get();
    }

    public function findOne(int $id): GetMoney
    {
        return GetMoney::findOrFail($id);
    }
}
