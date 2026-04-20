<?php

namespace Tests\Feature\FollowUps;

use App\Models\Contact;
use App\Models\FollowUp;
use App\Models\Intake;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReassignFromQueueTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_reassign_an_intake_from_the_queue(): void
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

        /** @var User $owner */
        $owner = User::factory()->createOne([
            'organization_id' => $organization->id,
            'role' => 'owner',
            'is_active' => true,
        ]);

        /** @var User $originalAssignee */
        $originalAssignee = User::factory()->createOne([
            'organization_id' => $organization->id,
            'role' => 'intake_specialist',
            'is_active' => true,
        ]);

        /** @var User $newAssignee */
        $newAssignee = User::factory()->createOne([
            'organization_id' => $organization->id,
            'role' => 'intake_specialist',
            'is_active' => true,
        ]);

        /** @var User $outsideUser */
        $outsideUser = User::factory()->createOne([
            'organization_id' => $otherOrganization->id,
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
            'assigned_user_id' => $originalAssignee->id,
            'source' => 'referral',
            'status' => 'contacted',
            'urgency' => 'high',
            'summary' => 'Slip and fall inquiry',
            'details' => 'Queue reassignment test.',
            'received_at' => now()->subDays(3),
            'last_activity_at' => now()->subDays(2),
        ]);

        FollowUp::create([
            'organization_id' => $organization->id,
            'intake_id' => $intake->id,
            'user_id' => $originalAssignee->id,
            'channel' => 'call',
            'outcome' => 'left_voicemail',
            'note' => 'Overdue follow-up for queue.',
            'attempted_at' => now()->subDays(2),
            'next_follow_up_at' => now()->subDay(),
        ]);

        $response = $this
            ->actingAs($owner)
            ->patch(route('follow-ups.queue.reassign', $intake), [
                'assigned_user_id' => $newAssignee->id,
            ]);

        $response
            ->assertRedirect()
            ->assertSessionHas('status', 'Assignee updated successfully.');

        $this->assertDatabaseHas('intakes', [
            'id' => $intake->id,
            'assigned_user_id' => $newAssignee->id,
        ]);

        $this->assertDatabaseMissing('intakes', [
            'id' => $intake->id,
            'assigned_user_id' => $originalAssignee->id,
        ]);

        $invalidResponse = $this
            ->actingAs($owner)
            ->from(route('follow-ups.queue'))
            ->patch(route('follow-ups.queue.reassign', $intake), [
                'assigned_user_id' => $outsideUser->id,
            ]);

        $invalidResponse
            ->assertRedirect(route('follow-ups.queue'))
            ->assertSessionHasErrors('assigned_user_id');

        $this->assertDatabaseHas('intakes', [
            'id' => $intake->id,
            'assigned_user_id' => $newAssignee->id,
        ]);
    }
}
