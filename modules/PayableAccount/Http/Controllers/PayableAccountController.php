<?php

namespace Modules\PayableAccount\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\ValidationException;
use Modules\PayableAccount\Http\Requests\StorePayableAccountRequest;
use Modules\PayableAccount\Http\Requests\UpdatePayableAccountRequest;
use Modules\PayableAccount\Http\Resources\PayableAccountResource;
use Modules\PayableAccount\Models\PayableAccount;
use Modules\PayableAccount\Services\PayableAccountService;

class PayableAccountController extends Controller
{
    public function __construct(
        private readonly PayableAccountService $service
    ) {}

    /**
     * @throws ValidationException
     */
    public function counts(Request $request): JsonResponse
    {
        $request->validate(['period' => ['required', 'date']]);

        $period = $request->input('period');
        $periodString = is_string($period) ? $period : '';

        $counts = $this->service->getPaidUnpaidCounts($periodString);

        return response()->json(['data' => $counts]);
    }

    /**
     * @throws ValidationException
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $request->validate(['period' => ['required', 'date']]);

        $period = $request->input('period');
        $periodString = is_string($period) ? $period : '';

        $accounts = $this->service->list($periodString);
        $summary = $this->service->getSummary($periodString);

        $summaryWithPeriod = array_merge($summary, [
            'period' => Carbon::parse($periodString)->format('Y-m'),
        ]);

        return PayableAccountResource::collection($accounts)
            ->additional(['summary' => $summaryWithPeriod]);
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
