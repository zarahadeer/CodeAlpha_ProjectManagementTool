<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Crewboard') }} &ndash; Plan together. Ship together.</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-white text-slate-900">

        {{-- Top bar --}}
        <header class="border-b border-slate-100">
            <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
                <a href="/" class="flex items-center gap-2">
                    <x-application-logo class="h-9 w-9 text-teal-600" />
                    <span class="text-xl font-bold tracking-tight">{{ config('app.name') }}</span>
                </a>

                <nav class="flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}"
                           class="rounded-lg bg-teal-600 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-700">
                            My Projects
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-3 py-2 text-sm font-semibold text-slate-700 hover:text-teal-700">Log in</a>
                        <a href="{{ route('register') }}"
                           class="rounded-lg bg-teal-600 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-700">
                            Get started
                        </a>
                    @endauth
                </nav>
            </div>
        </header>

        {{-- Hero --}}
        <section class="bg-teal-50">
            <div class="mx-auto grid max-w-6xl items-center gap-12 px-6 py-16 lg:grid-cols-2 lg:py-24">
                <div>
                    <span class="inline-block rounded-full bg-teal-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-teal-800">
                        Project management for teams
                    </span>
                    <h1 class="mt-4 text-4xl font-extrabold leading-tight tracking-tight sm:text-5xl">
                        Plan together.<br>
                        <span class="text-teal-600">Ship together.</span>
                    </h1>
                    <p class="mt-5 max-w-lg text-lg text-slate-600">
                        {{ config('app.name') }} gives your crew one board for every project: create tasks,
                        assign them, set deadlines and talk it through right on the task card.
                    </p>

                    <div class="mt-8 flex flex-wrap gap-3">
                        @auth
                            <a href="{{ route('dashboard') }}"
                               class="rounded-lg bg-teal-600 px-6 py-3 font-semibold text-white shadow-sm hover:bg-teal-700">
                                Go to my projects
                            </a>
                        @else
                            <a href="{{ route('register') }}"
                               class="rounded-lg bg-teal-600 px-6 py-3 font-semibold text-white shadow-sm hover:bg-teal-700">
                                Get started free
                            </a>
                            <a href="{{ route('login') }}"
                               class="rounded-lg border border-slate-300 bg-white px-6 py-3 font-semibold text-slate-700 hover:border-teal-600 hover:text-teal-700">
                                Log in
                            </a>
                        @endauth
                    </div>
                </div>

                {{-- Mock board --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-xl">
                    <div class="mb-3 flex items-center gap-1.5">
                        <span class="h-2.5 w-2.5 rounded-full bg-red-300"></span>
                        <span class="h-2.5 w-2.5 rounded-full bg-amber-300"></span>
                        <span class="h-2.5 w-2.5 rounded-full bg-teal-300"></span>
                    </div>

                    <div class="grid grid-cols-3 gap-3 text-xs">
                        <div class="rounded-lg bg-slate-100 p-2">
                            <p class="mb-2 font-semibold text-slate-600">To Do</p>
                            <div class="mb-2 rounded-md bg-white p-2 shadow-sm">
                                <p class="font-medium">Design login page</p>
                                <span class="mt-2 inline-block rounded bg-red-100 px-1.5 py-0.5 text-[10px] font-semibold text-red-700">HIGH</span>
                            </div>
                            <div class="rounded-md bg-white p-2 shadow-sm">
                                <p class="font-medium">Write API docs</p>
                                <span class="mt-2 inline-block rounded bg-slate-100 px-1.5 py-0.5 text-[10px] font-semibold text-slate-600">LOW</span>
                            </div>
                        </div>

                        <div class="rounded-lg bg-slate-100 p-2">
                            <p class="mb-2 font-semibold text-slate-600">In Progress</p>
                            <div class="mb-2 rounded-md bg-white p-2 shadow-sm">
                                <p class="font-medium">Build task board</p>
                                <div class="mt-2 flex items-center justify-between">
                                    <span class="rounded bg-amber-100 px-1.5 py-0.5 text-[10px] font-semibold text-amber-700">MEDIUM</span>
                                    <span class="flex h-5 w-5 items-center justify-center rounded-full bg-teal-600 text-[10px] font-semibold text-white">A</span>
                                </div>
                            </div>
                            <div class="rounded-md bg-white p-2 shadow-sm">
                                <p class="font-medium">Add comments</p>
                                <div class="mt-2 flex items-center justify-between">
                                    <span class="rounded bg-amber-100 px-1.5 py-0.5 text-[10px] font-semibold text-amber-700">MEDIUM</span>
                                    <span class="flex h-5 w-5 items-center justify-center rounded-full bg-slate-700 text-[10px] font-semibold text-white">S</span>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-lg bg-slate-100 p-2">
                            <p class="mb-2 font-semibold text-slate-600">Done</p>
                            <div class="mb-2 rounded-md bg-white p-2 shadow-sm">
                                <p class="font-medium">Set up database</p>
                                <span class="mt-2 inline-block rounded bg-teal-100 px-1.5 py-0.5 text-[10px] font-semibold text-teal-700">DONE</span>
                            </div>
                            <div class="rounded-md bg-white p-2 shadow-sm">
                                <p class="font-medium">User accounts</p>
                                <span class="mt-2 inline-block rounded bg-teal-100 px-1.5 py-0.5 text-[10px] font-semibold text-teal-700">DONE</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Features --}}
        <section class="mx-auto max-w-6xl px-6 py-16 lg:py-20">
            <h2 class="text-center text-3xl font-extrabold tracking-tight">Everything your crew needs</h2>
            <p class="mx-auto mt-3 max-w-xl text-center text-slate-600">
                Simple enough to start in a minute, organised enough to run a whole project.
            </p>

            <div class="mt-12 grid gap-6 md:grid-cols-3">
                <div class="rounded-2xl border border-slate-200 p-6">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-teal-100 text-teal-700">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="5" height="16" rx="1.5"/><rect x="10" y="4" width="5" height="10" rx="1.5"/><rect x="17" y="4" width="4" height="13" rx="1.5"/>
                        </svg>
                    </div>
                    <h3 class="mt-4 text-lg font-bold">Project boards</h3>
                    <p class="mt-2 text-sm text-slate-600">
                        Every project gets its own board with columns like To Do, In Progress and Done.
                        See where everything stands at a glance.
                    </p>
                </div>

                <div class="rounded-2xl border border-slate-200 p-6">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-teal-100 text-teal-700">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="9" cy="8" r="3.5"/><path d="M2.5 20c0-3.6 2.9-6 6.5-6s6.5 2.4 6.5 6"/><path d="M18 8v6M15 11h6"/>
                        </svg>
                    </div>
                    <h3 class="mt-4 text-lg font-bold">Assign and track</h3>
                    <p class="mt-2 text-sm text-slate-600">
                        Add teammates to a project, assign tasks, set priorities and due dates.
                        Overdue work is highlighted so nothing slips.
                    </p>
                </div>

                <div class="rounded-2xl border border-slate-200 p-6">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-teal-100 text-teal-700">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 5h16a1 1 0 011 1v10a1 1 0 01-1 1H10l-4 3.5V17H4a1 1 0 01-1-1V6a1 1 0 011-1z"/>
                        </svg>
                    </div>
                    <h3 class="mt-4 text-lg font-bold">Talk in the task</h3>
                    <p class="mt-2 text-sm text-slate-600">
                        Comment right on the task card so decisions and context stay with the work,
                        not buried in a chat.
                    </p>
                </div>
            </div>
        </section>

        {{-- Call to action --}}
        <section class="bg-teal-700">
            <div class="mx-auto max-w-4xl px-6 py-14 text-center text-white">
                <h2 class="text-3xl font-extrabold tracking-tight">Ready to get your crew organised?</h2>
                <p class="mt-3 text-teal-100">Create your first project in under a minute.</p>
                <a href="{{ auth()->check() ? route('dashboard') : route('register') }}"
                   class="mt-6 inline-block rounded-lg bg-white px-6 py-3 font-semibold text-teal-800 hover:bg-teal-50">
                    {{ auth()->check() ? 'Go to my projects' : 'Get started free' }}
                </a>
            </div>
        </section>

        <footer class="border-t border-slate-100">
            <p class="mx-auto max-w-6xl px-6 py-6 text-center text-sm text-slate-500">
                &copy; {{ date('Y') }} {{ config('app.name') }}. Built as an internship project.
            </p>
        </footer>
    </body>
</html>