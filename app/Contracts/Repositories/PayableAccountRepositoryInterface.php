<?php

namespace App\Contracts\Repositories;

use App\Models\PayableAccount;
use Illuminate\Database\Eloquent\Collection;

interface PayableAccountRepositoryInterface
{
    /**
     * @param  string|null  $period  First day of month (Y-m-d) to filter payments; null = all payments
     */
    public function getAll(?string $period = null): Collection;

    public function find(int $id): ?PayableAccount;

    public function create(array $data): PayableAccount;

    public function update(PayableAccount $payableAccount, array $data): PayableAccount;

    public function delete(PayableAccount $payableAccount): bool;
}
