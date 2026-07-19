@php
    $user = auth()->user();
    $unread = $user ? $user->unreadNotifications()->latest()->limit(8)->get() : collect();
    $unreadCount = $user ? $user->unreadNotifications()->count() : 0;
@endphp
<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 text-brand-600 font-semibold text-lg">
                        <img src="{{ asset('images/logo.png') }}" alt="Leave Desk" style="height: 5rem; width: 5rem; object-fit: contain;">

                    </a>
                </div>

                <div class="hidden space-x-6 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">Dashboard</x-nav-link>
                    <x-nav-link :href="route('team-calendar')" :active="request()->routeIs('team-calendar')">Calendar</x-nav-link>
                    <x-nav-link :href="route('leave-requests.index')" :active="request()->routeIs('leave-requests.*')">Leave Requests</x-nav-link>
                    @if (isAdmin())
                        <x-nav-link :href="route('admin-leaves.index')" :active="request()->routeIs('admin-leaves.*')">Admin Leaves</x-nav-link>
                    @endif
                    <x-nav-link :href="route('leave-balances.index')" :active="request()->routeIs('leave-balances.*')">Leave Balances</x-nav-link>
                    @if (isAdmin())
                        <x-nav-link :href="route('employees.index')" :active="request()->routeIs('employees.*')">Employees</x-nav-link>
                    @endif
                    <x-nav-link :href="route('holidays.index')" :active="request()->routeIs('holidays.*')">Holidays</x-nav-link>
                    @if (isAdmin())
                        <x-nav-link :href="route('weekend-settings.index')" :active="request()->routeIs('weekend-settings.*')">Weekends</x-nav-link>
                    @endif
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-2">
                <!-- Notification bell -->
                <div class="relative" x-data="{ openBell: false }">
                    <button @click="openBell = !openBell" class="relative p-2 text-gray-500 hover:text-gray-700 rounded-full hover:bg-gray-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2c0 .5-.2 1-.6 1.4L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        @if ($unreadCount > 0)
                            <span class="absolute top-1 right-1 inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-bold leading-none text-white bg-red-600 rounded-full">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                        @endif
                    </button>
                    <div x-show="openBell" @click.outside="openBell = false" x-transition style="display:none"
                         class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg ring-1 ring-black/5 z-50">
                        <div class="flex items-center justify-between px-4 py-2 border-b">
                            <span class="text-sm font-semibold text-gray-700">Notifications</span>
                            @if ($unreadCount > 0)
                                <form method="POST" action="{{ route('notifications.readAll') }}">@csrf
                                    <button class="text-xs text-brand-600 hover:underline">Mark all read</button>
                                </form>
                            @endif
                        </div>
                        <div class="max-h-80 overflow-y-auto divide-y">
                            @forelse ($unread as $n)
                                <a href="{{ $n->data['url'] ?? route('notifications.index') }}"
                                   class="block px-4 py-3 hover:bg-gray-50">
                                    <p class="text-sm font-medium text-gray-800">{{ $n->data['title'] ?? 'Notification' }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">{{ $n->data['body'] ?? '' }}</p>
                                    <p class="text-[11px] text-gray-400 mt-1">{{ $n->created_at->diffForHumans() }}</p>
                                </a>
                            @empty
                                <p class="px-4 py-6 text-sm text-gray-500 text-center">No new notifications</p>
                            @endforelse
                        </div>
                        <a href="{{ route('notifications.index') }}" class="block text-center text-xs text-brand-600 py-2 border-t hover:bg-gray-50">View all</a>
                    </div>
                </div>

                <!-- User dropdown -->
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2 px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none">
                            <x-user-avatar :user="$user" size="7" class="text-xs" />
                            <div>{{ $user->name }}</div>
                            <span class="ms-1 text-[10px] uppercase px-1.5 py-0.5 rounded {{ isAdmin() ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">{{ $user->role }}</span>
                            <svg class="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">Profile</x-dropdown-link>
                        @if ($user->discord_user_id)
                            <a href="{{ route('discord.disconnect') }}" class="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 hover:bg-gray-100">Disconnect Discord</a>
                        @else
                            <a href="{{ route('discord.redirect') }}" class="block w-full px-4 py-2 text-start text-sm leading-5 text-indigo-600 hover:bg-gray-100">Connect Discord</a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">Log Out</x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24"><path :class="{'hidden': open, 'inline-flex': ! open }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /><path :class="{'hidden': ! open, 'inline-flex': open }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">Dashboard</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('team-calendar')" :active="request()->routeIs('team-calendar')">Calendar</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('leave-requests.index')" :active="request()->routeIs('leave-requests.*')">Leave Requests</x-responsive-nav-link>
            @if (isAdmin())
                <x-responsive-nav-link :href="route('admin-leaves.index')" :active="request()->routeIs('admin-leaves.*')">Admin Leaves</x-responsive-nav-link>
            @endif
            <x-responsive-nav-link :href="route('leave-balances.index')" :active="request()->routeIs('leave-balances.*')">Leave Balances</x-responsive-nav-link>
            @if (isAdmin())
                <x-responsive-nav-link :href="route('employees.index')" :active="request()->routeIs('employees.*')">Employees</x-responsive-nav-link>
            @endif
            <x-responsive-nav-link :href="route('holidays.index')" :active="request()->routeIs('holidays.*')">Holidays</x-responsive-nav-link>
            @if (isAdmin())
                <x-responsive-nav-link :href="route('weekend-settings.index')" :active="request()->routeIs('weekend-settings.*')">Weekends</x-responsive-nav-link>
            @endif
            <x-responsive-nav-link :href="route('notifications.index')" :active="request()->routeIs('notifications.*')">Notifications @if($unreadCount) ({{ $unreadCount }}) @endif</x-responsive-nav-link>
        </div>
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ $user->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ $user->email }}</div>
            </div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">Profile</x-responsive-nav-link>
                @if ($user->discord_user_id)
                    <x-responsive-nav-link :href="route('discord.disconnect')">Disconnect Discord</x-responsive-nav-link>
                @else
                    <x-responsive-nav-link :href="route('discord.redirect')">Connect Discord</x-responsive-nav-link>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">Log Out</x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
