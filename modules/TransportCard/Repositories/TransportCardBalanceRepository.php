<?php

namespace Modules\TransportCard\Repositories;

use Carbon\Carbon;
use Modules\TransportCard\Contracts\Repositories\TransportCardBalanceRepositoryInterface;
use Modules\TransportCard\Models\TransportCardBalance;

class TransportCardBalanceRepository implements TransportCardBalanceRepositoryInterface
{
    public function getForDate(Carbon $date): ?TransportCardBalance
    {
        return TransportCardBalance::query()
            ->whereDate('snapshot_date', $date)
            ->first();
    }

    /**
     * @param  array<string, mixed>|null  $raw
     */
    public function upsertForDate(Carbon $date, float $balance, string $cardNumber, ?array $raw = null): TransportCardBalance
    {
        $record = TransportCardBalance::query()
            ->whereDate('snapshot_date', $date)
            ->first();

        $data = [
            'snapshot_date' => $date->format('Y-m-d'),
            'balance' => $balance,
            'card_number' => $cardNumber,
            'raw_response' => $raw,
        ];

        if ($record !== null) {
            $record->update($data);

            $fresh = $record->fresh();

            return $fresh ?? $record;
        }

        return TransportCardBalance::query()->create($data);
    }
}
