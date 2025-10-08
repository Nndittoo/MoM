@extends('layouts.app')

@section('title', 'Reminder | MoM Telkom')

@section('content')
<div class="pt-14">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row items-center justify-between p-6 md:p-8 overflow-hidden rounded-lg shadow-md bg-component-bg dark:bg-dark-component-bg border-l-4 border-primary mb-8">
        <div class="flex items-center space-x-4">
            <div>
                <h1 class="text-3xl font-bold text-text-primary dark:text-dark-text-primary">📌 Reminder Tugas</h1>
                <p class="mt-1 text-text-secondary dark:text-dark-text-secondary">
                    Daftar tugas penting yang mendekati deadline.
                    <span class="font-semibold text-primary">{{ $stats['total_count'] }} tugas</span> memerlukan perhatian Anda.
                </p>
            </div>
        </div>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
    <div class="mb-6 p-4 bg-green-100 dark:bg-green-900/20 border-l-4 border-green-500 text-green-700 dark:text-green-400 rounded">
        <p class="font-medium">{{ session('success') }}</p>
    </div>
    @endif

    {{-- Kategori: Mendekati Deadline --}}
    <div>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-text-primary dark:text-dark-text-primary">
                Mendekati Deadline (&lt; 24 Jam)
            </h2>
            <span class="px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">
                {{ $stats['urgent_count'] }} Tugas
            </span>
        </div>

        <div class="flex flex-col gap-4">
            @forelse($urgentTasks as $task)
            <div class="flex items-center justify-between bg-component-bg dark:bg-dark-component-bg shadow-md rounded-lg p-5 hover:shadow-lg transition {{ $task['border_color'] }} border-l-4">
                <div class="flex items-center space-x-4 w-full">
                    <div class="flex-shrink-0 w-12 h-12 flex items-center justify-center rounded-lg {{ $task['bg_color'] }} {{ $task['text_color'] }} {{ $task['dark_bg'] }} {{ $task['dark_text'] }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="flex-grow">
                        <h3 class="text-lg font-semibold text-text-primary dark:text-dark-text-primary">
                            {{ $task['task'] }}
                        </h3>
                        <p class="text-sm text-text-secondary dark:text-dark-text-secondary flex items-center mt-1">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 mr-1.5 flex-shrink-0">
                                <path fill-rule="evenodd" d="M4.25 2A2.25 2.25 0 002 4.25v11.5A2.25 2.25 0 004.25 18h11.5A2.25 2.25 0 0018 15.75V6.31l-5.04-5.042A1.5 1.5 0 0011.435 1H4.25zm5.75.5a.75.75 0 00-.75.75v2.5a.75.75 0 00.75.75h2.5a.75.75 0 000-1.5h-1.75v-1.75a.75.75 0 00-.75-.75z" clip-rule="evenodd" />
                            </svg>
                            <span>MoM - {{ \Str::limit($task['mom_title'], 50) }}</span>
                        </p>
                        <p class="text-sm text-text-secondary dark:text-dark-text-secondary mt-1">
                            Deadline: {{ $task['deadline_formatted'] }}
                        </p>
                    </div>
                    <div class="flex items-center gap-3 flex-shrink-0 ml-4">
                        <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $task['bg_color'] }} {{ $task['text_color'] }} {{ $task['dark_bg'] }} {{ $task['dark_text'] }}">
                            {{ $task['badge'] }}
                        </span>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-10 bg-component-bg dark:bg-dark-component-bg rounded-lg shadow-md">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mx-auto w-12 h-12 text-gray-400">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="mt-2 text-sm text-text-secondary dark:text-dark-text-secondary">Tidak ada tugas mendesak saat ini.</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Kategori: Minggu Ini --}}
    <div class="mt-10">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-text-primary dark:text-dark-text-primary">
                Minggu Ini (&lt; 7 Hari)
            </h2>
            <span class="px-3 py-1 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400">
                {{ $stats['weekly_count'] }} Tugas
            </span>
        </div>

        <div class="flex flex-col gap-4">
            @forelse($weeklyTasks as $task)
            <div class="flex items-center justify-between bg-component-bg dark:bg-dark-component-bg shadow-md rounded-lg p-5 hover:shadow-lg transition {{ $task['border_color'] }} border-l-4">
                <div class="flex items-center space-x-4 w-full">
                    <div class="flex-shrink-0 w-12 h-12 flex items-center justify-center rounded-lg {{ $task['bg_color'] }} {{ $task['text_color'] }} {{ $task['dark_bg'] }} {{ $task['dark_text'] }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0h18M-7.5 12h22.5" />
                        </svg>
                    </div>
                    <div class="flex-grow">
                        <h3 class="text-lg font-semibold text-text-primary dark:text-dark-text-primary">
                            {{ $task['task'] }}
                        </h3>
                        <p class="text-sm text-text-secondary dark:text-dark-text-secondary flex items-center mt-1">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 mr-1.5 flex-shrink-0">
                                <path fill-rule="evenodd" d="M4.25 2A2.25 2.25 0 002 4.25v11.5A2.25 2.25 0 004.25 18h11.5A2.25 2.25 0 0018 15.75V6.31l-5.04-5.042A1.5 1.5 0 0011.435 1H4.25zm5.75.5a.75.75 0 00-.75.75v2.5a.75.75 0 00.75.75h2.5a.75.75 0 000-1.5h-1.75v-1.75a.75.75 0 00-.75-.75z" clip-rule="evenodd" />
                            </svg>
                            <span>MoM - {{ \Str::limit($task['mom_title'], 50) }}</span>
                        </p>
                        <p class="text-sm text-text-secondary dark:text-dark-text-secondary mt-1">
                            Deadline: {{ $task['deadline_formatted'] }}
                        </p>
                    </div>
                    <div class="flex items-center gap-3 flex-shrink-0 ml-4">
                        <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $task['bg_color'] }} {{ $task['text_color'] }} {{ $task['dark_bg'] }} {{ $task['dark_text'] }}">
                            {{ $task['badge'] }}
                        </span>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-10 bg-component-bg dark:bg-dark-component-bg rounded-lg shadow-md">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mx-auto w-12 h-12 text-gray-400">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="mt-2 text-sm text-text-secondary dark:text-dark-text-secondary">Tidak ada tugas untuk minggu ini.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
