<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-slate-900">My Projects</h2>
                <p class="text-sm text-slate-500">
                    {{ $projects->count() }} {{ \Illuminate\Support\Str::plural('project', $projects->count()) }}
                </p>
            </div>

            <a href="{{ route('projects.create') }}"
               class="inline-flex items-center gap-2 rounded-lg bg-teal-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-teal-700">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                    <path d="M12 5v14M5 12h14"/>
                </svg>
                New project
            </a>
        </div>
    </x-slot>

    @php
        $tints = [
            'bg-teal-100 text-teal-700',
            'bg-sky-100 text-sky-700',
            'bg-violet-100 text-violet-700',
            'bg-amber-100 text-amber-700',
            'bg-rose-100 text-rose-700',
        ];
        $roleStyles = [
            'owner' => 'bg-teal-100 text-teal-800',
            'admin' => 'bg-violet-100 text-violet-800',
            'member' => 'bg-slate-100 text-slate-600',
        ];
    @endphp

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 rounded-lg bg-teal-50 px-4 py-2 text-sm text-teal-800 ring-1 ring-teal-200">{{ session('status') }}</div>
            @endif

            @if ($projects->isEmpty())
                <div class="rounded-2xl border-2 border-dashed border-slate-300 bg-white p-12 text-center">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-teal-100 text-teal-700">
                        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="5" height="16" rx="1.5"/><rect x="10" y="4" width="5" height="10" rx="1.5"/><rect x="17" y="4" width="4" height="13" rx="1.5"/>
                        </svg>
                    </div>
                    <h3 class="mt-4 text-lg font-bold text-slate-900">No projects yet</h3>
                    <p class="mt-1 text-sm text-slate-500">Create your first project to start planning with your crew.</p>
                    <a href="{{ route('projects.create') }}"
                       class="mt-6 inline-block rounded-lg bg-teal-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-teal-700">
                        Create a project
                    </a>
                </div>
            @else
                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($projects as $project)
                        <a href="{{ route('projects.show', $project) }}"
                           class="group block rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200 transition hover:-translate-y-0.5 hover:shadow-md hover:ring-teal-300">
                            <div class="flex items-start justify-between">
                                <div class="flex h-11 w-11 items-center justify-center rounded-xl text-lg font-bold {{ $tints[$project->id % count($tints)] }}">
                                    {{ strtoupper(mb_substr($project->title, 0, 1)) }}
                                </div>
                                <span class="rounded-full px-2.5 py-0.5 text-[11px] font-semibold uppercase tracking-wide {{ $roleStyles[$project->pivot->role] ?? $roleStyles['member'] }}">
                                    {{ $project->pivot->role }}
                                </span>
                            </div>

                            <h3 class="mt-4 text-lg font-bold text-slate-900 group-hover:text-teal-700">{{ $project->title }}</h3>
                            <p class="mt-1 min-h-[2.5rem] text-sm text-slate-500">
                                {{ \Illuminate\Support\Str::limit($project->description, 80) ?: 'No description' }}
                            </p>

                            <div class="mt-4 flex items-center gap-4 border-t border-slate-100 pt-3 text-xs text-slate-500">
                                <span>{{ $project->tasks_count }} {{ \Illuminate\Support\Str::plural('task', $project->tasks_count) }}</span>
                                <span>{{ $project->members_count }} {{ \Illuminate\Support\Str::plural('member', $project->members_count) }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>