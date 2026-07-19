<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Team Calendar</h2>
    </x-slot>

    <x-card>
        {{-- Toolbar: month nav + legend --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-4">
            <div class="flex items-center gap-2">
                <a href="{{ route('team-calendar', ['month' => $prevMonth]) }}"
                   class="w-9 h-9 inline-flex items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:bg-brand-50 hover:text-brand-600 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <h3 class="text-lg font-semibold text-gray-900 min-w-[10rem] text-center">{{ $monthLabel }}</h3>
                <a href="{{ route('team-calendar', ['month' => $nextMonth]) }}"
                   class="w-9 h-9 inline-flex items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:bg-brand-50 hover:text-brand-600 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </a>
                <a href="{{ route('team-calendar', ['month' => $thisMonth]) }}"
                   class="ml-1 text-sm text-brand-600 hover:underline">Today</a>
            </div>

            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-gray-500">
                <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>Employee</span>
                <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-violet-500"></span>Admin</span>
                <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span>Holiday</span>
                <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-sm bg-rose-300"></span>Weekend</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <div class="min-w-[46rem]">
                {{-- Weekday header --}}
                <div class="grid grid-cols-7 border border-gray-200 rounded-t-lg overflow-hidden">
                    @foreach (['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $d)
                        <div class="bg-gray-50 py-2 text-center text-xs font-semibold uppercase tracking-wide text-gray-500 border-r last:border-r-0 border-gray-200">{{ $d }}</div>
                    @endforeach
                </div>

                {{-- Weeks --}}
                <div class="border-x border-b border-gray-200 rounded-b-lg overflow-hidden">
                    @foreach ($weeks as $week)
                        <div class="grid grid-cols-7 border-t border-gray-200 first:border-t-0">
                            @foreach ($week as $day)
                                <div @class([
                                    'min-h-[7rem] p-1.5 border-r last:border-r-0 border-gray-200 align-top',
                                    'bg-white' => $day['inMonth'] && ! $day['isWeekend'] && ! $day['holiday'],
                                    'bg-rose-50/70' => $day['isWeekend'] && $day['inMonth'],
                                    'bg-amber-50/60' => $day['holiday'] && $day['inMonth'],
                                    'bg-gray-50/40 text-gray-400' => ! $day['inMonth'],
                                ])>
                                    {{-- date number --}}
                                    <div class="flex items-center justify-between">
                                        <span @class([
                                            'inline-flex items-center justify-center w-6 h-6 text-xs font-semibold rounded-full',
                                            'bg-brand-600 text-white' => $day['isToday'],
                                            'text-gray-700' => ! $day['isToday'] && $day['inMonth'],
                                            'text-gray-400' => ! $day['inMonth'],
                                        ])>{{ $day['date']->format('j') }}</span>

                                        @if ($day['leaves']->isNotEmpty())
                                            <span class="text-[10px] font-medium text-gray-400">{{ $day['leaves']->count() }}</span>
                                        @endif
                                    </div>

                                    {{-- holiday --}}
                                    @if ($day['holiday'])
                                        <div class="mt-1 truncate rounded bg-amber-100 px-1.5 py-0.5 text-[11px] font-medium text-amber-700" title="{{ $day['holiday']->name }}">
                                            🎉 {{ $day['holiday']->name }}
                                        </div>
                                    @endif

                                    {{-- leaves (up to 3, then +N more) --}}
                                    <div class="mt-1 space-y-1">
                                        @foreach ($day['leaves']->take(3) as $leave)
                                            @php $isAdmin = $leave->employee?->role === 'admin'; @endphp
                                            <div class="flex items-center gap-1 truncate rounded px-1 py-0.5 text-[11px] {{ $isAdmin ? 'bg-violet-50 text-violet-700' : 'bg-blue-50 text-blue-700' }}"
                                                 title="{{ $leave->employee?->name }} — {{ $leave->from_date->format('j M') }} to {{ $leave->to_date->format('j M') }}">
                                                @if ($leave->employee?->avatar_url)
                                                    <img src="{{ $leave->employee->avatar_url }}" alt="" class="w-3.5 h-3.5 rounded-full object-cover flex-none">
                                                @else
                                                    <span class="w-1.5 h-1.5 rounded-full flex-none {{ $isAdmin ? 'bg-violet-500' : 'bg-blue-500' }}"></span>
                                                @endif
                                                <span class="truncate">{{ $leave->employee?->name }}</span>
                                            </div>
                                        @endforeach

                                        @if ($day['leaves']->count() > 3)
                                            <div class="text-[10px] text-gray-400 pl-1">+{{ $day['leaves']->count() - 3 }} more</div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </x-card>
</x-app-layout>
