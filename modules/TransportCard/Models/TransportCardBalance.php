<?php

namespace Modules\TransportCard\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property array<string, mixed>|null $raw_response
 * @property \Carbon\Carbon|null $updated_at
 */
class TransportCardBalance extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'snapshot_date',
        'balance',
        'card_number',
        'raw_response',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'snapshot_date' => 'date',
            'balance' => 'decimal:2',
            'raw_response' => 'array',
        ];
    }
}
