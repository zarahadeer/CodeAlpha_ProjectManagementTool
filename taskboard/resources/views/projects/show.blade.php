<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <a href="{{ route('dashboard') }}" class="text-xs font-medium text-slate-500 hover:text-teal-700">&larr; All projects</a>
                <h2 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">{{ $project->title }}</h2>
                @if ($project->description)
                    <p class="mt-1 max-w-2xl text-sm text-slate-500">{{ $project->description }}</p>
                @endif
            </div>

            <div class="flex items-center gap-4">
                {{-- Member avatars --}}
                <a href="{{ route('members.index', $project) }}" class="flex items-center gap-3" title="Manage members">
                    <div class="flex -space-x-2">
                        @foreach ($project->members->take(5) as $member)
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-teal-600 text-xs font-semibold text-white ring-2 ring-white"
                                  title="{{ $member->name }}">
                                {{ strtoupper(mb_substr($member->name, 0, 1)) }}
                            </span>
                        @endforeach
                        @if ($project->members->count() > 5)
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-slate-200 text-xs font-semibold text-slate-600 ring-2 ring-white">
                                +{{ $project->members->count() - 5 }}
                            </span>
                        @endif
                    </div>
                    <span class="text-sm font-medium text-teal-700 hover:underline">Members</span>
                </a>

                @can('update', $project)
                    <a href="{{ route('projects.edit', $project) }}"
                       class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-sm font-medium text-slate-700 hover:border-teal-500 hover:text-teal-700">
                        Edit
                    </a>
                @endcan

                @can('delete', $project)
                    <form method="POST" action="{{ route('projects.destroy', $project) }}"
                          onsubmit="return confirm('Delete this project and all its tasks?')">
                        @csrf
                        @method('DELETE')
                        <button class="text-sm font-medium text-red-600 hover:underline">Delete</button>
                    </form>
                @endcan
            </div>
        </div>
    </x-slot>

    @php
        $accents = ['border-slate-400', 'border-sky-400', 'border-violet-400', 'border-teal-500'];
        $priorityStyles = [
            'high' => 'bg-red-100 text-red-700',
            'medium' => 'bg-amber-100 text-amber-700',
            'low' => 'bg-slate-200 text-slate-600',
        ];
    @endphp

    <div class="py-6">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 rounded-lg bg-teal-50 px-4 py-2 text-sm text-teal-800 ring-1 ring-teal-200">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="mb-4 rounded-lg bg-red-50 px-4 py-2 text-sm text-red-800 ring-1 ring-red-200">{{ $errors->first() }}</div>
            @endif

            <p class="mb-3 text-xs text-slate-400">Tip: drag cards to reorder them or move them between columns.</p>

            {{-- Board --}}
            <div class="flex items-start gap-4 overflow-x-auto pb-4">
                @foreach ($project->columns as $column)
                    <div data-column class="w-72 shrink-0 rounded-xl border-t-4 bg-slate-100 p-3 {{ $accents[$loop->index % count($accents)] }}">
                        <h3 class="mb-3 flex items-center justify-between text-sm font-bold uppercase tracking-wide text-slate-700">
                            {{ $column->name }}
                            <span data-count class="rounded-full bg-white px-2 py-0.5 text-xs font-semibold text-slate-500">{{ $column->tasks->count() }}</span>
                        </h3>

                        {{-- Drop zone: stays tall enough to drop into even when empty --}}
                        <div data-task-list data-column-id="{{ $column->id }}" class="min-h-[4rem] space-y-2 pb-1">
                            @foreach ($column->tasks as $task)
                                <a data-task-id="{{ $task->id }}" href="{{ route('tasks.show', $task) }}"
                                   class="block cursor-grab select-none rounded-lg bg-white p-3 shadow-sm ring-1 ring-slate-200 hover:shadow hover:ring-teal-400 active:cursor-grabbing">
                                    <p class="text-sm font-medium text-slate-900">{{ $task->title }}</p>

                                    <div class="mt-3 flex items-center justify-between">
                                        <div class="flex flex-wrap items-center gap-1.5">
                                            <span class="rounded px-1.5 py-0.5 text-[10px] font-bold uppercase {{ $priorityStyles[$task->priority] ?? $priorityStyles['medium'] }}">
                                                {{ $task->priority }}
                                            </span>

                                            @if ($task->due_date)
                                                <span class="rounded px-1.5 py-0.5 text-[10px] font-semibold
                                                    {{ $task->isOverdue() ? 'bg-red-100 text-red-700' : 'bg-slate-100 text-slate-600' }}">
                                                    {{ $task->due_date->format('M j') }}{{ $task->isOverdue() ? ' · overdue' : '' }}
                                                </span>
                                            @endif
                                        </div>

                                        @if ($task->assignee)
                                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-teal-600 text-[11px] font-semibold text-white"
                                                  title="{{ $task->assignee->name }}">
                                                {{ strtoupper(mb_substr($task->assignee->name, 0, 1)) }}
                                            </span>
                                        @else
                                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full border border-dashed border-slate-300 text-[11px] text-slate-400"
                                                  title="Unassigned">?</span>
                                        @endif
                                    </div>
                                </a>
                            @endforeach
                        </div>

                        {{-- Quick add --}}
                        <form method="POST" action="{{ route('tasks.store', $project) }}" class="mt-3">
                            @csrf
                            <input type="hidden" name="board_column_id" value="{{ $column->id }}">
                            <input type="text" name="title" placeholder="+ Add a task" required maxlength="255"
                                   class="block w-full rounded-lg border-slate-200 bg-white text-sm shadow-sm placeholder:text-slate-400 focus:border-teal-500 focus:ring-teal-500">
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>