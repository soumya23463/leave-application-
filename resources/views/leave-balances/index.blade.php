<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Leave Balances</h2>
            @if (isAdmin())
                <a href="{{ route('leave-balances.create') }}" class="inline-flex items-center px-4 py-2 bg-brand-600 text-white text-sm font-medium rounded-md hover:bg-brand-700">
                    + New Balance
                </a>
            @endif
        </div>
    </x-slot>

    <x-card>
        @if (isAdmin())
            <form method="GET" class="mb-4 flex items-center gap-2">
                <select name="year" onchange="this.form.submit()" class="border-gray-300 rounded-md shadow-sm text-sm">
                    <option value="">All years</option>
                    @foreach ($years as $y)
                        <option value="{{ $y }}" @selected(request('year') == $y)>{{ $y }}</option>
                    @endforeach
                </select>
                @if (request('year'))
                    <a href="{{ route('leave-balances.index') }}" class="text-sm text-gray-500 hover:underline">Reset</a>
                @endif
            </form>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b">
                        @if (isAdmin())<th class="py-2 pr-4">Employee</th>@endif
                        <th class="py-2 pr-4">Year</th>
                        <th class="py-2 pr-4">Total</th>
                        <th class="py-2 pr-4">Carried Forward</th>
                        <th class="py-2 pr-4">Used</th>
                        <th class="py-2 pr-4">Remaining</th>
                        <th class="py-2 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($leaveBalances as $balance)
                        <tr>
                            @if (isAdmin())<td class="py-2 pr-4">{{ $balance->employee?->name }}</td>@endif
                            <td class="py-2 pr-4">{{ $balance->year }}</td>
                            <td class="py-2 pr-4">{{ $balance->total_days }}</td>
                            <td class="py-2 pr-4">{{ $balance->carried_forward }}</td>
                            <td class="py-2 pr-4">{{ $balance->used_days }}</td>
                            <td class="py-2 pr-4 font-medium text-gray-800">{{ $balance->remaining_days }}</td>
                            <td class="py-2 text-right whitespace-nowrap">
                                <a href="{{ route('leave-balances.show', $balance) }}" class="text-brand-600 hover:underline">View</a>
                                @if (isAdmin())
                                    <a href="{{ route('leave-balances.edit', $balance) }}" class="ms-2 text-gray-600 hover:underline">Edit</a>
                                    <form method="POST" action="{{ route('leave-balances.destroy', $balance) }}" class="inline" onsubmit="return confirm('Delete this leave balance?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="ms-2 text-red-600 hover:underline">Delete</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="{{ isAdmin() ? 7 : 6 }}" class="py-8 text-center text-gray-500">No leave balances found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($leaveBalances->hasPages())
            <div class="mt-4">{{ $leaveBalances->links() }}</div>
        @endif
    </x-card>
</x-app-layout>
