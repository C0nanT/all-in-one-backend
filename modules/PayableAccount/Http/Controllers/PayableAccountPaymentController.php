<?php

namespace Modules\PayableAccount\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\PayableAccount\Http\Requests\StorePayableAccountPaymentRequest;
use Modules\PayableAccount\Http\Requests\UpdatePayableAccountPaymentRequest;
use Modules\PayableAccount\Http\Resources\PayableAccountPaymentResource;
use Modules\PayableAccount\Models\PayableAccount;
use Modules\PayableAccount\Models\PayableAccountPayment;
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

    public function update(
        UpdatePayableAccountPaymentRequest $request,
        PayableAccount $payable_account,
        PayableAccountPayment $payment,
    ): JsonResponse {
        if ($payment->payable_account_id !== $payable_account->id) {
            abort(404);
        }
        $payment = $this->service->update($payment, $request->validated());

        return (new PayableAccountPaymentResource($payment->load('payer')))->response();
    }
}
