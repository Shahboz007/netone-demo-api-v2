<?php

namespace App\Services\Status;

use App\Models\Status;

class StatusService
{
  static function findByCode(string $code)
  {
    return Status::where('code', $code)->firstOrFail();
  }
}
