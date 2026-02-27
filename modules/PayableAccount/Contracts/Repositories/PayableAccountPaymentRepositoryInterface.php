<?php

namespace Modules\PayableAccount\Contracts\Repositories;

use Modules\PayableAccount\Models\PayableAccountPayment;

interface PayableAccountPaymentRepositoryInterface
{
    public function create(array $data): PayableAccountPayment;

    public function update(PayableAccountPayment $payment, array $data): PayableAccountPayment;
}
