<?php

namespace App\Repositories;

use App\Contracts\Repositories\PayableAccountRepositoryInterface;
use App\Models\PayableAccount;
use App\Models\PayableAccountPayment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class PayableAccountRepository implements PayableAccountRepositoryInterface
{
    public function getAll(string $period): Collection
    {
        $start = Carbon::parse($period)->startOfMonth()->format('Y-m-d');
        $end = Carbon::parse($period)->endOfMonth()->format('Y-m-d');

        $latestPaymentIdSubquery = PayableAccountPayment::query()
            ->selectRaw('MAX(id)')
            ->groupBy('payable_account_id', 'period');

        $query = PayableAccount::query()
            ->orderByDesc('id')
            ->with([
                'payments' => function ($q) use ($latestPaymentIdSubquery, $start, $end): void {
                    $q->whereIn('id', $latestPaymentIdSubquery);
                    $q->whereBetween('period', [$start, $end]);
                },
                'payments.payer',
            ]);

        return $query->get();
    }

    public function find(int $id): ?PayableAccount
    {
        return PayableAccount::query()->find($id);
    }

    public function create(array $data): PayableAccount
    {
        return PayableAccount::query()->create($data);
    }

    public function update(PayableAccount $payableAccount, array $data): PayableAccount
    {
        $payableAccount->update($data);

        return $payableAccount->fresh();
    }

    public function delete(PayableAccount $payableAccount): bool
    {
        return $payableAccount->delete();
    }
}
