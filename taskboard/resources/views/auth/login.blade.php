<x-guest-layout>
    <h1 class="text-2xl font-bold text-slate-900">Welcome back</h1>
    <p class="mt-1 text-sm text-slate-500">Log in to see your projects.</p>

    <x-auth-session-status class="mt-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email"
                :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password"
                required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" name="remember"
                    class="rounded border-slate-300 text-teal-600 shadow-sm focus:ring-teal-500">
                <span class="ms-2 text-sm text-slate-600">{{ __('Remember me') }}</span>
            </label>

        </div>

        <x-primary-button class="w-full justify-center">{{ __('Log in') }}</x-primary-button>
    </form>

    <p class="mt-6 text-center text-sm text-slate-600">
        New to {{ config('app.name') }}?
        <a href="{{ route('register') }}" class="font-semibold text-teal-700 hover:underline">Create an account</a>
    </p>
</x-guest-layout>