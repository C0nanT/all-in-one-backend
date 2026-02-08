<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePayableAccountRequest;
use App\Http\Requests\UpdatePayableAccountRequest;
use App\Http\Resources\PayableAccountResource;
use App\Models\PayableAccount;
use App\Services\PayableAccountService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PayableAccountController extends Controller
{
    public function __construct(
        private readonly PayableAccountService $service
    ) {}

    public function index(): AnonymousResourceCollection
    {
        $accounts = $this->service->list();

        return PayableAccountResource::collection($accounts);
    }

    public function store(StorePayableAccountRequest $request): JsonResponse
    {
        $payableAccount = $this->service->create($request->validated());

        return (new PayableAccountResource($payableAccount))->response()->setStatusCode(201);
    }

    public function show(PayableAccount $payable_account): PayableAccountResource
    {
        return new PayableAccountResource($payable_account);
    }

    public function update(UpdatePayableAccountRequest $request, PayableAccount $payable_account): PayableAccountResource
    {
        $payableAccount = $this->service->update($payable_account, $request->validated());

        return new PayableAccountResource($payableAccount);
    }

    public function destroy(PayableAccount $payable_account): JsonResponse
    {
        $this->service->delete($payable_account);

        return response()->json(null, 204);
    }
}
