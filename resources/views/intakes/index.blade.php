<x-layouts::app :title="__('Intakes')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-neutral-900 dark:text-white">
                    Intakes
                </h1>
                <p class="text-sm text-neutral-600 dark:text-neutral-400">
                    All intake records for your organization.
                </p>
            </div>

            <a href="{{ route('intakes.create') }}"
                class="inline-flex items-center rounded-lg bg-neutral-900 px-4 py-2 text-sm font-medium text-white hover:bg-neutral-700 dark:bg-white dark:text-neutral-900 dark:hover:bg-neutral-200">
                New Intake
            </a>
        </div>

        @if (session('status'))
            <div
                class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-900 dark:bg-emerald-950/40 dark:text-emerald-300">
                {{ session('status') }}
            </div>
        @endif

        <form method="GET" action="{{ route('intakes.index') }}">
            <div
                class="grid gap-4 rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900 md:grid-cols-4">
                <div>
                    <label for="status" class="mb-2 block text-sm font-medium text-neutral-900 dark:text-white">
                        Status
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
                                {{ $sourceOption }}
                            </option>
                        @endforeach
                    </select>
                </div>

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

                <div class="flex items-end gap-3">
                    <button type="submit"
                        class="inline-flex items-center rounded-lg bg-neutral-900 px-4 py-2 text-sm font-medium text-white hover:bg-neutral-700 dark:bg-white dark:text-neutral-900 dark:hover:bg-neutral-200">
                        Apply Filters
                    </button>

                    <a href="{{ route('intakes.index') }}"
                        class="inline-flex items-center rounded-lg border border-neutral-300 px-4 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-100 dark:border-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-800">
                        Reset
                    </a>
                </div>
            </div>
        </form>

        <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
            @if ($intakes->isEmpty())
                <p class="text-sm text-neutral-500 dark:text-neutral-400">
                    No intakes found yet.
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
                                    Summary
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                                    Status
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                                    Source
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                                    Assigned To
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                                    Received
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-200 dark:divide-neutral-800">
                            @foreach ($intakes as $intake)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-neutral-900 dark:text-white">
                                        {{ $intake->contact->first_name }} {{ $intake->contact->last_name }}
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        <a href="{{ route('intakes.show', $intake) }}"
                                            class="font-medium text-neutral-900 hover:underline dark:text-white">
                                            {{ $intake->summary }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        @php
                                            $statusClasses = match ($intake->status) {
                                                'new' => 'bg-sky-100 text-sky-800 dark:bg-sky-900/40 dark:text-sky-300',
                                                'contacted'
                                                    => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300',
                                                'qualified'
                                                    => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300',
                                                'appointment_set'
                                                    => 'bg-violet-100 text-violet-800 dark:bg-violet-900/40 dark:text-violet-300',
                                                'won'
                                                    => 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300',
                                                'lost'
                                                    => 'bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300',
                                                default
                                                    => 'bg-neutral-100 text-neutral-800 dark:bg-neutral-800 dark:text-neutral-300',
                                            };
                                        @endphp

                                        <span
                                            class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $statusClasses }}">
                                            {{ str($intake->status)->headline() }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-neutral-900 dark:text-white">
                                        {{ $intake->source ? str($intake->source)->headline() : '—' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-neutral-900 dark:text-white">
                                        {{ $intake->assignedUser->name ?? 'Unassigned' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-neutral-900 dark:text-white">
                                        {{ $intake->received_at?->format('M d, Y g:i A') ?? '—' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $intakes->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts::app>
