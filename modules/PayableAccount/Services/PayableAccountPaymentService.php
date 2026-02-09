<?php

namespace Modules\PayableAccount\Services;

use Carbon\Carbon;
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
        $data['period'] = Carbon::parse($data['period'])->startOfMonth()->format('Y-m-d');

        return $this->repository->create($data);
    }
}
