<?php

namespace App\Contracts\Repositories;

use App\Models\PayableAccountPayment;

interface PayableAccountPaymentRepositoryInterface
{
    public function create(array $data): PayableAccountPayment;
}
