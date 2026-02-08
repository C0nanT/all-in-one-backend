<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePayableAccountRequest;
use App\Http\Requests\UpdatePayableAccountRequest;
use App\Http\Resources\PayableAccountResource;
use App\Models\PayableAccount;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PayableAccountController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $accounts = PayableAccount::query()->orderBy('id', 'desc')->get();

        return PayableAccountResource::collection($accounts);
    }

    public function store(StorePayableAccountRequest $request): JsonResponse
    {
        $data = array_merge(
            $request->validated(),
            ['status' => $request->validated('status', 'open')]
        );
        $payableAccount = PayableAccount::query()->create($data);

        return (new PayableAccountResource($payableAccount))->response()->setStatusCode(201);
    }

    public function show(PayableAccount $payable_account): PayableAccountResource
    {
        return new PayableAccountResource($payable_account);
    }

    public function update(UpdatePayableAccountRequest $request, PayableAccount $payable_account): PayableAccountResource
    {
        $payable_account->update($request->validated());

        return new PayableAccountResource($payable_account->fresh());
    }

    public function destroy(PayableAccount $payable_account): JsonResponse
    {
        $payable_account->delete();

        return response()->json(null, 204);
    }
}
