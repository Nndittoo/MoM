@extends('admin.layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-text-primary dark:text-dark-text-primary">Admin Dashboard 👋</h1>
            <p class="mt-1 text-text-secondary dark:text-dark-text-secondary">Ringkasan aktivitas sistem pada {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}.</p>
        </div>
    </div>

    {{-- Main Statistic Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="flex items-center justify-between p-6 rounded-lg bg-component-bg dark:bg-dark-component-bg shadow-md">
            <div>
                <p class="text-3xl font-bold text-yellow-500">5</p>
                <p class="text-md text-text-secondary mt-1">Menunggu Persetujuan</p>
            </div>
            <div class="flex items-center justify-center h-16 w-16 rounded-full bg-yellow-100 dark:bg-yellow-500/20"><i class="fa-solid fa-hourglass-half fa-xl text-yellow-500"></i></div>
        </div>
        <div class="flex items-center justify-between p-6 rounded-lg bg-component-bg dark:bg-dark-component-bg shadow-md">
            <div>
                <p class="text-3xl font-bold text-green-500">28</p>
                <p class="text-md text-text-secondary mt-1">MoM Disetujui</p>
            </div>
            <div class="flex items-center justify-center h-16 w-16 rounded-full bg-green-100 dark:bg-green-500/20"><i class="fa-solid fa-check-double fa-xl text-green-500"></i></div>
        </div>
        <div class="flex items-center justify-between p-6 rounded-lg bg-component-bg dark:bg-dark-component-bg shadow-md">
            <div>
                <p class="text-3xl font-bold text-red-500">3</p>
                <p class="text-md text-text-secondary mt-1">MoM Ditolak</p>
            </div>
            <div class="flex items-center justify-center h-16 w-16 rounded-full bg-red-100 dark:bg-red-500/20"><i class="fa-solid fa-times-circle fa-xl text-red-500"></i></div>
        </div>
        <div class="flex items-center justify-between p-6 rounded-lg bg-component-bg dark:bg-dark-component-bg shadow-md">
            <div>
                <p class="text-3xl font-bold text-blue-500">34</p>
                <p class="text-md text-text-secondary mt-1">Total Pengguna Aktif</p>
            </div>
            <div class="flex items-center justify-center h-16 w-16 rounded-full bg-blue-100 dark:bg-blue-500/20"><i class="fa-solid fa-users fa-xl text-blue-500"></i></div>
        </div>
    </div>

    {{-- Chart Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Line Chart: Weekly Activity --}}
        <div class="lg:col-span-2 w-full bg-component-bg rounded-lg shadow-md dark:bg-dark-component-bg p-4 md:p-6">
            <h5 class="text-xl font-bold text-text-primary dark:text-white pb-1">Aktivitas MoM Mingguan</h5>
            <p class="text-sm text-text-secondary dark:text-dark-text-secondary">MoM Dibuat vs. Disetujui (7 hari terakhir)</p>
            <div id="weekly-activity-chart" class="mt-4"></div>
        </div>

        {{-- Donut Chart: Status Breakdown --}}
        <div class="bg-component-bg rounded-lg shadow-md dark:bg-dark-component-bg p-4 md:p-6">
            <h5 class="text-xl font-bold text-text-primary dark:text-white mb-4">Status Seluruh MoM</h5>
            <div id="status-breakdown-chart" class="mt-4"></div>
        </div>
    </div>

    {{-- Tables and Other Activities Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Pending Approvals Table --}}
        <div class="lg:col-span-2 bg-component-bg dark:bg-dark-component-bg shadow-md sm:rounded-lg overflow-hidden">
            <div class="p-4 border-b dark:border-border-dark">
                <h5 class="text-xl font-bold text-text-primary dark:text-white">Menunggu Persetujuan Anda</h5>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-text-secondary dark:text-dark-text-secondary">
                    <thead class="text-xs uppercase bg-body-bg dark:bg-dark-component-bg/50">
                        <tr>
                            <th scope="col" class="px-6 py-3">Judul MoM</th>
                            <th scope="col" class="px-6 py-3">Pembuat</th>
                            <th scope="col" class="px-6 py-3">Tanggal Dibuat</th>
                            <th scope="col" class="px-6 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b dark:border-border-dark"><th scope="row" class="px-6 py-4 font-medium text-text-primary dark:text-white whitespace-nowrap">Evaluasi Kinerja Tim Q3</th><td class="px-6 py-4">Neil Sims</td><td class="px-6 py-4">30 Sep 2025</td><td class="px-6 py-4 text-center"><a href="{{ url('/detail') }}" class="font-medium text-primary dark:text-primary-dark hover:underline">Review</a></td></tr>
                        <tr class="border-b dark:border-border-dark"><th scope="row" class="px-6 py-4 font-medium text-text-primary dark:text-white whitespace-nowrap">Perencanaan Fitur Baru v2.1</th><td class="px-6 py-4">Bonnie Green</td><td class="px-6 py-4">29 Sep 2025</td><td class="px-6 py-4 text-center"><a href="{{ url('/detail') }}" class="font-medium text-primary dark:text-primary-dark hover:underline">Review</a></td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Most Active Users --}}
        <div class="bg-component-bg rounded-lg shadow-md dark:bg-dark-component-bg p-4 md:p-6 h-full">
            <h5 class="text-xl font-bold text-text-primary dark:text-white mb-4">Pengguna Teraktif</h5>
            <ul class="space-y-4">
                <li class="flex items-center space-x-4"><img class="w-10 h-10 rounded-full" src="https://flowbite.com/docs/images/people/profile-picture-1.jpg" alt="Neil image"><div class="font-medium dark:text-white"><div>Neil Sims</div><div class="text-sm text-gray-500 dark:text-gray-400">Membuat 18 MoM</div></div></li>
                <li class="flex items-center space-x-4"><img class="w-10 h-10 rounded-full" src="https://flowbite.com/docs/images/people/profile-picture-3.jpg" alt="Bonnie image"><div class="font-medium dark:text-white"><div>Bonnie Green</div><div class="text-sm text-gray-500 dark:text-gray-400">Membuat 15 MoM</div></div></li>
                <li class="flex items-center space-x-4"><img class="w-10 h-10 rounded-full" src="https://flowbite.com/docs/images/people/profile-picture-2.jpg" alt="Michael image"><div class="font-medium dark:text-white"><div>Michael Gough</div><div class="text-sm text-gray-500 dark:text-gray-400">Membuat 11 MoM</div></div></li>
            </ul>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        // Line Chart: Weekly Activity
        const weeklyChartOptions = {
            chart: { type: 'area', height: 350, toolbar: { show: false } },
            series: [
                { name: 'MoM Dibuat', data: [10, 15, 12, 18, 14, 20, 17] },
                { name: 'MoM Disetujui', data: [8, 12, 10, 15, 11, 18, 15] }
            ],
            xaxis: { 
                categories: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
                labels: { style: { colors: '#6B7280' } }
             },
            yaxis: { labels: { style: { colors: '#6B7280' } } },
            colors: ['#4f46e5', '#16a34a'],
            stroke: { curve: 'smooth' },
            dataLabels: { enabled: false },
            grid: { borderColor: '#E5E7EB20' },
            tooltip: { theme: 'dark' }
        };
        const weeklyChart = new ApexCharts(document.querySelector("#weekly-activity-chart"), weeklyChartOptions);
        weeklyChart.render();

        // Donut Chart: Status Breakdown
        const statusChartOptions = {
            chart: { type: 'donut', height: 350 },
            series: [125, 5, 12], // Data: Approved, Pending, Rejected
            labels: ['Disetujui', 'Menunggu', 'Ditolak'],
            colors: ['#22c55e', '#facc15', '#ef4444'],
            legend: { labels: { colors: '#6B7280' } },
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: { width: 200 },
                    legend: { position: 'bottom' }
                }
            }]
        };
        const statusChart = new ApexCharts(document.querySelector("#status-breakdown-chart"), statusChartOptions);
        statusChart.render();
    });
</script>
@endpush