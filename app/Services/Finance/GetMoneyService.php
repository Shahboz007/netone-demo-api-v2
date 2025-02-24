<?php

namespace App\Services\Finance;

use App\Exceptions\InvalidDataException;
use App\Exceptions\NotFoundException;
use App\Models\GetMoney;
use Illuminate\Database\Eloquent\Collection;

class GetMoneyService
{
    public function findAll(): Collection
    {
        return GetMoney::with('children')->get();
    }


    public function create(array $data)
    {
        return GetMoney::create($data);
    }

    public function findOne(int $id): GetMoney
    {
        return GetMoney::with('children')->findOrFail($id);
    }

    public function update(array $data, int $id)
    {
        $getMoney = $this->findOne($id);
        if (!$getMoney) throw new NotFoundException();

        // validate exist
        if ($data['name']) {
            $isExist = GetMoney::where('name', $data['name'])
                ->where('id', "<>", $getMoney->id)->exists();
            if ($isExist) throw new InvalidDataException('Bu allaqachon mavjud!');
        }

        // Check Parent And Children
        if (!empty($data['parent_id'])) {
            $pluckChildren = $getMoney->children->pluck('name', 'id');

            if ($getMoney->id === $data['parent_id'] || !empty($pluckChildren[$data['parent_id']])) {
                throw new InvalidDataException("Ma'lumotni noto'g'ri kiritdingiz!");
            }
        }

        $getMoney->update($data);

        return $getMoney;
    }

    public function delete(int $id): void
    {
        $getMoney = $this->findOne($id);
        $getMoney->delete();
    }
}
