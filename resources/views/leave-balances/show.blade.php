<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Leave Balance</h2>
            <a href="{{ route('leave-balances.index') }}" class="text-sm text-gray-600 hover:underline">← Back</a>
        </div>
    </x-slot>

    <x-card title="Details">
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div>
                <dt class="text-gray-500">Employee</dt>
                <dd class="font-medium text-gray-800">{{ $leaveBalance->employee?->name }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Year</dt>
                <dd class="font-medium text-gray-800">{{ $leaveBalance->year }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Total days</dt>
                <dd class="font-medium text-gray-800">{{ $leaveBalance->total_days }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Carried forward</dt>
                <dd class="font-medium text-gray-800">{{ $leaveBalance->carried_forward }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Used days</dt>
                <dd class="font-medium text-gray-800">{{ $leaveBalance->used_days }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Remaining days</dt>
                <dd class="font-medium text-brand-600">{{ $leaveBalance->remaining_days }}</dd>
            </div>
        </dl>

        @if (isAdmin())
            <div class="mt-6 flex items-center gap-3">
                <a href="{{ route('leave-balances.edit', $leaveBalance) }}" class="inline-flex items-center px-4 py-2 bg-brand-600 text-white text-sm font-medium rounded-md hover:bg-brand-700">
                    Edit
                </a>
                <form method="POST" action="{{ route('leave-balances.destroy', $leaveBalance) }}" onsubmit="return confirm('Delete this leave balance?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-sm text-red-600 hover:underline">Delete</button>
                </form>
            </div>
        @endif
    </x-card>
</x-app-layout>
