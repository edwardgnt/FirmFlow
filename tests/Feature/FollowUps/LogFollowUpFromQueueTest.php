<?php

namespace Tests\Feature\FollowUps;

use App\Models\Contact;
use App\Models\FollowUp;
use App\Models\Intake;
use App\Models\Organization;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogFollowUpFromQueueTest extends TestCase
{
    use RefreshDatabase;

    public function test_logging_a_follow_up_from_the_queue_clears_the_overdue_item_when_next_follow_up_is_future_dated(): void
    {
        $organization = Organization::create([
            'name' => 'Test Firm',
            'slug' => 'test-firm',
            'timezone' => 'America/Los_Angeles',
            'is_active' => true,
        ]);

        /** @var User $owner */
        $owner = User::factory()->createOne([
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

        $contact = Contact::create([
            'organization_id' => $organization->id,
            'first_name' => 'Sandra',
            'last_name' => 'Lopez',
            'email' => 'sandra@example.com',
            'phone' => '555-333-4444',
            'preferred_contact_method' => 'email',
        ]);

        $intake = Intake::create([
            'organization_id' => $organization->id,
            'contact_id' => $contact->id,
            'assigned_user_id' => $assignee->id,
            'source' => 'referral',
            'status' => 'contacted',
            'urgency' => 'high',
            'summary' => 'Overdue slip and fall inquiry',
            'details' => 'This intake starts in the overdue queue.',
            'received_at' => now()->subDays(3),
            'last_activity_at' => now()->subDays(2),
        ]);

        $oldFollowUp = FollowUp::create([
            'organization_id' => $organization->id,
            'intake_id' => $intake->id,
            'user_id' => $assignee->id,
            'channel' => 'call',
            'outcome' => 'left_voicemail',
            'note' => 'Existing overdue follow-up.',
            'attempted_at' => now()->subDays(2),
            'next_follow_up_at' => now()->subDay(),
        ]);

        $attemptedAt = Carbon::now()->startOfMinute();
        $nextFollowUpAt = Carbon::now()->addDay()->startOfMinute();

        $response = $this
            ->actingAs($owner)
            ->from(route('follow-ups.queue'))
            ->post(route('follow-ups.queue.log', $intake), [
                'channel' => 'email',
                'outcome' => 'responded',
                'attempted_at' => $attemptedAt->format('Y-m-d H:i:s'),
                'next_follow_up_at' => $nextFollowUpAt->format('Y-m-d H:i:s'),
                'note' => 'Logged from queue and scheduled next follow-up.',
            ]);

        $response
            ->assertRedirect(route('follow-ups.queue'))
            ->assertSessionHas('status', 'Follow-up logged successfully.');

        $this->assertDatabaseHas('follow_ups', [
            'id' => $oldFollowUp->id,
            'next_follow_up_at' => null,
        ]);

        $this->assertDatabaseHas('follow_ups', [
            'organization_id' => $organization->id,
            'intake_id' => $intake->id,
            'user_id' => $owner->id,
            'channel' => 'email',
            'outcome' => 'responded',
            'note' => 'Logged from queue and scheduled next follow-up.',
            'attempted_at' => $attemptedAt->format('Y-m-d H:i:s'),
            'next_follow_up_at' => $nextFollowUpAt->format('Y-m-d H:i:s'),
        ]);

        $this->assertDatabaseHas('intakes', [
            'id' => $intake->id,
            'last_activity_at' => $attemptedAt->format('Y-m-d H:i:s'),
        ]);

        $queueResponse = $this
            ->actingAs($owner)
            ->get(route('follow-ups.queue'));

        $queueResponse->assertOk();
        $queueResponse->assertDontSeeText('Overdue slip and fall inquiry');

        $queueIntakes = $queueResponse->viewData('intakes');

        $this->assertCount(0, $queueIntakes);
    }
}
