<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Notifications</h2>
            @if (auth()->user()->unreadNotifications()->count())
                <form method="POST" action="{{ route('notifications.readAll') }}">
                    @csrf
                    <button class="text-sm text-brand-600 hover:underline">Mark all as read</button>
                </form>
            @endif
        </div>
    </x-slot>

    <x-card>
        <div class="divide-y divide-gray-100 -m-6">
            @forelse ($notifications as $n)
                <div class="flex items-start gap-4 px-6 py-4 {{ $n->read_at ? 'bg-white' : 'bg-brand-50' }}">
                    <div class="mt-1">
                        <span class="inline-block w-2 h-2 rounded-full {{ $n->read_at ? 'bg-gray-300' : 'bg-brand-600' }}"></span>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-800">{{ $n->data['title'] ?? 'Notification' }}</p>
                        <p class="text-sm text-gray-600 mt-0.5">{{ $n->data['body'] ?? '' }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ $n->created_at->diffForHumans() }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        @if (!empty($n->data['url']))
                            <form method="POST" action="{{ route('notifications.read', $n->id) }}">
                                @csrf
                                <button class="text-xs text-brand-600 hover:underline">View</button>
                            </form>
                        @elseif (!$n->read_at)
                            <form method="POST" action="{{ route('notifications.read', $n->id) }}">
                                @csrf
                                <button class="text-xs text-gray-500 hover:underline">Mark read</button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <p class="px-6 py-10 text-center text-sm text-gray-500">You have no notifications.</p>
            @endforelse
        </div>

        @if ($notifications->hasPages())
            <div class="mt-6">{{ $notifications->links() }}</div>
        @endif
    </x-card>
</x-app-layout>
