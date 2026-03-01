<?php

namespace Modules\PayableAccount\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \Modules\PayableAccount\Models\PayableAccount
 */
class PayableAccountResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $payment = $this->relationLoaded('payments') ? $this->payments->first() : null;
        $status = !$payment ? 'unpaid' : ((float) $payment->amount > 0 ? 'paid' : 'paid_zero');

        return [
            'id' => $this->id,
            'name' => $this->name,
            'status' => $status,
            'payment' => $payment ? new PayableAccountPaymentResource($payment) : null,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
