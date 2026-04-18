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

            <a
                href="{{ route('intakes.create') }}"
                class="inline-flex items-center rounded-lg bg-neutral-900 px-4 py-2 text-sm font-medium text-white hover:bg-neutral-700 dark:bg-white dark:text-neutral-900 dark:hover:bg-neutral-200"
            >
                New Intake
            </a>
        </div>

        @if (session('status'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-900 dark:bg-emerald-950/40 dark:text-emerald-300">
                {{ session('status') }}
            </div>
        @endif

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
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                                    Contact
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                                    Summary
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                                    Status
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                                    Source
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                                    Assigned To
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
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
                                        <a
                                            href="{{ route('intakes.show', $intake) }}"
                                            class="font-medium text-neutral-900 hover:underline dark:text-white"
                                        >
                                            {{ $intake->summary }}
                                        </a>
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