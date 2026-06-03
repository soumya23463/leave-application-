<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">Employees Currently On Leave</x-slot>

        @php $leaves = $this->getLeaves(); @endphp

        @if ($leaves->isEmpty())
            <p class="text-sm text-gray-500 dark:text-gray-400">No employees on leave today.</p>
        @else
            <div class="flex flex-wrap gap-3">
                @foreach ($leaves as $leave)
                    <div class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-medium bg-primary-50 text-primary-700 ring-1 ring-primary-200 dark:bg-primary-900/20 dark:text-primary-300 dark:ring-primary-800">
                        <span class="h-2 w-2 rounded-full bg-primary-500"></span>
                        <span class="font-semibold">{{ $leave->employee?->name }}</span>
                        <span class="text-primary-500 dark:text-primary-400">
                            {{ $leave->from_date->format('d M') }} – {{ $leave->to_date->format('d M Y') }}
                        </span>
                    </div>
                @endforeach
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
