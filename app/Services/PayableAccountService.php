<?php

namespace App\Services;

use App\Contracts\Repositories\PayableAccountRepositoryInterface;
use App\Models\PayableAccount;
use Illuminate\Database\Eloquent\Collection;

class PayableAccountService
{
    public function __construct(
        private readonly PayableAccountRepositoryInterface $repository
    ) {}

    public function list(string $period): Collection
    {
        return $this->repository->getAll($period);
    }

    public function find(int $id): ?PayableAccount
    {
        return $this->repository->find($id);
    }

    public function create(array $data): PayableAccount
    {
        return $this->repository->create($data);
    }

    public function update(PayableAccount $payableAccount, array $data): PayableAccount
    {
        return $this->repository->update($payableAccount, $data);
    }

    public function delete(PayableAccount $payableAccount): bool
    {
        return $this->repository->delete($payableAccount);
    }
}
