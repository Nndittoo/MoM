@extends('layouts.app')

@section('title', 'Draft MoM | MoM Telkom')

@section('content')
<div class="p-4 rounded-lg mt-14">
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col md:flex-row items-center justify-between p-6 md:p-8 overflow-hidden rounded-lg shadow-md bg-component-bg dark:bg-dark-component-bg border-l-4 border-primary">
            <div class="flex items-center space-x-4">
                <div>
                    <h1 class="text-3xl font-bold text-text-primary dark:text-dark-text-primary">Draft MoM</h1>
                    <p class="mt-1 text-text-secondary dark:text-dark-text-secondary">Menampilkan semua MoM dari sistem.</p>
                </div>
            </div>
        </div>

        {{-- Tab and Filter Section --}}
        <div class="bg-component-bg dark:bg-dark-component-bg rounded-lg shadow p-4">
            <div class="flex flex-col md:flex-row justify-between items-center border-b border-border-light dark:border-border-dark pb-4">
                {{-- Tabs --}}
                <ul class="flex flex-wrap -mb-px text-sm font-medium text-center text-gray-500 dark:text-gray-400">
                    <li class="me-2">
                        <button onclick="switchTab('my-mom')" id="my-mom-tab" class="inline-flex items-center justify-center p-4 border-b-2 text-primary border-primary rounded-t-lg active" aria-current="page">
                            <i class="fa-solid fa-user me-2"></i>My MoM
                        </button>
                    </li>
                    <li class="me-2">
                        <button onclick="switchTab('all-mom')" id="all-mom-tab" class="inline-flex items-center justify-center p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300">
                            <i class="fa-solid fa-users me-2"></i>All MoM
                        </button>
                    </li>
                </ul>

                {{-- Filters --}}
                <div class="flex items-center space-x-3 mt-4 md:mt-0">
                    <button id="month-filter-button" data-dropdown-toggle="month-filter-dropdown" class="w-full md:w-auto flex items-center justify-center py-2 px-4 text-sm font-medium text-text-primary focus:outline-none bg-component-bg rounded-lg border border-border-light hover:bg-body-bg focus:z-10 focus:ring-4 focus:ring-primary/20 dark:bg-dark-component-bg dark:text-dark-text-secondary dark:border-border-dark" type="button">
                        <i class="fa-solid fa-calendar-day w-4 h-4 me-2"></i>Month
                        <i class="fa-solid fa-chevron-down w-2.5 h-2.5 ms-2.5"></i>
                    </button>
                    <div id="month-filter-dropdown" class="z-10 hidden bg-component-bg divide-y divide-border-light rounded-lg shadow-md w-44 dark:bg-dark-component-bg">
                        <ul class="py-1 text-sm text-gray-700 dark:text-gray-200">
                            <li><a href="#" class="block px-4 py-2 hover:bg-body-bg dark:hover:bg-dark-body-bg">September</a></li>
                            <li><a href="#" class="block px-4 py-2 hover:bg-body-bg dark:hover:bg-dark-body-bg">Oktober</a></li>
                            <li><a href="#" class="block px-4 py-2 hover:bg-body-bg dark:hover:bg-dark-body-bg">November</a></li>
                        </ul>
                    </div>
                    <button id="status-filter-button" data-dropdown-toggle="status-filter-dropdown" class="w-full md:w-auto flex items-center justify-center py-2 px-4 text-sm font-medium text-text-primary focus:outline-none bg-component-bg rounded-lg border border-border-light hover:bg-body-bg focus:z-10 focus:ring-4 focus:ring-primary/20 dark:bg-dark-component-bg dark:text-dark-text-secondary dark:border-border-dark" type="button">
                        <i class="fa-solid fa-filter w-4 h-4 me-2"></i>Status
                        <i class="fa-solid fa-chevron-down w-2.5 h-2.5 ms-2.5"></i>
                    </button>
                    <div id="status-filter-dropdown" class="z-10 hidden bg-component-bg divide-y divide-border-light rounded-lg shadow-md w-44 dark:bg-dark-component-bg">
                        <ul class="py-1 text-sm text-gray-700 dark:text-gray-200">
                            <li><a href="#" class="block px-4 py-2 hover:bg-body-bg dark:hover:bg-dark-body-bg">Approved</a></li>
                            <li><a href="#" class="block px-4 py-2 hover:bg-body-bg dark:hover:bg-dark-body-bg">Pending</a></li>
                            <li><a href="#" class="block px-4 py-2 hover:bg-body-bg dark:hover:bg-dark-body-bg">Rejected</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Tab Content --}}
            <div class="pt-6">
                {{-- My MoM Content --}}
                <div id="my-mom-content">
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        {{-- My MoM Card - Pending --}}
                        <div class="bg-component-bg dark:bg-dark-component-bg rounded-2xl shadow-lg overflow-hidden transition-all duration-300 hover:shadow-2xl hover:-translate-y-2">
                            <div class="relative">
                                <img class="w-full h-48 object-cover" src="{{ asset('lampiran.png') }}" alt="Dokumentasi Rapat">
                                <span class="absolute top-3 right-3 bg-yellow-500 text-white text-xs font-semibold px-3 py-1 rounded-full shadow-md">Pending</span>
                                <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                            </div>
                            <div class="p-5 flex flex-col">
                                <h3 class="text-xl font-bold text-text-primary dark:text-dark-text-primary mb-2">Sprint Review Meeting</h3>
                                <div class="flex items-center text-sm text-text-secondary dark:text-dark-text-secondary mb-3">
                                    <i class="fa-solid fa-user-pen mr-2 text-primary"></i>Dibuat oleh<span class="ml-1 font-medium">Anda</span>
                                </div>
                                <p class="text-sm text-text-secondary dark:text-dark-text-secondary mb-4 line-clamp-2">Review hasil sprint 5 dan evaluasi backlog untuk persiapan sprint berikutnya.</p>
                                <div class="pt-4 border-t border-border-light dark:border-border-dark">
                                    <h4 class="text-sm font-semibold text-text-primary dark:text-dark-text-primary mb-3">Peserta</h4>
                                    <div class="flex items-center justify-between">
                                        <div class="text-sm text-text-secondary dark:text-dark-text-secondary leading-relaxed">• Lana Byrd<br>• Thomas Lean</div>
                                        <a href="{{ url('/detail') }}" class="text-sm font-medium text-primary hover:underline ml-4">View Details</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- My MoM Card - Rejected --}}
                        <div class="bg-component-bg dark:bg-dark-component-bg rounded-2xl shadow-lg overflow-hidden transition-all duration-300 hover:shadow-2xl hover:-translate-y-2">
                            <div class="relative">
                                <img class="w-full h-48 object-cover" src="{{ asset('lampiran.png') }}" alt="Dokumentasi Rapat">
                                <span class="absolute top-3 right-3 bg-red-500 text-white text-xs font-semibold px-3 py-1 rounded-full shadow-md">Rejected</span>
                                <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                            </div>
                            <div class="p-5 flex flex-col">
                                <h3 class="text-xl font-bold text-text-primary dark:text-dark-text-primary mb-2">Perencanaan Sprint Oktober</h3>
                                <div class="flex items-center text-sm text-text-secondary dark:text-dark-text-secondary mb-3">
                                    <i class="fa-solid fa-user-pen mr-2 text-primary"></i>Dibuat oleh<span class="ml-1 font-medium">Anda</span>
                                </div>
                                <p class="text-sm text-text-secondary dark:text-dark-text-secondary mb-4 line-clamp-2">Perlu revisi pada alokasi sumber daya. Mohon diperbarui dan ajukan kembali.</p>
                                <div class="pt-4 border-t border-border-light dark:border-border-dark">
                                    <h4 class="text-sm font-semibold text-primary dark:text-dark-text-primary mb-3">Bagian yang salah</h4>
                                    <div class="flex items-center justify-between">
                                        <div class="text-sm bg-red-500 p-1 text-white rounded-sm text-text-secondary dark:text-dark-text-secondary leading-relaxed"> Pimpinan dan Peserta ada yang kurang. </div>
                                        <a href="{{ url('/create') }}" class="text-sm font-medium text-primary hover:underline ml-4">Revisi</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- All MoM Content --}}
                <div id="all-mom-content" class="hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        <div class="bg-component-bg dark:bg-dark-component-bg rounded-2xl shadow-lg overflow-hidden transition-all duration-300 hover:shadow-2xl hover:-translate-y-2">
                            <div class="relative">
                                <img class="w-full h-48 object-cover" src="{{ asset('lampiran.png') }}" alt="Dokumentasi Rapat">
                                <span class="absolute top-3 right-3 bg-primary text-white text-xs font-semibold px-3 py-1 rounded-full shadow-md">25 Sep 2025</span>
                                <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                            </div>
                            <div class="p-5 flex flex-col">
                                <h3 class="text-xl font-bold text-text-primary dark:text-dark-text-primary mb-2">Brainstorming Fitur Baru</h3>
                                <div class="flex items-center text-sm text-text-secondary dark:text-dark-text-secondary mb-3">
                                    <i class="fa-solid fa-user-pen mr-2 text-primary"></i>Dibuat oleh<span class="ml-1 font-medium">Bonnie Green</span>
                                </div>
                                <p class="text-sm text-text-secondary dark:text-dark-text-secondary mb-4 line-clamp-2">Sesi kreatif untuk mengumpulkan ide-ide inovatif untuk pengembangan fitur pada kuartal berikutnya.</p>
                                <div class="pt-4 border-t border-border-light dark:border-border-dark">
                                    <h4 class="text-sm font-semibold text-text-primary dark:text-dark-text-primary mb-3">Peserta</h4>
                                    <div class="flex items-center justify-between">
                                        <div class="text-sm text-text-secondary dark:text-dark-text-secondary leading-relaxed">• Lana Byrd<br>• Thomas Lean</div>
                                        <a href="{{ url('/detail') }}" class="text-sm font-medium text-primary hover:underline ml-4">View Details</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="flex justify-center mt-8 mb-6">
            <div class="flex items-center gap-8">
                <button disabled class="rounded-md border border-slate-300 p-2.5 text-center text-sm transition-all shadow-sm text-slate-600 disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none" type="button">
                    <svg xmlns="http://www.w3.org/2000/svg" viewbox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M11.03 3.97a.75.75 0 0 1 0 1.06l-6.22 6.22H21a.75.75 0 0 1 0 1.5H4.81l6.22 6.22a.75.75 0 1 1-1.06 1.06l-7.5-7.5a.75.75 0 0 1 0-1.06l7.5-7.5a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd"/></svg>
                </button>
                <p class="text-slate-600">Page <strong class="text-slate-800">1</strong> of&nbsp;<strong class="text-slate-800">10</strong></p>
                <button class="rounded-md border border-slate-300 p-2.5 text-center text-sm transition-all shadow-sm hover:shadow-lg text-slate-600 hover:text-white hover:bg-slate-800 focus:text-white focus:bg-slate-800" type="button">
                    <svg xmlns="http://www.w3.org/2000/svg" viewbox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M12.97 3.97a.75.75 0 0 1 1.06 0l7.5 7.5a.75.75 0 0 1 0 1.06l-7.5 7.5a.75.75 0 1 1-1.06-1.06l6.22-6.22H3a.75.75 0 0 1 0-1.5h16.19l-6.22-6.22a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"/></svg>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function switchTab(tabId) {
        const myMomTab = document.getElementById('my-mom-tab');
        const allMomTab = document.getElementById('all-mom-tab');
        const myMomContent = document.getElementById('my-mom-content');
        const allMomContent = document.getElementById('all-mom-content');

        const activeClasses = ['text-primary', 'border-primary'];
        const inactiveClasses = [
            'border-transparent',
            'hover:text-gray-600',
            'hover:border-gray-300',
            'dark:hover:text-gray-300',
            'text-gray-500',
            'dark:text-gray-400'
        ];

        myMomTab.classList.remove(...activeClasses);
        allMomTab.classList.remove(...activeClasses);
        myMomTab.classList.add(...inactiveClasses);
        allMomTab.classList.add(...inactiveClasses);

        if (tabId === 'my-mom') {
            myMomTab.classList.add(...activeClasses);
            myMomTab.classList.remove(...inactiveClasses);
            myMomContent.classList.remove('hidden');
            allMomContent.classList.add('hidden');
        } else { // 'all-mom'
            allMomTab.classList.add(...activeClasses);
            allMomTab.classList.remove(...inactiveClasses);
            allMomContent.classList.remove('hidden');
            myMomContent.classList.add('hidden');
        }
    }
</script>
@endpush
