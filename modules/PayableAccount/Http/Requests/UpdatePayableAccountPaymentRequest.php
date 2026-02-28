<?php

namespace Modules\PayableAccount\Http\Requests;

use Carbon\Carbon;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Modules\PayableAccount\Models\PayableAccountPayment;

class UpdatePayableAccountPaymentRequest extends FormRequest
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
     * @return array<string, array<int, string|Closure>>
     */
    public function rules(): array
    {
        $payableAccountId = $this->route('payable_account')->id;
        $paymentId = $this->route('payment')->id;
        Log::info('payable_account', [$payableAccountId]);
        Log::info('payment', [$paymentId]);

        return [
            'amount' => ['required', 'numeric', 'min:0'],
            'payer_id' => ['required', 'integer', 'exists:users,id'],
            'period' => [
                'required',
                'date',
                function (string $attribute, mixed $period, Closure $fail) use ($payableAccountId, $paymentId): void {
                    $exists = PayableAccountPayment::query()
                        ->where('payable_account_id', $payableAccountId)
                        ->whereDate('period', $period)
                        ->where('id', '!=', $paymentId)
                        ->exists();
                    if ($exists) {
                        $fail(__('validation.unique', ['attribute' => $attribute]));
                    }
                },
            ],
        ];
    }
}
