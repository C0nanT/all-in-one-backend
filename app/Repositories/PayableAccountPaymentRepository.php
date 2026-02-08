<?php

namespace App\Repositories;

use App\Contracts\Repositories\PayableAccountPaymentRepositoryInterface;
use App\Models\PayableAccountPayment;

class PayableAccountPaymentRepository implements PayableAccountPaymentRepositoryInterface
{
    public function create(array $data): PayableAccountPayment
    {
        return PayableAccountPayment::query()->create($data);
    }
}
