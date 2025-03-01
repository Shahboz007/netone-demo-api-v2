<?php

namespace App\Services\Status;

use App\Models\Status;

class StatusService
{
    static function findByCode(string $code)
    {
        return Status::where('code', $code)->firstOrFail();
    }

    public function getIdList(array $statusCodes): array
    {
        return Status::whereIn('code', $statusCodes)->pluck('id')->toArray();
    }
}
