<?php

namespace Modules\PayableAccount\Services;

use Modules\PayableAccount\Contracts\Repositories\PayableAccountPaymentRepositoryInterface;
use Modules\PayableAccount\Models\PayableAccount;
use Modules\PayableAccount\Models\PayableAccountPayment;

class PayableAccountPaymentService
{
    public function __construct(
        private readonly PayableAccountPaymentRepositoryInterface $repository
    ) {}

    public function create(PayableAccount $payableAccount, array $data): PayableAccountPayment
    {
        $data['payable_account_id'] = $payableAccount->id;

        return $this->repository->create($data);
    }

    public function update(PayableAccountPayment $payment, array $data): PayableAccountPayment
    {
        return $this->repository->update($payment, $data);
    }
}
