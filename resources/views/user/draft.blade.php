@extends('layouts.app')

@section('title', 'Browse MoM | TR1 MoMatic')

@php
    use Illuminate\Support\Str;
@endphp

@push('styles')
<style>
    .card-mom {
        opacity: 0;
        transform: scale(0.95);
        animation: fadeInScaleUp 0.5s ease-out forwards;
    }
    @keyframes fadeInScaleUp {
        to {
            opacity: 1;
            transform: scale(1);
        }
    }
</style>
@endpush

@section('content')
<div class="pt-2">
    <div class="space-y-6">
        {{-- Header halaman --}}
        <div class="p-6 md:p-8 rounded-xl shadow-lg bg-gray-800 border-l-4 border-red-500">
            <h1 class="text-3xl font-bold font-orbitron text-neon-red">Browse MoM</h1>
            <p class="mt-1 text-gray-400">Tinjau draf Anda atau jelajahi semua MoM yang telah disetujui.</p>
        </div>

        <div class="bg-gray-800 rounded-xl shadow p-4 border border-gray-700">
            <div class="flex flex-col md:flex-row justify-between items-center border-b border-gray-700 pb-4">
                {{-- Tabs --}}
                <ul class="flex flex-wrap -mb-px text-sm font-medium text-center text-gray-400">
                    <li class="me-2">
                        {{-- Menggunakan class 'active' untuk default My MoM --}}
                        <button onclick="switchTab('my-mom')" id="my-mom-tab" class="tab-button inline-flex items-center justify-center p-4 border-b-2 text-red-400 border-red-500 rounded-t-lg">
                            <i class="fa-solid fa-user me-2"></i>My MoM
                        </button>
                    </li>
                    <li class="me-2">
                        <button onclick="switchTab('all-mom')" id="all-mom-tab" class="tab-button inline-flex items-center justify-center p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-300 hover:border-gray-500">
                            <i class="fa-solid fa-users me-2"></i>All MoM
                        </button>
                    </li>
                </ul>

                {{-- Search dan Filter --}}
                <div class="flex flex-wrap items-center gap-3">
                    <div class="relative">
                        <input type="text" id="searchInput"
                            placeholder="Cari MoM Disini . . ."
                            class="pl-10 pr-3 py-2 rounded-lg bg-gray-900 border border-gray-700 text-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent w-60">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-gray-500"></i>
                    </div>

                    {{-- Filter Bulan --}}
                    <select id="filterMonth"
                        class="bg-gray-900 border border-gray-700 text-gray-300 text-sm rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        <option value="">Semua Bulan</option>
                        @foreach(range(1, 12) as $month)
                            <option value="{{ $month }}">{{ \Carbon\Carbon::create()->month($month)->translatedFormat('F') }}</option>
                        @endforeach
                    </select>

                    {{-- Filter Status --}}
                    <select id="filterStatus"
                        class="bg-gray-900 border border-gray-700 text-gray-300 text-sm rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        <option value="">Semua Status</option>
                        <option value="Menunggu">Pending</option>
                        <option value="Disetujui">Approved</option>
                        <option value="Ditolak">Rejected</option>
                    </select>
                </div>
            </div>

            <div class="pt-6">
                {{-- =================== --}}
                {{-- KONTEN My MoM --}}
                {{-- =================== --}}
                <div id="my-mom-content">
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        @forelse($myMoms as $index => $mom)
                            @php
                                $statusText = $mom->status->status ?? 'Unknown';
                                $statusInfo = match ($statusText) {
                                    'Menunggu' => ['dot' => 'bg-yellow-400', 'text' => 'text-yellow-300', 'label' => 'Pending'],
                                    'Ditolak'  => ['dot' => 'bg-red-500', 'text' => 'text-red-400', 'label' => 'Rejected'],
                                    'Disetujui'=> ['dot' => 'bg-green-500', 'text' => 'text-green-400', 'label' => 'Approved'],
                                    default    => ['dot' => 'bg-gray-500', 'text' => 'text-gray-400', 'label' => 'Unknown'],
                                };

                                $actionRouteName = ($statusText === 'Ditolak') ? 'moms.edit' : 'moms.detail';
                                $actionUrl = route($actionRouteName, $mom->version_id);
                                $imageUrl = $mom->attachments->first() ? asset('storage/' . $mom->attachments->first()->file_path) : asset('img/lampiran-kosong.png');
                                $creatorName = $mom->creator?->name ?? 'N/A';

                                // Decode nama_peserta
                                $allAttendees = [];
                                if (!empty($mom->nama_peserta)) {
                                    $decoded = is_string($mom->nama_peserta)
                                        ? json_decode($mom->nama_peserta, true)
                                        : $mom->nama_peserta;

                                    if (is_array($decoded)) {
                                        foreach ($decoded as $group) {
                                            if (isset($group['attendees']) && is_array($group['attendees'])) {
                                                foreach ($group['attendees'] as $person) {
                                                    $allAttendees[] = (string) $person;
                                                }
                                            }
                                        }
                                    }
                                }
                            @endphp

                            {{-- Card --}}
                            <div class="card-mom bg-gray-800 rounded-2xl shadow-lg overflow-hidden flex flex-col transition-all duration-300 hover:shadow-2xl hover:shadow-red-500/20 hover:-translate-y-2 border border-gray-700" style="animation-delay: {{ $index * 100 }}ms;">
                                <div class="relative">
                                    <a href="{{ $actionUrl }}">
                                        <img class="w-full h-48 object-cover" src="{{ $imageUrl }}" alt="Dokumentasi Rapat">
                                    </a>
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent"></div>
                                    <div class="absolute bottom-0 left-0 p-4 text-white">
                                        <p class="text-xs font-semibold uppercase tracking-wider">{{ $mom->created_at->translatedFormat('F Y') }}</p>
                                        <p class="text-3xl font-bold">{{ $mom->created_at->format('d') }}</p>
                                    </div>
                                    <div class="absolute top-3 right-3">
                                        <div class="inline-flex items-center gap-x-2 px-3 py-1 bg-gray-900/50 backdrop-blur-sm rounded-full border border-gray-700">
                                            <span class="w-2.5 h-2.5 rounded-full {{ $statusInfo['dot'] }}"></span>
                                            <span class="text-xs font-medium {{ $statusInfo['text'] }}">{{ $statusInfo['label'] }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-5 flex flex-col flex-grow">
                                    <h3 class="text-xl font-bold text-white mb-2 line-clamp-1" title="{{ $mom->title }}">
                                        <a href="{{ $actionUrl }}" class="hover:underline">{{ $mom->title }}</a>
                                    </h3>

                                    <div class="flex items-center text-sm text-gray-400 mb-4">
                                        <i class="fa-solid fa-user-pen mr-2 text-red-400"></i> Dibuat oleh
                                        <span class="ml-1 font-medium text-gray-300">{{ ($creatorName !== 'N/A' && $creatorName === Auth::user()->name) ? 'Anda' : $creatorName }}</span>
                                    </div>

                                    {{-- Peserta Rapat --}}
                                    <div class="mb-4 flex-grow">
                                        <h4 class="text-sm font-semibold text-gray-300 mb-2">Peserta Rapat:</h4>
                                        <div class="text-sm text-gray-400 space-y-1">
                                            @if(count($allAttendees) > 0)
                                                @foreach(array_slice($allAttendees, 0, 2) as $attendee)
                                                    <p class="truncate"><i class="fa-solid fa-circle fa-xs text-gray-600 mr-2"></i>{{ $attendee }}</p>
                                                @endforeach
                                                @if(count($allAttendees) > 2)
                                                    <p class="text-xs text-gray-500 italic ml-4">+{{ count($allAttendees) - 2 }} peserta lainnya</p>
                                                @endif
                                            @else
                                                <p class="text-xs text-gray-500 italic">Tidak ada data peserta.</p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="pt-4 border-t border-gray-700 flex items-center justify-end">
                                        <a href="{{ $actionUrl }}" class="text-sm font-medium text-red-400 hover:underline hover:text-red-300">
                                            {{ $statusText === 'Ditolak' ? 'Revisi' : 'Lihat Detail' }} <i class="fa-solid fa-arrow-right ml-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-3 text-center py-16 bg-gray-800/50 rounded-xl border border-dashed border-gray-700">
                                <i class="fa-solid fa-folder-open fa-3x text-gray-600"></i>
                                <p class="mt-4 text-lg text-gray-500">Anda belum memiliki draf MoM.</p>
                                <a href="{{ url('/create') }}" class="mt-3 inline-block text-sm font-medium text-red-400 hover:underline">Buat MoM baru?</a>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- =================== --}}
                {{-- KONTEN All MoM --}}
                {{-- =================== --}}
                <div id="all-mom-content" class="hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        @php $approvedCount = 0; @endphp
                        @forelse($allMoms as $index => $mom)
                            {{-- Filter manual: HANYA tampilkan yang statusnya 'Disetujui' --}}
                            @if(($mom->status->status ?? 'Unknown') === 'Disetujui')
                                @php
                                    $approvedCount++;
                                    $statusText = 'Disetujui';
                                    $statusInfo = ['dot' => 'bg-green-500', 'text' => 'text-green-400', 'label' => 'Approved'];

                                    $actionUrl = route('moms.detail', $mom->version_id);
                                    $imageUrl = $mom->attachments->first() ? asset('storage/' . $mom->attachments->first()->file_path) : asset('img/lampiran-kosong.png');
                                    $creatorName = $mom->creator?->name ?? 'N/A';

                                    // Decode nama_peserta
                                    $allAttendees = [];
                                    if (!empty($mom->nama_peserta)) {
                                        $decoded = is_string($mom->nama_peserta)
                                            ? json_decode($mom->nama_peserta, true)
                                            : $mom->nama_peserta;

                                        if (is_array($decoded)) {
                                            foreach ($decoded as $group) {
                                                if (isset($group['attendees']) && is_array($group['attendees'])) {
                                                    foreach ($group['attendees'] as $person) {
                                                        $allAttendees[] = (string) $person;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                @endphp

                                {{-- Card --}}
                                <div class="card-mom bg-gray-800 rounded-2xl shadow-lg overflow-hidden flex flex-col transition-all duration-300 hover:shadow-2xl hover:shadow-red-500/20 hover:-translate-y-2 border border-gray-700" style="animation-delay: {{ $approvedCount * 100 }}ms;">
                                    <div class="relative">
                                        <a href="{{ $actionUrl }}">
                                            <img class="w-full h-48 object-cover" src="{{ $imageUrl }}" alt="Dokumentasi Rapat">
                                        </a>
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent"></div>
                                        <div class="absolute bottom-0 left-0 p-4 text-white">
                                            <p class="text-xs font-semibold uppercase tracking-wider">{{ $mom->created_at->translatedFormat('F Y') }}</p>
                                            <p class="text-3xl font-bold">{{ $mom->created_at->format('d') }}</p>
                                        </div>
                                        <div class="absolute top-3 right-3">
                                            <div class="inline-flex items-center gap-x-2 px-3 py-1 bg-gray-900/50 backdrop-blur-sm rounded-full border border-gray-700">
                                                <span class="w-2.5 h-2.5 rounded-full {{ $statusInfo['dot'] }}"></span>
                                                <span class="text-xs font-medium {{ $statusInfo['text'] }}">{{ $statusInfo['label'] }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="p-5 flex flex-col flex-grow">
                                        <h3 class="text-xl font-bold text-white mb-2 line-clamp-1" title="{{ $mom->title }}">
                                            <a href="{{ $actionUrl }}" class="hover:underline">{{ $mom->title }}</a>
                                        </h3>

                                        <div class="flex items-center text-sm text-gray-400 mb-4">
                                            <i class="fa-solid fa-user-pen mr-2 text-red-400"></i> Dibuat oleh
                                            <span class="ml-1 font-medium text-gray-300">{{ $creatorName }}</span>
                                        </div>

                                        {{-- Peserta Rapat --}}
                                        <div class="mb-4 flex-grow">
                                            <h4 class="text-sm font-semibold text-gray-300 mb-2">Peserta Rapat:</h4>
                                            <div class="text-sm text-gray-400 space-y-1">
                                                @if(count($allAttendees) > 0)
                                                    @foreach(array_slice($allAttendees, 0, 2) as $attendee)
                                                        <p class="truncate"><i class="fa-solid fa-circle fa-xs text-gray-600 mr-2"></i>{{ $attendee }}</p>
                                                    @endforeach
                                                    @if(count($allAttendees) > 2)
                                                        <p class="text-xs text-gray-500 italic ml-4">+{{ count($allAttendees) - 2 }} peserta lainnya</p>
                                                    @endif
                                                @else
                                                    <p class="text-xs text-gray-500 italic">Tidak ada data peserta.</p>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="pt-4 border-t border-gray-700 flex items-center justify-end">
                                            <a href="{{ $actionUrl }}" class="text-sm font-medium text-red-400 hover:underline hover:text-red-300">
                                                Lihat Detail <i class="fa-solid fa-arrow-right ml-1"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @empty
                            {{-- Jika $allMoms kosong, tampilkan pesan ini --}}
                            <div class="col-span-3 text-center py-16 bg-gray-800/50 rounded-xl border border-dashed border-gray-700">
                                <i class="fa-solid fa-folder-open fa-3x text-gray-600"></i>
                                <p class="mt-4 text-lg text-gray-500">Tidak ada data MoM sama sekali.</p>
                            </div>
                        @endforelse
                        
                        {{-- Jika $allMoms ada, tetapi tidak ada yang berstatus 'Disetujui' --}}
                        @if($allMoms->isNotEmpty() && $approvedCount === 0)
                            <div class="col-span-3 text-center py-16 bg-gray-800/50 rounded-xl border border-dashed border-gray-700">
                                <i class="fa-solid fa-file-circle-check fa-3x text-gray-600"></i>
                                <p class="mt-4 text-lg text-gray-500">Belum ada MoM yang disetujui.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Paginasi --}}
        <div class="flex justify-center mt-8 mb-6" id="pagination-my-mom">
            {{ $myMoms->links() }}
        </div>
        
        <div class="flex justify-center mt-8 mb-6 hidden" id="pagination-all-mom">
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function switchTab(tabId) {
    const tabs = document.querySelectorAll('.tab-button');
    const myMomContent = document.getElementById('my-mom-content');
    const allMomContent = document.getElementById('all-mom-content');
    const filterStatus = document.getElementById('filterStatus');
    const paginationMyMom = document.getElementById('pagination-my-mom');
    const paginationAllMom = document.getElementById('pagination-all-mom');

    // Reset Tabs
    tabs.forEach(tab => {
        tab.classList.remove('text-red-400', 'border-red-500');
        tab.classList.add('border-transparent', 'hover:text-gray-300', 'hover:border-gray-500');
    });

    // Set Active Tab, Content, and Filters
    if (tabId === 'my-mom') {
        document.getElementById('my-mom-tab').classList.add('text-red-400', 'border-red-500');
        myMomContent.classList.remove('hidden');
        allMomContent.classList.add('hidden');
        
        filterStatus.classList.remove('hidden'); // Tampilkan filter status
        paginationMyMom.classList.remove('hidden'); // Tampilkan paginasi My MoM
        paginationAllMom.classList.add('hidden');   // Sembunyikan paginasi All MoM
    } else {
        document.getElementById('all-mom-tab').classList.add('text-red-400', 'border-red-500');
        allMomContent.classList.remove('hidden');
        myMomContent.classList.add('hidden');

        filterStatus.classList.add('hidden'); // Sembunyikan filter status
        
        paginationAllMom.classList.remove('hidden');
        paginationMyMom.classList.add('hidden');
    }
}
</script>
@endpush