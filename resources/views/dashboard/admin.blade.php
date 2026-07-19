<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Admin Dashboard</h2>
    </x-slot>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <x-stat-card label="Active Employees" :value="$stats['active_employees']" color="brand" />
        <x-stat-card label="Holidays This Year" :value="$stats['yearly_holidays']" color="purple" :sub="$stats['remaining_holidays'].' remaining'" />
        <x-stat-card label="On Leave Today" :value="$onLeaveToday->count()" color="green" />
        <x-stat-card label="Total Requests" :value="$stats['total_requests']" color="gray" />
        <x-stat-card label="Pending Requests" :value="$stats['pending_requests']" color="yellow" />
        <x-stat-card label="Approved Requests" :value="$stats['approved_requests']" color="green" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        <x-card title="Leave Requests (Last 12 Months)">
            <canvas id="monthlyChart" height="140"></canvas>
        </x-card>

        <x-card title="Employees On Leave Today">
            @if ($onLeaveToday->isEmpty())
                <p class="text-sm text-gray-500">Nobody is on leave today.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left text-gray-500 border-b">
                                <th class="py-2 pr-4">Employee</th>
                                <th class="py-2 pr-4">From</th>
                                <th class="py-2 pr-4">To</th>
                                <th class="py-2">Days</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($onLeaveToday as $leave)
                                <tr>
                                    <td class="py-2 pr-4">{{ $leave->employee?->name }}
                                        <span class="text-xs text-gray-400">({{ $leave->employee?->role }})</span>
                                    </td>
                                    <td class="py-2 pr-4">{{ $leave->from_date->format('d M Y') }}</td>
                                    <td class="py-2 pr-4">{{ $leave->to_date->format('d M Y') }}</td>
                                    <td class="py-2">{{ $leave->days_requested }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-card>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('monthlyChart');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($chartLabels),
                    datasets: [{
                        label: 'Requests',
                        data: @json($chartData),
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37,99,235,0.1)',
                        tension: 0.3,
                        fill: true,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
                }
            });
        });
    </script>
</x-app-layout>
