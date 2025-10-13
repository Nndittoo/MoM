@extends('layouts.app')

@section('title', 'Dashboard | TR1 MoMatic')

@push('styles')
{{-- Menambahkan style untuk animasi --}}
<style>
    /* Animasi fade-in dan slide-up untuk kartu */
    .card-animate {
        opacity: 0;
        transform: translateY(20px);
        animation: fadeInSlideUp 0.6s ease-out forwards;
    }

    /* Delay animasi agar kartu muncul berurutan */
    .card-animate:nth-child(1) { animation-delay: 0.1s; }
    .card-animate:nth-child(2) { animation-delay: 0.2s; }
    .card-animate:nth-child(3) { animation-delay: 0.3s; }
    .card-animate:nth-child(4) { animation-delay: 0.4s; }

    @keyframes fadeInSlideUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Animasi pulse halus untuk tombol utama */
    .btn-pulse {
        animation: pulse-animation 2s infinite;
    }

    @keyframes pulse-animation {
        0% { box-shadow: 0 0 8px rgba(239, 68, 68, 0.6); }
        50% { box-shadow: 0 0 20px rgba(239, 68, 68, 0.9); }
        100% { box-shadow: 0 0 8px rgba(239, 68, 68, 0.6); }
    }
    .text-neon-red {
    color: #EF4444 !important; /* Tambahkan !important di sini */
    text-shadow: 0 0 5px rgba(239, 68, 68, 0.7);
}
</style>
@endpush

@section('content')
<div class="pt-2">
    <div class="space-y-8">

        {{-- PERUBAHAN BESAR: Welcome Banner dibuat lebih seimbang dan informatif --}}
        <div class="flex flex-col md:flex-row items-center justify-between p-6 md:p-8 rounded-xl shadow-lg shimmer-bg bg-gray-800 border-l-4 border-red-500">
            <div class="flex items-center w-full">
                {{-- Teks dipindahkan ke kiri untuk keseimbangan --}}
                <div class="flex-grow">
                    <h1 class="text-3xl lg:text-4xl font-bold font-orbitron text-neon-red">
                        <span id="greeting">Selamat Pagi</span>, {{ explode(' ', auth()->user()->name)[0] }}!
                    </h1>
                    <p class="mt-2 text-gray-400">
                        Anda memiliki <strong class="text-white">{{ $stats['tasks_due'] ?? 0 }}</strong> tugas yang mendekati tenggat.
                    </p>
                    <p class="text-sm text-gray-500">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>

                    {{-- Tombol utama dipindahkan ke dalam banner untuk akses cepat --}}
                    <div class="mt-6">
                        <a href="{{ url('/create') }}"
                           class="inline-flex justify-center items-center px-6 py-3 text-base font-semibold text-white btn-neon-red rounded-lg shadow-lg btn-pulse">
                            <i class="fa-solid fa-plus mr-2"></i>New MoM
                        </a>
                    </div>
                </div>

                {{-- Animasi Lottie di sebelah kanan --}}
                <div class="hidden md:block flex-shrink-0 ml-8">
                    <dotlottie-wc src="https://lottie.host/2b90db48-64c1-4db2-94aa-7e0050057845/1VnmfIXw32.lottie"
                                  style="width: 250px; height: 250px;" autoplay loop>
                    </dotlottie-wc>
                </div>
            </div>
        </div>

        {{-- PERUBAHAN: Menambahkan kelas 'card-animate' untuk efek animasi pada kartu --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="card-animate flex items-center justify-between p-6 rounded-xl bg-gray-800 shadow-md border border-gray-700 hover:border-red-500 transition-all">
                <div>
                    <p class="text-3xl font-bold text-white">{{ $stats['approved'] ?? 0 }}</p>
                    <p class="text-md text-gray-400 mt-1">MoM Approved</p>
                </div>
                <div class="flex items-center justify-center h-16 w-16 rounded-full bg-red-500/10">
                    <i class="fa-solid fa-check-double fa-xl text-red-500"></i>
                </div>
            </div>

            <div class="card-animate flex items-center justify-between p-6 rounded-xl bg-gray-800 shadow-md border border-gray-700 hover:border-red-500 transition-all">
                <div>
                    <p class="text-3xl font-bold text-white">{{ $stats['pending'] ?? 0 }}</p>
                    <p class="text-md text-gray-400 mt-1">MoM Pending</p>
                </div>
                <div class="flex items-center justify-center h-16 w-16 rounded-full bg-red-500/10">
                    <i class="fa-solid fa-clock fa-xl text-red-500"></i>
                </div>
            </div>

            <div class="card-animate flex items-center justify-between p-6 rounded-xl bg-gray-800 shadow-md border border-gray-700 hover:border-red-500 transition-all">
                <div>
                    <p class="text-3xl font-bold text-white">{{ $stats['tasks_due'] ?? 0 }}</p>
                    <p class="text-md text-gray-400 mt-1">Tasks Due</p>
                </div>
                <div class="flex items-center justify-center h-16 w-16 rounded-full bg-red-500/10">
                    <i class="fa-solid fa-triangle-exclamation fa-xl text-red-500"></i>
                </div>
            </div>

            <div class="card-animate flex items-center justify-between p-6 rounded-xl bg-gray-800 shadow-md border border-gray-700 hover:border-red-500 transition-all">
                <div>
                    <p class="text-3xl font-bold text-white">{{ $stats['tasks_completed'] ?? 0 }}</p>
                    <p class="text-md text-gray-400 mt-1">Tasks Completed</p>
                </div>
                <div class="flex items-center justify-center h-16 w-16 rounded-full bg-red-500/10">
                    <i class="fa-solid fa-clipboard-check fa-xl text-red-500"></i>
                </div>
            </div>
        </div>

        {{-- Sisa konten (Chart, Recent Activity, Tabel MoM) tetap sama --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- MoM Statistics Chart --}}
            <div class="lg:col-span-2 w-full bg-gray-800 rounded-xl shadow-md p-4 md:p-6 border border-gray-700">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4">
                    <div>
                        <h5 class="text-2xl font-bold text-white font-orbitron pb-1">MoM Statistics</h5>
                        <p id="chart-subtitle" class="text-sm text-gray-400">Progress per minggu</p>
                    </div>
                    <div class="flex items-center space-x-1 text-sm mt-3 sm:mt-0 p-1 bg-gray-900 rounded-lg">
                        <button id="filter-week" class="chart-filter-btn px-3 py-1 rounded-md bg-red-600 text-white">Minggu</button>
                        <button id="filter-month" class="chart-filter-btn px-3 py-1 rounded-md text-gray-400 hover:bg-gray-700">Bulan</button>
                        <button id="filter-year" class="chart-filter-btn px-3 py-1 rounded-md text-gray-400 hover:bg-gray-700">Tahun</button>
                    </div>
                </div>
                <div id="column-chart" class="mt-4"></div>
            </div>

            {{-- Recent Activity --}}
            <div class="lg:col-span-1 bg-gray-800 rounded-xl shadow-md p-4 md:p-6 h-full border border-gray-700">
                <h5 class="text-xl font-bold text-white font-orbitron mb-4">Recent Activity</h5>
                <ol class="relative border-s border-gray-700">
                    @forelse($recentActivity as $activity)
                    <li class="mb-6 ms-6">
                        {{-- PERUBAHAN: Ikon timeline seragam merah --}}
                        <span class="absolute flex items-center justify-center w-6 h-6 bg-red-900 rounded-full -start-3 ring-8 ring-gray-800">
                            <i class="fa-solid {{ $activity['icon'] }} text-red-500"></i>
                        </span>
                        <h3 class="flex items-center mb-1 text-lg font-semibold text-white">
                            {{ $activity['title'] }}
                        </h3>
                        @if(isset($activity['subtitle']))
                        <p class="text-sm text-gray-400 mb-1">
                            {{ $activity['subtitle'] }}
                        </p>
                        @endif
                        <time class="block mb-2 text-sm text-gray-400">
                            @if($activity['type'] === 'task_due')
                                Due on {{ \Carbon\Carbon::parse($activity['date'])->format('M jS, Y') }}
                            @else
                                {{ \Carbon\Carbon::parse($activity['date'])->diffForHumans() }}
                            @endif
                        </time>
                    </li>
                    @empty
                    <li class="ms-6">
                        <p class="text-gray-400">No recent activity</p>
                    </li>
                    @endforelse
                </ol>
            </div>
        </div>

        {{-- Recent MoMs Table --}}
        {{-- ======================================================= --}}
{{--         KOMPONEN TABEL MOM YANG TELAH DIPERBARUI        --}}
{{-- ======================================================= --}}

<div class="bg-gray-800 shadow-md sm:rounded-xl overflow-hidden border border-gray-700">
    <div class="p-4 md:p-6">
        <h5 class="text-xl font-bold text-white font-orbitron">Recent MoM</h5>
        <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 mt-4">
            <div class="w-full md:w-1/2">
                <form class="flex items-center" id="search-form">
                    <label for="simple-search" class="sr-only">Search</label>
                    <div class="relative w-full">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="fa-solid fa-search text-gray-400"></i>
                        </div>
                        <input type="text" id="simple-search" name="search" class="bg-gray-900 border border-gray-700 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full pl-10 p-2.5" placeholder="Search MoM">
                    </div>
                </form>
            </div>
            <div class="w-full md:w-auto flex items-center space-x-3">
                <button id="dropdownActionButton" data-dropdown-toggle="dropdownAction" class="w-full md:w-auto flex items-center justify-center py-2.5 px-4 text-sm font-medium text-gray-300 focus:outline-none bg-gray-800 rounded-lg border border-gray-700 hover:bg-gray-700 focus:z-10 focus:ring-2 focus:ring-red-500" type="button">
                    Filter<i class="fa-solid fa-chevron-down w-2.5 h-2.5 ms-2.5"></i>
                </button>
                <div id="dropdownAction" class="z-10 hidden bg-gray-800 divide-y divide-gray-700 rounded-lg shadow-lg w-44 border border-gray-700">
                    <ul class="py-1 text-sm text-gray-300">
                        <li><a href="#" class="filter-status block px-4 py-2 hover:bg-gray-700" data-status="">All</a></li>
                        <li><a href="#" class="filter-status block px-4 py-2 hover:bg-gray-700" data-status="2">Approved</a></li>
                        <li><a href="#" class="filter-status block px-4 py-2 hover:bg-gray-700" data-status="1">Pending</a></li>
                        <li><a href="#" class="filter-status block px-4 py-2 hover:bg-gray-700" data-status="3">Rejected</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-400">
            <thead class="text-xs uppercase bg-gray-700/50 text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">No</th>
                    <th scope="col" class="px-6 py-3">Judul MoM</th>
                    <th scope="col" class="px-6 py-3">Created At</th>
                    <th scope="col" class="px-6 py-3">Status</th>
                </tr>
            </thead>
            <tbody id="mom-table-body">
                @forelse($recentMoms as $index => $mom)
                <tr class="border-b border-gray-700 hover:bg-gray-700/50">
                    <td class="px-6 py-4">{{ $index + 1 }}</td>
                    <th scope="row" class="px-6 py-4 font-medium text-white whitespace-nowrap">
                        {{ $mom->title }}
                    </th>
                    <td class="px-6 py-4">{{ $mom->created_at->format('d M Y') }}</td>
                    <td class="px-6 py-4">

                        {{-- PERUBAHAN BESAR: Desain ulang badge status menjadi titik warna + teks --}}
                        @php
                            $statusInfo = [
                                1 => ['dot' => 'bg-yellow-400', 'text' => 'text-yellow-300', 'label' => 'Pending'],
                                2 => ['dot' => 'bg-green-500',  'text' => 'text-green-400',  'label' => 'Approved'],
                                3 => ['dot' => 'bg-red-500',    'text' => 'text-red-400',    'label' => 'Rejected']
                            ];
                            $currentStatus = $statusInfo[$mom->status_id] ?? ['dot' => 'bg-gray-500', 'text' => 'text-gray-400', 'label' => 'Unknown'];
                        @endphp

                        <div class="inline-flex items-center gap-x-2">
                            <span class="w-2.5 h-2.5 rounded-full {{ $currentStatus['dot'] }}"></span>
                            <span class="text-xs font-medium {{ $currentStatus['text'] }}">
                                {{ $currentStatus['label'] }}
                            </span>
                        </div>

                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">No MoM data available</td>
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

    // PERUBAHAN: Opsi chart disesuaikan dengan tema
    const chartOptions = {
        series: [
            // Warna disesuaikan dengan tema merah dan kuning
            { name: "Approved", color: "#EF4444", data: data.week.series[0].data },
            { name: "Pending", color: "#facc15", data: data.week.series[1].data }
        ],
        chart: { type: "bar", height: "320px", fontFamily: "Inter, sans-serif", toolbar: { show: false } },
        plotOptions: { bar: { horizontal: false, columnWidth: "70%", borderRadiusApplication: "end", borderRadius: 8 } },
        tooltip: { theme: 'dark', shared: true, intersect: false, style: { fontFamily: "Inter, sans-serif" } },
        states: { hover: { filter: { type: "darken", value: 1 } } },
        stroke: { show: true, width: 0, colors: ["transparent"] },
        grid: { show: false },
        dataLabels: { enabled: false },
        legend: { show: false },
        xaxis: {
            categories: data.week.categories,
            labels: {
                style: {
                    fontFamily: "Inter, sans-serif",
                    colors: '#9CA3AF' // Warna teks abu-abu
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

    // PERUBAHAN: Logika styling filter button disesuaikan
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
                btn.classList.remove('bg-red-600', 'text-white');
                btn.classList.add('text-gray-400', 'hover:bg-gray-700');
            });
            button.classList.add('bg-red-600', 'text-white');
            button.classList.remove('text-gray-400', 'hover:bg-gray-700');

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
    // ... (Sisa dari JavaScript Anda untuk search dan filter tetap sama) ...
});

document.addEventListener("DOMContentLoaded", () => {
        const greetingElement = document.getElementById('greeting');
        const currentHour = new Date().getHours();

        if (currentHour < 12) {
            greetingElement.textContent = 'Selamat Pagi';
        } else if (currentHour < 18) {
            greetingElement.textContent = 'Selamat Siang';
        } else {
            greetingElement.textContent = 'Selamat Malam';
        }
    });
</script>
<script
  src="https://unpkg.com/@lottiefiles/dotlottie-wc@0.8.1/dist/dotlottie-wc.js"
  type="module"
></script>
@endpush
