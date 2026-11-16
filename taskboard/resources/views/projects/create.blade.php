<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">New Project</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                <form method="POST" action="{{ route('projects.store') }}">
                    @include('projects._form')

                    <div class="mt-6 flex items-center gap-4">
                        <x-primary-button>Create project</x-primary-button>
                        <a href="{{ route('dashboard') }}" class="text-sm text-gray-600 dark:text-gray-400 underline">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>