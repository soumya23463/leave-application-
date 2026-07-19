<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Admin Leave</h2>
            <a href="{{ route('admin-leaves.index') }}" class="text-sm text-gray-600 hover:underline">← Back</a>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <x-card title="Details">
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-gray-500">Employee</dt>
                        <dd class="font-medium text-gray-800">
                            <div class="flex items-center gap-2">
                                <x-user-avatar :user="$leaveRequest->employee" size="8" class="text-xs" />
                                <span>{{ $leaveRequest->employee?->name }}</span>
                            </div>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Status</dt>
                        <dd><x-status-badge :status="$leaveRequest->status" /></dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">From</dt>
                        <dd class="font-medium text-gray-800">{{ $leaveRequest->from_date->format('j F, Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">To</dt>
                        <dd class="font-medium text-gray-800">{{ $leaveRequest->to_date->format('j F, Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Days</dt>
                        <dd class="font-medium text-gray-800">{{ $leaveRequest->days_requested }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Applied</dt>
                        <dd class="font-medium text-gray-800">{{ $leaveRequest->created_at->format('j F, Y') }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-gray-500">Reason</dt>
                        <dd class="text-gray-800 whitespace-pre-line">{{ $leaveRequest->reason }}</dd>
                    </div>
                </dl>
            </x-card>

            <x-card title="Approval">
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-gray-500">Approved by</dt>
                        <dd class="font-medium text-gray-800">
                            @if ($leaveRequest->approvedBy)
                                <div class="flex items-center gap-2">
                                    <x-user-avatar :user="$leaveRequest->approvedBy" size="8" class="text-xs" />
                                    <span>{{ $leaveRequest->approvedBy->name }}</span>
                                </div>
                            @else
                                —
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">On</dt>
                        <dd class="font-medium text-gray-800">{{ $leaveRequest->approved_at?->format('j F, Y H:i') ?? '—' }}</dd>
                    </div>
                </dl>
            </x-card>
        </div>

        <div class="space-y-4">
            <x-card title="Actions">
                <a href="{{ route('admin-leaves.edit', $leaveRequest) }}" class="w-full inline-flex justify-center px-4 py-2 bg-brand-600 text-white text-sm font-medium rounded-md hover:bg-brand-700">
                    Edit
                </a>

                <form method="POST" action="{{ route('admin-leaves.destroy', $leaveRequest) }}" class="mt-3" onsubmit="return confirm('Delete this admin leave?');">
                    @csrf
                    @method('DELETE')
                    <button class="w-full inline-flex justify-center px-4 py-2 bg-white border border-red-300 text-red-700 text-sm font-medium rounded-md hover:bg-red-50">
                        Delete
                    </button>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>
