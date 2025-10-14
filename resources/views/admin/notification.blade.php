@extends('admin.layouts.app')

@section('title', 'Notifikasi | TR1 MoMatic')

@push('styles')
<style>
    /* Animasi fade-in untuk kartu notifikasi */
    .notification-card {
        opacity: 0;
        transform: translateX(20px);
        animation: fadeInSlideLeft 0.5s ease-out forwards;
    }
    @keyframes fadeInSlideLeft {
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
</style>
@endpush

@section('content')
<div class="pt-2">
    {{-- Header Halaman --}}
    <div class="p-6 md:p-8 rounded-xl shadow-lg bg-gray-800 border-l-4 border-red-500 mb-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold font-orbitron text-neon-red">Notifikasi</h1>
                <p class="mt-1 text-gray-400">Semua pemberitahuan sistem ada di sini.</p>
            </div>
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <a href="javascript:history.back()" class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 text-sm font-medium text-gray-300 bg-gray-800 border border-gray-700 rounded-lg hover:bg-gray-700">
                    <i class="fa-solid fa-arrow-left mr-2"></i>Kembali
                </a>
            </div>
        </div>
    </div>

    {{-- Pesan Sukses (jika ada) --}}
    @if(session('success'))
    <div class="mb-4 p-4 bg-green-900/50 border-l-4 border-green-500 text-green-300 rounded-lg">
        <i class="fa-solid fa-check-circle mr-2"></i>{{ session('success') }}
    </div>
    @endif

    {{-- Daftar Kartu Notifikasi --}}
    <div class="space-y-4">
        @forelse($notifications as $index => $notification)
        <a href="{{ route('admin.notification.read', $notification->id) }}"
           class="notification-card block p-4 border rounded-lg shadow-sm transition-all duration-300
                  {{ !$notification->is_read ? 'bg-red-900/20 border-red-500/50 hover:bg-red-900/30' : 'bg-gray-800 border-gray-700 hover:bg-gray-700/50' }}"
           style="animation-delay: {{ $index * 50 }}ms;">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0">
                    {{-- Ikon diseragamkan dengan warna merah --}}
                    <div class="inline-flex items-center justify-center w-12 h-12 bg-red-500/10 rounded-full">
                        <i class="fa-solid {{ $notification->icon }} text-red-400 text-xl"></i>
                    </div>
                </div>
                <div class="flex-1">
                    <h2 class="font-semibold text-white flex items-center gap-2">
                        {{ $notification->title }}
                        @if(!$notification->is_read)
                        <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium bg-red-500/20 text-red-300 rounded-full">
                            New
                        </span>
                        @endif
                    </h2>
                    <div class="text-sm text-gray-400 mt-1 space-y-1">
                        {!! $notification->message !!}
                    </div>
                    <span class="text-xs text-gray-500 mt-2 block">
                        <i class="fa-solid fa-clock mr-1"></i>{{ $notification->created_at->diffForHumans() }}
                    </span>
                </div>
            </div>
        </a>
        @empty
        <div class="text-center py-16 bg-gray-800/50 rounded-xl border border-dashed border-gray-700">
            <i class="fa-solid fa-bell-slash text-6xl text-gray-700 mb-4"></i>
            <p class="text-gray-500 text-lg">Belum ada notifikasi</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
