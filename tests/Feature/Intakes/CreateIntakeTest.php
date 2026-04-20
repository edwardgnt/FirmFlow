<?php

namespace Tests\Feature\Intakes;

use App\Models\Contact;
use App\Models\Intake;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateIntakeTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_an_intake(): void
    {
        /** @var Organization $organization */
        $organization = Organization::query()->create([
            'name' => 'Test Firm',
            'slug' => 'test-firm',
            'timezone' => 'America/Los_Angeles',
            'is_active' => true,
        ]);

        /** @var User $user */
        $user = User::factory()->create([
            'organization_id' => $organization->id,
            'role' => 'owner',
            'is_active' => true,
        ]);

        /** @var User $assignee */
        $assignee = User::factory()->create([
            'organization_id' => $organization->id,
            'role' => 'intake_specialist',
            'is_active' => true,
        ]);

        $response = $this
            ->actingAs($user)
            ->post(route('intakes.store'), [
                'first_name' => 'Sandra',
                'last_name' => 'Lopez',
                'email' => 'sandra@example.com',
                'phone' => '555-333-4444',
                'preferred_contact_method' => 'email',
                'source' => 'website',
                'summary' => 'Slip and fall inquiry',
                'details' => 'Potential personal injury matter.',
                'status' => 'new',
                'urgency' => 'high',
                'assigned_user_id' => $assignee->id,
            ]);

        $response
            ->assertRedirect(route('intakes.index'))
            ->assertSessionHas('status', 'Intake created successfully.');

        $contact = Contact::query()
            ->where('email', 'sandra@example.com')
            ->firstOrFail();

        $this->assertDatabaseHas('contacts', [
            'organization_id' => $organization->id,
            'first_name' => 'Sandra',
            'last_name' => 'Lopez',
            'email' => 'sandra@example.com',
            'phone' => '555-333-4444',
            'preferred_contact_method' => 'email',
        ]);

        $this->assertDatabaseHas('intakes', [
            'organization_id' => $organization->id,
            'contact_id' => $contact->id,
            'assigned_user_id' => $assignee->id,
            'source' => 'website',
            'summary' => 'Slip and fall inquiry',
            'status' => 'new',
            'urgency' => 'high',
        ]);

        $this->assertSame(1, Intake::query()->count());
    }
}
