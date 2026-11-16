<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Members &ndash; {{ $project->title }}
            </h2>
            <a href="{{ route('projects.show', $project) }}"
               class="text-sm text-teal-600 dark:text-teal-400 underline">&larr; Back to board</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="rounded-md bg-green-100 text-green-800 px-4 py-2 text-sm">{{ session('status') }}</div>
            @endif

            @if ($errors->has('role') || $errors->has('member'))
                <div class="rounded-md bg-red-100 text-red-800 px-4 py-2 text-sm">{{ $errors->first() }}</div>
            @endif

            @can('update', $project)
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                    <h3 class="font-semibold text-gray-900 dark:text-gray-100">Add a member</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">The person must already have an account.</p>

                    <form method="POST" action="{{ route('members.store', $project) }}" class="mt-3 flex gap-3">
                        @csrf
                        <x-text-input name="email" type="email" class="block w-full"
                            placeholder="their-email@example.com" :value="old('email')" required />
                        <x-primary-button>Add</x-primary-button>
                    </form>
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>
            @endcan

            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">
                    Team ({{ $project->members->count() }})
                </h3>

                <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach ($project->members as $member)
                        <li class="flex items-center justify-between py-3">
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $member->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $member->email }}</p>
                            </div>

                            <div class="flex items-center gap-3">
                                @if ($member->id === $project->owner_id)
                                    <span class="text-xs uppercase text-gray-500 dark:text-gray-400">owner</span>
                                @else
                                    @can('manageRoles', $project)
                                        <form method="POST" action="{{ route('members.update', [$project, $member]) }}">
                                            @csrf
                                            @method('PUT')
                                            <select name="role" onchange="this.form.submit()"
                                                class="rounded-md border-gray-300 text-sm shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                                <option value="member" @selected($member->pivot->role === 'member')>Member</option>
                                                <option value="admin" @selected($member->pivot->role === 'admin')>Admin</option>
                                            </select>
                                        </form>
                                    @else
                                        <span class="text-xs uppercase text-gray-500 dark:text-gray-400">{{ $member->pivot->role }}</span>
                                    @endcan

                                    @can('update', $project)
                                        <form method="POST" action="{{ route('members.destroy', [$project, $member]) }}"
                                              onsubmit="return confirm('Remove {{ $member->name }} from this project?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-sm text-red-600 underline">Remove</button>
                                        </form>
                                    @endcan
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>