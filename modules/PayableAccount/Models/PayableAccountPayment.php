<?php

namespace Modules\PayableAccount\Models;

use Database\Factories\PayableAccountPaymentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\User\Models\User;

class PayableAccountPayment extends Model
{
    use HasFactory, SoftDeletes;

    /** @use HasFactory<PayableAccountPaymentFactory> */
    protected static function newFactory(): \Illuminate\Database\Eloquent\Factories\Factory
    {
        return PayableAccountPaymentFactory::new();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'payable_account_id',
        'amount',
        'payer_id',
        'period',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'period' => 'date',
        ];
    }

    public function payableAccount(): BelongsTo
    {
        return $this->belongsTo(PayableAccount::class, 'payable_account_id');
    }

    public function payer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payer_id');
    }
}
