<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Leave Request</h2>
            <a href="{{ route('leave-requests.index') }}" class="text-sm text-gray-600 hover:underline">← Back</a>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <x-card title="Details">
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-gray-500">Employee</dt>
                        <dd class="font-medium text-gray-800">{{ $leaveRequest->employee?->name }}</dd>
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

            @if ($leaveRequest->status !== 'pending')
                <x-card title="Approval">
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div>
                            <dt class="text-gray-500">{{ $leaveRequest->status === 'approved' ? 'Approved by' : 'Actioned by' }}</dt>
                            <dd class="font-medium text-gray-800">{{ $leaveRequest->approvedBy?->name ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">On</dt>
                            <dd class="font-medium text-gray-800">{{ $leaveRequest->approved_at?->format('j F, Y H:i') ?? '—' }}</dd>
                        </div>
                        @if ($leaveRequest->status === 'rejected')
                            <div class="sm:col-span-2">
                                <dt class="text-gray-500">Rejection reason</dt>
                                <dd class="text-gray-800 whitespace-pre-line">{{ $leaveRequest->rejection_reason }}</dd>
                            </div>
                        @endif
                    </dl>
                </x-card>
            @endif
        </div>

        @if (isAdmin() && $leaveRequest->status === 'pending')
            <div class="space-y-4">
                <x-card title="Actions" x-data="{ rejecting: false }">
                    <form method="POST" action="{{ route('leave-requests.approve', $leaveRequest) }}">
                        @csrf
                        <button class="w-full inline-flex justify-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700">
                            Approve
                        </button>
                    </form>

                    <button @click="rejecting = !rejecting" class="w-full mt-3 inline-flex justify-center px-4 py-2 bg-white border border-red-300 text-red-700 text-sm font-medium rounded-md hover:bg-red-50">
                        Reject
                    </button>

                    <form x-show="rejecting" x-transition method="POST" action="{{ route('leave-requests.reject', $leaveRequest) }}" class="mt-3" style="display:none">
                        @csrf
                        <textarea name="rejection_reason" rows="3" required placeholder="Reason for rejection"
                                  class="block w-full border-gray-300 rounded-md shadow-sm text-sm focus:border-red-500 focus:ring-red-500"></textarea>
                        <button class="w-full mt-2 inline-flex justify-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700">
                            Confirm rejection
                        </button>
                    </form>
                </x-card>
            </div>
        @endif
    </div>
</x-app-layout>
