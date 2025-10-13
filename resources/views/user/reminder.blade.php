@extends('layouts.app')

@section('title', 'Reminder | TR1 MoMatic')

@section('content')
<div class="pt-2">
    {{-- Header Halaman (Tema Disesuaikan) --}}
    <div class="p-6 md:p-8 rounded-xl shadow-lg bg-gray-800 border-l-4 border-red-500 mb-8">
        <h1 class="text-3xl font-bold font-orbitron text-neon-red">Reminder Tugas</h1>
        <p class="mt-1 text-gray-400">
            Daftar tugas penting yang mendekati deadline.
            <strong class="text-white">{{ $stats['total_count'] }} tugas</strong> memerlukan perhatian Anda.
        </p>
    </div>

    {{-- Pesan Sukses (Tema Disesuaikan) --}}
    @if(session('success'))
    <div class="mb-6 p-4 bg-green-900/50 border-l-4 border-green-500 text-green-300 rounded-lg">
        <p class="font-medium">{{ session('success') }}</p>
    </div>
    @endif

    {{-- Kategori: Mendekati Deadline --}}
    <div>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-white font-orbitron">
                Mendesak (&lt; 24 Jam)
            </h2>
            <span class="px-3 py-1 text-sm font-semibold rounded-full bg-red-900/50 text-red-400">
                {{ $stats['urgent_count'] }} Tugas
            </span>
        </div>

        <div class="flex flex-col gap-4">
            @forelse($urgentTasks as $task)
            {{-- Kartu Tugas (Tema Disesuaikan) --}}
            <div class="flex items-center justify-between bg-gray-800 shadow-md rounded-lg p-5 hover:bg-gray-700/50 transition border-l-4 border-red-500">
                <div class="flex items-center space-x-4 w-full">
                    <div class="flex-shrink-0 w-12 h-12 flex items-center justify-center rounded-lg bg-red-500/10 text-red-400">
                        <i class="fa-solid fa-hourglass-half fa-xl"></i>
                    </div>
                    <div class="flex-grow">
                        <h3 class="text-lg font-semibold text-white">
                            {{ $task['task'] }}
                        </h3>
                        <p class="text-sm text-gray-400 flex items-center mt-1">
                            <i class="fa-solid fa-file-lines fa-xs mr-2"></i>
                            <span>MoM - {{ \Str::limit($task['mom_title'], 50) }}</span>
                        </p>
                        <p class="text-sm font-bold text-red-400 mt-1">
                            Deadline: {{ $task['deadline_formatted'] }}
                        </p>
                    </div>
                    <div class="flex items-center gap-3 flex-shrink-0 ml-4">
                        {{-- Badge (Tema Disesuaikan) --}}
                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-900/50 text-red-400">
                            {{ $task['badge'] }}
                        </span>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-10 bg-gray-800/50 rounded-lg border border-dashed border-gray-700">
                <i class="fa-solid fa-check-circle fa-3x text-green-500 opacity-50"></i>
                <p class="mt-4 text-sm text-gray-500">Tidak ada tugas mendesak saat ini. Kerja bagus!</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Kategori: Minggu Ini --}}
    <div class="mt-10">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-white font-orbitron">
                Minggu Ini (&lt; 7 Hari)
            </h2>
            <span class="px-3 py-1 text-sm font-semibold rounded-full bg-yellow-900/50 text-yellow-400">
                {{ $stats['weekly_count'] }} Tugas
            </span>
        </div>

        <div class="flex flex-col gap-4">
            @forelse($weeklyTasks as $task)
            <div class="flex items-center justify-between bg-gray-800 shadow-md rounded-lg p-5 hover:bg-gray-700/50 transition border-l-4 border-yellow-500">
                <div class="flex items-center space-x-4 w-full">
                    <div class="flex-shrink-0 w-12 h-12 flex items-center justify-center rounded-lg bg-yellow-500/10 text-yellow-400">
                        <i class="fa-solid fa-calendar-week fa-xl"></i>
                    </div>
                    <div class="flex-grow">
                        <h3 class="text-lg font-semibold text-white">
                            {{ $task['task'] }}
                        </h3>
                        <p class="text-sm text-gray-400 flex items-center mt-1">
                            <i class="fa-solid fa-file-lines fa-xs mr-2"></i>
                            <span>MoM - {{ \Str::limit($task['mom_title'], 50) }}</span>
                        </p>
                        <p class="text-sm text-yellow-400 mt-1">
                            Deadline: {{ $task['deadline_formatted'] }}
                        </p>
                    </div>
                    <div class="flex items-center gap-3 flex-shrink-0 ml-4">
                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-900/50 text-yellow-400">
                            {{ $task['badge'] }}
                        </span>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-10 bg-gray-800/50 rounded-lg border border-dashed border-gray-700">
                <i class="fa-solid fa-thumbs-up fa-3x text-gray-600"></i>
                <p class="mt-4 text-sm text-gray-500">Tidak ada tugas untuk minggu ini.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
