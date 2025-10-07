@extends('layouts.app')

@section('title', 'Dashboard | MoM Telkom')

@section('content')
<div class="pt-14">
    <div class="space-y-6">
        <div class="flex flex-col md:flex-row items-center justify-between p-6 md:p-8 overflow-hidden rounded-lg shadow-md bg-component-bg dark:bg-dark-component-bg border-l-4 border-primary">
            <div class="flex items-center space-x-4">
                <img src="https://media.giphy.com/media/v1.Y2lkPWVjZjA1ZTQ3b284ZXpvZHVrcmwxZnc0MHRxN284anlsdmNtY3E1MDg1dWQ2c2txdCZlcD12MV9naWZzX3NlYXJjaCZjdD1n/8MiY7r4EfWVINa8LiK/giphy.gif" alt="Welcome GIF" class="w-16 h-16 rounded-full shadow-md object-cover">
                <div>
                    <h1 class="text-3xl font-bold text-text-primary dark:text-dark-text-primary">Selamat Pagi! 👋</h1>
                    <p class="mt-1 text-text-secondary dark:text-dark-text-secondary">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}.</p>
                </div>
            </div>
            <div class="flex space-x-2 mt-4 md:mt-0 w-full md:w-auto">
                <a href="{{ url('/create') }}" class="flex justify-center items-center px-4 py-2 text-sm font-semibold text-white bg-primary rounded-lg shadow-lg hover:bg-primary-dark transform transition-all duration-200 focus:ring-4 focus:ring-red-300 w-full">
                    <i class="fa-solid fa-plus mr-2"></i>New MoM
                </a>
            </div>
        </div>

        {{-- Statistics Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="flex items-center justify-between p-6 rounded-lg bg-component-bg dark:bg-dark-component-bg shadow-md">
                <div>
                    <p class="text-3xl font-bold text-text-primary dark:text-dark-text-primary">{{ $stats['approved'] }}</p>
                    <p class="text-md text-text-secondary mt-1">MoM Approved</p>
                </div>
                <div class="flex items-center justify-center h-16 w-16 rounded-full bg-green-100 dark:bg-green-500/20">
                    <i class="fa-solid fa-check-double fa-xl text-green-500"></i>
                </div>
            </div>

            <div class="flex items-center justify-between p-6 rounded-lg bg-component-bg dark:bg-dark-component-bg shadow-md">
                <div>
                    <p class="text-3xl font-bold text-text-primary dark:text-dark-text-primary">{{ $stats['pending'] }}</p>
                    <p class="text-md text-text-secondary mt-1">MoM Pending</p>
                </div>
                <div class="flex items-center justify-center h-16 w-16 rounded-full bg-yellow-100 dark:bg-yellow-500/20">
                    <i class="fa-solid fa-clock fa-xl text-yellow-500"></i>
                </div>
            </div>

            <div class="flex items-center justify-between p-6 rounded-lg bg-component-bg dark:bg-dark-component-bg shadow-md">
                <div>
                    <p class="text-3xl font-bold text-text-primary dark:text-dark-text-primary">{{ $stats['tasks_due'] }}</p>
                    <p class="text-md text-text-secondary mt-1">Tasks Due</p>
                </div>
                <div class="flex items-center justify-center h-16 w-16 rounded-full bg-red-100 dark:bg-red-500/20">
                    <i class="fa-solid fa-triangle-exclamation fa-xl text-red-500"></i>
                </div>
            </div>

            <div class="flex items-center justify-between p-6 rounded-lg bg-component-bg dark:bg-dark-component-bg shadow-md">
                <div>
                    <p class="text-3xl font-bold text-text-primary dark:text-dark-text-primary">{{ $stats['tasks_completed'] }}</p>
                    <p class="text-md text-text-secondary mt-1">Tasks Completed</p>
                </div>
                <div class="flex items-center justify-center h-16 w-16 rounded-full bg-blue-100 dark:bg-blue-500/20">
                    <i class="fa-solid fa-clipboard-check fa-xl text-blue-500"></i>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- MoM Statistics Chart --}}
            <div class="lg:col-span-2 w-full bg-component-bg rounded-lg shadow-md dark:bg-dark-component-bg p-4 md:p-6">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4">
                    <div>
                        <h5 class="text-2xl font-bold text-text-primary dark:text-white pb-1">MoM Statistics</h5>
                        <p id="chart-subtitle" class="text-sm text-text-secondary dark:text-dark-text-secondary">Progress per minggu</p>
                    </div>
                    <div class="flex items-center space-x-1 text-sm mt-3 sm:mt-0 p-1 bg-body-bg dark:bg-dark-body-bg rounded-lg">
                        <button id="filter-week" class="chart-filter-btn px-3 py-1 rounded-md bg-primary text-white">Minggu</button>
                        <button id="filter-month" class="chart-filter-btn px-3 py-1 rounded-md">Bulan</button>
                        <button id="filter-year" class="chart-filter-btn px-3 py-1 rounded-md">Tahun</button>
                    </div>
                </div>
                <div id="column-chart" class="mt-4"></div>
            </div>

            {{-- Recent Activity --}}
            <div class="lg:col-span-1 bg-component-bg rounded-lg shadow-md dark:bg-dark-component-bg p-4 md:p-6 h-full">
                <h5 class="text-xl font-bold text-text-primary dark:text-white mb-4">Recent Activity</h5>
                <ol class="relative border-s border-border-light dark:border-border-dark">
                    @foreach($recentActivity as $activity)
                    <li class="mb-6 ms-6 {{ $loop->last ? '' : '' }}">
                        <span class="absolute flex items-center justify-center w-6 h-6 bg-{{ $activity['color'] }}-100 rounded-full -start-3 ring-8 ring-component-bg dark:ring-dark-component-bg dark:bg-{{ $activity['color'] }}-900">
                            <i class="fa-solid {{ $activity['icon'] }} text-{{ $activity['color'] }}-500"></i>
                        </span>
                        <h3 class="flex items-center mb-1 text-lg font-semibold text-text-primary dark:text-white">
                            {{ $activity['title'] }}
                        </h3>
                        <time class="block mb-2 text-sm text-text-secondary dark:text-dark-text-secondary">
                            {{ $activity['type'] === 'task_due' ? 'Due on ' : '' }}
                            {{ \Carbon\Carbon::parse($activity['date'])->format('M jS, Y') }}
                        </time>
                    </li>
                    @endforeach
                </ol>
            </div>
        </div>

        {{-- Recent MoMs Table --}}
        <div class="bg-component-bg dark:bg-dark-component-bg shadow-md sm:rounded-lg overflow-hidden">
            <div class="p-4">
                <h5 class="text-xl font-bold text-text-primary dark:text-white">Recent MoM</h5>
                <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 mt-4">
                    <div class="w-full md:w-1/2">
                        <form class="flex items-center" id="search-form">
                            <label for="simple-search" class="sr-only">Search</label>
                            <div class="relative w-full">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <i class="fa-solid fa-search text-text-secondary"></i>
                                </div>
                                <input type="text" id="simple-search" name="search" class="bg-body-bg border border-border-light text-text-primary text-sm rounded-lg focus:ring-primary focus:border-primary block w-full pl-10 p-2 dark:bg-dark-component-bg dark:border-border-dark" placeholder="Search MoM">
                            </div>
                        </form>
                    </div>
                    <div class="w-full md:w-auto flex items-center space-x-3">
                        <button id="dropdownActionButton" data-dropdown-toggle="dropdownAction" class="w-full md:w-auto flex items-center justify-center py-2 px-4 text-sm font-medium text-text-primary focus:outline-none bg-component-bg rounded-lg border border-border-light hover:bg-body-bg focus:z-10 focus:ring-4 focus:ring-primary/20 dark:bg-dark-component-bg dark:text-dark-text-secondary dark:border-border-dark" type="button">
                            Filter<i class="fa-solid fa-chevron-down w-2.5 h-2.5 ms-2.5"></i>
                        </button>
                        <div id="dropdownAction" class="z-10 hidden bg-component-bg divide-y divide-border-light rounded-lg shadow-md w-44 dark:bg-dark-component-bg">
                            <ul>
                                <li><a href="#" class="filter-status block px-4 py-2 hover:bg-body-bg dark:hover:bg-dark-body-bg" data-status="">All</a></li>
                                <li><a href="#" class="filter-status block px-4 py-2 hover:bg-body-bg dark:hover:bg-dark-body-bg" data-status="2">Approved</a></li>
                                <li><a href="#" class="filter-status block px-4 py-2 hover:bg-body-bg dark:hover:bg-dark-body-bg" data-status="1">Pending</a></li>
                                <li><a href="#" class="filter-status block px-4 py-2 hover:bg-body-bg dark:hover:bg-dark-body-bg" data-status="3">Rejected</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-text-secondary dark:text-dark-text-secondary">
                    <thead class="text-xs uppercase bg-body-bg dark:bg-dark-component-bg/50">
                        <tr>
                            <th scope="col" class="px-6 py-3">No</th>
                            <th scope="col" class="px-6 py-3">Judul MoM</th>
                            <th scope="col" class="px-6 py-3">Created At</th>
                            <th scope="col" class="px-6 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody id="mom-table-body">
                        @forelse($recentMoms as $index => $mom)
                        <tr class="border-b dark:border-border-dark">
                            <td class="px-6 py-4">{{ $index + 1 }}</td>
                            <th scope="row" class="px-6 py-4 font-medium text-text-primary dark:text-white whitespace-nowrap">
                                {{ $mom->title }}
                            </th>
                            <td class="px-6 py-4">{{ $mom->created_at->format('d M Y') }}</td>
                            <td class="px-6 py-4">
                                @php
                                    $statusColors = [
                                        1 => ['bg' => 'yellow', 'text' => 'Pending'],
                                        2 => ['bg' => 'green', 'text' => 'Approved'],
                                        3 => ['bg' => 'red', 'text' => 'Rejected']
                                    ];
                                    $statusColor = $statusColors[$mom->status_id] ?? ['bg' => 'gray', 'text' => 'Unknown'];
                                @endphp
                                <span class="bg-{{ $statusColor['bg'] }}-100 text-{{ $statusColor['bg'] }}-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-full dark:bg-{{ $statusColor['bg'] }}-900 dark:text-{{ $statusColor['bg'] }}-300">
                                    {{ $statusColor['text'] }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-text-secondary">No MoM data available</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener("DOMContentLoaded", () => {
    // Data dari controller
    const data = @json($chartData);

    const chartOptions = {
        series: [
            { name: "Approved", color: "#DC2626", data: data.week.series[0].data },
            { name: "Pending", color: "#facc15", data: data.week.series[1].data }
        ],
        chart: { type: "bar", height: "320px", fontFamily: "Inter, sans-serif", toolbar: { show: false } },
        plotOptions: { bar: { horizontal: false, columnWidth: "70%", borderRadiusApplication: "end", borderRadius: 8 } },
        tooltip: { shared: true, intersect: false, style: { fontFamily: "Inter, sans-serif" } },
        states: { hover: { filter: { type: "darken", value: 1 } } },
        stroke: { show: true, width: 0, colors: ["transparent"] },
        grid: { show: false },
        dataLabels: { enabled: false },
        legend: { show: false },
        xaxis: {
            categories: data.week.categories,
            floating: false,
            labels: {
                show: true,
                style: {
                    fontFamily: "Inter, sans-serif",
                    cssClass: 'text-xs font-normal fill-text-secondary dark:fill-dark-text-secondary'
                }
            },
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: { show: false },
        fill: { opacity: 1 }
    };

    const chart = new ApexCharts(document.getElementById("column-chart"), chartOptions);
    chart.render();

    // Filter buttons logic
    const filterButtons = document.querySelectorAll('.chart-filter-btn');
    const chartSubtitle = document.getElementById('chart-subtitle');
    const subtitles = {
        week: 'Progress per minggu',
        month: 'Progress per bulan',
        year: 'Progress per tahun'
    };

    filterButtons.forEach(button => {
        button.addEventListener('click', () => {
            const filter = button.id.split('-')[1];

            filterButtons.forEach(btn => {
                btn.classList.remove('bg-primary', 'text-white');
                btn.classList.add('hover:bg-body-bg', 'dark:hover:bg-dark-body-bg');
            });
            button.classList.add('bg-primary', 'text-white');
            button.classList.remove('hover:bg-body-bg', 'dark:hover:bg-dark-body-bg');

            chartSubtitle.textContent = subtitles[filter];

            chart.updateOptions({
                series: [
                    { data: data[filter].series[0].data },
                    { data: data[filter].series[1].data }
                ],
                xaxis: {
                    categories: data[filter].categories
                }
            });
        });
    });

    // Search and Filter functionality
    const searchInput = document.getElementById('simple-search');
    let currentStatus = '';

    searchInput.addEventListener('input', debounce(function() {
        filterMoms(this.value, currentStatus);
    }, 500));

    document.querySelectorAll('.filter-status').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            currentStatus = this.dataset.status;
            filterMoms(searchInput.value, currentStatus);
        });
    });

    function filterMoms(search, status) {
        fetch(`{{ route('dashboard.search') }}?search=${search}&status=${status}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            updateTable(data);
        })
        .catch(error => console.error('Error:', error));
    }

    function updateTable(moms) {
        const tbody = document.getElementById('mom-table-body');
        const statusColors = {
            1: { bg: 'yellow', text: 'Pending' },
            2: { bg: 'green', text: 'Approved' },
            3: { bg: 'red', text: 'Rejected' }
        };

        if (moms.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4" class="px-6 py-4 text-center text-text-secondary">No MoM data available</td></tr>';
            return;
        }

        tbody.innerHTML = moms.map((mom, index) => {
            const status = statusColors[mom.status_id] || { bg: 'gray', text: 'Unknown' };
            const date = new Date(mom.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });

            return `
                <tr class="border-b dark:border-border-dark">
                    <td class="px-6 py-4">${index + 1}</td>
                    <th scope="row" class="px-6 py-4 font-medium text-text-primary dark:text-white whitespace-nowrap">
                        ${mom.title}
                    </th>
                    <td class="px-6 py-4">${date}</td>
                    <td class="px-6 py-4">
                        <span class="bg-${status.bg}-100 text-${status.bg}-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-full dark:bg-${status.bg}-900 dark:text-${status.bg}-300">
                            ${status.text}
                        </span>
                    </td>
                </tr>
            `;
        }).join('');
    }

    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
});
</script>
@endpush
