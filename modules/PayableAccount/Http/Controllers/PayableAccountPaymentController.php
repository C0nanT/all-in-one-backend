<?php

namespace Modules\PayableAccount\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\PayableAccount\Http\Requests\StorePayableAccountPaymentRequest;
use Modules\PayableAccount\Http\Resources\PayableAccountPaymentResource;
use Modules\PayableAccount\Models\PayableAccount;
use Modules\PayableAccount\Services\PayableAccountPaymentService;

class PayableAccountPaymentController extends Controller
{
    public function __construct(
        private readonly PayableAccountPaymentService $service
    ) {}

    public function store(StorePayableAccountPaymentRequest $request, PayableAccount $payable_account): JsonResponse
    {
        $payment = $this->service->create($payable_account, $request->validated());

        return (new PayableAccountPaymentResource($payment))->response()->setStatusCode(201);
    }
}
