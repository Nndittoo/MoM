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

            {{-- Notifikasi: MoM Baru Menunggu Persetujuan --}}
            <a href="#" class="block p-4 bg-blue-50 border border-blue-200 rounded-lg shadow-sm dark:bg-blue-900/20 dark:border-blue-800 flex items-start gap-4 hover:bg-blue-100 dark:hover:bg-blue-900/30">
                <i class="fa-solid fa-file-signature text-blue-500 text-xl mt-1"></i>
                <div>
                    <h2 class="font-semibold text-text-primary dark:text-dark-text-primary">MoM Baru Menunggu Persetujuan</h2>
                    <p class="text-sm text-text-secondary dark:text-dark-text-secondary">MoM berjudul "Evaluasi Kinerja Tim Q3" yang dibuat oleh <span class="font-medium">Neil Sims</span> menunggu untuk Anda review.</p>
                    <span class="text-xs text-blue-600 dark:text-blue-400 mt-1">15 menit yang lalu</span>
                </div>
            </a>

            {{-- Notifikasi: Tugas Mendekati Deadline --}}
            <a href="#" class="block p-4 bg-yellow-50 border border-yellow-200 rounded-lg shadow-sm dark:bg-yellow-900/20 dark:border-yellow-800 flex items-start gap-4 hover:bg-yellow-100 dark:hover:bg-yellow-900/30">
                <i class="fa-solid fa-triangle-exclamation text-yellow-500 text-xl mt-1"></i>
                <div>
                    <h2 class="font-semibold text-text-primary dark:text-dark-text-primary">Tugas Mendekati Deadline</h2>
                    <p class="text-sm text-text-secondary dark:text-dark-text-secondary">Tugas "Follow-up status OSP KIS768" dari MoM "Evaluasi Progress Project OTN TR1" akan jatuh tempo dalam 3 hari.</p>
                    <span class="text-xs text-yellow-600 dark:text-yellow-400 mt-1">1 jam yang lalu</span>
                </div>
            </a>

            {{-- Notifikasi: Pengguna Baru Mendaftar --}}
            <a href="#" class="p-4 bg-body-bg border border-border-light rounded-lg shadow-sm dark:bg-dark-body-bg dark:border-border-dark flex items-start gap-4 hover:bg-gray-100 dark:hover:bg-gray-900/40">
                <i class="fa-solid fa-user-plus text-green-500 text-xl mt-1"></i>
                <div>
                    <h2 class="font-semibold text-text-primary dark:text-dark-text-primary">Pengguna Baru Mendaftar</h2>
                    <p class="text-sm text-text-secondary dark:text-dark-text-secondary">Pengguna baru dengan nama <span class="font-medium">Jese Leos</span> telah terdaftar di sistem.</p>
                    <span class="text-xs text-text-secondary dark:text-dark-text-secondary/70 mt-1">2 hari yang lalu</span>
                </div>
            </a>

        </div>
    </div>
</div>
@endsection
