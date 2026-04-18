<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\FollowUp;
use App\Models\Intake;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class FirmFlowDemoSeeder extends Seeder
{
    public function run(): void
    {
        $organization = Organization::create([
            'name' => 'Acme Legal Group',
            'slug' => 'acme-legal-group',
            'timezone' => 'America/Los_Angeles',
            'is_active' => true,
        ]);

        $owner = User::create([
            'name' => 'Olivia Owner',
            'email' => 'owner@firmflow.test',
            'password' => 'password',
            'organization_id' => $organization->id,
            'role' => 'owner',
            'is_active' => true,
        ]);

        $manager = User::create([
            'name' => 'Mark Manager',
            'email' => 'manager@firmflow.test',
            'password' => 'password',
            'organization_id' => $organization->id,
            'role' => 'manager',
            'is_active' => true,
        ]);

        $specialist = User::create([
            'name' => 'Ivy Intake',
            'email' => 'intake@firmflow.test',
            'password' => 'password',
            'organization_id' => $organization->id,
            'role' => 'intake_specialist',
            'is_active' => true,
        ]);

        $contact1 = Contact::create([
            'organization_id' => $organization->id,
            'first_name' => 'John',
            'last_name' => 'Carter',
            'email' => 'john.carter@example.com',
            'phone' => '555-111-2222',
            'preferred_contact_method' => 'call',
            'notes' => 'Reached out after viewing the website contact form.',
        ]);

        $contact2 = Contact::create([
            'organization_id' => $organization->id,
            'first_name' => 'Sandra',
            'last_name' => 'Lopez',
            'email' => 'sandra.lopez@example.com',
            'phone' => '555-333-4444',
            'preferred_contact_method' => 'email',
            'notes' => 'Requested a consultation regarding a personal injury matter.',
        ]);

        $intake1 = Intake::create([
            'organization_id' => $organization->id,
            'contact_id' => $contact1->id,
            'assigned_user_id' => $specialist->id,
            'source' => 'website',
            'status' => 'new',
            'urgency' => 'normal',
            'summary' => 'Potential car accident case',
            'details' => 'Caller said they were rear-ended last week and wants to discuss options.',
            'received_at' => Carbon::now()->subHours(5),
            'last_activity_at' => Carbon::now()->subHours(5),
        ]);

        $intake2 = Intake::create([
            'organization_id' => $organization->id,
            'contact_id' => $contact2->id,
            'assigned_user_id' => $manager->id,
            'source' => 'referral',
            'status' => 'contacted',
            'urgency' => 'high',
            'summary' => 'Slip and fall inquiry',
            'details' => 'Prospective client referred by former client. Wants callback ASAP.',
            'received_at' => Carbon::now()->subDay(),
            'last_activity_at' => Carbon::now()->subHours(2),
        ]);

        FollowUp::create([
            'organization_id' => $organization->id,
            'intake_id' => $intake2->id,
            'user_id' => $manager->id,
            'channel' => 'call',
            'outcome' => 'left_voicemail',
            'note' => 'Left voicemail and requested callback.',
            'attempted_at' => Carbon::now()->subHours(4),
            'next_follow_up_at' => Carbon::now()->addDay(),
        ]);

        FollowUp::create([
            'organization_id' => $organization->id,
            'intake_id' => $intake2->id,
            'user_id' => $manager->id,
            'channel' => 'email',
            'outcome' => 'responded',
            'note' => 'Sent follow-up email with consultation availability.',
            'attempted_at' => Carbon::now()->subHours(2),
            'next_follow_up_at' => Carbon::now()->addHours(20),
        ]);
    }
}
