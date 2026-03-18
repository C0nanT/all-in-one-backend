<?php

namespace Modules\TransportCard\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\TransportCard\Services\TransportCardService;

class TransportCardController extends Controller
{
    public function __construct(
        private readonly TransportCardService $service
    ) {}

    public function balance(Request $request): JsonResponse
    {
        $forceRefresh = $request->boolean('refresh');

        try {
            $data = $this->service->getBalance($forceRefresh);

            return response()->json($data);
        } catch (\RuntimeException $e) {
            return response()->json(
                ['message' => 'Unable to fetch transport card balance. Please try again later.'],
                Response::HTTP_BAD_GATEWAY
            );
        }
    }

    public function refresh(): JsonResponse
    {
        try {
            $data = $this->service->getBalance(true);

            return response()->json($data);
        } catch (\RuntimeException $e) {
            return response()->json(
                ['message' => 'Unable to fetch transport card balance. Please try again later.'],
                Response::HTTP_BAD_GATEWAY
            );
        }
    }
}
