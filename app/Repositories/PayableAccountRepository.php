<?php

namespace App\Repositories;

use App\Contracts\Repositories\PayableAccountRepositoryInterface;
use App\Models\PayableAccount;
use App\Models\PayableAccountPayment;
use Illuminate\Database\Eloquent\Collection;

class PayableAccountRepository implements PayableAccountRepositoryInterface
{
    public function getAll(?string $period = null): Collection
    {
        $latestPaymentIdSubquery = PayableAccountPayment::query()
            ->selectRaw('MAX(id)')
            ->groupBy('payable_account_id', 'period');

        $query = PayableAccount::query()
            ->orderByDesc('id')
            ->with([
                'payments' => function ($q) use ($period, $latestPaymentIdSubquery): void {
                    $q->whereIn('id', $latestPaymentIdSubquery);
                    if ($period !== null) {
                        $q->whereDate('period', $period);
                    }else{
                        $q->whereDate('period','>=',now()->startOfMonth()->format('Y-m-d'));
                    }
                },
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
