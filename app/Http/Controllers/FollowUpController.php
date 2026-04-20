<?php

namespace App\Http\Controllers;

use App\Models\FollowUp;
use App\Models\Intake;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class FollowUpController extends Controller
{
    public function queue(Request $request)
    {
        $user = $request->user();

        if (! $user) {
            abort(403);
        }

        $assignedUserId = $request->string('assigned_user_id')->toString();
        $status = $request->string('status')->toString();
        $source = $request->string('source')->toString();
        $sort = $request->string('sort')->toString() ?: 'oldest_due';

        $overdueScope = function ($query) {
            $query->whereNotNull('next_follow_up_at')
                ->where('next_follow_up_at', '<', now());
        };

        $intakesQuery = Intake::with(['contact', 'assignedUser'])
            ->where('organization_id', $user->organization_id)
            ->whereHas('followUps', $overdueScope)
            ->withCount([
                'followUps as overdue_follow_ups_count' => $overdueScope,
            ])
            ->withMin([
                'followUps as oldest_overdue_at' => $overdueScope,
            ], 'next_follow_up_at')
            ->when($assignedUserId !== '', function ($query) use ($assignedUserId) {
                if ($assignedUserId === 'unassigned') {
                    $query->whereNull('assigned_user_id');

                    return;
                }

                $query->where('assigned_user_id', $assignedUserId);
            })
            ->when($status !== '', function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($source !== '', function ($query) use ($source) {
                $query->where('source', $source);
            });

        if ($sort === 'newest_due') {
            $intakesQuery->orderByDesc('oldest_overdue_at');
        } else {
            $intakesQuery->orderBy('oldest_overdue_at');
        }

        $intakes = $intakesQuery
            ->paginate(10)
            ->withQueryString();

        $assignees = User::where('organization_id', $user->organization_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $sources = Intake::where('organization_id', $user->organization_id)
            ->whereNotNull('source')
            ->select('source')
            ->distinct()
            ->orderBy('source')
            ->pluck('source');

        return view('follow-ups.queue', compact(
            'intakes',
            'assignees',
            'assignedUserId',
            'status',
            'source',
            'sources',
            'sort'
        ));
    }

    public function reassign(Request $request, Intake $intake)
    {
        $user = $request->user();

        if (! $user) {
            abort(403);
        }

        if ($intake->organization_id !== $user->organization_id) {
            abort(404);
        }

        $organizationId = $user->organization_id;

        $validated = $request->validate([
            'assigned_user_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id')->where(function ($query) use ($organizationId) {
                    $query->where('organization_id', $organizationId);
                }),
            ],
        ]);

        $intake->update([
            'assigned_user_id' => $validated['assigned_user_id'] ?? null,
        ]);

        return back()->with('status', 'Assignee updated successfully.');
    }

    public function logFromQueue(Request $request, Intake $intake)
    {
        $user = $request->user();

        if (! $user) {
            abort(403);
        }

        if ($intake->organization_id !== $user->organization_id) {
            abort(404);
        }

        $validated = $request->validate([
            'channel' => [
                'required',
                Rule::in(['call', 'email', 'sms', 'chat', 'internal_note']),
            ],
            'outcome' => [
                'required',
                Rule::in([
                    'no_answer',
                    'left_voicemail',
                    'responded',
                    'appointment_booked',
                    'not_interested',
                    'wrong_number',
                    'other',
                ]),
            ],
            'attempted_at' => ['required', 'date'],
            'next_follow_up_at' => ['nullable', 'date', 'after_or_equal:attempted_at'],
            'note' => ['nullable', 'string'],
        ], [
            'channel.required' => 'Please select a channel.',
            'outcome.required' => 'Please select an outcome.',
            'attempted_at.required' => 'Please enter the attempted date and time.',
        ]);

        DB::transaction(function () use ($validated, $intake, $user) {
            // Mark existing overdue reminders on this intake as handled for queue purposes.
            $intake->followUps()
                ->whereNotNull('next_follow_up_at')
                ->where('next_follow_up_at', '<', now())
                ->update(['next_follow_up_at' => null]);

            FollowUp::create([
                'organization_id' => $intake->organization_id,
                'intake_id' => $intake->id,
                'user_id' => $user->id,
                'channel' => $validated['channel'],
                'outcome' => $validated['outcome'] ?? null,
                'note' => $validated['note'] ?? null,
                'attempted_at' => $validated['attempted_at'],
                'next_follow_up_at' => $validated['next_follow_up_at'] ?? null,
            ]);

            $intake->update([
                'last_activity_at' => $validated['attempted_at'],
            ]);
        });

        return back()->with('status', 'Follow-up logged successfully.');
    }
}
