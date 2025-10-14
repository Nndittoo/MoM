@extends('admin.layouts.app')

@section('title', 'Repository MoM | TR1 MoMatic')

@push('styles')
<style>
    /* Animasi fade-in untuk kartu MoM */
    .card-mom {
        opacity: 0;
        transform: scale(0.95);
        animation: fadeInScaleUp 0.5s ease-out forwards;
    }
    @keyframes fadeInScaleUp { to { opacity: 1; transform: scale(1); } }
</style>
@endpush

@section('content')
<div class="pt-2">
    {{-- Toast Notification --}}
    <div id="toast" class="hidden fixed top-24 right-5 z-50 items-center gap-3 px-4 py-3 rounded-xl shadow-lg bg-gray-700 border border-gray-600 text-white transition-all duration-500 opacity-0">
        {{-- Konten diisi oleh JS --}}
    </div>

    <div class="space-y-6">
        {{-- Header Halaman --}}
        <div class="p-6 md:p-8 rounded-xl shadow-lg bg-gray-800 border-l-4 border-red-500">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold font-orbitron text-neon-red">Repository MoM</h1>
                    <p class="mt-1 text-gray-400">Cari, lihat, dan kelola semua Minute of Meetings.</p>
                </div>
                <a href="{{ route('admin.creates') }}" class="w-full md:w-auto flex justify-center items-center px-5 py-2.5 text-sm font-semibold text-white btn-neon-red rounded-lg shadow-lg">
                    <i class="fa-solid fa-plus mr-2"></i>Buat MoM Baru
                </a>
            </div>
        </div>

        {{-- Search dan Filter Section --}}
        <form action="{{ route('admin.repository') }}" method="GET">
            <div class="bg-gray-800 p-4 rounded-xl shadow border border-gray-700">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari berdasarkan judul atau proyek..."
                           class="w-full md:col-span-3 bg-gray-700 border-gray-600 text-white rounded-lg text-sm focus:ring-red-500 focus:border-red-500">
                    <input type="date" name="date" value="{{ request('date') }}"
                           class="w-full bg-gray-700 border-gray-600 text-white rounded-lg text-sm focus:ring-red-500 focus:border-red-500">
                    <div class="flex items-center gap-2">
                        <button type="submit" class="w-full flex justify-center items-center px-4 py-2 text-sm font-semibold text-white btn-neon-red rounded-lg">
                            <i class="fa-solid fa-search mr-2"></i>Cari
                        </button>
                        <a href="{{ route('admin.repository') }}" class="p-2.5 text-gray-400 hover:text-white bg-gray-700 hover:bg-gray-600 rounded-lg" title="Reset Filter">
                            <i class="fa-solid fa-times"></i>
                        </a>
                    </div>
                </div>
            </div>
        </form>

        {{-- Tabs Section --}}
        <div class="bg-gray-800 rounded-xl shadow p-4 border border-gray-700">
            <ul class="flex flex-wrap text-sm font-medium text-center border-b border-gray-700">
                <li class="me-2">
                    <button onclick="switchTab('my-mom')" id="my-mom-tab" class="tab-button inline-block p-4 rounded-t-lg border-b-2">My MoM</button>
                </li>
                <li class="me-2">
                    <button onclick="switchTab('all-mom')" id="all-mom-tab" class="tab-button inline-block p-4 rounded-t-lg border-b-2">Semua MoM</button>
                </li>
            </ul>

            <div class="pt-6">
                {{-- MY MOM CONTENT --}}
                <div id="my-mom-content">
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        @forelse($momsByAdmin as $index => $mom)
                            @php
                                $attachment = $mom->attachments->first();
                                $imageUrl = ($attachment && str_starts_with($attachment->mime_type, 'image/')) ? asset('storage/' . $attachment->file_path) : asset('img/lampiran-kosong.png');
                                $statusText = $mom->status->status ?? 'Unknown';
                                $statusInfo = match ($statusText) {
                                    'Menunggu' => ['dot' => 'bg-yellow-400', 'text' => 'text-yellow-300', 'label' => 'Pending'],
                                    'Ditolak'  => ['dot' => 'bg-red-500',    'text' => 'text-red-400',    'label' => 'Rejected'],
                                    'Disetujui'=> ['dot' => 'bg-green-500',  'text' => 'text-green-400',  'label' => 'Approved'],
                                    default    => ['dot' => 'bg-gray-500',   'text' => 'text-gray-400',   'label' => 'Unknown'],
                                };
                            @endphp
                            <div x-data="{ actionsOpen: false }" class="card-mom bg-gray-800 rounded-2xl shadow-lg overflow-hidden flex flex-col transition-all duration-300 hover:shadow-2xl hover:shadow-red-500/20 hover:-translate-y-2 border border-gray-700" style="animation-delay: {{ $index * 100 }}ms;">
                                <div class="relative">
                                    <a href="{{ route('admin.moms.show', $mom->version_id) }}"><img class="w-full h-48 object-cover" src="{{ $imageUrl }}" alt="Dokumentasi Rapat"></a>
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent"></div>
                                    <div class="absolute top-3 right-3"><div class="inline-flex items-center gap-x-2 px-3 py-1 bg-gray-900/50 backdrop-blur-sm rounded-full border border-gray-700"><span class="w-2.5 h-2.5 rounded-full {{ $statusInfo['dot'] }}"></span><span class="text-xs font-medium {{ $statusInfo['text'] }}">{{ $statusInfo['label'] }}</span></div></div>
                                    <div class="absolute bottom-0 left-0 p-4 text-white"><p class="text-xs font-semibold uppercase tracking-wider">{{ $mom->created_at->translatedFormat('F Y') }}</p><p class="text-3xl font-bold">{{ $mom->created_at->format('d') }}</p></div>
                                </div>
                                <div class="p-5 flex flex-col flex-grow relative">
                                    <h3 class="text-xl font-bold text-white mb-2 line-clamp-1" title="{{ $mom->title }}"><a href="{{ route('admin.moms.show', $mom->version_id) }}" class="hover:underline">{{ $mom->title }}</a></h3>
                                    <div class="flex items-center text-sm text-gray-400 mb-4"><i class="fa-solid fa-user-pen mr-2 text-red-400"></i> Dibuat oleh <span class="ml-1 font-medium text-gray-300">{{ $mom->creator->name ?? 'N/A' }}</span></div>
                                    <div class="pt-4 border-t border-gray-700 flex items-center justify-between mt-auto">
                                        <span class="text-sm text-gray-500"></span>
                                        <button @click="actionsOpen = !actionsOpen" class="p-2 text-gray-400 hover:text-white hover:bg-gray-700 rounded-full"><i class="fa-solid fa-ellipsis-vertical"></i></button>
                                    </div>
                                    <div x-show="actionsOpen" x-transition @click.outside="actionsOpen = false" class="absolute right-5 bottom-16 z-10 w-48 bg-gray-700 rounded-lg shadow-lg border border-gray-600" style="display: none;">
                                        <ul class="py-2 text-sm text-gray-200">
                                            <li><a href="{{ route('admin.moms.show', $mom->version_id) }}" class="flex items-center w-full px-4 py-2 hover:bg-gray-600"><i class="fa-solid fa-eye w-6"></i>Lihat Detail</a></li>
                                            <li><a href="{{ route('admin.moms.edit', $mom->version_id) }}" class="flex items-center w-full px-4 py-2 hover:bg-gray-600"><i class="fa-solid fa-pen-to-square w-6"></i>Edit</a></li>
                                            <li><button @click.prevent="deleteMom({{ $mom->version_id }}, $event)" class="w-full flex items-center px-4 py-2 text-red-400 hover:bg-gray-600"><i class="fa-solid fa-trash-can w-6"></i>Hapus</button></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-1 md:col-span-2 xl:col-span-3 text-center py-16 bg-gray-800/50 rounded-xl border border-dashed border-gray-700">
                                <i class="fa-solid fa-folder-open fa-3x text-gray-600"></i>
                                <p class="mt-4 text-lg text-gray-500">MoM kosong.</p>
                                <a href="{{ route('admin.creates') }}" class="mt-3 inline-block text-sm font-medium text-red-400 hover:underline">Buat MoM ?</a>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- ALL MOM CONTENT --}}
                <div id="all-mom-content" class="hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        @forelse($allMoms as $index => $mom)
                            @php
                                $attachment = $mom->attachments->first();
                                $imageUrl = ($attachment && str_starts_with($attachment->mime_type, 'image/')) ? asset('storage/' . $attachment->file_path) : asset('img/lampiran-kosong.png');
                                $statusText = $mom->status->status ?? 'Unknown';
                                $statusInfo = match ($statusText) {
                                    'Menunggu' => ['dot' => 'bg-yellow-400', 'text' => 'text-yellow-300', 'label' => 'Pending'],
                                    'Ditolak'  => ['dot' => 'bg-red-500',    'text' => 'text-red-400',    'label' => 'Rejected'],
                                    'Disetujui'=> ['dot' => 'bg-green-500',  'text' => 'text-green-400',  'label' => 'Approved'],
                                    default    => ['dot' => 'bg-gray-500',   'text' => 'text-gray-400',   'label' => 'Unknown'],
                                };
                            @endphp
                                <div x-data="{ actionsOpen: false }" class="card-mom bg-gray-800 rounded-2xl shadow-lg overflow-hidden flex flex-col transition-all duration-300 hover:shadow-2xl hover:shadow-red-500/20 hover:-translate-y-2 border border-gray-700" style="animation-delay: {{ $index * 100 }}ms;">
                                    <div class="relative">
                                        <a href="{{ route('admin.moms.show', $mom->version_id) }}"><img class="w-full h-48 object-cover" src="{{ $imageUrl }}" alt="Dokumentasi Rapat"></a>
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent"></div>
                                        <div class="absolute top-3 right-3"><div class="inline-flex items-center gap-x-2 px-3 py-1 bg-gray-900/50 backdrop-blur-sm rounded-full border border-gray-700"><span class="w-2.5 h-2.5 rounded-full {{ $statusInfo['dot'] }}"></span><span class="text-xs font-medium {{ $statusInfo['text'] }}">{{ $statusInfo['label'] }}</span></div></div>
                                        <div class="absolute bottom-0 left-0 p-4 text-white"><p class="text-xs font-semibold uppercase tracking-wider">{{ $mom->created_at->translatedFormat('F Y') }}</p><p class="text-3xl font-bold">{{ $mom->created_at->format('d') }}</p></div>
                                    </div>
                                    <div class="p-5 flex flex-col flex-grow relative">
                                        <h3 class="text-xl font-bold text-white mb-2 line-clamp-1" title="{{ $mom->title }}"><a href="{{ route('admin.moms.show', $mom->version_id) }}" class="hover:underline">{{ $mom->title }}</a></h3>
                                        <div class="flex items-center text-sm text-gray-400 mb-4"><i class="fa-solid fa-user-pen mr-2 text-red-400"></i> Dibuat oleh <span class="ml-1 font-medium text-gray-300">{{ $mom->creator->name ?? 'N/A' }}</span></div>
                                        <div class="pt-4 border-t border-gray-700 flex items-center justify-between mt-auto">
                                            <span class="text-sm text-gray-500"></span>
                                            <button @click="actionsOpen = !actionsOpen" class="p-2 text-gray-400 hover:text-white hover:bg-gray-700 rounded-full"><i class="fa-solid fa-ellipsis-vertical"></i></button>
                                        </div>
                                        <div x-show="actionsOpen" x-transition @click.outside="actionsOpen = false" class="absolute right-5 bottom-16 z-10 w-48 bg-gray-700 rounded-lg shadow-lg border border-gray-600" style="display: none;">
                                            <ul class="py-2 text-sm text-gray-200">
                                                <li><a href="{{ route('admin.moms.show', $mom->version_id) }}" class="flex items-center w-full px-4 py-2 hover:bg-gray-600"><i class="fa-solid fa-eye w-6"></i>Lihat Detail</a></li>
                                                <li><a href="{{ route('admin.moms.edit', $mom->version_id) }}" class="flex items-center w-full px-4 py-2 hover:bg-gray-600"><i class="fa-solid fa-pen-to-square w-6"></i>Edit</a></li>
                                                <li><button @click.prevent="deleteMom({{ $mom->version_id }}, $event)" class="w-full flex items-center px-4 py-2 text-red-400 hover:bg-gray-600"><i class="fa-solid fa-trash-can w-6"></i>Hapus</button></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                        @empty
                            <div class="col-span-1 md:col-span-2 xl:col-span-3 text-center py-16 bg-gray-800/50 rounded-xl border border-dashed border-gray-700">
                                <img src="https://www.svgrepo.com/show/500403/database-search.svg" alt="No Data" class="w-24 h-24 mx-auto opacity-30">
                                <p class="mt-4 text-lg text-gray-500">Tidak ada MoM yang ditemukan.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
{{-- SweetAlert2 dan AlpineJS --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<script>
    let activeTab = 'my-mom'; // Default tab

    function switchTab(tabId) {
        activeTab = tabId;
        document.getElementById('my-mom-content').classList.toggle('hidden', tabId !== 'my-mom');
        document.getElementById('all-mom-content').classList.toggle('hidden', tabId !== 'all-mom');

        // Update style untuk My MoM Tab
        const myMomTab = document.getElementById('my-mom-tab');
        myMomTab.classList.toggle('text-red-400', tabId === 'my-mom');
        myMomTab.classList.toggle('border-red-500', tabId === 'my-mom');
        myMomTab.classList.toggle('text-gray-400', tabId !== 'my-mom');
        myMomTab.classList.toggle('border-transparent', tabId !== 'my-mom');
        myMomTab.classList.toggle('hover:text-gray-300', tabId !== 'my-mom');
        myMomTab.classList.toggle('hover:border-gray-500', tabId !== 'my-mom');

        // Update style untuk All MoM Tab
        const allMomTab = document.getElementById('all-mom-tab');
        allMomTab.classList.toggle('text-red-400', tabId === 'all-mom');
        allMomTab.classList.toggle('border-red-500', tabId === 'all-mom');
        allMomTab.classList.toggle('text-gray-400', tabId !== 'all-mom');
        allMomTab.classList.toggle('border-transparent', tabId !== 'all-mom');
        allMomTab.classList.toggle('hover:text-gray-300', tabId !== 'all-mom');
        allMomTab.classList.toggle('hover:border-gray-500', tabId !== 'all-mom');
    }

    window.deleteMom = async function (momId, event) {
    const result = await Swal.fire({
        title: 'Yakin ingin menghapus MoM ini?',
        text: 'Tindakan ini tidak dapat dibatalkan!',
        icon: 'warning',
        background: '#1f2937', // bg-gray-800
        color: '#f3f4f6', // text-gray-100
        showCancelButton: true,
        confirmButtonColor: '#ef4444', // red-500
        cancelButtonColor: '#6b7280', // gray-500
        confirmButtonText: 'Ya, hapus',
        cancelButtonText: 'Batal',
        customClass: {
            popup: 'bg-gray-800 rounded-2xl border border-gray-700',
            title: 'text-white font-orbitron',
            htmlContainer: 'text-gray-400',
            confirmButton: 'btn-neon-red text-white font-semibold px-6 py-2 rounded-lg',
            cancelButton: 'bg-gray-700 text-gray-300 font-semibold px-6 py-2 rounded-lg hover:bg-gray-600 border border-gray-600'
        },
    });

    if (!result.isConfirmed) return;

    const momCard = document.getElementById(`mom-card-${momId}`);

    try {
        const response = await fetch(`/moms/${momId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
        });

        const data = await response.json();

        if (response.ok) {
            // Animasi menghilang
            if (momCard) {
                momCard.classList.add('opacity-0', 'scale-90', 'transition-all', 'duration-300');
            }

            await Swal.fire({
                icon: 'success',
                title: 'MoM Berhasil Dihapus',
                text: data.message || 'Data telah dihapus dari repository.',
                background: '#1f2937',
                color: '#f3f4f6',
                confirmButtonColor: '#ef4444',
                customClass: {
            popup: 'bg-gray-800 rounded-2xl border border-gray-700',
            title: 'text-white font-orbitron',
            htmlContainer: 'text-gray-400',
            confirmButton: 'btn-neon-red text-white font-semibold px-6 mr-2 py-2 rounded-lg',
            cancelButton: 'bg-gray-700 text-gray-300 font-semibold px-6 py-2 rounded-lg hover:bg-gray-600 border border-gray-600'
        },
                timer: 1800,
                showConfirmButton: false,
            });

            // Tunggu sebentar agar animasi sempat terlihat, lalu refresh
            setTimeout(() => {
                window.location.reload();
            }, 200);

        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal Menghapus!',
                text: data.message || 'Terjadi kesalahan, coba lagi nanti.',
                background: '#1f2937',
                color: '#f3f4f6',
                confirmButtonColor: '#ef4444',
                customClass: {
                    popup: 'rounded-2xl shadow-lg border border-gray-700',
                    title: 'text-red-400 font-semibold'
                }
            });
        }
    } catch (error) {
        console.error('Error saat menghapus MoM:', error);
        Swal.fire({
            icon: 'error',
            title: 'Terjadi Kesalahan!',
            text: 'Tidak dapat terhubung ke server.',
            background: '#1f2937',
            color: '#f3f4f6',
            confirmButtonColor: '#ef4444',
            customClass: {
                popup: 'rounded-2xl shadow-lg border border-gray-700',
                title: 'text-red-400 font-semibold'
            }
        });
    }
};



    document.addEventListener('DOMContentLoaded', () => {
        // Tentukan tab aktif berdasarkan parameter URL atau default ke 'my-mom'
        const urlParams = new URLSearchParams(window.location.search);
        const tabFromUrl = urlParams.get('tab') || 'my-mom';
        switchTab(tabFromUrl);
    });
</script>
@endpush
