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
