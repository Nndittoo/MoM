@extends('layouts.app')

@section('title', 'Create MoM | MoM Telkom')

@push('styles')
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
@endpush

@section('content')
<div class="pt-16">
            <div id="toast" class="hidden fixed top-24 right-5 z-50 items-center gap-3 px-4 py-3 rounded-xl shadow-lg bg-white dark:bg-dark-component-bg border border-border-light dark:border-border-dark text-text-primary dark:text-dark-text-primary transition-all duration-500 opacity-0"><div class="flex-shrink-0"><i class="fa-solid fa-circle-check text-green-500 text-lg"></i></div><div class="text-sm font-medium">MoM berhasil disubmit!</div></div>

            <div
                    class="flex flex-col md:flex-row items-center justify-between p-6 md:p-8 overflow-hidden rounded-lg shadow-md bg-component-bg dark:bg-dark-component-bg border-l-4 border-primary mb-6">
                    <div class="flex items-center space-x-4">
                        <div>
                            <h1 class="text-3xl font-bold text-text-primary dark:text-dark-text-primary">
                                Create New MoM
                            </h1>
                            <p class="mt-1 text-text-secondary dark:text-dark-text-secondary">
                                Buat notulensi rapat baru dengan mengisi form di bawah ini.
                            </p>
                        </div>
                    </div>
                </div>

            <div class="bg-component-bg dark:bg-dark-component-bg p-6 md:p-8 rounded-2xl shadow-lg">
                <form id="mom-form" class="space-y-10">

                    <div class="space-y-6">
                        <h2 class="text-base font-semibold text-text-primary dark:text-dark-text-primary border-b border-border-light dark:border-border-dark pb-3">Informasi Rapat</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div><label for="judul" class="block mb-2 text-sm font-medium">Judul Rapat</label><input type="text" id="judul" class="bg-body-bg border border-border-light text-text-primary text-sm rounded-lg focus:ring-primary focus:border-primary block w-full p-2.5 dark:bg-dark-component-bg dark:border-border-dark" placeholder="Contoh: Rapat Progres Proyek Q3" required></div>
                            <div><label for="tempat" class="block mb-2 text-sm font-medium">Tempat</label><input type="text" id="tempat" class="bg-body-bg border border-border-light text-text-primary text-sm rounded-lg focus:ring-primary focus:border-primary block w-full p-2.5 dark:bg-dark-component-bg dark:border-border-dark" placeholder="Contoh: Ruang Rapat Lt. 5 / Online" required></div>
                            <div><label for="pimpinan" class="block mb-2 text-sm font-medium">Pimpinan Rapat</label><input type="text" id="pimpinan" class="bg-body-bg border border-border-light text-text-primary text-sm rounded-lg focus:ring-primary focus:border-primary block w-full p-2.5 dark:bg-dark-component-bg dark:border-border-dark" placeholder="Nama pimpinan" required></div>
                            <div><label for="notulen" class="block mb-2 text-sm font-medium">Notulen</label><input type="text" id="notulen" class="bg-body-bg border border-border-light text-text-primary text-sm rounded-lg focus:ring-primary focus:border-primary block w-full p-2.5 dark:bg-dark-component-bg dark:border-border-dark" placeholder="Nama pencatat notulensi" required></div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div><label for="tanggal" class="block mb-2 text-sm font-medium">Tanggal</label><input type="date" id="tanggal" class="bg-body-bg border border-border-light text-text-primary text-sm rounded-lg focus:ring-primary focus:border-primary block w-full p-2.5 dark:bg-dark-component-bg dark:border-border-dark"></div>
                            <div><label for="waktu_mulai" class="block mb-2 text-sm font-medium">Waktu Mulai</label><input type="time" id="waktu_mulai" class="bg-body-bg border border-border-light text-text-primary text-sm rounded-lg focus:ring-primary focus:border-primary block w-full p-2.5 dark:bg-dark-component-bg dark:border-border-dark"></div>
                            <div><label for="waktu_selesai" class="block mb-2 text-sm font-medium">Waktu Selesai</label><input type="time" id="waktu_selesai" class="bg-body-bg border border-border-light text-text-primary text-sm rounded-lg focus:ring-primary focus:border-primary block w-full p-2.5 dark:bg-dark-component-bg dark:border-border-dark"></div>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <h2 class="text-base font-semibold text-text-primary dark:text-dark-text-primary border-b border-border-light dark:border-border-dark pb-3">Peserta & Agenda</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div><label class="block mb-2 text-sm font-medium">Peserta</label><div class="flex gap-2"><input type="text" id="input-peserta" class="bg-body-bg border border-border-light text-text-primary text-sm rounded-lg focus:ring-primary focus:border-primary block w-full p-2.5 dark:bg-dark-component-bg dark:border-border-dark" placeholder="Ketik nama lalu Enter..."><button type="button" id="btn-add-peserta" class="px-4 py-2 text-sm font-medium text-white bg-gradient-primary rounded-lg hover:opacity-90">Add</button></div><div id="list-peserta" class="flex flex-wrap gap-2 mt-3"></div></div>
                            <div><label class="block mb-2 text-sm font-medium">Agenda</label><div class="flex gap-2"><input type="text" id="input-agenda" class="bg-body-bg border border-border-light text-text-primary text-sm rounded-lg focus:ring-primary focus:border-primary block w-full p-2.5 dark:bg-dark-component-bg dark:border-border-dark" placeholder="Ketik agenda lalu Enter..."><button type="button" id="btn-add-agenda" class="px-4 py-2 text-sm font-medium text-white bg-gradient-primary rounded-lg hover:opacity-90">Add</button></div><ol id="list-agenda" class="mt-3 space-y-2 list-decimal list-inside text-sm"></ol></div>
                        </div>
                    </div>

                    <div><h2 class="text-base font-semibold text-text-primary dark:text-dark-text-primary border-b border-border-light dark:border-border-dark pb-3 mb-6">Pembahasan</h2><div id="pembahasan-editor"></div><input type="hidden" name="pembahasan" id="pembahasan"></div>

                    <div class="space-y-4">
                        <h2 class="text-base font-semibold text-text-primary dark:text-dark-text-primary border-b border-border-light dark:border-border-dark pb-3">Tindak Lanjut (Action Items)</h2>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                            <div class="md:col-span-2"><label for="input-task" class="text-xs font-medium text-text-secondary">Deskripsi Task</label><input type="text" id="input-task" class="mt-1 bg-body-bg border border-border-light text-text-primary text-sm rounded-lg focus:ring-primary focus:border-primary block w-full p-2.5 dark:bg-dark-component-bg dark:border-border-dark" placeholder="Contoh: Finalisasi desain UI/UX"></div>
                            <div><label for="input-deadline" class="text-xs font-medium text-text-secondary">Deadline</label><input type="date" id="input-deadline" class="mt-1 bg-body-bg border border-border-light text-text-primary text-sm rounded-lg focus:ring-primary focus:border-primary block w-full p-2.5 dark:bg-dark-component-bg dark:border-border-dark"></div>
                        </div>
                        <div class="text-right"><button type="button" id="btn-add-task" class="inline-flex items-center px-4 py-2 text-xs font-semibold text-primary border border-primary/50 rounded-lg hover:bg-primary/10 dark:text-primary dark:hover:bg-primary/20">+ Tambah Tindak Lanjut</button></div>
                        <div id="list-tindak-lanjut" class="space-y-3 pt-3 border-t border-border-light dark:border-border-dark"></div>
                    </div>

                    <div><h2 class="text-base font-semibold text-text-primary dark:text-dark-text-primary border-b border-border-light dark:border-border-dark pb-3 mb-6">Lampiran</h2><input class="block w-full text-sm text-text-primary border border-border-light rounded-lg cursor-pointer bg-body-bg dark:text-dark-text-secondary focus:outline-none dark:bg-dark-component-bg dark:border-border-dark" id="lampiran" type="file" multiple><p class="mt-1 text-xs text-text-secondary">PNG, JPG, PDF, DOCX (MAX. 5MB per file).</p></div>

                    <div class="flex justify-end gap-4 pt-6 border-t border-border-light dark:border-border-dark">
                        <button type="button" id="btn-submit" class="text-white bg-gradient-primary hover:opacity-90 font-medium rounded-lg text-sm px-8 py-2.5 text-center">Submit MoM</button>
                    </div>
                </form>
            </div>
        </div>
@endsection

@push('scripts')
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
    // All the JavaScript from the bottom of create.html goes here
    document.addEventListener("DOMContentLoaded", () => {
        // --- Setup Peserta (Pills) ---
        function setupParticipantPills() { /* ... */ }
        setupParticipantPills();
        // --- Setup Agenda (List) ---
        function setupAgendaList() { /* ... */ }
        setupAgendaList();
        // --- Setup Action Items ---
        function setupActionItems() { /* ... */ }
        setupActionItems();
        // --- Inisialisasi Quill JS ---
        const quill = new Quill('#pembahasan-editor', { /* ... */ });
        // --- Toast Notification ---
        const btnSubmit = document.getElementById("btn-submit");
        // ... rest of the script
    });
</script>
@endpush
