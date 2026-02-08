<?php

namespace App\Contracts\Repositories;

use App\Models\PayableAccount;
use Illuminate\Database\Eloquent\Collection;

interface PayableAccountRepositoryInterface
{
    public function getAll(): Collection;

    public function find(int $id): ?PayableAccount;

    public function create(array $data): PayableAccount;

    public function update(PayableAccount $payableAccount, array $data): PayableAccount;

    public function delete(PayableAccount $payableAccount): bool;
}
