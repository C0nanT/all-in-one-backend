<?php

namespace Modules\PayableAccount\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PayableAccount extends Model
{
    /** @use HasFactory<\Database\Factories\PayableAccountFactory> */
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [];
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory<\Modules\PayableAccount\Models\PayableAccount>
     */
    protected static function newFactory(): \Illuminate\Database\Eloquent\Factories\Factory
    {
        return \Database\Factories\PayableAccountFactory::new();
    }

    /**
     * @return HasMany<PayableAccountPayment, $this>
     */
    public function payments(): HasMany
    {
        return $this->hasMany(PayableAccountPayment::class, 'payable_account_id')->orderByDesc('period');
    }
}
