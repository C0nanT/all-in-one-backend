<?php

namespace Modules\PayableAccount\Services;

use Illuminate\Database\Eloquent\Collection;
use Modules\PayableAccount\Contracts\Repositories\PayableAccountRepositoryInterface;
use Modules\PayableAccount\Models\PayableAccount;

class PayableAccountService
{
    public function __construct(
        private readonly PayableAccountRepositoryInterface $repository
    ) {}

    /**
     * @return Collection<int, PayableAccount>
     */
    public function list(string $period): Collection
    {
        return $this->repository->getAll($period);
    }

    /**
     * @return array{month_total: float, paid_by_user: array<int, array{user_id: int, name: string, total_paid: float}>}
     */
    public function getSummary(string $period): array
    {
        return $this->repository->getSummary($period);
    }

    public function find(int $id): ?PayableAccount
    {
        return $this->repository->find($id);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): PayableAccount
    {
        return $this->repository->create($data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(PayableAccount $payableAccount, array $data): PayableAccount
    {
        return $this->repository->update($payableAccount, $data);
    }

    public function delete(PayableAccount $payableAccount): bool
    {
        return $this->repository->delete($payableAccount);
    }
}
