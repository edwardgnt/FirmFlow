<x-layouts::app :title="__('Create Intake')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-neutral-900 dark:text-white">
                    Create Intake
                </h1>
                <p class="text-sm text-neutral-600 dark:text-neutral-400">
                    Add a new intake record for your organization.
                </p>
            </div>

            <a
                href="{{ route('intakes.index') }}"
                class="inline-flex items-center rounded-lg border border-neutral-300 px-4 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-100 dark:border-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-800"
            >
                Back to Intakes
            </a>
        </div>

        <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
            <form method="POST" action="{{ route('intakes.store') }}" class="space-y-6">
                @csrf

                <div class="grid gap-6 md:grid-cols-2">
                    <div>
                        <label for="first_name" class="mb-2 block text-sm font-medium text-neutral-900 dark:text-white">
                            First Name
                        </label>
                        <input
                            type="text"
                            id="first_name"
                            name="first_name"
                            value="{{ old('first_name') }}"
                            class="w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 text-neutral-900 focus:border-neutral-500 focus:outline-none dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                        >
                        @error('first_name')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="last_name" class="mb-2 block text-sm font-medium text-neutral-900 dark:text-white">
                            Last Name
                        </label>
                        <input
                            type="text"
                            id="last_name"
                            name="last_name"
                            value="{{ old('last_name') }}"
                            class="w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 text-neutral-900 focus:border-neutral-500 focus:outline-none dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                        >
                        @error('last_name')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="mb-2 block text-sm font-medium text-neutral-900 dark:text-white">
                            Email
                        </label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            class="w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 text-neutral-900 focus:border-neutral-500 focus:outline-none dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                        >
                        @error('email')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone" class="mb-2 block text-sm font-medium text-neutral-900 dark:text-white">
                            Phone
                        </label>
                        <input
                            type="text"
                            id="phone"
                            name="phone"
                            value="{{ old('phone') }}"
                            class="w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 text-neutral-900 focus:border-neutral-500 focus:outline-none dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                        >
                        @error('phone')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="preferred_contact_method" class="mb-2 block text-sm font-medium text-neutral-900 dark:text-white">
                            Preferred Contact Method
                        </label>
                        <select
                            id="preferred_contact_method"
                            name="preferred_contact_method"
                            class="w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 text-neutral-900 focus:border-neutral-500 focus:outline-none dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                        >
                            <option value="">Select one</option>
                            <option value="call" @selected(old('preferred_contact_method') === 'call')>Call</option>
                            <option value="email" @selected(old('preferred_contact_method') === 'email')>Email</option>
                            <option value="sms" @selected(old('preferred_contact_method') === 'sms')>SMS</option>
                        </select>
                        @error('preferred_contact_method')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="source" class="mb-2 block text-sm font-medium text-neutral-900 dark:text-white">
                            Source
                        </label>
                        <input
                            type="text"
                            id="source"
                            name="source"
                            value="{{ old('source') }}"
                            placeholder="website, referral, call, chat..."
                            class="w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 text-neutral-900 focus:border-neutral-500 focus:outline-none dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                        >
                        @error('source')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="status" class="mb-2 block text-sm font-medium text-neutral-900 dark:text-white">
                            Status
                        </label>
                        <select
                            id="status"
                            name="status"
                            class="w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 text-neutral-900 focus:border-neutral-500 focus:outline-none dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                        >
                            <option value="new" @selected(old('status', 'new') === 'new')>New</option>
                            <option value="contacted" @selected(old('status') === 'contacted')>Contacted</option>
                            <option value="qualified" @selected(old('status') === 'qualified')>Qualified</option>
                            <option value="appointment_set" @selected(old('status') === 'appointment_set')>Appointment Set</option>
                            <option value="won" @selected(old('status') === 'won')>Won</option>
                            <option value="lost" @selected(old('status') === 'lost')>Lost</option>
                        </select>
                        @error('status')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="urgency" class="mb-2 block text-sm font-medium text-neutral-900 dark:text-white">
                            Urgency
                        </label>
                        <select
                            id="urgency"
                            name="urgency"
                            class="w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 text-neutral-900 focus:border-neutral-500 focus:outline-none dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                        >
                            <option value="low" @selected(old('urgency') === 'low')>Low</option>
                            <option value="normal" @selected(old('urgency', 'normal') === 'normal')>Normal</option>
                            <option value="high" @selected(old('urgency') === 'high')>High</option>
                        </select>
                        @error('urgency')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="assigned_user_id" class="mb-2 block text-sm font-medium text-neutral-900 dark:text-white">
                            Assign To
                        </label>
                        <select
                            id="assigned_user_id"
                            name="assigned_user_id"
                            class="w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 text-neutral-900 focus:border-neutral-500 focus:outline-none dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                        >
                            <option value="">Unassigned</option>
                            @foreach ($assignees as $assignee)
                                <option value="{{ $assignee->id }}" @selected(old('assigned_user_id') == $assignee->id)>
                                    {{ $assignee->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('assigned_user_id')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="summary" class="mb-2 block text-sm font-medium text-neutral-900 dark:text-white">
                            Summary
                        </label>
                        <input
                            type="text"
                            id="summary"
                            name="summary"
                            value="{{ old('summary') }}"
                            class="w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 text-neutral-900 focus:border-neutral-500 focus:outline-none dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                        >
                        @error('summary')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="details" class="mb-2 block text-sm font-medium text-neutral-900 dark:text-white">
                            Details
                        </label>
                        <textarea
                            id="details"
                            name="details"
                            rows="5"
                            class="w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 text-neutral-900 focus:border-neutral-500 focus:outline-none dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                        >{{ old('details') }}</textarea>
                        @error('details')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <a
                        href="{{ route('intakes.index') }}"
                        class="inline-flex items-center rounded-lg border border-neutral-300 px-4 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-100 dark:border-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-800"
                    >
                        Cancel
                    </a>

                    <button
                        type="submit"
                        class="inline-flex items-center rounded-lg bg-neutral-900 px-4 py-2 text-sm font-medium text-white hover:bg-neutral-700 dark:bg-white dark:text-neutral-900 dark:hover:bg-neutral-200"
                    >
                        Create Intake
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts::app>