<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Admin Leaves</h2>
            <a href="{{ route('admin-leaves.create') }}" class="inline-flex items-center px-4 py-2 bg-brand-600 text-white text-sm font-medium rounded-md hover:bg-brand-700">
                + New Admin Leave
            </a>
        </div>
    </x-slot>

    <x-card>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b">
                        <th class="py-2 pr-4">Employee</th>
                        <th class="py-2 pr-4">From</th>
                        <th class="py-2 pr-4">To</th>
                        <th class="py-2 pr-4">Days</th>
                        <th class="py-2 pr-4">Status</th>
                        <th class="py-2 pr-4">Applied</th>
                        <th class="py-2 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($leaveRequests as $req)
                        <tr>
                            <td class="py-2 pr-4">
                                <div class="flex items-center gap-2">
                                    <x-user-avatar :user="$req->employee" size="8" class="text-xs" />
                                    <span>{{ $req->employee?->name }}</span>
                                </div>
                            </td>
                            <td class="py-2 pr-4">{{ $req->from_date->format('j F, Y') }}</td>
                            <td class="py-2 pr-4">{{ $req->to_date->format('j F, Y') }}</td>
                            <td class="py-2 pr-4">{{ $req->days_requested }}</td>
                            <td class="py-2 pr-4"><x-status-badge :status="$req->status" /></td>
                            <td class="py-2 pr-4 text-gray-500">{{ $req->created_at->format('j F, Y') }}</td>
                            <td class="py-2 text-right whitespace-nowrap">
                                <a href="{{ route('admin-leaves.show', $req) }}" class="text-brand-600 hover:underline">View</a>
                                <a href="{{ route('admin-leaves.edit', $req) }}" class="ms-2 text-gray-600 hover:underline">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="py-8 text-center text-gray-500">No admin leaves found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($leaveRequests->hasPages())
            <div class="mt-4">{{ $leaveRequests->links() }}</div>
        @endif
    </x-card>
</x-app-layout>
