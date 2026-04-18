<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FollowUp extends Model
{
    protected $fillable = [
        'organization_id',
        'intake_id',
        'user_id',
        'channel',
        'outcome',
        'note',
        'attempted_at',
        'next_follow_up_at',
    ];

    protected $casts = [
        'attempted_at' => 'datetime',
        'next_follow_up_at' => 'datetime',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function intake(): BelongsTo
    {
        return $this->belongsTo(Intake::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
