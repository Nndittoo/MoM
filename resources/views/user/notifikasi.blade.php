@extends('layouts.app')

@section('title', 'Notifikasi | MoM Telkom')

@section('content')
<div class="pt-14">
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

            <div class="p-4 bg-body-bg border border-border-light rounded-lg shadow-sm dark:bg-dark-body-bg dark:border-border-dark flex items-start gap-4">
                <i class="fa-solid fa-calendar-check text-green-500 text-xl mt-1"></i>
                <div>
                    <h2 class="font-semibold text-text-primary dark:text-dark-text-primary">KOM (Kick Off Meeting) Project NIQE 2025</h2>
                    <p class="text-sm text-text-secondary dark:text-dark-text-secondary">Time: Oct 1, 2025 08.30 am</p>
                    <span class="text-xs text-text-secondary dark:text-dark-text-secondary/70">1 hari 40 menit yang lalu</span>
                </div>
            </div>

            <div class="p-4 bg-body-bg border border-border-light rounded-lg shadow-sm dark:bg-dark-body-bg dark:border-border-dark flex items-start gap-4">
                <i class="fa-solid fa-file-lines text-yellow-500 text-xl mt-1"></i>
                <div>
                    <h2 class="font-semibold text-text-primary dark:text-dark-text-primary">Notulen Siap</h2>
                    <p class="text-sm text-text-secondary dark:text-dark-text-secondary">Notulen rapat “VALIDASI NEW ORDER MINI OLT PLATFORM ZTE (14 September 2025)” sudah tersedia dan bisa diunduh.</p>
                    <span class="text-xs text-text-secondary dark:text-dark-text-secondary/70">15 September 2025</span>
                </div>
            </div>

            <div class="p-4 bg-body-bg border border-border-light rounded-lg shadow-sm dark:bg-dark-body-bg dark:border-border-dark flex items-start gap-4">
                <i class="fa-solid fa-user-plus text-blue-500 text-xl mt-1"></i>
                <div>
                    <h2 class="font-semibold text-text-primary dark:text-dark-text-primary">Anggota Baru Bergabung</h2>
                    <p class="text-sm text-text-secondary dark:text-dark-text-secondary">Yamin baru saja bergabung ke dalam grup MoM Anda.</p>
                    <span class="text-xs text-text-secondary dark:text-dark-text-secondary/70">5 jam yang lalu</span>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
