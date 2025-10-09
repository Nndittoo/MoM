@extends('admin.layouts.app')

@section('title', 'Notifikasi | MoM Telkom')

@section('content')
<div class="pt-4">
    <div class="p-6 bg-white dark:bg-dark-component-bg rounded-lg shadow-md">

        {{-- Header with Back Button --}}
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-text-primary dark:text-dark-text-primary">Notifikasi</h1>
            <a href="javascript:history.back()" class="inline-flex justify-center items-center px-4 py-2 text-sm font-medium text-text-secondary bg-component-bg border border-border-light rounded-lg hover:bg-body-bg dark:bg-dark-component-bg dark:text-dark-text-secondary dark:border-border-dark dark:hover:text-white dark:hover:bg-dark-body-bg">
                <i class="fa-solid fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>

        {{-- Notification Cards --}}
        <div class="space-y-4">
            @forelse($notifications as $notification)
                <a href="{{ route('admin.notification.read', $notification->id) }}"
                   class="block p-4
                          @if(!$notification->is_read)
                              bg-{{ $notification->color }}-50 border-l-4 border-{{ $notification->color }}-500 dark:bg-{{ $notification->color }}-900/20
                          @else
                              bg-body-bg border dark:bg-dark-body-bg
                          @endif
                          border-{{ $notification->color }}-200 rounded-lg shadow-sm dark:border-{{ $notification->color }}-800 flex items-start gap-4 hover:bg-{{ $notification->color }}-100 dark:hover:bg-{{ $notification->color }}-900/30">

                    <i class="{{ $notification->icon }} text-{{ $notification->color }}-500 text-xl mt-1"></i>

                    <div>
                        <h2 class="font-semibold text-text-primary dark:text-dark-text-primary">{{ $notification->title }}</h2>
                        <p class="text-sm text-text-secondary dark:text-dark-text-secondary">{!! $notification->message !!}</p>
                        <span class="text-xs text-{{ $notification->color }}-600 dark:text-{{ $notification->color }}-400 mt-1">{{ $notification->created_at->diffForHumans() }}</span>
                    </div>
                </a>
            @empty
                <div class="text-center py-12">
                    <i class="fa-solid fa-bell-slash text-6xl text-text-secondary/30 dark:text-dark-text-secondary/30 mb-4"></i>
                    <p class="text-text-secondary dark:text-dark-text-secondary text-lg">Belum ada notifikasi untuk Anda</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
