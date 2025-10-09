@extends('layouts.app')

@section('title', 'Draft MoM | MoM Telkom')

@php use Illuminate\Support\Str; @endphp

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
                
                {{--START: My MoM Content (Drafts/Rejected)--}}
                <div id="my-mom-content">
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        
                        {{-- Cek apakah ada data $myMoms yang dikirim dari controller --}}
                        @forelse($myMoms as $mom)
                            @php
                                // Menggunakan Safe Operator (?->) untuk mencegah error 'Attempt to read property on null'
                                $statusText = $mom->status->status ?? 'Unknown';
                                $statusColor = match ($statusText) {
                                    'Menunggu' => 'bg-yellow-500',
                                    'Ditolak'  => 'bg-red-500',
                                    'Disetujui'=> 'bg-green-500',
                                    default    => 'bg-gray-500', 
                                };
                                
                                $actionRouteName = ($statusText === 'Ditolak') ? 'moms.edit' : 'moms.detail';
                                $actionUrl = route($actionRouteName, $mom->version_id);

                                // Ambil Lampiran pertama untuk preview
                                $attachment = $mom->attachments->first();
                                $imageUrl = $attachment 
                                    ? asset('storage/' . $attachment->file_path) 
                                    : asset('img/lampiran-kosong.png');

                                // --- LOGIC UNTUK PESERTA DARI KOLOM JSON ---
                                $internalNames = $mom->nama_peserta ?? []; 
                                $partnerNames = [];
                                
                                if (is_array($mom->nama_mitra)) {
                                    foreach ($mom->nama_mitra as $mitra) {
                                        if (is_array($mitra['attendees'] ?? null)) {
                                            $partnerNames = array_merge($partnerNames, $mitra['attendees']);
                                        }
                                    }
                                }
                                $allAttendees = array_unique(array_merge($internalNames, $partnerNames));
                                
                                $totalAttendees = count($allAttendees);

                                // Ambil nama creator dengan aman menggunakan Safe Operator
                                $creatorName = $mom->creator?->name ?? 'N/A';
                                
                            @endphp
                            
                            <div class="bg-component-bg dark:bg-dark-component-bg rounded-2xl shadow-lg overflow-hidden transition-all duration-300 hover:shadow-2xl hover:-translate-y-2">
                                <div class="relative">
                                    {{-- Image --}}
                                    <img class="w-full h-48 object-cover" src="{{ $imageUrl }}" alt="Dokumentasi Rapat">
                                    
                                    {{-- Status Badge --}}
                                    <span class="absolute top-3 right-3 {{ $statusColor }} text-white text-xs font-semibold px-3 py-1 rounded-full shadow-md">
                                        {{ $statusText }}
                                    </span>
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                                </div>
                                
                                <div class="p-5 flex flex-col">
                                    <h3 class="text-xl font-bold text-text-primary dark:text-dark-text-primary mb-2">{{ $mom->title }}</h3>
                                    
                                    <div class="flex items-center text-sm text-text-secondary dark:text-dark-text-secondary mb-3">
                                        <i class="fa-solid fa-user-pen mr-2 text-primary"></i>Dibuat oleh
                                        <span class="ml-1 font-medium">
                                            {{-- Safe check using $creatorName --}}
                                            {{ ($creatorName !== 'N/A' && $creatorName === Auth::user()->name) ? 'Anda' : $creatorName }}
                                        </span>
                                    </div>
                                    
                                    <p class="text-sm text-text-secondary dark:text-dark-text-secondary mb-4 line-clamp-2">
                                        {!! Str::limit(strip_tags($mom->pembahasan), 100) !!}
                                    </p>
                                    
                                    <div class="pt-4 border-t border-border-light dark:border-border-dark">
                                        <h4 class="text-sm font-semibold text-text-primary dark:text-dark-text-primary mb-3">Peserta</h4>
                                        <div class="flex items-center justify-between">
                                            <div class="text-sm text-text-secondary dark:text-dark-text-secondary leading-relaxed">
                                                @if($totalAttendees > 0)
                                                    {{-- Tampilkan 2 peserta pertama --}}
                                                    @foreach(array_slice($allAttendees, 0, 2) as $attendeeName)
                                                        • {{ $attendeeName }}<br>
                                                    @endforeach
                                                    
                                                    {{-- Hitung dan tampilkan peserta sisanya --}}
                                                    @if($totalAttendees > 2)
                                                        ... (+{{ $totalAttendees - 2 }} lainnya)
                                                    @endif
                                                @else
                                                    <span class="italic">Tidak ada peserta tercatat.</span>
                                                @endif
                                            </div>
                                            <a href="{{ $actionUrl }}" class="text-sm font-medium text-primary hover:underline ml-4">
                                                {{ $statusText === 'Ditolak' ? 'Revisi' : 'Lihat Detail' }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-3 text-center py-10 bg-body-bg dark:bg-dark-body-bg rounded-xl">
                                <p class="text-lg text-text-secondary dark:text-dark-text-secondary">Tidak ada MoM yang dibuat oleh Anda dalam status Draft atau Revisi.</p>
                                <a href="{{ url('/create') }}" class="mt-3 inline-block text-sm font-medium text-primary hover:underline">Buat MoM baru?</a>
                            </div>
                        @endforelse
                        
                    </div>
                </div>
                {{--END: My MoM Content--}}
                
                
                {{--START: All MoM Content (Approved)--}}
                <div id="all-mom-content" class="hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        @php
                            // Pastikan $allMoms sudah didefinisikan dan berisi MoM yang Approved dari controller
                            $allMoms = $allMoms ?? [];
                        @endphp
                        
                        @forelse($allMoms as $mom)
                            @php
                                // Status di sini harus selalu 'Disetujui' (Approved)
                                $statusText = $mom->status->status ?? 'Unknown';
                                $statusColor = match ($statusText) {
                                    'Disetujui'=> 'bg-green-500', 
                                    default    => 'bg-gray-500', 
                                };
                                
                                $actionUrl = route('moms.detail', $mom->version_id);

                                // Ambil Lampiran pertama untuk preview
                                $attachment = $mom->attachments->first();
                                $imageUrl = $attachment 
                                    ? asset('storage/' . $attachment->file_path) 
                                    : asset('img/lampiran-kosong.png');
                                    
                                // --- LOGIC UNTUK PESERTA DARI KOLOM JSON ---
                                $internalNames = $mom->nama_peserta ?? []; 
                                $partnerNames = [];
                                
                                if (is_array($mom->nama_mitra)) {
                                    foreach ($mom->nama_mitra as $mitra) {
                                        if (is_array($mitra['attendees'] ?? null)) {
                                            $partnerNames = array_merge($partnerNames, $mitra['attendees']);
                                        }
                                    }
                                }
                                
                                $allAttendees = array_merge($internalNames, $partnerNames);
                                $totalAttendees = count($allAttendees);
                                // Ambil nama creator dengan aman menggunakan Safe Operator
                                $creatorName = $mom->creator?->name ?? 'N/A';
                            @endphp
                            
                            <div class="bg-component-bg dark:bg-dark-component-bg rounded-2xl shadow-lg overflow-hidden transition-all duration-300 hover:shadow-2xl hover:-translate-y-2">
                                <div class="relative">
                                    {{-- Image --}}
                                    <img class="w-full h-48 object-cover" src="{{ $imageUrl }}" alt="Dokumentasi Rapat">
                                    
                                    {{-- Status Badge --}}
                                    <span class="absolute top-3 right-3 {{ $statusColor }} text-white text-xs font-semibold px-3 py-1 rounded-full shadow-md">
                                        {{ $statusText }}
                                    </span>
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                                </div>
                                
                                <div class="p-5 flex flex-col">
                                    <h3 class="text-xl font-bold text-text-primary dark:text-dark-text-primary mb-2">{{ $mom->title }}</h3>
                                    
                                    <div class="flex items-center text-sm text-text-secondary dark:text-dark-text-secondary mb-3">
                                        <i class="fa-solid fa-user-pen mr-2 text-primary"></i>Dibuat oleh
                                        <span class="ml-1 font-medium">{{ $creatorName }}</span>
                                    </div>
                                    
                                    <p class="text-sm text-text-secondary dark:text-dark-text-secondary mb-4 line-clamp-2">
                                        {!! Str::limit(strip_tags($mom->pembahasan), 100) !!}
                                    </p>
                                    
                                    <div class="pt-4 border-t border-border-light dark:border-border-dark">
                                        <h4 class="text-sm font-semibold text-text-primary dark:text-dark-text-primary mb-3">Peserta</h4>
                                        <div class="flex items-center justify-between">
                                            <div class="text-sm text-text-secondary dark:text-dark-text-secondary leading-relaxed">
                                                @if($totalAttendees > 0)
                                                    @foreach(array_slice($allAttendees, 0, 2) as $attendeeName)
                                                        • {{ $attendeeName }}<br>
                                                    @endforeach
                                                    
                                                    @if($totalAttendees > 2)
                                                        ... (+{{ $totalAttendees - 2 }} lainnya)
                                                    @endif
                                                @else
                                                    <span class="italic">Tidak ada peserta tercatat.</span>
                                                @endif
                                            </div>
                                            <a href="{{ $actionUrl }}" class="text-sm font-medium text-primary hover:underline ml-4">
                                                Lihat Detail
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-3 text-center py-10 bg-body-bg dark:bg-dark-body-bg rounded-xl">
                                <p class="text-lg text-text-secondary dark:text-dark-text-secondary">Belum ada MoM yang disetujui (Approved).</p>
                            </div>
                        @endforelse
                    </div>
                </div>
                {{--END: All MoM Content--}}
                
            </div>
        </div>
        
        {{-- Pagination --}}
        <div class="flex justify-center mt-8 mb-6">
            {{-- Mengganti div statis dengan link pagination Laravel --}}
            {{ $myMoms->links() }} 
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
        } else { 
            allMomTab.classList.add(...activeClasses);
            allMomTab.classList.remove(...inactiveClasses);
            allMomContent.classList.remove('hidden');
            myMomContent.classList.add('hidden');
        }
    }
</script>
@endpush