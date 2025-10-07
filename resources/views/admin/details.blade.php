@extends('admin.layouts.app')

@section('title', 'Detail MoM | MoM Telkom')

@section('content')
<div class="p-4 rounded-lg mt-3">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center sm:justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-text-primary dark:text-dark-text-primary">Detail Minute of Meeting</h1>
            <p class="mt-1 text-text-secondary dark:text-dark-text-secondary">EVALUASI PROGRESS PROJECT OTN TR1</p>
        </div>
        <div class="flex space-x-2 mt-4 sm:mt-0 w-full sm:w-auto">
            <a href="{{ url()->previous() }}" class="flex-1 sm:flex-initial inline-flex justify-center items-center px-4 py-2 text-sm font-medium text-text-secondary bg-component-bg border border-border-light rounded-lg hover:bg-body-bg dark:bg-dark-component-bg dark:text-dark-text-secondary dark:border-border-dark dark:hover:bg-dark-body-bg">
                <i class="fa-solid fa-arrow-left mr-2"></i>Kembali
            </a>
            <a href="{{ url('/mom/export') }}" target="_blank" class="flex-1 sm:flex-initial inline-flex justify-center items-center px-4 py-2 text-sm font-medium text-white bg-gradient-primary rounded-lg hover:opacity-90">
                <i class="fa-solid fa-file-pdf mr-2"></i>Export
            </a>
        </div>
    </div>

    {{-- Konten Detail dalam Bentuk Card --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Kolom Kiri: Informasi Utama & Tindak Lanjut --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Card Informasi Rapat --}}
            <div class="bg-component-bg dark:bg-dark-component-bg rounded-lg shadow-md p-6">
                <h3 class="text-xl font-bold text-text-primary dark:text-dark-text-primary mb-4 border-b dark:border-border-dark pb-3">Informasi Rapat</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div><p class="text-text-secondary"><strong>Pimpinan:</strong></p><p>PED TR1</p></div>
                    <div><p class="text-text-secondary"><strong>Notulen:</strong></p><p>M. Hanif A</p></div>
                    <div><p class="text-text-secondary"><strong>Waktu:</strong></p><p>Senin, 15 Sep 2025 | 09.00 – 10.50</p></div>
                    <div><p class="text-text-secondary"><strong>Tempat:</strong></p><p>Meeting Zoom</p></div>
                </div>
            </div>

            {{-- Card Hasil Pembahasan --}}
            <div class="bg-component-bg dark:bg-dark-component-bg rounded-lg shadow-md p-6">
                 <h3 class="text-xl font-bold text-text-primary dark:text-dark-text-primary mb-4 border-b dark:border-border-dark pb-3">Hasil Pembahasan</h3>
                <div class="prose dark:prose-invert max-w-none text-sm">
                    <pre class="font-sans whitespace-pre-wrap bg-transparent p-0">
1. rap058
   - osp berstatus drop dan perlu survei ulang terkait perizinan warga.
   - remark: request mengubah lokasi ke sidamanik desa sibontuan.
2. kis768
   - osp berstatus drop dan perlu survei ulang.
   - status osp matdel.
</pre>
                </div>
            </div>

             {{-- Card Lampiran --}}
            <div class="bg-component-bg dark:bg-dark-component-bg rounded-lg shadow-md p-6">
                <h3 class="text-xl font-bold text-text-primary dark:text-dark-text-primary mb-4 border-b dark:border-border-dark pb-3">Lampiran</h3>
                <img src="{{ asset('img/lampiran.png') }}" alt="Lampiran Rapat" class="w-full rounded-lg">
            </div>
        </div>

        {{-- Kolom Kanan: Peserta & Agenda --}}
        <div class="space-y-6">
            <div class="bg-component-bg dark:bg-dark-component-bg rounded-lg shadow-md p-6">
                <h3 class="text-xl font-bold text-text-primary dark:text-dark-text-primary mb-4"><i class="fa-solid fa-users mr-2"></i>Peserta</h3>
                <ul class="space-y-2 text-sm list-disc list-inside">
                    <li>Bg @satriasitorus</li>
                    <li>Anggota Tim A</li>
                    <li>Anggota Tim B</li>
                </ul>
            </div>
            <div class="bg-component-bg dark:bg-dark-component-bg rounded-lg shadow-md p-6">
                <h3 class="text-xl font-bold text-text-primary dark:text-dark-text-primary mb-4"><i class="fa-solid fa-list-check mr-2"></i>Agenda</h3>
                <ul class="space-y-2 text-sm list-disc list-inside">
                    <li>Evaluasi Progress Project OTN TR1</li>
                </ul>
            </div>
            {{-- Card Tindak Lanjut --}}
            <div class="bg-component-bg dark:bg-dark-component-bg rounded-lg shadow-md p-6">
                <h3 class="text-xl font-bold text-text-primary dark:text-dark-text-primary mb-4"><i class="fa-solid fa-bullseye mr-2"></i>Tindak Lanjut</h3>
                <div id="tindak-lanjut-list" class="space-y-3">
                    <div class="p-3 bg-body-bg dark:bg-dark-body-bg rounded-lg">
                        <p class="font-semibold text-sm">Plan survei ulang lokasi RAP058</p>
                        <p class="text-xs text-text-secondary">Deadline: 22 Sep 2025</p>
                    </div>
                </div>
                 <button data-modal-target="tindak-lanjut-modal" data-modal-toggle="tindak-lanjut-modal" class="mt-4 w-full flex justify-center items-center px-4 py-2 text-sm font-semibold text-white bg-green-600 rounded-lg hover:bg-green-700">
                    <i class="fa-solid fa-plus mr-2"></i>Tambah Tindak Lanjut
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Form Tambah Tindak Lanjut --}}
<div id="tindak-lanjut-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Tambah Tindak Lanjut Baru</h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-toggle="tindak-lanjut-modal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/></svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <form id="tindak-lanjut-form" class="p-4 md:p-5">
                <div class="grid gap-4 mb-4 grid-cols-1">
                    <div>
                        <label for="task-description" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Deskripsi Tugas</label>
                        <input type="text" name="description" id="task-description" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500" placeholder="Contoh: Finalisasi desain UI/UX" required>
                    </div>
                    <div>
                        <label for="task-deadline" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Deadline</label>
                        <input type="date" name="deadline" id="task-deadline" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500" required>
                    </div>
                </div>
                <button type="submit" class="text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    <i class="fa-solid fa-plus me-1 -ms-1 w-5 h-5"></i>
                    Tambahkan Tugas
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('tindak-lanjut-form');
        const listContainer = document.getElementById('tindak-lanjut-list');

        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const description = document.getElementById('task-description').value;
            const deadline = document.getElementById('task-deadline').value;

            if (!description || !deadline) {
                alert('Mohon isi semua field.');
                return;
            }

            // Format tanggal agar lebih mudah dibaca
            const formattedDate = new Date(deadline).toLocaleDateString('id-ID', {
                day: '2-digit', month: 'short', year: 'numeric'
            });

            // Buat elemen baru untuk ditambahkan ke daftar
            const newItem = document.createElement('div');
            newItem.className = 'p-3 bg-body-bg dark:bg-dark-body-bg rounded-lg';
            newItem.innerHTML = `
                <p class="font-semibold text-sm">${description}</p>
                <p class="text-xs text-text-secondary">Deadline: ${formattedDate}</p>
            `;

            // Tambahkan item baru ke dalam list
            listContainer.appendChild(newItem);

            // Reset form dan tutup modal (jika menggunakan Flowbite)
            form.reset();
            const modal = Flowbite.getInstance('tindak-lanjut-modal');
            modal.hide();
        });
    });
</script>
@endpush
