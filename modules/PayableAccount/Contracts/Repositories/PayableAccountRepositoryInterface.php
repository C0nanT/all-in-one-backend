<?php

namespace Modules\PayableAccount\Contracts\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Modules\PayableAccount\Models\PayableAccount;

interface PayableAccountRepositoryInterface
{
    public function getAll(string $period): Collection;

    public function find(int $id): ?PayableAccount;

    public function create(array $data): PayableAccount;

    public function update(PayableAccount $payableAccount, array $data): PayableAccount;

    public function delete(PayableAccount $payableAccount): bool;
}
