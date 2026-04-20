<?php

namespace Tests\Feature\FollowUps;

use App\Models\Contact;
use App\Models\FollowUp;
use App\Models\Intake;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FollowUpQueueTest extends TestCase
{
    use RefreshDatabase;

    public function test_follow_up_queue_only_shows_overdue_intakes(): void
    {
        $organization = Organization::create([
            'name' => 'Test Firm',
            'slug' => 'test-firm',
            'timezone' => 'America/Los_Angeles',
            'is_active' => true,
        ]);

        $otherOrganization = Organization::create([
            'name' => 'Other Firm',
            'slug' => 'other-firm',
            'timezone' => 'America/Los_Angeles',
            'is_active' => true,
        ]);

        /** @var User $user */
        $user = User::factory()->createOne([
            'organization_id' => $organization->id,
            'role' => 'owner',
            'is_active' => true,
        ]);

        /** @var User $assignee */
        $assignee = User::factory()->createOne([
            'organization_id' => $organization->id,
            'role' => 'intake_specialist',
            'is_active' => true,
        ]);

        /** @var User $otherOrgUser */
        $otherOrgUser = User::factory()->createOne([
            'organization_id' => $otherOrganization->id,
            'role' => 'owner',
            'is_active' => true,
        ]);

        $overdueContact = Contact::create([
            'organization_id' => $organization->id,
            'first_name' => 'Sandra',
            'last_name' => 'Lopez',
            'email' => 'sandra@example.com',
            'phone' => '555-333-4444',
            'preferred_contact_method' => 'email',
        ]);

        $overdueIntake = Intake::create([
            'organization_id' => $organization->id,
            'contact_id' => $overdueContact->id,
            'assigned_user_id' => $assignee->id,
            'source' => 'referral',
            'status' => 'contacted',
            'urgency' => 'high',
            'summary' => 'Overdue slip and fall inquiry',
            'details' => 'This intake should appear in the queue.',
            'received_at' => now()->subDays(3),
            'last_activity_at' => now()->subDays(2),
        ]);

        FollowUp::create([
            'organization_id' => $organization->id,
            'intake_id' => $overdueIntake->id,
            'user_id' => $assignee->id,
            'channel' => 'call',
            'outcome' => 'left_voicemail',
            'note' => 'First overdue follow-up.',
            'attempted_at' => now()->subDays(2),
            'next_follow_up_at' => now()->subDay(),
        ]);

        FollowUp::create([
            'organization_id' => $organization->id,
            'intake_id' => $overdueIntake->id,
            'user_id' => $assignee->id,
            'channel' => 'email',
            'outcome' => 'responded',
            'note' => 'Second overdue follow-up.',
            'attempted_at' => now()->subDay(),
            'next_follow_up_at' => now()->subHours(6),
        ]);

        $futureContact = Contact::create([
            'organization_id' => $organization->id,
            'first_name' => 'John',
            'last_name' => 'Carter',
            'email' => 'john@example.com',
            'phone' => '555-222-1111',
            'preferred_contact_method' => 'call',
        ]);

        $futureIntake = Intake::create([
            'organization_id' => $organization->id,
            'contact_id' => $futureContact->id,
            'assigned_user_id' => $assignee->id,
            'source' => 'website',
            'status' => 'new',
            'urgency' => 'normal',
            'summary' => 'Future follow-up inquiry',
            'details' => 'This intake should not appear in the overdue queue.',
            'received_at' => now()->subDay(),
            'last_activity_at' => now()->subDay(),
        ]);

        FollowUp::create([
            'organization_id' => $organization->id,
            'intake_id' => $futureIntake->id,
            'user_id' => $assignee->id,
            'channel' => 'call',
            'outcome' => 'no_answer',
            'note' => 'Future reminder.',
            'attempted_at' => now(),
            'next_follow_up_at' => now()->addDay(),
        ]);

        $otherOrgContact = Contact::create([
            'organization_id' => $otherOrganization->id,
            'first_name' => 'Other',
            'last_name' => 'Person',
            'email' => 'other@example.com',
            'phone' => '555-999-8888',
            'preferred_contact_method' => 'email',
        ]);

        $otherOrgIntake = Intake::create([
            'organization_id' => $otherOrganization->id,
            'contact_id' => $otherOrgContact->id,
            'assigned_user_id' => $otherOrgUser->id,
            'source' => 'website',
            'status' => 'new',
            'urgency' => 'high',
            'summary' => 'Other organization overdue intake',
            'details' => 'This should never appear for the authenticated user.',
            'received_at' => now()->subDays(2),
            'last_activity_at' => now()->subDays(2),
        ]);

        FollowUp::create([
            'organization_id' => $otherOrganization->id,
            'intake_id' => $otherOrgIntake->id,
            'user_id' => $otherOrgUser->id,
            'channel' => 'call',
            'outcome' => 'left_voicemail',
            'note' => 'Other org overdue reminder.',
            'attempted_at' => now()->subDay(),
            'next_follow_up_at' => now()->subHours(3),
        ]);

        $response = $this
            ->actingAs($user)
            ->get(route('follow-ups.queue'));

        $response->assertOk();
        $response->assertSeeText('Overdue slip and fall inquiry');
        $response->assertDontSeeText('Future follow-up inquiry');
        $response->assertDontSeeText('Other organization overdue intake');

        $queueIntakes = $response->viewData('intakes');

        $this->assertCount(1, $queueIntakes);
        $this->assertSame($overdueIntake->id, $queueIntakes->items()[0]->id);
        $this->assertSame(2, $queueIntakes->items()[0]->overdue_follow_ups_count);
    }
}
