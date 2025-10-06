@extends('admin.layouts.app')

@section('title', 'Persetujuan MoM')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-text-primary dark:text-dark-text-primary">Persetujuan MoM</h1>
            <p class="mt-1 text-text-secondary dark:text-dark-text-secondary">Review dan kelola MoM yang menunggu persetujuan.</p>
        </div>
    </div>

    {{-- Daftar MoM yang Perlu Persetujuan --}}
    <div class="bg-component-bg dark:bg-dark-component-bg shadow-md sm:rounded-lg overflow-hidden">
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
                    {{-- Looping data dari controller --}}
                    <tr class="border-b dark:border-border-dark hover:bg-body-bg dark:hover:bg-dark-body-bg">
                        <th scope="row" class="px-6 py-4 font-medium text-text-primary dark:text-white">Evaluasi Kinerja Tim Q3</th>
                        <td class="px-6 py-4">Neil Sims</td>
                        <td class="px-6 py-4">30 Sep 2025</td>
                        <td class="px-6 py-4 text-center">
                            {{-- Arahkan ke halaman review detail --}}
                            <a href="#" class="font-medium text-primary hover:underline">Review & Tindak Lanjut</a>
                        </td>
                    </tr>
                     <tr class="border-b dark:border-border-dark hover:bg-body-bg dark:hover:bg-dark-body-bg">
                        <th scope="row" class="px-6 py-4 font-medium text-text-primary dark:text-white">Perencanaan Fitur Baru v2.1</th>
                        <td class="px-6 py-4">Bonnie Green</td>
                        <td class="px-6 py-4">29 Sep 2025</td>
                        <td class="px-6 py-4 text-center">
                             <a href="#" class="font-medium text-primary hover:underline">Review & Tindak Lanjut</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
