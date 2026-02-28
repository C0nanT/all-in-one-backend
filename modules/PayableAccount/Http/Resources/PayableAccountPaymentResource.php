<?php

namespace Modules\PayableAccount\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\User\Models\User;

/**
 * @mixin \Modules\PayableAccount\Models\PayableAccountPayment
 */
class PayableAccountPaymentResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'amount' => (float) $this->amount,
            'payer_id' => $this->payer_id,
            'payer' => $this->payer_id === null ? null : $this->whenLoaded('payer', fn (User $payer) => $payer->name),
            'period' => $this->period?->format('d-m-Y'),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
