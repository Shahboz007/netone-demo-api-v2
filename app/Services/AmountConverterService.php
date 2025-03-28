<?php

namespace App\Services;

use App\Models\AmountType;
use App\Models\AmountSettings;

class AmountConverterService
{
    private AmountSettings|null $settings;

    public function __construct() {}



    public function convert(float $amount, int $fromTypeId, int $toTypeId): array | null
    {
        if (!$fromTypeId) {
            abort(500, '`$fromTypeId` is required');
        }
        if (!$toTypeId) {
            abort(500, '`$toTypeId` is required');
        }
        if ($fromTypeId === $toTypeId) {
            return [
                'ok' => true,
                'value' => $amount,
            ];
        }

        $this->configConverterData($fromTypeId, $toTypeId);

        $result = $this->convertAmount($amount);


        if (is_null($result)) {
            $from_type_name = $this->getAmountType($fromTypeId);
            $to_type_name = $this->getAmountType($toTypeId);
            return [
                'ok' => false,
                'msg' => "`{$from_type_name}` va `{$to_type_name}` o'lchov birligi, maxsus sozlamalar ro'yxatida mavjud emas!"
            ];
        }

        return [
            'ok' => true,
            'value' => $result,
        ];
    }

    private function configConverterData(int $fromTypeId, int $toTypeId):AmountSettings
    {
        return AmountSettings::where('type_from_id', $fromTypeId)
            ->where('type_to_id', $toTypeId)
            ->firstOrFail();
    }

    private function convertAmount($amount): float | null
    {
        if (is_null($this->settings)) return null;

        // Parse
        $amountFrom = $this->settings->amount_from;
        $amountTo = $this->settings->amount_to;

        if ($amountFrom > $amountTo) {
            return (float) ($amount / ($amountFrom * $amountTo));
        } else if ($amountFrom < $amountTo) {
            return (float) ($amount *  $amountTo);
        } else {
            return (float) $amount;
        }
    }

    private function getAmountType(int $id): string
    {
        $amountType = AmountType::findOrFail($id);
        return $amountType->name;
    }
}
