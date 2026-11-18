@php
    $priorityStyles = [
        'high' => 'bg-red-100 text-red-700',
        'medium' => 'bg-amber-100 text-amber-700',
        'low' => 'bg-slate-200 text-slate-600',
    ];
@endphp
<a data-task-id="{{ $task->id }}" href="{{ route('tasks.show', $task, false) }}"
   class="block cursor-grab select-none rounded-lg bg-white p-3 shadow-sm ring-1 ring-slate-200 hover:shadow hover:ring-teal-400 active:cursor-grabbing">
    <p class="text-sm font-medium text-slate-900">{{ $task->title }}</p>

    <div class="mt-3 flex items-center justify-between">
        <div class="flex flex-wrap items-center gap-1.5">
            <span class="rounded px-1.5 py-0.5 text-[10px] font-bold uppercase {{ $priorityStyles[$task->priority] ?? $priorityStyles['medium'] }}">
                {{ $task->priority }}
            </span>

            @if ($task->due_date)
                <span class="rounded px-1.5 py-0.5 text-[10px] font-semibold {{ $task->isOverdue() ? 'bg-red-100 text-red-700' : 'bg-slate-100 text-slate-600' }}">
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