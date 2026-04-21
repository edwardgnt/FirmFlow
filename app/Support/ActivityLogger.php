<?php

namespace App\Support;

use App\Models\ActivityLog;
use App\Models\Intake;
use Illuminate\Database\Eloquent\Model;

class ActivityLogger
{
    public static function log(
        int $organizationId,
        ?int $userId,
        string $action,
        string $description,
        ?Intake $intake = null,
        ?Model $subject = null,
        ?array $metadata = null,
    ): ActivityLog {
        return ActivityLog::query()->create([
            'organization_id' => $organizationId,
            'user_id' => $userId,
            'intake_id' => $intake?->id, // shorthand for 'intake_id' => $intake ? $intake->id : null,
            'action' => $action,
            'description' => $description,
            'subject_type' => $subject ? $subject::class : null,
            'subject_id' => $subject?->getKey(),
            'metadata' => $metadata,
        ]);
    }
}
