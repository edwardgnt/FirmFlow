<x-layouts::app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
        <div>
            <h1 class="text-2xl font-semibold text-neutral-900 dark:text-white">
                FirmFlow Dashboard
            </h1>
            <p class="text-sm text-neutral-600 dark:text-neutral-400">
                Overview of activity for your organization.
            </p>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">
                        Overdue Follow-Ups
                    </h2>
                </div>

                @if ($overdueFollowUps->isEmpty())
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">
                        No overdue follow-ups right now.
                    </p>
                @else
                    <div class="space-y-4">
                        @foreach ($overdueFollowUps as $followUp)
                            <a href="{{ route('intakes.show', $followUp->intake) }}"
                                class="block rounded-lg border border-neutral-200 p-4 transition hover:bg-neutral-50 dark:border-neutral-800 dark:hover:bg-neutral-800/50">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <p class="text-sm font-medium text-neutral-900 dark:text-white">
                                            {{ $followUp->intake->summary }}
                                        </p>
                                        <p class="mt-1 text-sm text-neutral-600 dark:text-neutral-400">
                                            {{ $followUp->intake->contact->first_name }}
                                            {{ $followUp->intake->contact->last_name }}
                                        </p>
                                        <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-400">
                                            Due {{ $followUp->next_follow_up_at->format('M d, Y g:i A') }}
                                        </p>
                                    </div>

                                    <span
                                        class="inline-flex items-center rounded-full bg-rose-100 px-2.5 py-1 text-xs font-medium text-rose-800 dark:bg-rose-900/40 dark:text-rose-300">
                                        Overdue
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">
                        Needs Attention
                    </h2>
                </div>

                @if ($needsAttentionIntakes->isEmpty())
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">
                        Nothing urgent right now.
                    </p>
                @else
                    <div class="space-y-4">
                        @foreach ($needsAttentionIntakes as $intake)
                            <a href="{{ route('intakes.show', $intake) }}"
                                class="block rounded-lg border border-neutral-200 p-4 transition hover:bg-neutral-50 dark:border-neutral-800 dark:hover:bg-neutral-800/50">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <p class="text-sm font-medium text-neutral-900 dark:text-white">
                                            {{ $intake->summary }}
                                        </p>
                                        <p class="mt-1 text-sm text-neutral-600 dark:text-neutral-400">
                                            {{ $intake->contact->first_name }} {{ $intake->contact->last_name }}
                                        </p>
                                        <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-400">
                                            Last activity:
                                            {{ $intake->last_activity_at?->format('M d, Y g:i A') ?? '—' }}
                                        </p>
                                    </div>

                                    <div class="flex flex-col items-end gap-2">
                                        @if (!$intake->assigned_user_id)
                                            <span
                                                class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-1 text-xs font-medium text-amber-800 dark:bg-amber-900/40 dark:text-amber-300">
                                                Unassigned
                                            </span>
                                        @endif

                                        @if ($intake->last_activity_at && $intake->last_activity_at->lte(now()->subDays(2)))
                                            <span
                                                class="inline-flex items-center rounded-full bg-rose-100 px-2.5 py-1 text-xs font-medium text-rose-800 dark:bg-rose-900/40 dark:text-rose-300">
                                                Stale
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
            <div class="rounded-xl border border-neutral-200 bg-white p-5 dark:border-neutral-700 dark:bg-neutral-900">
                <p class="text-sm text-neutral-500 dark:text-neutral-400">Total Contacts</p>
                <p class="mt-2 text-3xl font-semibold text-neutral-900 dark:text-white">
                    {{ $totalContacts }}
                </p>
            </div>

            <div class="rounded-xl border border-neutral-200 bg-white p-5 dark:border-neutral-700 dark:bg-neutral-900">
                <p class="text-sm text-neutral-500 dark:text-neutral-400">Total Intakes</p>
                <p class="mt-2 text-3xl font-semibold text-neutral-900 dark:text-white">
                    {{ $totalIntakes }}
                </p>
            </div>

            <div class="rounded-xl border border-neutral-200 bg-white p-5 dark:border-neutral-700 dark:bg-neutral-900">
                <p class="text-sm text-neutral-500 dark:text-neutral-400">New Intakes</p>
                <p class="mt-2 text-3xl font-semibold text-neutral-900 dark:text-white">
                    {{ $newIntakes }}
                </p>
            </div>

            <div class="rounded-xl border border-neutral-200 bg-white p-5 dark:border-neutral-700 dark:bg-neutral-900">
                <p class="text-sm text-neutral-500 dark:text-neutral-400">Contacted</p>
                <p class="mt-2 text-3xl font-semibold text-neutral-900 dark:text-white">
                    {{ $contactedIntakes }}
                </p>
            </div>

            <div class="rounded-xl border border-neutral-200 bg-white p-5 dark:border-neutral-700 dark:bg-neutral-900">
                <p class="text-sm text-neutral-500 dark:text-neutral-400">Follow-Ups</p>
                <p class="mt-2 text-3xl font-semibold text-neutral-900 dark:text-white">
                    {{ $totalFollowUps }}
                </p>
            </div>
        </div>

        <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">
                    Recent Intakes
                </h2>
            </div>

            @if ($recentIntakes->isEmpty())
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
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-200 dark:divide-neutral-800">
                            @foreach ($recentIntakes as $intake)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-neutral-900 dark:text-white">
                                        {{ $intake->contact->first_name }} {{ $intake->contact->last_name }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-neutral-900 dark:text-white">
                                        {{ $intake->summary }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-neutral-900 dark:text-white">
                                        {{ str($intake->status)->headline() }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-neutral-900 dark:text-white">
                                        {{ $intake->source ?? '—' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-neutral-900 dark:text-white">
                                        {{ $intake->assignedUser->name ?? 'Unassigned' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-layouts::app>
