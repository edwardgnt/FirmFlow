<x-layouts::app :title="$intake->summary">
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-neutral-900 dark:text-white">
                    {{ $intake->summary }}
                </h1>
                <p class="text-sm text-neutral-600 dark:text-neutral-400">
                    Intake details and contact information.
                </p>
            </div>

            <a href="{{ route('intakes.index') }}"
                class="inline-flex items-center rounded-lg border border-neutral-300 px-4 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-100 dark:border-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-800">
                Back to Intakes
            </a>
        </div>

        @if (session('status'))
            <div
                class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-900 dark:bg-emerald-950/40 dark:text-emerald-300">
                {{ session('status') }}
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-3">
            <div
                class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900 lg:col-span-2">
                <h2 class="mb-4 text-lg font-semibold text-neutral-900 dark:text-white">
                    Intake Overview
                </h2>

                <dl class="grid gap-4 md:grid-cols-2">
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                            Status
                        </dt>
                        @php
                            $statusClasses = match ($intake->status) {
                                'new' => 'bg-sky-100 text-sky-800 dark:bg-sky-900/40 dark:text-sky-300',
                                'contacted' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300',
                                'qualified'
                                    => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300',
                                'appointment_set'
                                    => 'bg-violet-100 text-violet-800 dark:bg-violet-900/40 dark:text-violet-300',
                                'won' => 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300',
                                'lost' => 'bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300',
                                default => 'bg-neutral-100 text-neutral-800 dark:bg-neutral-800 dark:text-neutral-300',
                            };
                        @endphp

                        <span
                            class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $statusClasses }}">
                            {{ str($intake->status)->headline() }}
                        </span>
                    </div>

                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                            Urgency
                        </dt>
                        <dd class="mt-1 text-sm text-neutral-900 dark:text-white">
                            {{ str($intake->urgency)->headline() }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                            Source
                        </dt>
                        <dd class="mt-1 text-sm text-neutral-900 dark:text-white">
                            {{ $intake->source ?? '—' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                            Assigned To
                        </dt>
                        <dd class="mt-1 text-sm text-neutral-900 dark:text-white">
                            {{ $intake->assignedUser->name ?? 'Unassigned' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                            Received At
                        </dt>
                        <dd class="mt-1 text-sm text-neutral-900 dark:text-white">
                            {{ $intake->received_at?->format('M d, Y g:i A') ?? '—' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                            Last Activity
                        </dt>
                        <dd class="mt-1 text-sm text-neutral-900 dark:text-white">
                            {{ $intake->last_activity_at?->format('M d, Y g:i A') ?? '—' }}
                        </dd>
                    </div>

                    <div class="md:col-span-2">
                        <dt class="text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                            Details
                        </dt>
                        <dd class="mt-1 whitespace-pre-line text-sm text-neutral-900 dark:text-white">
                            {{ $intake->details ?: 'No details provided.' }}
                        </dd>
                    </div>
                </dl>
            </div>

            <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
                <h2 class="mb-4 text-lg font-semibold text-neutral-900 dark:text-white">
                    Contact
                </h2>

                <dl class="space-y-4">
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                            Name
                        </dt>
                        <dd class="mt-1 text-sm text-neutral-900 dark:text-white">
                            {{ $intake->contact->first_name }} {{ $intake->contact->last_name }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                            Email
                        </dt>
                        <dd class="mt-1 text-sm text-neutral-900 dark:text-white">
                            {{ $intake->contact->email ?: '—' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                            Phone
                        </dt>
                        <dd class="mt-1 text-sm text-neutral-900 dark:text-white">
                            {{ $intake->contact->phone ?: '—' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                            Preferred Contact Method
                        </dt>
                        <dd class="mt-1 text-sm text-neutral-900 dark:text-white">
                            {{ $intake->contact->preferred_contact_method ? str($intake->contact->preferred_contact_method)->headline() : '—' }}
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
        <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">
                    Update Intake
                </h2>
            </div>

            <form method="POST" action="{{ route('intakes.update', $intake) }}" class="space-y-6">
                @csrf
                @method('PATCH')

                <div class="grid gap-6 md:grid-cols-3">
                    <div>
                        <label for="status" class="mb-2 block text-sm font-medium text-neutral-900 dark:text-white">
                            Status
                        </label>
                        <select id="status" name="status"
                            class="w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 text-neutral-900 focus:border-neutral-500 focus:outline-none dark:border-neutral-700 dark:bg-neutral-800 dark:text-white">
                            <option value="new" @selected(old('status', $intake->status) === 'new')>New</option>
                            <option value="contacted" @selected(old('status', $intake->status) === 'contacted')>Contacted</option>
                            <option value="qualified" @selected(old('status', $intake->status) === 'qualified')>Qualified</option>
                            <option value="appointment_set" @selected(old('status', $intake->status) === 'appointment_set')>Appointment Set</option>
                            <option value="won" @selected(old('status', $intake->status) === 'won')>Won</option>
                            <option value="lost" @selected(old('status', $intake->status) === 'lost')>Lost</option>
                        </select>
                        @error('status')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="urgency" class="mb-2 block text-sm font-medium text-neutral-900 dark:text-white">
                            Urgency
                        </label>
                        <select id="urgency" name="urgency"
                            class="w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 text-neutral-900 focus:border-neutral-500 focus:outline-none dark:border-neutral-700 dark:bg-neutral-800 dark:text-white">
                            <option value="low" @selected(old('urgency', $intake->urgency) === 'low')>Low</option>
                            <option value="normal" @selected(old('urgency', $intake->urgency) === 'normal')>Normal</option>
                            <option value="high" @selected(old('urgency', $intake->urgency) === 'high')>High</option>
                        </select>
                        @error('urgency')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="assigned_user_id"
                            class="mb-2 block text-sm font-medium text-neutral-900 dark:text-white">
                            Assigned User
                        </label>
                        <select id="assigned_user_id" name="assigned_user_id"
                            class="w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 text-neutral-900 focus:border-neutral-500 focus:outline-none dark:border-neutral-700 dark:bg-neutral-800 dark:text-white">
                            <option value="">Unassigned</option>
                            @foreach ($assignees as $assignee)
                                <option value="{{ $assignee->id }}" @selected(old('assigned_user_id', $intake->assigned_user_id) == $assignee->id)>
                                    {{ $assignee->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('assigned_user_id')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                        class="inline-flex items-center rounded-lg bg-neutral-900 px-4 py-2 text-sm font-medium text-white hover:bg-neutral-700 dark:bg-white dark:text-neutral-900 dark:hover:bg-neutral-200">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>

        <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">
                    Add Follow-Up
                </h2>
            </div>

            <form method="POST" action="{{ route('intakes.follow-ups.store', $intake) }}" class="space-y-6">
                @csrf

                <div class="grid gap-6 md:grid-cols-2">
                    <div>
                        <label for="channel" class="mb-2 block text-sm font-medium text-neutral-900 dark:text-white">
                            Channel
                        </label>
                        <select id="channel" name="channel"
                            class="w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 text-neutral-900 focus:border-neutral-500 focus:outline-none dark:border-neutral-700 dark:bg-neutral-800 dark:text-white">
                            <option value="">Select one</option>
                            <option value="call" @selected(old('channel') === 'call')>Call</option>
                            <option value="email" @selected(old('channel') === 'email')>Email</option>
                            <option value="sms" @selected(old('channel') === 'sms')>SMS</option>
                            <option value="chat" @selected(old('channel') === 'chat')>Chat</option>
                            <option value="internal_note" @selected(old('channel') === 'internal_note')>Internal Note</option>
                        </select>
                        @error('channel')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="outcome" class="mb-2 block text-sm font-medium text-neutral-900 dark:text-white">
                            Outcome
                        </label>
                        <select id="outcome" name="outcome"
                            class="w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 text-neutral-900 focus:border-neutral-500 focus:outline-none dark:border-neutral-700 dark:bg-neutral-800 dark:text-white">
                            <option value="">Select one</option>
                            <option value="no_answer" @selected(old('outcome') === 'no_answer')>No Answer</option>
                            <option value="left_voicemail" @selected(old('outcome') === 'left_voicemail')>Left Voicemail</option>
                            <option value="responded" @selected(old('outcome') === 'responded')>Responded</option>
                            <option value="appointment_booked" @selected(old('outcome') === 'appointment_booked')>Appointment Booked
                            </option>
                            <option value="not_interested" @selected(old('outcome') === 'not_interested')>Not Interested</option>
                            <option value="wrong_number" @selected(old('outcome') === 'wrong_number')>Wrong Number</option>
                            <option value="other" @selected(old('outcome') === 'other')>Other</option>
                        </select>
                        @error('outcome')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="attempted_at"
                            class="mb-2 block text-sm font-medium text-neutral-900 dark:text-white">
                            Attempted At
                        </label>
                        <input type="datetime-local" id="attempted_at" name="attempted_at"
                            value="{{ old('attempted_at', now()->format('Y-m-d\TH:i')) }}"
                            class="w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 text-neutral-900 focus:border-neutral-500 focus:outline-none dark:border-neutral-700 dark:bg-neutral-800 dark:text-white">
                        @error('attempted_at')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="next_follow_up_at"
                            class="mb-2 block text-sm font-medium text-neutral-900 dark:text-white">
                            Next Follow-Up At
                        </label>
                        <input type="datetime-local" id="next_follow_up_at" name="next_follow_up_at"
                            value="{{ old('next_follow_up_at') }}"
                            class="w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 text-neutral-900 focus:border-neutral-500 focus:outline-none dark:border-neutral-700 dark:bg-neutral-800 dark:text-white">
                        @error('next_follow_up_at')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="note" class="mb-2 block text-sm font-medium text-neutral-900 dark:text-white">
                            Note
                        </label>
                        <textarea id="note" name="note" rows="4"
                            class="w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 text-neutral-900 focus:border-neutral-500 focus:outline-none dark:border-neutral-700 dark:bg-neutral-800 dark:text-white">{{ old('note') }}</textarea>
                        @error('note')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                        class="inline-flex items-center rounded-lg bg-neutral-900 px-4 py-2 text-sm font-medium text-white hover:bg-neutral-700 dark:bg-white dark:text-neutral-900 dark:hover:bg-neutral-200">
                        Add Follow-Up
                    </button>
                </div>
            </form>
        </div>

        <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">
                    Follow-Ups
                </h2>
            </div>

            @if ($intake->followUps->isEmpty())
                <p class="text-sm text-neutral-500 dark:text-neutral-400">
                    No follow-ups yet.
                </p>
            @else
                <div class="space-y-4">
                    @foreach ($intake->followUps->sortByDesc('attempted_at') as $followUp)
                        <div class="rounded-lg border border-neutral-200 p-4 dark:border-neutral-800">
                            <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                                <div>
                                    <p class="text-sm font-medium text-neutral-900 dark:text-white">
                                        {{ str($followUp->channel)->headline() }}
                                        @if ($followUp->outcome)
                                            · {{ str($followUp->outcome)->headline() }}
                                        @endif
                                    </p>
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400">
                                        {{ $followUp->attempted_at?->format('M d, Y g:i A') ?? '—' }}
                                        @if ($followUp->user)
                                            · by {{ $followUp->user->name }}
                                        @endif
                                    </p>
                                </div>

                                @if ($followUp->next_follow_up_at)
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400">
                                        Next follow-up: {{ $followUp->next_follow_up_at->format('M d, Y g:i A') }}
                                    </p>
                                @endif
                            </div>

                            @if ($followUp->note)
                                <p class="mt-3 whitespace-pre-line text-sm text-neutral-700 dark:text-neutral-300">
                                    {{ $followUp->note }}
                                </p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-layouts::app>
