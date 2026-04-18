<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Intake;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class IntakeController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if (! $user) {
            abort(403);
        }

        $intakes = Intake::with(['contact', 'assignedUser'])
            ->where('organization_id', $user->organization_id)
            ->latest()
            ->paginate(10);

        return view('intakes.index', compact('intakes'));
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

    public function store(Request $request)
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

        DB::transaction(function () use ($validated, $organizationId) {
            $contact = Contact::create([
                'organization_id' => $organizationId,
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'] ?? null,
                'email' => $validated['email'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'preferred_contact_method' => $validated['preferred_contact_method'] ?? null,
                'notes' => null,
            ]);

            Intake::create([
                'organization_id' => $organizationId,
                'contact_id' => $contact->id,
                'assigned_user_id' => $validated['assigned_user_id'] ?? null,
                'source' => $validated['source'] ?? null,
                'status' => $validated['status'],
                'urgency' => $validated['urgency'],
                'summary' => $validated['summary'],
                'details' => $validated['details'] ?? null,
                'received_at' => now(),
                'last_activity_at' => now(),
            ]);
        });

        return redirect()
            ->route('intakes.index')
            ->with('status', 'Intake created successfully.');
    }
}
