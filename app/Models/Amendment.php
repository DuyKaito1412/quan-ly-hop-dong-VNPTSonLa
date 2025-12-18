<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Amendment extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id',
        'amendment_no',
        'type',
        'new_end_date',
        'additional_amount',
        'description',
        'status',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'new_end_date' => 'date',
            'additional_amount' => 'decimal:2',
            'approved_at' => 'datetime',
        ];
    }

    // Relationships
    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
