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
        <div class="bg-component-bg dark:bg-dark-component-bg p-4 rounded-lg shadow">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <input type="text" placeholder="Cari berdasarkan topik atau proyek..." class="w-full md:col-span-2 bg-body-bg border-border-light rounded-lg text-sm focus:ring-primary focus:border-primary dark:bg-dark-component-bg dark:border-border-dark">
                <input type="date" class="w-full bg-body-bg border-border-light rounded-lg text-sm focus:ring-primary focus:border-primary dark:bg-dark-component-bg dark:border-border-dark">
            </div>
        </div>

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
                        <div class="bg-component-bg dark:bg-dark-component-bg rounded-2xl shadow-lg overflow-hidden transition-all duration-300 hover:shadow-2xl hover:-translate-y-2">
                            <div class="relative">
                                @php
                                    $imagePath = $mom->attachments->first() ? Storage::url($mom->attachments->first()->file_path) : asset('img/lampiran.png');
                                @endphp
                                <img class="w-full h-48 object-cover" src="{{ $imagePath }}" alt="Dokumentasi Rapat">
                                <span class="absolute top-3 right-3 bg-primary text-white text-xs font-semibold px-3 py-1 rounded-full shadow-md">{{ \Carbon\Carbon::parse($mom->meeting_date)->isoFormat('DD MMMM YYYY') }}</span>
                                <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                            </div>
                            <div class="p-5 flex flex-col">
                                <h3 class="text-xl font-bold text-text-primary dark:text-dark-text-primary mb-2">{{ $mom->title }}</h3>
                                <div class="flex items-center text-sm text-text-secondary dark:text-dark-text-secondary mb-3">
                                    <i class="fa-solid fa-user-pen mr-2 text-primary"></i>Dibuat oleh<span class="ml-1 font-medium">{{ $mom->creator->name ?? 'Admin' }}</span>
                                    <span class="ml-auto text-xs font-semibold px-2 py-0.5 rounded-full text-white {{ $mom->status->status_id == 1 ? 'bg-yellow-500' : ($mom->status->status_id == 2 ? 'bg-green-500' : 'bg-red-500') }}">{{ $mom->status->status ?? 'N/A' }}</span>
                                </div>
                                <p class="text-sm text-text-secondary dark:text-dark-text-secondary mb-4 line-clamp-2">{{ $mom->pembahasan }}</p>
                                <div class="pt-4 border-t border-border-light dark:border-border-dark">
                                    <h4 class="text-sm font-semibold text-text-primary dark:text-dark-text-primary mb-3">Peserta</h4>
                                    <div class="flex items-center justify-between">
                                        <div class="text-sm text-text-secondary dark:text-dark-text-secondary leading-relaxed">
                                            @php
                                                // NAMA_PESERTA dan NAMA_MITRA sudah otomatis jadi array karena Model Casting
                                                $participants = array_merge($mom->nama_peserta ?? [], $mom->nama_mitra ?? []);
                                                $displayParticipants = array_slice($participants, 0, 2);
                                            @endphp
                                            @forelse($displayParticipants as $p)
                                                • {{ $p['name'] ?? $p['user_name'] ?? 'Peserta' }}<br>
                                            @empty
                                                Tidak ada peserta.
                                            @endforelse
                                        </div>
                                        <a href="{{ url('/admin/details/' . $mom->version_id) }}" class="text-sm font-medium text-primary hover:underline ml-4">View Details</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                            <p class="md:col-span-3 text-center text-text-secondary dark:text-dark-text-secondary p-8">Tidak ada MoM yang dibuat oleh Admin saat ini.</p>
                        @endforelse

                    </div>
                </div>

                {{-- ALL MOM CONTENT (SEMUA STATUS 1, 2, 3) --}}
                <div id="all-mom-content" class="hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">

                        @forelse($allMoms as $mom)
                        <div class="bg-component-bg dark:bg-dark-component-bg rounded-2xl shadow-lg overflow-hidden transition-all duration-300 hover:shadow-2xl hover:-translate-y-2">
                            <div class="relative">
                                @php
                                    $imagePath = $mom->attachments->first() ? Storage::url($mom->attachments->first()->file_path) : asset('img/lampiran.png');
                                @endphp
                                <img class="w-full h-48 object-cover" src="{{ $imagePath }}" alt="Dokumentasi Rapat">
                                <span class="absolute top-3 right-3 bg-primary text-white text-xs font-semibold px-3 py-1 rounded-full shadow-md">{{ \Carbon\Carbon::parse($mom->meeting_date)->isoFormat('DD MMMM YYYY') }}</span>
                                <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                            </div>
                            <div class="p-5 flex flex-col">
                                <h3 class="text-xl font-bold text-text-primary dark:text-dark-text-primary mb-2">{{ $mom->title }}</h3>
                                <div class="flex items-center text-sm text-text-secondary dark:text-dark-text-secondary mb-3">
                                    <i class="fa-solid fa-user-pen mr-2 text-primary"></i>Dibuat oleh<span class="ml-1 font-medium">{{ $mom->creator->name ?? 'Pengguna' }}</span>
                                    <span class="ml-auto text-xs font-semibold px-2 py-0.5 rounded-full text-white {{ $mom->status->status_id == 1 ? 'bg-yellow-500' : ($mom->status->status_id == 2 ? 'bg-green-500' : 'bg-red-500') }}">{{ $mom->status->status ?? 'N/A' }}</span>
                                </div>
                                <p class="text-sm text-text-secondary dark:text-dark-text-secondary mb-4 line-clamp-2">{{ $mom->pembahasan }}</p>
                                <div class="pt-4 border-t border-border-light dark:border-border-dark">
                                    <h4 class="text-sm font-semibold text-text-primary dark:text-dark-text-primary mb-3">Peserta</h4>
                                    <div class="flex items-center justify-between">
                                        <div class="text-sm text-text-secondary dark:text-dark-text-secondary leading-relaxed">
                                            @php
                                                // NAMA_PESERTA dan NAMA_MITRA sudah otomatis jadi array karena Model Casting
                                                $participants = array_merge($mom->nama_peserta ?? [], $mom->nama_mitra ?? []);
                                                $displayParticipants = array_slice($participants, 0, 2);
                                            @endphp
                                            @forelse($displayParticipants as $p)
                                                • {{ $p['name'] ?? $p['user_name'] ?? 'Peserta' }}<br>
                                            @empty
                                                Tidak ada peserta.
                                            @endforelse
                                        </div>
                                        <a href="{{ url('/admin/details/' . $mom->version_id) }}" class="text-sm font-medium text-primary hover:underline ml-4">View Details</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                            <p class="md:col-span-3 text-center text-text-secondary dark:text-dark-text-secondary p-8">Tidak ada MoM yang tersedia (Status 1, 2, atau 3).</p>
                        @endforelse
                        
                    </div>
                </div>
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

        // Kelas untuk style tab aktif dan tidak aktif
        const activeClasses = ['text-primary', 'border-primary'];
        const inactiveClasses = ['border-transparent', 'hover:text-gray-600', 'hover:border-gray-300', 'text-text-secondary'];

        // Reset kedua tombol ke status tidak aktif
        myMomTab.classList.remove(...activeClasses);
        allMomTab.classList.remove(...activeClasses);
        myMomTab.classList.add(...inactiveClasses);
        allMomTab.classList.add(...inactiveClasses);

        // Hapus kelas aktif dari tab yang tidak dipilih, lalu tambahkan kelas tidak aktif
        if (tabId === 'my-mom') {
            allMomTab.classList.remove(...activeClasses);
            allMomTab.classList.add(...inactiveClasses);
        } else { // 'all-mom'
            myMomTab.classList.remove(...activeClasses);
            myMomTab.classList.add(...inactiveClasses);
        }
        
        // Sembunyikan kedua konten
        myMomContent.classList.add('hidden');
        allMomContent.classList.add('hidden');

        // Aktifkan tab dan konten yang dipilih
        if (tabId === 'my-mom') {
            myMomTab.classList.add(...activeClasses);
            myMomTab.classList.remove(...inactiveClasses);
            myMomContent.classList.remove('hidden');
        } else { // 'all-mom'
            allMomTab.classList.add(...activeClasses);
            allMomTab.classList.remove(...inactiveClasses);
            allMomContent.classList.remove('hidden');
        }
    }
</script>
@endpush