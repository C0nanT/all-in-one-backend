<?php

namespace Modules\PayableAccount\Repositories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Relation;
use Modules\PayableAccount\Contracts\Repositories\PayableAccountRepositoryInterface;
use Modules\PayableAccount\Models\PayableAccount;
use Modules\PayableAccount\Models\PayableAccountPayment;

class PayableAccountRepository implements PayableAccountRepositoryInterface
{
    /**
     * @return Collection<int, PayableAccount>
     */
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
                'payments' => function (Relation $q) use ($latestPaymentIdSubquery, $start, $end): void {
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

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): PayableAccount
    {
        return PayableAccount::query()->create($data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(PayableAccount $payableAccount, array $data): PayableAccount
    {
        $payableAccount->update($data);

        $updated = $payableAccount->fresh();

        return $updated ?? $payableAccount;
    }

    public function delete(PayableAccount $payableAccount): bool
    {
        return (bool) $payableAccount->delete();
    }
}
