@extends('layouts.app')

@section('title', 'Reminder | MoM Telkom')

@section('content')
<div class="pt-14">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row items-center justify-between p-6 md:p-8 overflow-hidden rounded-lg shadow-md bg-component-bg dark:bg-dark-component-bg border-l-4 border-primary mb-8">
        <div class="flex items-center space-x-4">
            <div>
                <h1 class="text-3xl font-bold text-text-primary dark:text-dark-text-primary">📌 Reminder Tugas</h1>
                <p class="mt-1 text-text-secondary dark:text-dark-text-secondary">Daftar tugas penting yang mendekati deadline.</p>
            </div>
        </div>
    </div>

    {{-- Kategori: Mendekati Deadline --}}
    <div>
        <h2 class="text-xl font-bold text-text-primary dark:text-dark-text-primary mb-4">Mendekati Deadline (&lt; 24 Jam)</h2>
        <div class="flex flex-col gap-4">
            {{-- CARD 1 - Hari Ini --}}
            <div class="flex items-center justify-between bg-component-bg dark:bg-dark-component-bg shadow-md rounded-lg p-5 hover:shadow-lg transition border-l-4 border-red-500">
                <div class="flex items-center space-x-4 w-full">
                    <div class="flex-shrink-0 w-12 h-12 flex items-center justify-center rounded-lg bg-red-100 text-red-600 dark:bg-red-500/20 dark:text-red-400">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0h18M-7.5 12h22.5" />
                        </svg>
                    </div>
                    <div class="flex-grow">
                        <h3 class="text-lg font-semibold text-text-primary dark:text-dark-text-primary">Implementasi Fitur Notifikasi</h3>
                        <p class="text-sm text-text-secondary dark:text-dark-text-secondary flex items-center mt-1">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 mr-1.5 flex-shrink-0">
                                <path fill-rule="evenodd" d="M4.25 2A2.25 2.25 0 002 4.25v11.5A2.25 2.25 0 004.25 18h11.5A2.25 2.25 0 0018 15.75V6.31l-5.04-5.042A1.5 1.5 0 0011.435 1H4.25zm5.75.5a.75.75 0 00-.75.75v2.5a.75.75 0 00.75.75h2.5a.75.75 0 000-1.5h-1.75v-1.75a.75.75 0 00-.75-.75z" clip-rule="evenodd" />
                            </svg>
                            <span>MoM - Rapat Progress Mingguan</span>
                        </p>
                        <p class="text-sm text-text-secondary dark:text-dark-text-secondary mt-1">Deadline: Jumat, 3 Oktober 2025 - 20:00</p>
                    </div>
                    <div class="text-right flex-shrink-0 ml-4">
                         <span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400">Hari Ini</span>
                    </div>
                </div>
            </div>

            {{-- Jika tidak ada tugas di kategori ini, tampilkan pesan ini --}}
            {{--
            <div class="text-center py-10 bg-component-bg dark:bg-dark-component-bg rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mx-auto w-12 h-12 text-gray-400">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="mt-2 text-sm text-text-secondary dark:text-dark-text-secondary">Tidak ada tugas mendesak saat ini.</p>
            </div>
            --}}

        </div>
    </div>

    {{-- Kategori: Minggu Ini --}}
    <div class="mt-10">
        <h2 class="text-xl font-bold text-text-primary dark:text-dark-text-primary mb-4">Minggu Ini (&lt; 7 Hari)</h2>
        <div class="flex flex-col gap-4">
            {{-- CARD 2 - 3 Hari Lagi --}}
            <div class="flex items-center justify-between bg-component-bg dark:bg-dark-component-bg shadow-md rounded-lg p-5 hover:shadow-lg transition border-l-4 border-yellow-500">
                <div class="flex items-center space-x-4 w-full">
                    <div class="flex-shrink-0 w-12 h-12 flex items-center justify-center rounded-lg bg-yellow-100 text-yellow-600 dark:bg-yellow-500/20 dark:text-yellow-400">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0h18M-7.5 12h22.5" />
                        </svg>
                    </div>
                    <div class="flex-grow">
                        <h3 class="text-lg font-semibold text-text-primary dark:text-dark-text-primary">Persiapan Presentasi ke Klien</h3>
                        <p class="text-sm text-text-secondary dark:text-dark-text-secondary flex items-center mt-1">
                             <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 mr-1.5 flex-shrink-0">
                                <path fill-rule="evenodd" d="M4.25 2A2.25 2.25 0 002 4.25v11.5A2.25 2.25 0 004.25 18h11.5A2.25 2.25 0 0018 15.75V6.31l-5.04-5.042A1.5 1.5 0 0011.435 1H4.25zm5.75.5a.75.75 0 00-.75.75v2.5a.75.75 0 00.75.75h2.5a.75.75 0 000-1.5h-1.75v-1.75a.75.75 0 00-.75-.75z" clip-rule="evenodd" />
                            </svg>
                            <span>MoM - Kick-off Proyek Baru</span>
                        </p>
                        <p class="text-sm text-text-secondary dark:text-dark-text-secondary mt-1">Deadline: Senin, 6 Oktober 2025 - 10:00</p>
                    </div>
                    <div class="text-right flex-shrink-0 ml-4">
                         <span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-700 dark:bg-yellow-500/20 dark:text-yellow-400">3 Hari Lagi</span>
                    </div>
                </div>
            </div>

            {{-- CARD 3 - 6 Hari Lagi --}}
            <div class="flex items-center justify-between bg-component-bg dark:bg-dark-component-bg shadow-md rounded-lg p-5 hover:shadow-lg transition border-l-4 border-yellow-500">
                <div class="flex items-center space-x-4 w-full">
                    <div class="flex-shrink-0 w-12 h-12 flex items-center justify-center rounded-lg bg-yellow-100 text-yellow-600 dark:bg-yellow-500/20 dark:text-yellow-400">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0h18M-7.5 12h22.5" />
                        </svg>
                    </div>
                    <div class="flex-grow">
                        <h3 class="text-lg font-semibold text-text-primary dark:text-dark-text-primary">Review Instalasi OLT & OTN</h3>
                        <p class="text-sm text-text-secondary dark:text-dark-text-secondary flex items-center mt-1">
                             <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 mr-1.5 flex-shrink-0">
                                <path fill-rule="evenodd" d="M4.25 2A2.25 2.25 0 002 4.25v11.5A2.25 2.25 0 004.25 18h11.5A2.25 2.25 0 0018 15.75V6.31l-5.04-5.042A1.5 1.5 0 0011.435 1H4.25zm5.75.5a.75.75 0 00-.75.75v2.5a.75.75 0 00.75.75h2.5a.75.75 0 000-1.5h-1.75v-1.75a.75.75 0 00-.75-.75z" clip-rule="evenodd" />
                            </svg>
                            <span>MoM - Evaluasi Jaringan Fiber Optik</span>
                        </p>
                        <p class="text-sm text-text-secondary dark:text-dark-text-secondary mt-1">Deadline: Kamis, 9 Oktober 2025 - 16:30</p>
                    </div>
                    <div class="text-right flex-shrink-0 ml-4">
                         <span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-700 dark:bg-yellow-500/20 dark:text-yellow-400">6 Hari Lagi</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
