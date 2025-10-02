@extends('admin.layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row items-center justify-between p-6 md:p-8 overflow-hidden rounded-lg shadow-md bg-component-bg dark:bg-dark-component-bg border-l-4 border-primary">
        <div class="flex items-center space-x-4">
            <img src="https://media.giphy.com/media/v1.Y2lkPWVjZjA1ZTQ3b284ZXpvZHVrcmwxZnc0MHRxN284anlsdmNtY3E1MDg1dWQ2c2txdCZlcD12MV9naWZzX3NlYXJjaCZjdD1n/8MiY7r4EfWVINa8LiK/giphy.gif" alt="Welcome GIF" class="w-16 h-16 rounded-full shadow-md object-cover">
            <div>
                <h1 class="text-3xl font-bold text-text-primary dark:text-dark-text-primary">Selamat Datang, Admin! 👋</h1>
                <p class="mt-1 text-text-secondary dark:text-dark-text-secondary">Berikut adalah ringkasan aktivitas sistem hari ini.</p>
            </div>
        </div>
    </div>

    {{-- Kartu Statistik Admin --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="flex items-center justify-between p-6 rounded-lg bg-component-bg dark:bg-dark-component-bg shadow-md">
            <div>
                <p class="text-3xl font-bold text-yellow-500">5</p>
                <p class="text-md text-text-secondary mt-1">MoM Menunggu Persetujuan</p>
            </div>
            <div class="flex items-center justify-center h-16 w-16 rounded-full bg-yellow-100 dark:bg-yellow-500/20">
                <i class="fa-solid fa-hourglass-half fa-xl text-yellow-500"></i>
            </div>
        </div>
        <div class="flex items-center justify-between p-6 rounded-lg bg-component-bg dark:bg-dark-component-bg shadow-md">
            <div>
                <p class="text-3xl font-bold text-text-primary dark:text-dark-text-primary">125</p>
                <p class="text-md text-text-secondary mt-1">MoM Disetujui</p>
            </div>
            <div class="flex items-center justify-center h-16 w-16 rounded-full bg-green-100 dark:bg-green-500/20">
                <i class="fa-solid fa-check-double fa-xl text-green-500"></i>
            </div>
        </div>
        <div class="flex items-center justify-between p-6 rounded-lg bg-component-bg dark:bg-dark-component-bg shadow-md">
            <div>
                <p class="text-3xl font-bold text-text-primary dark:text-dark-text-primary">34</p>
                <p class="text-md text-text-secondary mt-1">Total Pengguna</p>
            </div>
            <div class="flex items-center justify-center h-16 w-16 rounded-full bg-blue-100 dark:bg-blue-500/20">
                <i class="fa-solid fa-users fa-xl text-blue-500"></i>
            </div>
        </div>
        <div class="flex items-center justify-between p-6 rounded-lg bg-component-bg dark:bg-dark-component-bg shadow-md">
            <div>
                <p class="text-3xl font-bold text-text-primary dark:text-dark-text-primary">15</p>
                <p class="text-md text-text-secondary mt-1">Tugas Mendekati Deadline</p>
            </div>
            <div class="flex items-center justify-center h-16 w-16 rounded-full bg-red-100 dark:bg-red-500/20">
                <i class="fa-solid fa-triangle-exclamation fa-xl text-red-500"></i>
            </div>
        </div>
    </div>

    {{-- Daftar MoM yang Perlu Persetujuan --}}
    <div class="bg-component-bg dark:bg-dark-component-bg shadow-md sm:rounded-lg overflow-hidden">
        <div class="p-4 border-b dark:border-border-dark">
            <h5 class="text-xl font-bold text-text-primary dark:text-white">Menunggu Persetujuan Anda</h5>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-text-secondary dark:text-dark-text-secondary">
                <thead class="text-xs uppercase bg-body-bg dark:bg-dark-component-bg/50">
                    <tr>
                        <th scope="col" class="px-6 py-3">Judul MoM</th>
                        <th scope="col" class="px-6 py-3">Pembuat</th>
                        <th scope="col" class="px-6 py-3">Tanggal Dibuat</th>
                        <th scope="col" class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b dark:border-border-dark">
                        <th scope="row" class="px-6 py-4 font-medium text-text-primary dark:text-white whitespace-nowrap">Evaluasi Kinerja Tim Q3</th>
                        <td class="px-6 py-4">Neil Sims</td>
                        <td class="px-6 py-4">30 Sep 2025</td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ url('/detail') }}" class="font-medium text-primary dark:text-primary-dark hover:underline">Review</a>
                        </td>
                    </tr>
                    <tr class="border-b dark:border-border-dark">
                        <th scope="row" class="px-6 py-4 font-medium text-text-primary dark:text-white whitespace-nowrap">Perencanaan Fitur Baru v2.1</th>
                        <td class="px-6 py-4">Bonnie Green</td>
                        <td class="px-6 py-4">29 Sep 2025</td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ url('/detail') }}" class="font-medium text-primary dark:text-primary-dark hover:underline">Review</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="p-4 text-center border-t dark:border-border-dark">
             <a href="{{ url('admin/approvals') }}" class="text-sm font-medium text-primary hover:underline">Lihat Semua Persetujuan</a>
        </div>
    </div>
</div>
@endsection
