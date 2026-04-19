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

        {{-- Filters --}}
        <form method="GET" action="{{ route('follow-ups.queue') }}">
            <div
                class="grid gap-4 rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900 md:grid-cols-5">
                <div>
                    <label for="assigned_user_id" class="mb-2 block text-sm font-medium text-neutral-900 dark:text-white">
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
            @if ($followUps->isEmpty())
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
                                    Channel
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                                    Outcome
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                                    Due
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                                    Days Overdue
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                                    Status
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-200 dark:divide-neutral-800">
                            @foreach ($followUps as $followUp)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-neutral-900 dark:text-white">
                                        {{ $followUp->intake->contact->first_name }}
                                        {{ $followUp->intake->contact->last_name }}
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        <a href="{{ route('intakes.show', $followUp->intake) }}"
                                            class="font-medium text-neutral-900 hover:underline dark:text-white">
                                            {{ $followUp->intake->summary }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-neutral-900 dark:text-white">
                                        {{ $followUp->intake->assignedUser->name ?? 'Unassigned' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-neutral-900 dark:text-white">
                                        {{ str($followUp->channel)->headline() }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-neutral-900 dark:text-white">
                                        {{ $followUp->outcome ? str($followUp->outcome)->headline() : '—' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-neutral-900 dark:text-white">
                                        {{ $followUp->next_follow_up_at?->format('M d, Y g:i A') ?? '—' }}
                                    </td>

                                    <td class="px-4 py-3 text-sm">
                                        @php
                                            $daysOverdue = $followUp->next_follow_up_at
                                                ? (int) floor($followUp->next_follow_up_at->diffInDays(now()))
                                                : 0;
                                        @endphp

                                        <span
                                            class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-1 text-xs font-medium text-amber-800 dark:bg-amber-900/40 dark:text-amber-300">
                                            {{ $daysOverdue > 0 ? $daysOverdue . ' ' . ($daysOverdue === 1 ? 'day' : 'days') : 'Due today' }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-3 text-sm">
                                        <span
                                            class="inline-flex items-center rounded-full bg-rose-100 px-2.5 py-1 text-xs font-medium text-rose-800 dark:bg-rose-900/40 dark:text-rose-300">
                                            Overdue
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        <a href="{{ route('intakes.show', $followUp->intake) }}"
                                            class="inline-flex items-center rounded-lg border border-neutral-300 px-3 py-1.5 text-sm font-medium text-neutral-700 hover:bg-neutral-100 dark:border-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-800">
                                            Open Intake
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $followUps->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts::app>
