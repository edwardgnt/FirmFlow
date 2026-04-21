<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\FollowUp;
use App\Models\Intake;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ActivityLogService
{
    public function logIntakeCreated(User $user, Intake $intake): ActivityLog
    {
        return $this->write(
            user: $user,
            intake: $intake,
            action: 'intake_created',
            description: "Created intake \"{$intake->summary}\".",
            subject: $intake,
            metadata: [
                'status' => $intake->status,
                'urgency' => $intake->urgency,
                'assigned_user_id' => $intake->assigned_user_id,
                'source' => $intake->source,
            ],
        );
    }

    public function logIntakeReassigned(User $user, Intake $intake, ?int $fromUserId, ?int $toUserId): ActivityLog
    {
        return $this->write(
            user: $user,
            intake: $intake,
            action: 'intake_reassigned',
            description: 'Reassigned intake ownership.',
            subject: $intake,
            metadata: [
                'from_user_id' => $fromUserId,
                'to_user_id' => $toUserId,
            ],
        );
    }

    public function logIntakeStatusUpdated(User $user, Intake $intake, string $fromStatus, string $toStatus): ActivityLog
    {
        return $this->write(
            user: $user,
            intake: $intake,
            action: 'intake_status_updated',
            description: "Updated intake status from {$fromStatus} to {$toStatus}.",
            subject: $intake,
            metadata: [
                'from_status' => $fromStatus,
                'to_status' => $toStatus,
            ],
        );
    }

    public function logFollowUpLogged(User $user, Intake $intake, FollowUp $followUp): ActivityLog
    {
        return $this->write(
            user: $user,
            intake: $intake,
            action: 'follow_up_logged',
            description: 'Logged a follow-up.',
            subject: $followUp,
            metadata: [
                'channel' => $followUp->channel,
                'outcome' => $followUp->outcome,
                'attempted_at' => $followUp->attempted_at,
                'next_follow_up_at' => $followUp->next_follow_up_at,
            ],
        );
    }

    public function logQueueFollowUpLogged(User $user, Intake $intake, FollowUp $followUp): ActivityLog
    {
        return $this->write(
            user: $user,
            intake: $intake,
            action: 'queue_follow_up_logged',
            description: 'Logged a follow-up from the queue.',
            subject: $followUp,
            metadata: [
                'channel' => $followUp->channel,
                'outcome' => $followUp->outcome,
                'attempted_at' => $followUp->attempted_at,
                'next_follow_up_at' => $followUp->next_follow_up_at,
            ],
        );
    }

    protected function write(
        User $user,
        Intake $intake,
        string $action,
        string $description,
        Model $subject,
        ?array $metadata = null,
    ): ActivityLog {
        return ActivityLog::query()->create([
            'organization_id' => $intake->organization_id,
            'user_id' => $user->id,
            'intake_id' => $intake->id,
            'action' => $action,
            'description' => $description,
            'subject_type' => $subject::class,
            'subject_id' => $subject->getKey(),
            'metadata' => $metadata,
        ]);
    }
}
