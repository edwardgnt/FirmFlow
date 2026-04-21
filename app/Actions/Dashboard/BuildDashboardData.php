<?php

namespace App\Actions\Dashboard;

use App\Models\Contact;
use App\Models\FollowUp;
use App\Models\Intake;
use App\Models\User;

class BuildDashboardData
{
    public function handle(User $user): array
    {
        $organizationId = $user->organization_id;
        $staleCutoff = now()->subDays(2);

        $totalContacts = Contact::query()
            ->where('organization_id', $organizationId)
            ->count();

        $totalIntakes = Intake::query()
            ->where('organization_id', $organizationId)
            ->count();

        $newIntakes = Intake::query()
            ->where('organization_id', $organizationId)
            ->where('status', 'new')
            ->count();

        $contactedIntakes = Intake::query()
            ->where('organization_id', $organizationId)
            ->where('status', 'contacted')
            ->count();

        $totalFollowUps = FollowUp::query()
            ->where('organization_id', $organizationId)
            ->count();

        $recentIntakes = Intake::query()
            ->with(['contact', 'assignedUser'])
            ->where('organization_id', $organizationId)
            ->latest()
            ->take(5)
            ->get();

        $overdueFollowUps = FollowUp::query()
            ->with(['intake.contact', 'intake.assignedUser', 'user'])
            ->where('organization_id', $organizationId)
            ->whereNotNull('next_follow_up_at')
            ->where('next_follow_up_at', '<', now())
            ->orderBy('next_follow_up_at')
            ->take(3)
            ->get();

        $needsAttentionIntakes = Intake::query()
            ->with(['contact', 'assignedUser'])
            ->where('organization_id', $organizationId)
            ->whereNotIn('status', ['won', 'lost'])
            ->where(function ($query) use ($staleCutoff) {
                $query->whereNull('assigned_user_id')
                    ->orWhere('last_activity_at', '<=', $staleCutoff);
            })
            ->orderBy('last_activity_at')
            ->take(3)
            ->get();

        return [
            'totalContacts' => $totalContacts,
            'totalIntakes' => $totalIntakes,
            'newIntakes' => $newIntakes,
            'contactedIntakes' => $contactedIntakes,
            'totalFollowUps' => $totalFollowUps,
            'recentIntakes' => $recentIntakes,
            'overdueFollowUps' => $overdueFollowUps,
            'needsAttentionIntakes' => $needsAttentionIntakes,
        ];
    }
}
