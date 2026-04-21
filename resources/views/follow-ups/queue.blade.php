<x-layouts::app :title="__('Follow-Up Queue')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
        <div>
            <h1 class="text-2xl font-semibold text-neutral-900 dark:text-white">
                Follow-Up Queue
            </h1>
            <p class="text-sm text-neutral-600 dark:text-neutral-400">
                Overdue follow-ups that need attention.
            </p>
        </div>

        @if (session('status'))
            <div x-data="{ show: true }" x-show="show" x-transition
                class="flex items-start justify-between gap-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-900 dark:bg-emerald-950/40 dark:text-emerald-300"
                role="alert">
                <span>{{ session('status') }}</span>

                <button type="button" @click="show = false"
                    class="shrink-0 rounded-md px-2 py-1 text-base font-semibold leading-none text-emerald-700 hover:bg-emerald-100 dark:text-emerald-300 dark:hover:bg-emerald-900/40"
                    aria-label="Dismiss success message">
                    ×
                </button>
            </div>
        @endif

        <form method="GET" action="{{ route('follow-ups.queue') }}">
            <div
                class="grid gap-4 rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900 md:grid-cols-6">
                <div>
                    <label for="assigned_user_id"
                        class="mb-2 block text-sm font-medium text-neutral-900 dark:text-white">
                        Assigned User
                    </label>
                    <select id="assigned_user_id" name="assigned_user_id"
                        class="w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 text-neutral-900 focus:border-neutral-500 focus:outline-none dark:border-neutral-700 dark:bg-neutral-800 dark:text-white">
                        <option value="">All Assignees</option>
                        <option value="unassigned" @selected($assignedUserId === 'unassigned')>Unassigned</option>
                        @foreach ($assignees as $assignee)
                            <option value="{{ $assignee->id }}" @selected($assignedUserId == $assignee->id)>
                                {{ $assignee->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="status" class="mb-2 block text-sm font-medium text-neutral-900 dark:text-white">
                        Intake Status
                    </label>
                    <select id="status" name="status"
                        class="w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 text-neutral-900 focus:border-neutral-500 focus:outline-none dark:border-neutral-700 dark:bg-neutral-800 dark:text-white">
                        <option value="">All Statuses</option>
                        <option value="new" @selected($status === 'new')>New</option>
                        <option value="contacted" @selected($status === 'contacted')>Contacted</option>
                        <option value="qualified" @selected($status === 'qualified')>Qualified</option>
                        <option value="appointment_set" @selected($status === 'appointment_set')>Appointment Set</option>
                        <option value="won" @selected($status === 'won')>Won</option>
                        <option value="lost" @selected($status === 'lost')>Lost</option>
                    </select>
                </div>

                <div>
                    <label for="source" class="mb-2 block text-sm font-medium text-neutral-900 dark:text-white">
                        Source
                    </label>
                    <select id="source" name="source"
                        class="w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 text-neutral-900 focus:border-neutral-500 focus:outline-none dark:border-neutral-700 dark:bg-neutral-800 dark:text-white">
                        <option value="">All Sources</option>
                        @foreach ($sources as $sourceOption)
                            <option value="{{ $sourceOption }}" @selected($source === $sourceOption)>
                                {{ str($sourceOption)->headline() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="sort" class="mb-2 block text-sm font-medium text-neutral-900 dark:text-white">
                        Sort By
                    </label>
                    <select id="sort" name="sort"
                        class="w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 text-neutral-900 focus:border-neutral-500 focus:outline-none dark:border-neutral-700 dark:bg-neutral-800 dark:text-white">
                        <option value="oldest_due" @selected(($sort ?? 'oldest_due') === 'oldest_due')>
                            Oldest Due First
                        </option>
                        <option value="newest_due" @selected(($sort ?? '') === 'newest_due')>
                            Newest Due First
                        </option>
                    </select>
                </div>

                <div class="flex items-end gap-3 md:col-span-2">
                    <button type="submit"
                        class="inline-flex items-center rounded-lg bg-neutral-900 px-4 py-2 text-sm font-medium text-white hover:bg-neutral-700 dark:bg-white dark:text-neutral-900 dark:hover:bg-neutral-200">
                        Apply Filters
                    </button>

                    <a href="{{ route('follow-ups.queue') }}"
                        class="inline-flex items-center rounded-lg border border-neutral-300 px-4 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-100 dark:border-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-800">
                        Reset
                    </a>
                </div>
            </div>
        </form>

        <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
            @if ($intakes->isEmpty())
                <p class="text-sm text-neutral-500 dark:text-neutral-400">
                    No overdue follow-ups right now.
                </p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700">
                        <thead>
                            <tr>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                                    Contact
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                                    Intake
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                                    Assigned To
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                                    Source
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                                    Intake Status
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                                    Oldest Due
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                                    Days Overdue
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                                    Overdue Count
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                                    Queue Status
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                                    Reassign
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                                    Action
                                </th>
                            </tr>
                        </thead>

                        @foreach ($intakes as $intake)
                            @php
                                $intakeStatusClasses = match ($intake->status) {
                                    'new' => 'bg-sky-100 text-sky-800 dark:bg-sky-900/40 dark:text-sky-300',
                                    'contacted'
                                        => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300',
                                    'qualified'
                                        => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300',
                                    'appointment_set'
                                        => 'bg-violet-100 text-violet-800 dark:bg-violet-900/40 dark:text-violet-300',
                                    'won' => 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300',
                                    'lost' => 'bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300',
                                    default
                                        => 'bg-neutral-100 text-neutral-800 dark:bg-neutral-800 dark:text-neutral-300',
                                };

                                $sourceClasses = match ($intake->source) {
                                    'website' => 'bg-sky-100 text-sky-800 dark:bg-sky-900/40 dark:text-sky-300',
                                    'referral'
                                        => 'bg-fuchsia-100 text-fuchsia-800 dark:bg-fuchsia-900/40 dark:text-fuchsia-300',
                                    'call'
                                        => 'bg-orange-100 text-orange-800 dark:bg-orange-900/40 dark:text-orange-300',
                                    'chat' => 'bg-teal-100 text-teal-800 dark:bg-teal-900/40 dark:text-teal-300',
                                    default
                                        => 'bg-neutral-100 text-neutral-800 dark:bg-neutral-800 dark:text-neutral-300',
                                };

                                $oldestDue = $intake->oldest_overdue_at
                                    ? \Illuminate\Support\Carbon::parse($intake->oldest_overdue_at)
                                    : null;

                                $daysOverdue = $oldestDue ? (int) floor($oldestDue->diffInDays(now())) : 0;
                            @endphp

                            <tbody x-data="{ open: false }" class="divide-y divide-neutral-200 dark:divide-neutral-800">
                                <tr>
                                    <td class="px-4 py-4 text-sm text-neutral-900 dark:text-white">
                                        {{ $intake->contact->first_name }} {{ $intake->contact->last_name }}
                                    </td>

                                    <td class="px-4 py-4 text-sm">
                                        <a href="{{ route('intakes.show', $intake) }}"
                                            class="font-medium text-neutral-900 hover:underline dark:text-white">
                                            {{ $intake->summary }}
                                        </a>
                                    </td>

                                    <td class="px-4 py-4 text-sm text-neutral-900 dark:text-white">
                                        {{ $intake->assignedUser->name ?? 'Unassigned' }}
                                    </td>

                                    <td class="px-4 py-4 text-sm">
                                        <span
                                            class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium whitespace-nowrap {{ $sourceClasses }}">
                                            {{ $intake->source ? str($intake->source)->headline() : '—' }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-4 text-sm">
                                        <span
                                            class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium whitespace-nowrap {{ $intakeStatusClasses }}">
                                            {{ str($intake->status)->headline() }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-4 text-sm text-neutral-900 dark:text-white">
                                        {{ $oldestDue?->format('M d, Y g:i A') ?? '—' }}
                                    </td>

                                    <td class="px-4 py-4 text-sm">
                                        <span
                                            class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-1 text-xs font-medium text-amber-800 whitespace-nowrap dark:bg-amber-900/40 dark:text-amber-300">
                                            {{ $daysOverdue > 0 ? $daysOverdue . ' ' . ($daysOverdue === 1 ? 'day' : 'days') : 'Due today' }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-4 text-sm text-neutral-900 dark:text-white">
                                        {{ $intake->overdue_follow_ups_count }}
                                    </td>

                                    <td class="px-4 py-4 text-sm">
                                        <span
                                            class="inline-flex items-center rounded-full bg-rose-100 px-2.5 py-1 text-xs font-medium text-rose-800 whitespace-nowrap dark:bg-rose-900/40 dark:text-rose-300">
                                            Overdue
                                        </span>
                                    </td>

                                    <td class="px-4 py-4 text-sm">
                                        <form method="POST" action="{{ route('follow-ups.queue.reassign', $intake) }}"
                                            x-data="{
                                                original: '{{ (string) ($intake->assigned_user_id ?? '') }}',
                                                current: '{{ (string) ($intake->assigned_user_id ?? '') }}'
                                            }">
                                            @csrf
                                            @method('PATCH')

                                            <div class="flex items-center gap-2">
                                                <select name="assigned_user_id" x-model="current"
                                                    class="min-w-42 rounded-lg border border-neutral-300 bg-white px-3 py-2 text-sm text-neutral-900 focus:border-neutral-500 focus:outline-none dark:border-neutral-700 dark:bg-neutral-800 dark:text-white">
                                                    <option value="">Unassigned</option>
                                                    @foreach ($assignees as $assignee)
                                                        <option value="{{ $assignee->id }}">
                                                            {{ $assignee->name }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                                <button type="submit" x-show="current !== original"
                                                    x-transition.opacity.scale.80 x-cloak
                                                    class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-emerald-200 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/30 dark:border-emerald-900 dark:bg-emerald-950/40 dark:text-emerald-300 dark:hover:bg-emerald-900/50"
                                                    title="Save reassignment" aria-label="Save reassignment">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                        viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd"
                                                            d="M16.704 5.29a1 1 0 010 1.42l-7.25 7.25a1 1 0 01-1.415 0l-3-3a1 1 0 111.414-1.42l2.293 2.294 6.543-6.544a1 1 0 011.415 0z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </form>
                                    </td>

                                    <td class="px-4 py-4 text-sm">
                                        <div class="flex flex-col gap-2">
                                            <button type="button" @click="open = !open"
                                                class="inline-flex items-center justify-center rounded-lg border border-neutral-300 px-3 py-1.5 text-sm font-medium text-neutral-700 hover:bg-neutral-100 dark:border-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-800">
                                                Log Follow-Up
                                            </button>

                                            <a href="{{ route('intakes.show', $intake) }}"
                                                class="inline-flex items-center justify-center rounded-lg border border-neutral-300 px-3 py-1.5 text-sm font-medium text-neutral-700 hover:bg-neutral-100 dark:border-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-800">
                                                Open Intake
                                            </a>
                                        </div>
                                    </td>
                                </tr>

                                <tr x-show="open" x-transition x-cloak>
                                    <td colspan="11" class="px-4 py-4">
                                        <div
                                            class="rounded-xl border border-neutral-200 bg-neutral-50 p-4 dark:border-neutral-800 dark:bg-neutral-900/40">
                                            <form method="POST"
                                                action="{{ route('follow-ups.queue.log', $intake) }}"
                                                class="grid gap-4 md:grid-cols-5">
                                                @csrf

                                                <div>
                                                    <label
                                                        class="mb-2 block text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                                                        Channel
                                                    </label>
                                                    <select name="channel" required
                                                        class="w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 text-sm text-neutral-900 focus:border-neutral-500 focus:outline-none dark:border-neutral-700 dark:bg-neutral-800 dark:text-white">
                                                        <option value="">Select one</option>
                                                        <option value="call" @selected(old('channel') === 'call')>Call
                                                        </option>
                                                        <option value="email" @selected(old('channel') === 'email')>Email
                                                        </option>
                                                        <option value="sms" @selected(old('channel') === 'sms')>SMS
                                                        </option>
                                                        <option value="chat" @selected(old('channel') === 'chat')>Chat
                                                        </option>
                                                        <option value="internal_note" @selected(old('channel') === 'internal_note')>
                                                            Internal Note</option>
                                                    </select>
                                                    @error('channel')
                                                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                                                    @enderror
                                                </div>

                                                <div>
                                                    <label
                                                        class="mb-2 block text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                                                        Outcome
                                                    </label>
                                                    <select name="outcome" required
                                                        class="w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 text-sm text-neutral-900 focus:border-neutral-500 focus:outline-none dark:border-neutral-700 dark:bg-neutral-800 dark:text-white">
                                                        <option value="">Select one</option>
                                                        <option value="no_answer" @selected(old('outcome') === 'no_answer')>No
                                                            Answer</option>
                                                        <option value="left_voicemail" @selected(old('outcome') === 'left_voicemail')>
                                                            Left Voicemail</option>
                                                        <option value="responded" @selected(old('outcome') === 'responded')>
                                                            Responded</option>
                                                        <option value="appointment_booked"
                                                            @selected(old('outcome') === 'appointment_booked')>Appointment Booked</option>
                                                        <option value="not_interested" @selected(old('outcome') === 'not_interested')>
                                                            Not Interested</option>
                                                        <option value="wrong_number" @selected(old('outcome') === 'wrong_number')>
                                                            Wrong Number</option>
                                                        <option value="other" @selected(old('outcome') === 'other')>Other
                                                        </option>
                                                    </select>
                                                    @error('outcome')
                                                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                                                    @enderror
                                                </div>

                                                <div>
                                                    <label
                                                        class="mb-2 block text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                                                        Attempted At
                                                    </label>
                                                    <input type="datetime-local" name="attempted_at" required
                                                        value="{{ old('attempted_at', now()->format('Y-m-d\TH:i')) }}"
                                                        class="w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 text-sm text-neutral-900 focus:border-neutral-500 focus:outline-none dark:border-neutral-700 dark:bg-neutral-800 dark:text-white">
                                                    @error('attempted_at')
                                                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                                                    @enderror
                                                </div>

                                                <div>
                                                    <label
                                                        class="mb-2 block text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                                                        Next Follow-Up At
                                                    </label>
                                                    <input type="datetime-local" name="next_follow_up_at"
                                                        value="{{ old('next_follow_up_at') }}"
                                                        class="w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 text-sm text-neutral-900 focus:border-neutral-500 focus:outline-none dark:border-neutral-700 dark:bg-neutral-800 dark:text-white">
                                                </div>

                                                <div class="md:col-span-5">
                                                    <label
                                                        class="mb-2 block text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                                                        Note
                                                    </label>
                                                    <textarea name="note" rows="3"
                                                        class="w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 text-sm text-neutral-900 focus:border-neutral-500 focus:outline-none dark:border-neutral-700 dark:bg-neutral-800 dark:text-white">{{ old('note') }}</textarea>
                                                </div>

                                                <div class="md:col-span-5 flex items-center justify-end gap-3">
                                                    <button type="button" @click="open = false"
                                                        class="inline-flex items-center rounded-lg border border-neutral-300 px-4 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-100 dark:border-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-800">
                                                        Cancel
                                                    </button>

                                                    <button type="submit"
                                                        class="inline-flex items-center rounded-lg bg-neutral-900 px-4 py-2 text-sm font-medium text-white hover:bg-neutral-700 dark:bg-white dark:text-neutral-900 dark:hover:bg-neutral-200">
                                                        Save Follow-Up
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        @endforeach
                    </table>
                </div>

                <div class="mt-6">
                    {{ $intakes->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts::app>
