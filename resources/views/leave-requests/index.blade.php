<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Leave Requests</h2>
            <a href="{{ route('leave-requests.create') }}" class="inline-flex items-center px-4 py-2 bg-brand-600 text-white text-sm font-medium rounded-md hover:bg-brand-700">
                + New Request
            </a>
        </div>
    </x-slot>

    <x-card>
        @if (isAdmin())
            <form method="GET" class="mb-4 flex items-center gap-2">
                <select name="status" onchange="this.form.submit()" class="border-gray-300 rounded-md shadow-sm text-sm">
                    <option value="">All statuses</option>
                    @foreach (['pending','approved','rejected'] as $s)
                        <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
                @if (request('status'))
                    <a href="{{ route('leave-requests.index') }}" class="text-sm text-gray-500 hover:underline">Reset</a>
                @endif
            </form>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b">
                        @if (isAdmin())<th class="py-2 pr-4">Employee</th>@endif
                        <th class="py-2 pr-4">From</th>
                        <th class="py-2 pr-4">To</th>
                        <th class="py-2 pr-4">Days</th>
                        <th class="py-2 pr-4">Approved By</th>
                        <th class="py-2 pr-4">Status</th>
                        <th class="py-2 pr-4">Applied</th>
                        <th class="py-2 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($leaveRequests as $req)
                        <tr>
                            @if (isAdmin())
                                <td class="py-2 pr-4">
                                    <div class="flex items-center gap-2">
                                        <x-user-avatar :user="$req->employee" size="8" class="text-xs" />
                                        <span>{{ $req->employee?->name }}</span>
                                    </div>
                                </td>
                            @endif
                            <td class="py-2 pr-4">{{ $req->from_date->format('j F, Y') }}</td>
                            <td class="py-2 pr-4">{{ $req->to_date->format('j F, Y') }}</td>
                            <td class="py-2 pr-4">{{ $req->days_requested }}</td>
                            <td class="py-2 pr-4">
                                @if ($req->approvedBy)
                                    <div class="flex items-center gap-2">
                                        <x-user-avatar :user="$req->approvedBy" size="8" class="text-xs" />
                                        <span>{{ $req->approvedBy->name }}</span>
                                    </div>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="py-2 pr-4"><x-status-badge :status="$req->status" /></td>
                            <td class="py-2 pr-4 text-gray-500">{{ $req->created_at->format('j F, Y') }}</td>
                            <td class="py-2 text-right whitespace-nowrap">
                                <a href="{{ route('leave-requests.show', $req) }}" class="text-brand-600 hover:underline">View</a>
                                @if ($req->status === 'pending' && (isAdmin() || $req->user_id === authId()))
                                    <a href="{{ route('leave-requests.edit', $req) }}" class="ms-2 text-gray-600 hover:underline">Edit</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="py-8 text-center text-gray-500">No leave requests found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($leaveRequests->hasPages())
            <div class="mt-4">{{ $leaveRequests->links() }}</div>
        @endif
    </x-card>
</x-app-layout>
