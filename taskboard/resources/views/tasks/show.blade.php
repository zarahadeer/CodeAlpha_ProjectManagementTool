<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ $task->title }}</h2>
            <a href="{{ route('projects.show', $task->project) }}"
               class="text-sm text-teal-600 dark:text-teal-400 underline">&larr; Back to board</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="rounded-md bg-green-100 text-green-800 px-4 py-2 text-sm">{{ session('status') }}</div>
            @endif

            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                <form method="POST" action="{{ route('tasks.update', $task) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="title" value="Title" />
                        <x-text-input id="title" name="title" type="text" class="mt-1 block w-full"
                            :value="old('title', $task->title)" required />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="description" value="Description" />
                        <textarea id="description" name="description" rows="5"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">{{ old('description', $task->description) }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        {{-- Status = which column --}}
                        <div>
                            <x-input-label for="board_column_id" value="Status" />
                            <select id="board_column_id" name="board_column_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                @foreach ($task->project->columns as $column)
                                    <option value="{{ $column->id }}"
                                        @selected(old('board_column_id', $task->board_column_id) == $column->id)>
                                        {{ $column->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('board_column_id')" class="mt-2" />
                        </div>

                        {{-- Assignee --}}
                        <div>
                            <x-input-label for="assigned_to" value="Assigned to" />
                            <select id="assigned_to" name="assigned_to"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                <option value="">Unassigned</option>
                                @foreach ($task->project->members as $member)
                                    <option value="{{ $member->id }}"
                                        @selected(old('assigned_to', $task->assigned_to) == $member->id)>
                                        {{ $member->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('assigned_to')" class="mt-2" />
                        </div>

                        {{-- Priority --}}
                        <div>
                            <x-input-label for="priority" value="Priority" />
                            <select id="priority" name="priority"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                @foreach (\App\Models\Task::PRIORITIES as $priority)
                                    <option value="{{ $priority }}"
                                        @selected(old('priority', $task->priority) === $priority)>
                                        {{ ucfirst($priority) }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('priority')" class="mt-2" />
                        </div>

                        {{-- Due date --}}
                        <div>
                            <x-input-label for="due_date" value="Due date" />
                            <x-text-input id="due_date" name="due_date" type="date" class="mt-1 block w-full"
                                :value="old('due_date', $task->due_date?->format('Y-m-d'))" />
                            <x-input-error :messages="$errors->get('due_date')" class="mt-2" />
                        </div>
                    </div>

                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Created by {{ $task->creator->name }} on {{ $task->created_at->format('M j, Y') }}
                    </p>

                    <x-primary-button>Save changes</x-primary-button>
                </form>
            </div>

            @can('delete', $task)
                <form method="POST" action="{{ route('tasks.destroy', $task) }}"
                      onsubmit="return confirm('Delete this task?')">
                    @csrf
                    @method('DELETE')
                    <button class="text-sm text-red-600 underline">Delete this task</button>
                </form>
            @endcan

                        {{-- Comments --}}
            <div id="comments" class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-4">
                    Comments ({{ $task->comments->count() }})
                </h3>

                <div class="space-y-4">
                    @forelse ($task->comments as $comment)
                        <div class="rounded-md bg-gray-50 dark:bg-gray-900 p-3">
                            <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                                <span>
                                    <strong class="text-gray-800 dark:text-gray-200">{{ $comment->user->name }}</strong>
                                    &middot; {{ $comment->created_at->diffForHumans() }}
                                </span>

                                @can('delete', $comment)
                                    <form method="POST" action="{{ route('comments.destroy', $comment) }}"
                                          onsubmit="return confirm('Delete this comment?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-600 underline">Delete</button>
                                    </form>
                                @endcan
                            </div>
                            <p class="mt-1 text-sm text-gray-800 dark:text-gray-200">{!! nl2br(e($comment->body)) !!}</p>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400">No comments yet. Start the conversation.</p>
                    @endforelse
                </div>

                <form method="POST" action="{{ route('comments.store', $task) }}" class="mt-4">
                    @csrf
                    <textarea name="body" rows="3" required maxlength="2000" placeholder="Write a comment..."
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">{{ old('body') }}</textarea>
                    <x-input-error :messages="$errors->get('body')" class="mt-2" />
                    <x-primary-button class="mt-2">Post comment</x-primary-button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>