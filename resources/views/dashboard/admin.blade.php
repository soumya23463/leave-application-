<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Admin Dashboard</h2>
    </x-slot>

    {{-- Who is on leave today — shown first, right below the title --}}
    @include('dashboard._on-leave-today')

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <x-stat-card label="Active Employees" :value="$stats['active_employees']" color="brand" />
        <x-stat-card label="Holidays This Year" :value="$stats['yearly_holidays']" color="purple" :sub="$stats['remaining_holidays'].' remaining'" />
        <x-stat-card label="On Leave Today" :value="$onLeaveToday->count()" color="green" />
        <x-stat-card label="Total Requests" :value="$stats['total_requests']" color="gray" />
        <x-stat-card label="Pending Requests" :value="$stats['pending_requests']" color="yellow" />
        <x-stat-card label="Approved Requests" :value="$stats['approved_requests']" color="green" />
    </div>

    <div class="mt-6">
        <x-card title="Leave Requests (Last 12 Months)">
            <canvas id="monthlyChart" height="100"></canvas>
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
