<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Intake;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\FollowUp;
use App\Services\ActivityLogService;

class IntakeController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if (! $user) {
            abort(403);
        }

        $status = $request->string('status')->toString();
        $source = $request->string('source')->toString();
        $assignedUserId = $request->string('assigned_user_id')->toString();

        $intakes = Intake::with(['contact', 'assignedUser'])
            ->where('organization_id', $user->organization_id)
            ->when($status !== '', function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($source !== '', function ($query) use ($source) {
                $query->where('source', $source);
            })
            ->when($assignedUserId !== '', function ($query) use ($assignedUserId) {
                if ($assignedUserId === 'unassigned') {
                    $query->whereNull('assigned_user_id');

                    return;
                }

                $query->where('assigned_user_id', $assignedUserId);
            })
            ->latest()
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

        return view('intakes.index', compact(
            'intakes',
            'assignees',
            'sources',
            'status',
            'source',
            'assignedUserId'
        ));
    }

    public function show(Request $request, Intake $intake)
    {
        $user = $request->user();

        if (! $user) {
            abort(403);
        }

        if ($intake->organization_id !== $user->organization_id) {
            abort(404);
        }

        $intake->load(['contact', 'assignedUser', 'followUps.user']);

        $assignees = User::where('organization_id', $user->organization_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('intakes.show', compact('intake', 'assignees'));
    }

    public function create(Request $request)
    {
        $user = $request->user();

        if (! $user) {
            abort(403);
        }

        $assignees = User::where('organization_id', $user->organization_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('intakes.create', compact('assignees'));
    }

    public function store(Request $request, ActivityLogService $activityLogService)
    {
        $user = $request->user();

        if (! $user) {
            abort(403);
        }

        $organizationId = $user->organization_id;

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'preferred_contact_method' => [
                'nullable',
                Rule::in(['call', 'email', 'sms']),
            ],
            'source' => ['nullable', 'string', 'max:100'],
            'summary' => ['required', 'string', 'max:255'],
            'details' => ['nullable', 'string'],
            'status' => [
                'required',
                Rule::in(['new', 'contacted', 'qualified', 'appointment_set', 'won', 'lost']),
            ],
            'urgency' => [
                'required',
                Rule::in(['low', 'normal', 'high']),
            ],
            'assigned_user_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id')->where(function ($query) use ($organizationId) {
                    $query->where('organization_id', $organizationId);
                }),
            ],
        ]);

        DB::transaction(function () use ($validated, $organizationId, $user, $activityLogService) {
            $contact = Contact::create([
                'organization_id' => $organizationId,
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'] ?? null,
                'email' => $validated['email'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'preferred_contact_method' => $validated['preferred_contact_method'] ?? null,
                'notes' => null,
            ]);

            $intake = Intake::create([
                'organization_id' => $organizationId,
                'contact_id' => $contact->id,
                'assigned_user_id' => $validated['assigned_user_id'] ?? null,
                'source' => $validated['source'] ?? null,
                'summary' => $validated['summary'],
                'details' => $validated['details'] ?? null,
                'status' => $validated['status'],
                'urgency' => $validated['urgency'],
                'received_at' => now(),
                'last_activity_at' => now(),
            ]);

            $activityLogService->logIntakeCreated($user, $intake);
        });

        return redirect()
            ->route('intakes.index')
            ->with('status', 'Intake created successfully.');
    }

    public function update(Request $request, Intake $intake, ActivityLogService $activityLogService)
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
            'status' => [
                'required',
                Rule::in(['new', 'contacted', 'qualified', 'appointment_set', 'won', 'lost']),
            ],
            'urgency' => [
                'required',
                Rule::in(['low', 'normal', 'high']),
            ],
            'assigned_user_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id')->where(function ($query) use ($organizationId) {
                    $query->where('organization_id', $organizationId);
                }),
            ],
        ]);

        DB::transaction(function () use ($validated, $intake, $user, $activityLogService) {
            $originalAssignedUserId = $intake->assigned_user_id;
            $originalStatus = $intake->status;

            $intake->update([
                'status' => $validated['status'],
                'urgency' => $validated['urgency'],
                'assigned_user_id' => $validated['assigned_user_id'] ?? null,
            ]);

            if ($originalAssignedUserId !== $intake->assigned_user_id) {
                $activityLogService->logIntakeReassigned(
                    $user,
                    $intake,
                    $originalAssignedUserId,
                    $intake->assigned_user_id
                );
            }

            if ($originalStatus !== $intake->status) {
                $activityLogService->logIntakeStatusUpdated(
                    $user,
                    $intake,
                    $originalStatus,
                    $intake->status
                );
            }
        });

        return redirect()
            ->route('intakes.show', $intake)
            ->with('status', 'Intake updated successfully.');
    }

    public function storeFollowUp(Request $request, Intake $intake)
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
                'nullable',
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
        ]);

        DB::transaction(function () use ($validated, $intake, $user) {
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

        return redirect()
            ->route('intakes.show', $intake)
            ->with('status', 'Follow-up added successfully.');
    }
}
