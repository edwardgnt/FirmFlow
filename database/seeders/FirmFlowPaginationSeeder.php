<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\FollowUp;
use App\Models\Intake;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class FirmFlowPaginationSeeder extends Seeder
{
    public function run(): void
    {
        $organization = Organization::query()->first();

        if (! $organization) {
            $this->command?->warn('No organization found. Seed your base demo data first.');
            return;
        }

        $users = User::query()
            ->where('organization_id', $organization->id)
            ->where('is_active', true)
            ->get();

        if ($users->isEmpty()) {
            $this->command?->warn('No active users found for the demo organization.');
            return;
        }

        $sources = ['website', 'referral', 'call', 'chat'];
        $statuses = ['new', 'contacted', 'qualified', 'appointment_set'];
        $urgencies = ['low', 'normal', 'high'];
        $contactMethods = ['call', 'email', 'sms'];
        $channels = ['call', 'email', 'sms', 'chat'];
        $outcomes = ['no_answer', 'left_voicemail', 'responded', 'appointment_booked', 'other'];

        for ($i = 1; $i <= 25; $i++) {
            $firstName = 'Demo' . $i;
            $lastName = 'Contact';
            $email = 'demo-contact-' . $i . '@example.com';

            $contact = Contact::query()->create([
                'organization_id' => $organization->id,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'phone' => '555-100-' . str_pad((string) $i, 4, '0', STR_PAD_LEFT),
                'preferred_contact_method' => $contactMethods[array_rand($contactMethods)],
                'notes' => 'Pagination demo contact ' . $i,
            ]);

            $assignedUser = $i % 5 === 0 ? null : $users->random();

            $intake = Intake::query()->create([
                'organization_id' => $organization->id,
                'contact_id' => $contact->id,
                'assigned_user_id' => $assignedUser?->id,
                'source' => $sources[array_rand($sources)],
                'status' => $statuses[array_rand($statuses)],
                'urgency' => $urgencies[array_rand($urgencies)],
                'summary' => 'Demo intake #' . $i . ' - ' . Str::title($sources[array_rand($sources)] ?? 'website'),
                'details' => 'Generated pagination demo intake #' . $i,
                'received_at' => Carbon::now()->subDays(rand(1, 14))->subHours(rand(0, 23)),
                'last_activity_at' => Carbon::now()->subDays(rand(0, 5))->subHours(rand(0, 23)),
            ]);

            // Every intake gets at least one follow-up
            FollowUp::query()->create([
                'organization_id' => $organization->id,
                'intake_id' => $intake->id,
                'user_id' => $assignedUser?->id ?? $users->random()->id,
                'channel' => $channels[array_rand($channels)],
                'outcome' => $outcomes[array_rand($outcomes)],
                'note' => 'Generated follow-up for intake #' . $i,
                'attempted_at' => Carbon::now()->subDays(rand(0, 5))->subHours(rand(0, 23)),
                'next_follow_up_at' => null,
            ]);

            // First 15 intakes get overdue queue entries
            if ($i <= 15) {
                FollowUp::query()->create([
                    'organization_id' => $organization->id,
                    'intake_id' => $intake->id,
                    'user_id' => $assignedUser?->id ?? $users->random()->id,
                    'channel' => $channels[array_rand($channels)],
                    'outcome' => $outcomes[array_rand($outcomes)],
                    'note' => 'Generated overdue queue follow-up for intake #' . $i,
                    'attempted_at' => Carbon::now()->subDays(rand(2, 6)),
                    'next_follow_up_at' => Carbon::now()->subDays(rand(1, 4))->subHours(rand(1, 12)),
                ]);
            }
        }
    }
}
