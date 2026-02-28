<?php

namespace Modules\PayableAccount\Http\Requests;

use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Modules\PayableAccount\Models\PayableAccount;
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
            $period = $this->input('period');
            $this->merge([
                'period' => Carbon::parse(is_string($period) ? $period : '')->startOfMonth()->format('Y-m-d'),
            ]);
        }
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $payableAccount = $this->route('payable_account');
        assert($payableAccount instanceof PayableAccount);
        $payableAccountId = $payableAccount->id;

        return [
            'amount' => ['required', 'numeric', 'min:0'],
            'payer_id' => ['required', 'integer', 'exists:users,id'],
            'period' => [
                'required',
                'date',
                function (string $attribute, mixed $value, Closure $fail) use ($payableAccountId): void {
                    $period = $value instanceof Carbon
                        ? $value->format('Y-m-d')
                        : Carbon::parse(is_string($value) ? $value : '')->format('Y-m-d');
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
