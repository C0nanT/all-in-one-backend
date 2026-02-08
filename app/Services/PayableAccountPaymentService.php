<?php

namespace App\Services;

use App\Contracts\Repositories\PayableAccountPaymentRepositoryInterface;
use App\Models\PayableAccount;
use App\Models\PayableAccountPayment;
use Carbon\Carbon;

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
