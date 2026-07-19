<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Employees</h2>
            <a href="{{ route('employees.create') }}" class="inline-flex items-center px-4 py-2 bg-brand-600 text-white text-sm font-medium rounded-md hover:bg-brand-700">
                + New Employee
            </a>
        </div>
    </x-slot>

    <x-card>
        <form method="GET" class="mb-4 flex items-center gap-2">
            <select name="status" onchange="this.form.submit()" class="border-gray-300 rounded-md shadow-sm text-sm">
                <option value="">All statuses</option>
                @foreach (['active','inactive'] as $s)
                    <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
            @if (request('status'))
                <a href="{{ route('employees.index') }}" class="text-sm text-gray-500 hover:underline">Reset</a>
            @endif
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b">
                        <th class="py-2 pr-4">Name</th>
                        <th class="py-2 pr-4">Email</th>
                        <th class="py-2 pr-4">Role</th>
                        <th class="py-2 pr-4">Department</th>
                        <th class="py-2 pr-4">Phone</th>
                        <th class="py-2 pr-4">Joining</th>
                        <th class="py-2 pr-4">Status</th>
                        <th class="py-2 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($users as $user)
                        <tr>
                            <td class="py-2 pr-4">
                                <div class="flex items-center gap-2">
                                    <x-user-avatar :user="$user" size="8" class="text-xs" />
                                    <span>{{ $user->name }}</span>
                                </div>
                            </td>
                            <td class="py-2 pr-4">{{ $user->email }}</td>
                            <td class="py-2 pr-4">
                                <x-role-badge :role="$user->role" />
                            </td>
                            <td class="py-2 pr-4">{{ $user->department?->name ?? '—' }}</td>
                            <td class="py-2 pr-4">{{ $user->phone ?? '—' }}</td>
                            <td class="py-2 pr-4">{{ $user->joining_date?->format('j F, Y') ?? '—' }}</td>
                            <td class="py-2 pr-4"><x-status-badge :status="$user->status" /></td>
                            <td class="py-2 text-right whitespace-nowrap">
                                <a href="{{ route('employees.show', $user) }}" class="text-brand-600 hover:underline">View</a>
                                <a href="{{ route('employees.edit', $user) }}" class="ms-2 text-gray-600 hover:underline">Edit</a>
                                <form method="POST" action="{{ route('employees.destroy', $user) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Delete?')" class="ms-2 text-red-600 hover:underline">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="py-8 text-center text-gray-500">No employees found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <div class="mt-4">{{ $users->links() }}</div>
        @endif
    </x-card>
</x-app-layout>
