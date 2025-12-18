<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Milestone extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id',
        'type',
        'title',
        'due_date',
        'done',
        'done_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'done' => 'boolean',
            'done_date' => 'date',
        ];
    }

    // Relationships
    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function reminders(): HasMany
    {
        return $this->hasMany(Reminder::class);
    }

    // Constants
    public const TYPE_EXPIRY = 'EXPIRY';
    public const TYPE_PAYMENT = 'PAYMENT';
    public const TYPE_DELIVERY = 'DELIVERY';
}
