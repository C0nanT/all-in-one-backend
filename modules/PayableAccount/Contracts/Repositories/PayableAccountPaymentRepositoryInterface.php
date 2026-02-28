<?php

namespace Modules\PayableAccount\Contracts\Repositories;

use Modules\PayableAccount\Models\PayableAccountPayment;

interface PayableAccountPaymentRepositoryInterface
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): PayableAccountPayment;

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(PayableAccountPayment $payment, array $data): PayableAccountPayment;
}
