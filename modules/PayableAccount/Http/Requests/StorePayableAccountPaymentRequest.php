<?php

namespace Modules\PayableAccount\Http\Requests;

use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Modules\PayableAccount\Models\PayableAccountPayment;

class StorePayableAccountPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('period')) {
            $this->merge([
                'period' => Carbon::parse($this->input('period'))->startOfMonth()->format('Y-m-d'),
            ]);
        }
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $payableAccountId = $this->route('payable_account')->id;

        return [
            'amount' => ['required', 'numeric', 'min:0'],
            'payer_id' => ['required', 'integer', 'exists:users,id'],
            'period' => [
                'required',
                'date',
                function (string $attribute, mixed $value, Closure $fail) use ($payableAccountId): void {
                    $period = $value instanceof Carbon
                        ? $value->format('Y-m-d')
                        : Carbon::parse($value)->format('Y-m-d');
                    $exists = PayableAccountPayment::query()
                        ->where('payable_account_id', $payableAccountId)
                        ->whereDate('period', $period)
                        ->exists();
                    if ($exists) {
                        $fail(__('validation.unique', ['attribute' => $attribute]));
                    }
                },
            ],
        ];
    }
}
