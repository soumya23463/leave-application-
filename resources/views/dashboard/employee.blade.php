<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">My Dashboard</h2>
            <a href="{{ route('leave-requests.create') }}" class="inline-flex items-center px-4 py-2 bg-brand-600 text-white text-sm font-medium rounded-md hover:bg-brand-700">
                Apply for Leave
            </a>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-stat-card label="Remaining Leaves" :value="$stats['remaining_leaves']" color="green" sub="This year" />
        <x-stat-card label="Used Leaves" :value="$stats['used_leaves']" color="brand" sub="This year" />
        <x-stat-card label="Pending Requests" :value="$stats['pending_requests']" color="yellow" />
        <x-stat-card label="Upcoming Holidays" :value="$stats['upcoming_holidays']" color="purple" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        <x-card title="My Recent Requests">
            @if ($recentRequests->isEmpty())
                <p class="text-sm text-gray-500">You haven't applied for any leave yet.</p>
            @else
                <div class="divide-y divide-gray-100 -m-6">
                    @foreach ($recentRequests as $req)
                        <a href="{{ route('leave-requests.show', $req) }}" class="flex items-center justify-between px-6 py-3 hover:bg-gray-50">
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ $req->from_date->format('d M') }} – {{ $req->to_date->format('d M Y') }}</p>
                                <p class="text-xs text-gray-500">{{ $req->days_requested }} day(s)</p>
                            </div>
                            <x-status-badge :status="$req->status" />
                        </a>
                    @endforeach
                </div>
            @endif
        </x-card>

        <x-card title="Upcoming Holidays">
            @if ($upcomingHolidays->isEmpty())
                <p class="text-sm text-gray-500">No upcoming holidays.</p>
            @else
                <div class="divide-y divide-gray-100 -m-6">
                    @foreach ($upcomingHolidays as $holiday)
                        <div class="flex items-center justify-between px-6 py-3">
                            <p class="text-sm font-medium text-gray-800">{{ $holiday->name }}</p>
                            <p class="text-xs text-gray-500">{{ $holiday->date->format('d M Y') }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-card>
    </div>
</x-app-layout>
