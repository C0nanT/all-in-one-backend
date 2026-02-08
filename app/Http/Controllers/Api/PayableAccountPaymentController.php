<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePayableAccountPaymentRequest;
use App\Http\Resources\PayableAccountPaymentResource;
use App\Models\PayableAccount;
use App\Services\PayableAccountPaymentService;
use Illuminate\Http\JsonResponse;

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
