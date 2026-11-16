<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold tracking-tight text-slate-900">Notifications</h2>

            @if (auth()->user()->unreadNotifications()->count() > 0)
                <form method="POST" action="{{ route('notifications.readAll') }}">
                    @csrf
                    <button class="text-sm font-medium text-teal-700 hover:underline">Mark all as read</button>
                </form>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
                @forelse ($notifications as $notification)
                    <a href="{{ route('notifications.open', $notification->id) }}"
                       class="flex items-start gap-3 border-b border-slate-100 px-5 py-4 last:border-b-0 hover:bg-slate-50">
                        <span class="mt-1.5 h-2.5 w-2.5 shrink-0 rounded-full {{ $notification->read_at ? 'bg-slate-200' : 'bg-teal-500' }}"></span>
                        <div>
                            <p class="text-sm {{ $notification->read_at ? 'text-slate-500' : 'font-medium text-slate-900' }}">
                                {{ $notification->data['message'] }}
                            </p>
                            <p class="mt-0.5 text-xs text-slate-400">{{ $notification->created_at->diffForHumans() }}</p>
                        </div>
                    </a>
                @empty
                    <p class="px-5 py-12 text-center text-sm text-slate-400">You're all caught up. Nothing new yet.</p>
                @endforelse
            </div>

            <div class="mt-4">{{ $notifications->links() }}</div>
        </div>
    </div>
</x-app-layout>