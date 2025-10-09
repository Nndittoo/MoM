@extends('admin.layouts.app')

@section('title', 'Repository MoM | MoM Telkom')

@section('content')
<div class="pt-2">
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col md:flex-row items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-text-primary dark:text-dark-text-primary">Repository MoM</h1>
                <p class="mt-1 text-text-secondary dark:text-dark-text-secondary">Cari, lihat, dan kelola semua Minute of Meetings.</p>
            </div>

            <a href="{{ route('admin.creates') }}" class="mt-4 md:mt-0 flex justify-center items-center px-4 py-2 text-sm font-semibold text-white bg-primary rounded-lg shadow-lg hover:bg-primary-dark">
                <i class="fa-solid fa-plus mr-2"></i>Buat MoM Baru
            </a>
        </div>

        {{-- Search and Filter Section --}}
        <form action="{{ route('admin.repository') }}" method="GET">
            <div class="bg-component-bg dark:bg-dark-component-bg p-4 rounded-lg shadow">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    {{-- Input Pencarian --}}
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari berdasarkan topik atau proyek..."
                        class="w-full md:col-span-3 bg-body-bg border-border-light rounded-lg text-sm focus:ring-primary focus:border-primary dark:bg-dark-component-bg dark:border-border-dark">

                    {{-- Input Tanggal --}}
                    <input type="date" name="date" value="{{ request('date') }}"
                        class="w-full bg-body-bg border-border-light rounded-lg text-sm focus:ring-primary focus:border-primary dark:bg-dark-component-bg dark:border-border-dark">

                    {{-- Tombol Aksi --}}
                    <div class="flex items-center gap-2">
                        <button type="submit" class="w-full flex justify-center items-center px-4 py-2 text-sm font-semibold text-white bg-primary rounded-lg shadow hover:bg-primary-dark">
                            <i class="fa-solid fa-search mr-2"></i>Cari
                        </button>
                        <a href="{{ route('admin.repository') }}" class="flex justify-center items-center p-2 text-text-secondary hover:text-text-primary dark:text-dark-text-secondary" title="Reset Filter">
                            <i class="fa-solid fa-times"></i>
                        </a>
                    </div>
                </div>
            </div>
        </form>

        {{-- Tabs Section --}}
        <div class="bg-component-bg dark:bg-dark-component-bg rounded-lg shadow p-4">
            {{-- Tab Buttons --}}
            <ul class="flex flex-wrap text-sm font-medium text-center border-b border-border-light dark:border-border-dark">
                <li class="me-2">
                    <button onclick="switchTab('my-mom')" id="my-mom-tab" class="inline-block p-4 rounded-t-lg border-b-2 text-primary border-primary">My MoM</button>
                </li>
                <li class="me-2">
                    <button onclick="switchTab('all-mom')" id="all-mom-tab" class="inline-block p-4 rounded-t-lg border-b-2 border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300">Semua MoM</button>
                </li>
            </ul>

            <div class="pt-6">

                {{-- MY MOM CONTENT (MOM DIBUAT OLEH ADMIN) --}}
                <div id="my-mom-content">
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">

                        @forelse($momsByAdmin as $mom)
                        {{-- Inisialisasi Alpine.js di kartu utama --}}
                        <div x-data="{ actionsOpen: false }" class="bg-body-bg dark:bg-dark-body-bg rounded-2xl shadow-lg overflow-hidden transition-all duration-300 hover:shadow-2xl hover:-translate-y-1">
                            <div class="relative">
                                @php
                                    $attachment = $mom->attachments->first();
                                    $imagePath = ($attachment && str_starts_with($attachment->mime_type, 'image/')) 
                                        ? Storage::url($attachment->file_path) 
                                        : asset('img/lampiran.png');
                                @endphp
                                <img class="w-full h-48 object-cover" src="{{ $imagePath }}" alt="Dokumentasi Rapat">
                                <span class="absolute top-3 right-3 bg-primary text-white text-xs font-semibold px-3 py-1 rounded-full shadow-md">{{ \Carbon\Carbon::parse($mom->meeting_date)->isoFormat('DD MMMM YYYY') }}</span>
                                <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                            </div>

                            {{-- Konten Kartu dengan posisi relative untuk menampung overlay --}}
                            <div class="p-5 flex flex-col relative">
                                <h3 class="text-xl font-bold text-text-primary dark:text-dark-text-primary mb-2">{{ $mom->title }}</h3>
                                <div class="flex items-center text-sm text-text-secondary dark:text-dark-text-secondary mb-3">
                                    <i class="fa-solid fa-user-pen mr-2 text-primary"></i>Dibuat oleh<span class="ml-1 font-medium">{{ $mom->creator->name ?? 'Admin' }}</span>
                                    <span class="ml-auto text-xs font-semibold px-2 py-0.5 rounded-full text-white {{ $mom->status->status_id == 1 ? 'bg-yellow-500' : ($mom->status->status_id == 2 ? 'bg-green-500' : 'bg-red-500') }}">{{ $mom->status->status ?? 'N/A' }}</span>
                                </div>
                                <p class="text-sm text-text-secondary dark:text-dark-text-secondary mb-4 line-clamp-2">{{ $mom->pembahasan }}</p>
                                <div class="pt-4 border-t border-border-light dark:border-border-dark">
                                    <h4 class="text-sm font-semibold text-text-primary dark:text-dark-text-primary mb-3">Peserta</h4>
                                    <div class="flex items-start justify-between">
                                        <div class="text-sm text-text-secondary dark:text-dark-text-secondary leading-relaxed">
                                            @php
                                                // 1. Ambil data, coba decode, atau default ke array kosong
                                                $peserta = is_string($mom->nama_peserta) ? json_decode($mom->nama_peserta, true) : ($mom->nama_peserta ?? []);
                                                $mitra = is_string($mom->nama_mitra) ? json_decode($mom->nama_mitra, true) : ($mom->nama_mitra ?? []);
                                        
                                                // 2. Pastikan keduanya adalah array sebelum digabung
                                                $peserta = is_array($peserta) ? $peserta : [];
                                                $mitra = is_array($mitra) ? $mitra : [];
                                        
                                                // 3. Gabungkan dan filter elemen kosong/null
                                                $allParticipants = array_filter(array_merge($peserta, $mitra));
                                                
                                                // 4. Hitung dan tentukan peserta yang ditampilkan
                                                $totalParticipants = count($allParticipants);
                                                $displayParticipants = array_slice($allParticipants, 0, 2);
                                                $remainingParticipantsCount = $totalParticipants - count($displayParticipants);
                                            @endphp
                                            {{-- Menampilkan 2 Peserta Pertama --}}
                                            @forelse($displayParticipants as $p)
                                                •
                                                @if(is_array($p))
                                                    {{ $p['name'] ?? $p['user_name'] ?? 'Peserta' }}<br>
                                                @else
                                                    {{ $p }}<br>
                                                @endif
                                            @empty 
                                                Tidak ada peserta.
                                            @endforelse
                                            
                                            {{-- Menampilkan indikator "lainnya" jika ada sisa peserta --}}
                                            @if($remainingParticipantsCount > 0)
                                                <span class="text-primary font-medium">+ {{ $remainingParticipantsCount }} lainnya</span>
                                            @endif
                                        </div>

                                        {{-- Tombol Pemicu Aksi dengan Label --}}
                                        <button @click="actionsOpen = true" class="flex items-center gap-2 text-sm font-medium text-text-secondary dark:text-dark-text-secondary hover:text-primary dark:hover:text-primary-dark p-2 rounded-lg hover:bg-gray-200 dark:hover:bg-dark-body-bg">
                                            <i class="fa-solid fa-ellipsis-vertical"></i>
                                            <span>Action</span>
                                        </button>
                                    </div>
                                </div>

                                {{-- Overlay Aksi --}}
                                <div x-show="actionsOpen"
                                    x-transition:enter="ease-out duration-300"
                                    x-transition:enter-start="opacity-0"
                                    x-transition:enter-end="opacity-100"
                                    x-transition:leave="ease-in duration-200"
                                    x-transition:leave-start="opacity-100"
                                    x-transition:leave-end="opacity-0"
                                    @click.self="actionsOpen = false"
                                    class="absolute inset-0 z-10 flex items-center justify-center bg-white/70 dark:bg-black/60 backdrop-blur-sm p-5">

                                    <div class="relative w-full max-w-xs bg-component-bg dark:bg-dark-component-bg rounded-xl shadow-2xl p-4 border border-border-light dark:border-border-dark">
                                        {{-- Tombol Tutup --}}
                                        <button @click="actionsOpen = false" class="absolute top-2 right-2 p-2 text-text-secondary hover:text-text-primary dark:text-dark-text-secondary dark:hover:text-dark-text-primary rounded-full hover:bg-gray-200 dark:hover:bg-dark-body-bg">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>

                                        <h5 class="text-lg font-bold text-center mb-4 text-text-primary dark:text-dark-text-primary">Action</h5>
                                        <div class="flex flex-col gap-2">
                                            <a href="{{ url('/admin/details/' . $mom->version_id) }}" class="flex w-full items-center gap-3 px-4 py-2 text-sm text-text-primary dark:text-dark-text-primary hover:bg-gray-100 dark:hover:bg-dark-body-bg rounded-lg">
                                                <i class="fa-solid fa-eye w-4"></i><span>Lihat Detail</span>
                                            </a>
                                            <a href="#" class="flex w-full items-center gap-3 px-4 py-2 text-sm text-text-primary dark:text-dark-text-primary hover:bg-gray-100 dark:hover:bg-dark-body-bg rounded-lg">
                                                <i class="fa-solid fa-pen-to-square w-4"></i><span>Edit</span>
                                            </a>
                                            <form action="#" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus MoM ini?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg">
                                                    <i class="fa-solid fa-trash-can w-4"></i><span>Hapus</span>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                            <p class="md:col-span-3 text-center text-text-secondary dark:text-dark-text-secondary p-8">Tidak ada MoM yang dibuat oleh Admin saat ini.</p>
                        @endforelse
                    </div>
                </div>

                {{-- ALL MOM CONTENT (WAJIB DISAMAKAN STRUKTURNYA) --}}
                <div id="all-mom-content" class="hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                         @forelse($allMoms as $mom)
                        {{-- Salin-tempel struktur kartu yang sama persis dari atas untuk konsistensi --}}
                        <div x-data="{ actionsOpen: false }" class="bg-body-bg dark:bg-dark-body-bg rounded-2xl shadow-lg overflow-hidden transition-all duration-300 hover:shadow-2xl hover:-translate-y-1">
                            <div class="relative">
                                @php 
                                    $attachment = $mom->attachments->first();
                                    $imagePath = ($attachment && str_starts_with($attachment->mime_type, 'image/')) 
                                        ? Storage::url($attachment->file_path) 
                                        : asset('img/lampiran.png'); 
                                @endphp
                                <img class="w-full h-48 object-cover" src="{{ $imagePath }}" alt="Dokumentasi Rapat">
                                <span class="absolute top-3 right-3 bg-primary text-white text-xs font-semibold px-3 py-1 rounded-full shadow-md">{{ \Carbon\Carbon::parse($mom->meeting_date)->isoFormat('DD MMMM YYYY') }}</span>
                                <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                            </div>
                            <div class="p-5 flex flex-col relative">
                                <h3 class="text-xl font-bold text-text-primary dark:text-dark-text-primary mb-2">{{ $mom->title }}</h3>
                                <div class="flex items-center text-sm text-text-secondary dark:text-dark-text-secondary mb-3">
                                    <i class="fa-solid fa-user-pen mr-2 text-primary"></i>Dibuat oleh<span class="ml-1 font-medium">{{ $mom->creator->name ?? 'Pengguna' }}</span>
                                    <span class="ml-auto text-xs font-semibold px-2 py-0.5 rounded-full text-white {{ $mom->status->status_id == 1 ? 'bg-yellow-500' : ($mom->status->status_id == 2 ? 'bg-green-500' : 'bg-red-500') }}">{{ $mom->status->status ?? 'N/A' }}</span>
                                </div>
                                <p class="text-sm text-text-secondary dark:text-dark-text-secondary mb-4 line-clamp-2">{{ $mom->pembahasan }}</p>
                                <div class="pt-4 border-t border-border-light dark:border-border-dark">
                                    <h4 class="text-sm font-semibold text-text-primary dark:text-dark-text-primary mb-3">Peserta</h4>
                                    <div class="flex items-start justify-between">
                                        <div class="text-sm text-text-secondary dark:text-dark-text-secondary leading-relaxed">
                                            @php 
                                                // Ambil data, coba decode, atau default ke array kosong
                                                $peserta = is_string($mom->nama_peserta) ? json_decode($mom->nama_peserta, true) : ($mom->nama_peserta ?? []);
                                                $mitra = is_string($mom->nama_mitra) ? json_decode($mom->nama_mitra, true) : ($mom->nama_mitra ?? []);

                                                // Memastikan keduanya adalah array sebelum digabung
                                                $peserta = is_array($peserta) ? $peserta : [];
                                                
                                                // Menggabungkan dan filter elemen kosong/null
                                                $allParticipants = array_filter(array_merge($peserta));
                                                
                                                // Hitung dan tentukan peserta yang ditampilkan
                                                $totalParticipants = count($allParticipants);
                                                $displayParticipants = array_slice($allParticipants, 0, 2); 
                                                $remainingParticipantsCount = $totalParticipants - count($displayParticipants);
                                            @endphp
                                            {{-- Menampilkan 2 Peserta Pertama --}}
                                            @forelse($displayParticipants as $p)
                                                •
                                                @if(is_array($p))
                                                    {{ $p['name'] ?? $p['user_name'] ?? 'Peserta' }}<br>
                                                @else
                                                    {{ $p }}<br>
                                                @endif
                                            @empty
                                                Tidak ada peserta.
                                            @endforelse
                                            
                                            {{-- Menampilkan indikator "lainnya" jika ada sisa peserta --}}
                                            @if($remainingParticipantsCount > 0)
                                                <span class="text-primary font-medium">+ {{ $remainingParticipantsCount }} lainnya</span>
                                            @endif
                                        </div>
                                        <button @click="actionsOpen = true" class="flex items-center gap-2 text-sm font-medium text-text-secondary dark:text-dark-text-secondary hover:text-primary dark:hover:text-primary-dark p-2 rounded-lg hover:bg-gray-200 dark:hover:bg-dark-body-bg">
                                            <i class="fa-solid fa-ellipsis-vertical"></i><span>Aksi</span>
                                        </button>
                                    </div>
                                </div>
                                <div x-show="actionsOpen" x-transition @click.self="actionsOpen = false" class="absolute inset-0 z-10 flex items-center justify-center bg-white/70 dark:bg-black/60 backdrop-blur-sm p-5">
                                    <div class="relative w-full max-w-xs bg-component-bg dark:bg-dark-component-bg rounded-xl shadow-2xl p-4 border border-border-light dark:border-border-dark">
                                        <button @click="actionsOpen = false" class="absolute top-2 right-2 p-2 text-text-secondary hover:text-text-primary dark:text-dark-text-secondary dark:hover:text-dark-text-primary rounded-full hover:bg-gray-200 dark:hover:bg-dark-body-bg">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                        <h5 class="text-lg font-bold text-center mb-4 text-text-primary dark:text-dark-text-primary">Pilih Aksi</h5>
                                        <div class="flex flex-col gap-2">
                                            <a href="{{ url('/admin/details/' . $mom->version_id) }}" class="flex w-full items-center gap-3 px-4 py-2 text-sm text-text-primary dark:text-dark-text-primary hover:bg-gray-100 dark:hover:bg-dark-body-bg rounded-lg">
                                                <i class="fa-solid fa-eye w-4"></i><span>Lihat Detail</span>
                                            </a>
                                            <a href="{{ route('admin.moms.edit', $mom->version_id) }}" class="flex w-full items-center gap-3 px-4 py-2 text-sm text-text-primary dark:text-dark-text-primary hover:bg-gray-100 dark:hover:bg-dark-body-bg rounded-lg">
                                                <i class="fa-solid fa-pen-to-square w-4"></i><span>Edit</span>
                                            </a>
                                            <form action="#" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus MoM ini?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg">
                                                    <i class="fa-solid fa-trash-can w-4"></i><span>Hapus</span>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                         @empty
                            <p class="md:col-span-3 text-center text-text-secondary dark:text-dark-text-secondary p-8">Tidak ada MoM tersedia.</p>
                         @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<script>
    function switchTab(tabId) {
        const myMomTab = document.getElementById('my-mom-tab');
        const allMomTab = document.getElementById('all-mom-tab');
        const myMomContent = document.getElementById('my-mom-content');
        const allMomContent = document.getElementById('all-mom-content');

        const activeClasses = ['text-primary', 'border-primary'];
        const inactiveClasses = ['border-transparent', 'hover:text-gray-600', 'hover:border-gray-300', 'dark:hover:text-gray-300'];

        // Reset classes for both tabs
        myMomTab.classList.remove(...activeClasses, ...inactiveClasses);
        allMomTab.classList.remove(...activeClasses, ...inactiveClasses);
        
        // Add inactive classes initially (this handles styling consistency)
        myMomTab.classList.add(...inactiveClasses, 'text-text-secondary');
        allMomTab.classList.add(...inactiveClasses, 'text-text-secondary');


        myMomContent.classList.add('hidden');
        allMomContent.classList.add('hidden');

        if (tabId === 'my-mom') {
            myMomTab.classList.remove(...inactiveClasses, 'text-text-secondary');
            myMomTab.classList.add(...activeClasses);
            myMomContent.classList.remove('hidden');
        } else {
            allMomTab.classList.remove(...inactiveClasses, 'text-text-secondary');
            allMomTab.classList.add(...activeClasses);
            allMomContent.classList.remove('hidden');
        }
    }
    document.addEventListener('DOMContentLoaded', function() {
        switchTab('my-mom');
    });
</script>
@endpush