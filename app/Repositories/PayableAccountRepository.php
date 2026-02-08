<?php

namespace App\Repositories;

use App\Contracts\Repositories\PayableAccountRepositoryInterface;
use App\Models\PayableAccount;
use Illuminate\Database\Eloquent\Collection;

class PayableAccountRepository implements PayableAccountRepositoryInterface
{
    public function getAll(): Collection
    {
        return PayableAccount::query()->orderBy('created_at', 'desc')->get();
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
