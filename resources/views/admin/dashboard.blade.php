@extends('admin.layouts.app')

@section('title', 'Admin Dashboard | TR1 MoMatic')

@push('styles')
<style>
    /* Animasi fade-in untuk kartu statistik */
    .stat-card {
        opacity: 0;
        transform: translateY(15px);
        animation: fadeInSlideUp 0.5s ease-out forwards;
    }
    @keyframes fadeInSlideUp { to { opacity: 1; transform: translateY(0); } }
</style>
@endpush

@section('content')
<div class="space-y-6">
    {{-- Header Halaman --}}
    <div class="p-6 md:p-8 rounded-xl shadow-lg bg-gray-800 border-l-4 border-red-500">
        <h1 class="text-3xl font-bold font-orbitron text-neon-red">Admin Dashboard</h1>
        <p class="mt-1 text-gray-400">Ringkasan aktivitas sistem pada {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}.</p>
    </div>

    {{-- Kartu Statistik Utama --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        {{-- Kartu "Menunggu Persetujuan" (dibuat paling menonjol) --}}
        <a href="{{ route('admin.approvals.index') }}" style="animation-delay: 100ms;"
           class="stat-card flex items-center justify-between p-6 rounded-xl bg-gray-800 shadow-md border border-gray-700 hover:border-red-500 transition-all duration-300 transform hover:-translate-y-1 cursor-pointer group">
            <div>
                <p class="text-4xl font-bold text-red-400 group-hover:text-red-300">{{ $stats['pending'] }}</p>
                <p class="text-md text-gray-400 mt-1 group-hover:text-white">Menunggu Persetujuan</p>
            </div>
            <div class="flex items-center justify-center h-16 w-16 rounded-full bg-red-500/10 group-hover:bg-red-500/20 transition-colors">
                <i class="fa-solid fa-hourglass-half fa-xl text-red-400"></i>
            </div>
        </a>

        <div class="stat-card flex items-center justify-between p-6 rounded-xl bg-gray-800 shadow-md border border-gray-700" style="animation-delay: 200ms;">
            <div>
                <p class="text-3xl font-bold text-green-400">{{ $stats['approved'] }}</p>
                <p class="text-md text-gray-400 mt-1">MoM Disetujui</p>
            </div>
            <div class="flex items-center justify-center h-16 w-16 rounded-full bg-green-500/10">
                <i class="fa-solid fa-check-double fa-xl text-green-400"></i>
            </div>
        </div>

        <div class="stat-card flex items-center justify-between p-6 rounded-xl bg-gray-800 shadow-md border border-gray-700" style="animation-delay: 300ms;">
            <div>
                <p class="text-3xl font-bold text-gray-400">{{ $stats['rejected'] }}</p>
                <p class="text-md text-gray-400 mt-1">MoM Ditolak</p>
            </div>
            <div class="flex items-center justify-center h-16 w-16 rounded-full bg-gray-500/10">
                <i class="fa-solid fa-times-circle fa-xl text-gray-400"></i>
            </div>
        </div>

        <div class="stat-card flex items-center justify-between p-6 rounded-xl bg-gray-800 shadow-md border border-gray-700" style="animation-delay: 400ms;">
            <div>
                <p class="text-3xl font-bold text-blue-400">{{ $stats['active_users'] }}</p>
                <p class="text-md text-gray-400 mt-1">Total Pengguna</p>
            </div>
            <div class="flex items-center justify-center h-16 w-16 rounded-full bg-blue-500/10">
                <i class="fa-solid fa-users fa-xl text-blue-400"></i>
            </div>
        </div>
    </div>

    {{-- Bagian Chart --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 w-full bg-gray-800 rounded-xl shadow-md p-4 md:p-6 border border-gray-700">
            <h5 class="text-xl font-bold text-white font-orbitron pb-1">Aktivitas MoM Mingguan</h5>
            <p class="text-sm text-gray-400">MoM Dibuat vs. Disetujui (7 hari terakhir)</p>
            <div id="weekly-activity-chart" class="mt-4"></div>
        </div>
        <div class="bg-gray-800 rounded-xl shadow-md p-4 md:p-6 border border-gray-700">
            <h5 class="text-xl font-bold text-white font-orbitron mb-4">Status Seluruh MoM</h5>
            <div id="status-breakdown-chart" class="mt-4"></div>
        </div>
    </div>

    {{-- Bagian Tabel dan Aktivitas Lainnya --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-gray-800 shadow-md sm:rounded-lg overflow-hidden border border-gray-700">
            <div class="p-4 border-b border-gray-700 flex justify-between items-center">
                <h5 class="text-xl font-bold text-white font-orbitron">Menunggu Persetujuan</h5>
                <a href="{{ url('admin/approvals') }}" class="text-sm text-red-400 hover:underline font-medium">Lihat Semua →</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-400">
                    <thead class="text-xs uppercase bg-gray-700/50">
                        <tr>
                            <th scope="col" class="px-6 py-3">Judul MoM</th>
                            <th scope="col" class="px-6 py-3">Pembuat</th>
                            <th scope="col" class="px-6 py-3">Tanggal Dibuat</th>
                            <th scope="col" class="px-6 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingApprovals as $mom)
                        <tr class="border-b border-gray-700 hover:bg-gray-700/50">
                            <th scope="row" class="px-6 py-4 font-medium text-white whitespace-nowrap">{{ \Str::limit($mom->title, 50) }}</th>
                            <td class="px-6 py-4">{{ $mom->creator->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4">{{ $mom->created_at->format('d M Y') }}</td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('admin.moms.show', $mom->version_id) }}" class="font-medium text-red-400 hover:underline">Review</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center py-4">
                                    <i class="fa-solid fa-check-circle text-4xl text-green-500/50 mb-2"></i>
                                    <p class="text-sm">Tidak ada MoM yang menunggu persetujuan</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-gray-800 rounded-xl shadow-md p-4 md:p-6 h-full border border-gray-700">
            <h5 class="text-xl font-bold text-white font-orbitron mb-4">Pengguna Teraktif</h5>
            <ul class="space-y-4">
                @forelse($activeUsers as $index => $user)
                <li class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        @if($index == 0)
                            <div class="w-10 h-10 rounded-full bg-yellow-500/10 flex items-center justify-center ring-2 ring-yellow-500/50">
                                <i class="fa-solid fa-crown text-yellow-400"></i>
                            </div>
                        @else
                            <img class="w-10 h-10 rounded-full object-cover" src="{{ $user->avatar_url ?? 'https://i.pravatar.cc/100?u='.$user->id }}" alt="Avatar">
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-white truncate">{{ $user->name }}</p>
                        <p class="text-sm text-gray-400">Membuat {{ $user->created_moms_count }} MoM</p>
                    </div>
                    <div class="text-2xl font-bold text-gray-600">
                        #{{ $index + 1 }}
                    </div>
                </li>
                @empty
                <li class="text-center text-gray-500 py-8">
                    <i class="fa-solid fa-users-slash text-4xl text-gray-700 mb-2"></i>
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
        const weeklyData = @json($weeklyData);
        const statusBreakdown = @json($statusBreakdown);

        // Opsi untuk Line Chart (Tema Disesuaikan)
        const weeklyChartOptions = {
            chart: { type: 'area', height: 350, toolbar: { show: false }, fontFamily: 'Inter, sans-serif' },
            series: weeklyData.series,
            xaxis: { categories: weeklyData.categories, labels: { style: { colors: '#9CA3AF' } }, axisBorder: { show: false }, axisTicks: { show: false } },
            yaxis: { labels: { style: { colors: '#9CA3AF' } } },
            colors: ['#EF4444', '#22c55e'], // Merah untuk Dibuat, Hijau untuk Disetujui
            stroke: { curve: 'smooth', width: 2 },
            dataLabels: { enabled: false },
            grid: { borderColor: '#374151', strokeDashArray: 4 },
            tooltip: { theme: 'dark' },
            fill: { type: 'gradient', gradient: { shade: 'dark', shadeIntensity: 1, opacityFrom: 0.7, opacityTo: 0.1 } },
            legend: { labels: { colors: '#D1D5DB' } }
        };
        const weeklyChart = new ApexCharts(document.querySelector("#weekly-activity-chart"), weeklyChartOptions);
        weeklyChart.render();

        // Opsi untuk Donut Chart (Tema Disesuaikan)
        const statusChartOptions = {
            chart: { type: 'donut', height: 350, fontFamily: 'Inter, sans-serif' },
            series: [statusBreakdown.approved, statusBreakdown.pending, statusBreakdown.rejected],
            labels: ['Disetujui', 'Menunggu', 'Ditolak'],
            colors: ['#22c55e', '#facc15', '#EF4444'],
            legend: { position: 'bottom', labels: { colors: '#D1D5DB' } },
            plotOptions: {
                pie: {
                    donut: {
                        labels: {
                            show: true,
                            name: { show: true, color: '#9CA3AF' },
                            value: { show: true, color: '#FFFFFF', fontWeight: 700, fontSize: '24px' },
                            total: { show: true, label: 'Total MoM', color: '#9CA3AF',
                                formatter: (w) => w.globals.seriesTotals.reduce((a, b) => a + b, 0)
                            }
                        }
                    }
                }
            },
            dataLabels: { enabled: false },
        };
        const statusChart = new ApexCharts(document.querySelector("#status-breakdown-chart"), statusChartOptions);
        statusChart.render();
    });
</script>
@endpush
