<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reminder extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id',
        'milestone_id',
        'recipient_user_id',
        'remind_before_days',
        'remind_date',
        'status',
        'sent_at',
        'message',
    ];

    protected function casts(): array
    {
        return [
            'remind_before_days' => 'integer',
            'remind_date' => 'date',
            'sent_at' => 'datetime',
        ];
    }

    // Relationships
    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function milestone(): BelongsTo
    {
        return $this->belongsTo(Milestone::class);
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_user_id');
    }
}
