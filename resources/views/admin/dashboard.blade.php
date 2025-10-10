@extends('admin.layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-text-primary dark:text-dark-text-primary">Admin Dashboard 👋</h1>
            <p class="mt-1 text-text-secondary dark:text-dark-text-secondary">
                Ringkasan aktivitas sistem pada {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}.
            </p>
        </div>
    </div>

    {{-- Main Statistic Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <a href="{{ route('admin.approvals.index') }}"
           class="flex items-center justify-between p-6 rounded-lg bg-component-bg dark:bg-dark-component-bg shadow-md hover:shadow-xl transition-all duration-200 transform hover:-translate-y-1 cursor-pointer group">
            <div>
                <p class="text-3xl font-bold text-yellow-500 group-hover:text-yellow-600">{{ $stats['pending'] }}</p>
                <p class="text-md text-text-secondary mt-1 group-hover:text-text-primary">Menunggu Persetujuan</p>
            </div>
            <div class="flex items-center justify-center h-16 w-16 rounded-full bg-yellow-100 dark:bg-yellow-500/20 group-hover:bg-yellow-200 dark:group-hover:bg-yellow-500/30 transition-colors">
                <i class="fa-solid fa-hourglass-half fa-xl text-yellow-500"></i>
            </div>
        </a>

        <div class="flex items-center justify-between p-6 rounded-lg bg-component-bg dark:bg-dark-component-bg shadow-md">
            <div>
                <p class="text-3xl font-bold text-green-500">{{ $stats['approved'] }}</p>
                <p class="text-md text-text-secondary mt-1">MoM Disetujui</p>
            </div>
            <div class="flex items-center justify-center h-16 w-16 rounded-full bg-green-100 dark:bg-green-500/20">
                <i class="fa-solid fa-check-double fa-xl text-green-500"></i>
            </div>
        </div>

        <div class="flex items-center justify-between p-6 rounded-lg bg-component-bg dark:bg-dark-component-bg shadow-md">
            <div>
                <p class="text-3xl font-bold text-red-500">{{ $stats['rejected'] }}</p>
                <p class="text-md text-text-secondary mt-1">MoM Ditolak</p>
            </div>
            <div class="flex items-center justify-center h-16 w-16 rounded-full bg-red-100 dark:bg-red-500/20">
                <i class="fa-solid fa-times-circle fa-xl text-red-500"></i>
            </div>
        </div>

        <div class="flex items-center justify-between p-6 rounded-lg bg-component-bg dark:bg-dark-component-bg shadow-md">
            <div>
                <p class="text-3xl font-bold text-blue-500">{{ $stats['active_users'] }}</p>
                <p class="text-md text-text-secondary mt-1">Total Pengguna</p>
            </div>
            <div class="flex items-center justify-center h-16 w-16 rounded-full bg-blue-100 dark:bg-blue-500/20">
                <i class="fa-solid fa-users fa-xl text-blue-500"></i>
            </div>
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
            <div class="p-4 border-b dark:border-border-dark flex justify-between items-center">
                <h5 class="text-xl font-bold text-text-primary dark:text-white">Menunggu Persetujuan Anda</h5>
                <a href="{{ url('admin/approvals') }}"
                   class="text-sm text-primary hover:underline font-medium">
                    Lihat Semua →
                </a>
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
                        @forelse($pendingApprovals as $mom)
                        <tr class="border-b dark:border-border-dark hover:bg-body-bg dark:hover:bg-dark-body-bg">
                            <th scope="row" class="px-6 py-4 font-medium text-text-primary dark:text-white">
                                {{ \Str::limit($mom->title, 50) }}
                            </th>
                            <td class="px-6 py-4">{{ $mom->creator->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4">{{ $mom->created_at->format('d M Y') }}</td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('admin.moms.show', $mom->version_id) }}"
                                   class="font-medium text-primary hover:underline">
                                    Review
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-text-secondary">
                                <div class="flex flex-col items-center justify-center py-4">
                                    <i class="fa-solid fa-check-circle text-4xl text-green-500 mb-2"></i>
                                    <p class="text-sm">Tidak ada MoM yang menunggu persetujuan</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Most Active Users --}}
        <div class="bg-component-bg rounded-lg shadow-md dark:bg-dark-component-bg p-4 md:p-6 h-full">
            <h5 class="text-xl font-bold text-text-primary dark:text-white mb-4">Pengguna Teraktif Bulan Ini</h5>
            <ul class="space-y-4">
                @forelse($activeUsers as $index => $user)
                <li class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 rounded-full {{ $index == 0 ? 'bg-yellow-100 dark:bg-yellow-500/20' : 'bg-primary/20' }} flex items-center justify-center">
                            @if($index == 0)
                                <i class="fa-solid fa-crown text-yellow-500"></i>
                            @else
                                <span class="text-primary font-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <div class="font-medium text-text-primary dark:text-white truncate">
                                {{ $user->name }}
                            </div>
                            @if($index == 0)
                                <span class="px-2 py-0.5 text-xs font-semibold bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300 rounded-full">
                                    Top
                                </span>
                            @endif
                        </div>
                        <div class="text-sm text-text-secondary dark:text-dark-text-secondary">
                            Membuat {{ $user->created_moms_count }} MoM
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-primary">
                        #{{ $index + 1 }}
                    </div>
                </li>
                @empty
                <li class="text-center text-text-secondary py-8">
                    <i class="fa-solid fa-users text-4xl text-gray-400 mb-2"></i>
                    <p class="text-sm">Belum ada aktivitas bulan ini</p>
                </li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        // Data dari backend
        const weeklyData = @json($weeklyData);
        const statusBreakdown = @json($statusBreakdown);

        // Line Chart: Weekly Activity
        const weeklyChartOptions = {
            chart: {
                type: 'area',
                height: 350,
                toolbar: { show: false },
                fontFamily: 'Inter, sans-serif'
            },
            series: weeklyData.series,
            xaxis: {
                categories: weeklyData.categories,
                labels: {
                    style: {
                        colors: '#6B7280',
                        fontFamily: 'Inter, sans-serif'
                    }
                }
            },
            yaxis: {
                labels: {
                    style: {
                        colors: '#6B7280',
                        fontFamily: 'Inter, sans-serif'
                    }
                }
            },
            colors: ['#4f46e5', '#16a34a'],
            stroke: { curve: 'smooth', width: 2 },
            dataLabels: { enabled: false },
            grid: {
                borderColor: '#E5E7EB20',
                strokeDashArray: 4
            },
            tooltip: {
                theme: 'dark',
                style: {
                    fontFamily: 'Inter, sans-serif'
                }
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.4,
                    opacityTo: 0.1,
                }
            }
        };
        const weeklyChart = new ApexCharts(document.querySelector("#weekly-activity-chart"), weeklyChartOptions);
        weeklyChart.render();

        // Donut Chart: Status Breakdown
        const statusChartOptions = {
            chart: {
                type: 'donut',
                height: 350,
                fontFamily: 'Inter, sans-serif'
            },
            series: [statusBreakdown.approved, statusBreakdown.pending, statusBreakdown.rejected],
            labels: ['Disetujui', 'Menunggu', 'Ditolak'],
            colors: ['#22c55e', '#facc15', '#ef4444'],
            legend: {
                labels: {
                    colors: '#6B7280',
                    useSeriesColors: false
                },
                fontFamily: 'Inter, sans-serif',
                position: 'bottom'
            },
            plotOptions: {
                pie: {
                    donut: {
                        labels: {
                            show: true,
                            name: {
                                show: true,
                                fontSize: '16px',
                                fontWeight: 600,
                                color: '#374151'
                            },
                            value: {
                                show: true,
                                fontSize: '24px',
                                fontWeight: 700,
                                color: '#111827'
                            },
                            total: {
                                show: true,
                                label: 'Total MoM',
                                fontSize: '14px',
                                fontWeight: 600,
                                color: '#6B7280',
                                formatter: function (w) {
                                    return w.globals.seriesTotals.reduce((a, b) => {
                                        return a + b
                                    }, 0)
                                }
                            }
                        }
                    }
                }
            },
            dataLabels: {
                enabled: true,
                formatter: function (val) {
                    return Math.round(val) + "%"
                }
            },
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
