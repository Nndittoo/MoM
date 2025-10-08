@extends('layouts.app')

@section('title', 'Notifikasi | MoM Telkom')

@section('content')
<div class="pt-14">
    <div class="p-6 bg-white dark:bg-dark-component-bg rounded-lg shadow-md">

        {{-- Header with Back Button --}}
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-text-primary dark:text-dark-text-primary">Notifikasi</h1>
            <div class="flex gap-3">
                @if($notifications->where('is_read', false)->count() > 0)
                <form action="{{ route('notifications.markAllRead') }}" method="POST">
                    @csrf
                    <button type="submit" class="inline-flex justify-center items-center px-4 py-2 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary-dark">
                        <i class="fa-solid fa-check-double mr-2"></i>Mark All as Read
                    </button>
                </form>
                @endif
                <a href="javascript:history.back()" class="inline-flex justify-center items-center px-4 py-2 text-sm font-medium text-text-secondary bg-component-bg border border-border-light rounded-lg hover:bg-body-bg dark:bg-dark-component-bg dark:text-dark-text-secondary dark:border-border-dark dark:hover:text-white dark:hover:bg-dark-body-bg">
                    <i class="fa-solid fa-arrow-left mr-2"></i>Kembali
                </a>
            </div>
        </div>

        {{-- Success Message --}}
        @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg dark:bg-green-900/20 dark:border-green-800 dark:text-green-400">
            <i class="fa-solid fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
        @endif

        {{-- Notification Cards --}}
        <div class="space-y-4">
            @forelse($notifications as $notification)
            <a href="{{ route('notifications.read', $notification->id) }}"
               class="block p-4 {{ !$notification->is_read ? 'bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500' : 'bg-body-bg dark:bg-dark-body-bg' }} border border-border-light rounded-lg shadow-sm dark:border-border-dark hover:shadow-md transition-shadow">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                        <div class="inline-flex items-center justify-center w-12 h-12 bg-{{ $notification->color }}-100 rounded-full dark:bg-{{ $notification->color }}-900">
                            <i class="fa-solid {{ $notification->icon }} text-{{ $notification->color }}-500 text-xl"></i>
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-start justify-between">
                            <div>
                                <h2 class="font-semibold text-text-primary dark:text-dark-text-primary flex items-center gap-2">
                                    {{ $notification->title }}
                                    @if(!$notification->is_read)
                                    <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium bg-blue-100 text-blue-800 rounded-full dark:bg-blue-900 dark:text-blue-300">
                                        New
                                    </span>
                                    @endif
                                </h2>
                                <p class="text-sm text-text-secondary dark:text-dark-text-secondary mt-1">
                                    {{ $notification->message }}
                                </p>
                                <p class="text-sm text-text-secondary dark:text-dark-text-secondary mt-1">
                                    <i class="fa-solid fa-file-alt mr-1"></i>MoM: {{ $notification->mom->title ?? 'N/A' }}
                                </p>
                            </div>
                        </div>
                        <span class="text-xs text-text-secondary dark:text-dark-text-secondary/70 mt-2 block">
                            <i class="fa-solid fa-clock mr-1"></i>{{ $notification->created_at->diffForHumans() }}
                        </span>
                    </div>
                </div>
            </a>
            @empty
            <div class="text-center py-12">
                <i class="fa-solid fa-bell-slash text-6xl text-text-secondary/30 dark:text-dark-text-secondary/30 mb-4"></i>
                <p class="text-text-secondary dark:text-dark-text-secondary text-lg">Belum ada notifikasi</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
