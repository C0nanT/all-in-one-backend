<?php

namespace Modules\PayableAccount\Repositories;

use Modules\PayableAccount\Contracts\Repositories\PayableAccountPaymentRepositoryInterface;
use Modules\PayableAccount\Models\PayableAccountPayment;

class PayableAccountPaymentRepository implements PayableAccountPaymentRepositoryInterface
{
    public function create(array $data): PayableAccountPayment
    {
        return PayableAccountPayment::query()->create($data);
    }

    public function update(PayableAccountPayment $payment, array $data): PayableAccountPayment
    {
        $payment->update($data);

        return $payment->fresh(['payer']);
    }
}
