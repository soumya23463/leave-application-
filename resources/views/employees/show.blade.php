<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Employee</h2>
            <div class="flex items-center gap-3">
                <a href="{{ route('employees.edit', $user) }}" class="inline-flex items-center px-4 py-2 bg-brand-600 text-white text-sm font-medium rounded-md hover:bg-brand-700">
                    Edit
                </a>
                <a href="{{ route('employees.index') }}" class="text-sm text-gray-600 hover:underline">← Back</a>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <x-card title="Details">
                <div class="flex items-center gap-4 mb-5 pb-5 border-b border-gray-100">
                    <x-user-avatar :user="$user" size="16" class="text-2xl" />
                    <div>
                        <p class="text-lg font-semibold text-gray-900">{{ $user->name }}</p>
                        <p class="text-sm text-gray-500">{{ $user->email }}</p>
                    </div>
                </div>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-gray-500">Name</dt>
                        <dd class="font-medium text-gray-800">{{ $user->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Email</dt>
                        <dd class="font-medium text-gray-800">{{ $user->email }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Role</dt>
                        <dd>
                            <span class="text-[10px] uppercase px-1.5 py-0.5 rounded {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">{{ $user->role }}</span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Status</dt>
                        <dd><x-status-badge :status="$user->status" /></dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Phone</dt>
                        <dd class="font-medium text-gray-800">{{ $user->phone ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Joining date</dt>
                        <dd class="font-medium text-gray-800">{{ $user->joining_date?->format('j F, Y') ?? '—' }}</dd>
                    </div>
                </dl>
            </x-card>
        </div>

        <div class="space-y-4">
            <x-card title="Leave Balances">
                @forelse ($leaveBalances as $balance)
                    <div class="{{ ! $loop->first ? 'mt-4 pt-4 border-t border-gray-100' : '' }}">
                        <div class="flex items-center justify-between">
                            <span class="font-semibold text-gray-800">{{ $balance->year }}</span>
                            <span class="text-sm text-gray-500">{{ $balance->remaining_days }} remaining</span>
                        </div>
                        <dl class="mt-2 grid grid-cols-2 gap-2 text-sm">
                            <div class="flex justify-between"><dt class="text-gray-500">Total</dt><dd class="text-gray-800">{{ $balance->total_days }}</dd></div>
                            <div class="flex justify-between"><dt class="text-gray-500">Used</dt><dd class="text-gray-800">{{ $balance->used_days }}</dd></div>
                            <div class="flex justify-between"><dt class="text-gray-500">Remaining</dt><dd class="text-gray-800">{{ $balance->remaining_days }}</dd></div>
                            <div class="flex justify-between"><dt class="text-gray-500">Carried</dt><dd class="text-gray-800">{{ $balance->carried_forward }}</dd></div>
                        </dl>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No leave balances found.</p>
                @endforelse
            </x-card>
        </div>
    </div>
</x-app-layout>
