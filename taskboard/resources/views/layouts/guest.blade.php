<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Crewboard') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-slate-50 text-slate-900">
        <div class="min-h-screen grid lg:grid-cols-2">

            {{-- Left: brand panel (desktop only) --}}
            <div class="hidden lg:flex flex-col justify-between bg-teal-700 p-12 text-white">
                <a href="/" class="flex items-center gap-3">
                    <x-application-logo class="h-10 w-10 text-teal-900" />
                    <span class="text-2xl font-bold tracking-tight">{{ config('app.name') }}</span>
                </a>

                <div>
                    <h2 class="text-4xl font-extrabold leading-tight">Plan together.<br>Ship together.</h2>
                    <p class="mt-4 max-w-md text-teal-100">
                        Create projects, assign tasks to your crew and keep every conversation
                        right where the work happens.
                    </p>
                </div>

                <p class="text-sm text-teal-200">&copy; {{ date('Y') }} {{ config('app.name') }}</p>
            </div>

            {{-- Right: form --}}
            <div class="flex items-center justify-center p-6">
                <div class="w-full max-w-md">
                    <a href="/" class="mb-6 flex items-center justify-center gap-2 lg:hidden">
                        <x-application-logo class="h-9 w-9 text-teal-600" />
                        <span class="text-xl font-bold tracking-tight">{{ config('app.name') }}</span>
                    </a>

                    <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>