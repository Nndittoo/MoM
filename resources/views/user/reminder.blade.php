@extends('layouts.app')

@section('title', 'Reminder | MoM Telkom')

@section('content')
<div class="pt-14">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row items-center justify-between p-6 md:p-8 overflow-hidden rounded-lg shadow-md bg-component-bg dark:bg-dark-component-bg border-l-4 border-primary mb-6">
        <div class="flex items-center space-x-4">
            <div>
                <h1 class="text-3xl font-bold text-text-primary dark:text-dark-text-primary">📌 Reminder Tugas</h1>
                <p class="mt-1 text-text-secondary dark:text-dark-text-secondary">Lihat tugas yang sudah mendekati deadline.</p>
            </div>
        </div>
    </div>

    {{-- Notification Banner --}}
    <div class="mb-4 flex items-center bg-yellow-100 border border-yellow-300 text-yellow-800 px-4 py-3 rounded-lg dark:bg-yellow-500/20 dark:border-yellow-500/30 dark:text-yellow-300">
        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12A9 9 0 113 12a9 9 0 0118 0z"></path>
        </svg>
        <span class="text-sm font-medium">⚠️ Ada 2 tugas mendekati deadline!</span>
    </div>

    {{-- Task List --}}
    <div class="flex flex-col gap-4">
        {{-- CARD 1 - Deadline Hari Ini --}}
        <div class="flex items-center justify-between bg-component-bg dark:bg-dark-component-bg shadow-md rounded-xl p-5 hover:shadow-lg transition">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 flex items-center justify-center rounded-lg bg-red-100 text-red-600 dark:bg-red-500/20 dark:text-red-400">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 10V6a4 4 0 00-8 0v4H4l1 9h14l1-9h-2z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-text-primary dark:text-dark-text-primary">Backup Data</h3>
                    <p class="text-sm text-text-secondary dark:text-dark-text-secondary">Deadline: Hari ini • 20:00</p>
                    <div class="mt-1 flex space-x-2">
                        <span class="px-2 py-0.5 text-xs rounded-full bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400">High</span>
                    </div>
                </div>
            </div>
            <div class="text-right">
                <p class="text-sm font-medium text-text-secondary dark:text-dark-text-secondary">1 Oct 2025</p>
                <p class="text-xs text-red-500">Hari ini</p>
            </div>
        </div>

        {{-- CARD 2 - Besok --}}
        <div class="flex items-center justify-between bg-component-bg dark:bg-dark-component-bg shadow-md rounded-xl p-5 hover:shadow-lg transition">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 flex items-center justify-center rounded-lg bg-yellow-100 text-yellow-600 dark:bg-yellow-500/20 dark:text-yellow-400">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-text-primary dark:text-dark-text-primary">Fiber Optic Installation</h3>
                    <p class="text-sm text-text-secondary dark:text-dark-text-secondary">Deadline: Besok • 09:00</p>
                    <div class="mt-1 flex space-x-2">
                        <span class="px-2 py-0.5 text-xs rounded-full bg-yellow-100 text-yellow-700 dark:bg-yellow-500/20 dark:text-yellow-400">Medium</span>
                    </div>
                </div>
            </div>
            <div class="text-right">
                <p class="text-sm font-medium text-text-secondary dark:text-dark-text-secondary">2 Oct 2025</p>
                <p class="text-xs text-orange-500">Besok</p>
            </div>
        </div>

        {{-- CARD 3 - 3 Hari Lagi --}}
        <div class="flex items-center justify-between bg-component-bg dark:bg-dark-component-bg shadow-md rounded-xl p-5 hover:shadow-lg transition">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 flex items-center justify-center rounded-lg bg-purple-100 text-purple-600 dark:bg-purple-500/20 dark:text-purple-400">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21c4.97 0 9-4.03 9-9s-4.03-9-9-9-9 4.03-9 9 4.03 9 9 9z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.6 9h16.8M3.6 15h16.8M12 3v18" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-text-primary dark:text-dark-text-primary">MINI OLT</h3>
                    <p class="text-sm text-text-secondary dark:text-dark-text-secondary">Deadline: 3 hari lagi • 17:10</p>
                    <div class="mt-1 flex space-x-2">
                        <span class="px-2 py-0.5 text-xs rounded-full bg-red-100 text-red-600 dark:bg-red-500/20 dark:text-red-400">Urgent</span>
                        <span class="px-2 py-0.5 text-xs rounded-full bg-blue-100 text-blue-600 dark:bg-blue-500/20 dark:text-blue-400">Reminder</span>
                    </div>
                </div>
            </div>
            <div class="text-right">
                <p class="text-sm font-medium text-text-secondary dark:text-dark-text-secondary">4 Oct 2025</p>
                <p class="text-xs text-text-secondary dark:text-dark-text-secondary">3 hari lagi</p>
            </div>
        </div>
    </div>
</div>
@endsection
