<?php

namespace Modules\PayableAccount\Contracts\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Modules\PayableAccount\Models\PayableAccount;

interface PayableAccountRepositoryInterface
{
    /**
     * @return Collection<int, PayableAccount>
     */
    public function getAll(string $period): Collection;

    /**
     * @return array{month_total: float, paid_by_user: array<int, array{user_id: int, name: string, total_paid: float}>}
     */
    public function getSummary(string $period): array;

    /**
     * @return array{paid: int, unpaid: int}
     */
    public function getPaidUnpaidCounts(string $period): array;

    public function find(int $id): ?PayableAccount;

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): PayableAccount;

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(PayableAccount $payableAccount, array $data): PayableAccount;

    public function delete(PayableAccount $payableAccount): bool;
}
