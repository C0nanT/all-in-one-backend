<?php

namespace Modules\PayableAccount\Repositories;

use Modules\PayableAccount\Contracts\Repositories\PayableAccountPaymentRepositoryInterface;
use Modules\PayableAccount\Models\PayableAccountPayment;

class PayableAccountPaymentRepository implements PayableAccountPaymentRepositoryInterface
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): PayableAccountPayment
    {
        return PayableAccountPayment::query()->create($data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(PayableAccountPayment $payment, array $data): PayableAccountPayment
    {
        $payment->update($data);

        $updated = $payment->fresh(['payer']);

        return $updated ?? $payment;
    }
}
