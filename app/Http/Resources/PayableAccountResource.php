<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PayableAccountResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'status' => $this->relationLoaded('payments') ? ($this->payments->first() ? 'paid' : 'unpaid') : 'unpaid',
            'payment' => $this->relationLoaded('payments') ? ($this->payments->first() ? new PayableAccountPaymentResource($this->payments->first()) : null) : null,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
