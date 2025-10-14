@extends('layouts.app')

@section('title', 'Edit MoM | MoM Telkom')

@push('styles')
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>
        /* Penyesuaian tema Quill agar sesuai dengan dark mode form */
        .ql-toolbar { border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem; background-color: #1F2937; border-color: #374151 !important; }
        .ql-container { border-bottom-left-radius: 0.5rem; border-bottom-right-radius: 0.5rem; background-color: #374151; border-color: #374151 !important; color: #D1D5DB; }
        .ql-editor.ql-blank::before { color: #9CA3AF !important; font-style: normal !important; }
        .ql-snow .ql-stroke { stroke: #9CA3AF; }
        .ql-snow .ql-picker-label { color: #9CA3AF; }

        /* Style Card untuk Unit/Mitra */
        .unit-card { 
            background-color: #1f2937; /* bg-gray-700 */
            border-color: #4b5563; /* border-gray-600 */
        }
        /* Style Neon Red sesuai tema Anda sebelumnya */
        .btn-neon-red {
            background-color: #ff3366; /* Contoh warna merah neon */
            box-shadow: 0 0 5px #ff3366, 0 0 10px #ff3366;
            color: white;
        }
    </style>
@endpush

@section('content')

{{-- Inisialisasi Data dari PHP ke JavaScript --}}
@php
    
    // Menggunakan $mom->nama_peserta dan mengkonversinya
    $internalAttendeesData = $mom->nama_peserta ?? []; 

    // Konversi array string lama (jika ada) ke struktur Unit Dinamis baru
    if (is_array($internalAttendeesData) && count($internalAttendeesData) > 0 && 
        (!isset($internalAttendeesData[0]['unit']) || !is_string($internalAttendeesData[0]['unit']))
    ) {
        $convertedData = [
            [
                'unit' => 'Peserta Internal (Data Sebelumnya)', // Default Unit Name
                'attendees' => $internalAttendeesData
            ]
        ];
        $internalAttendeesJs = json_encode($convertedData);
    } else {
        // Jika data sudah dalam format baru (array of objects), gunakan langsung
        $internalAttendeesJs = json_encode($internalAttendeesData);
    }
    // -------------------------------------

    // --- Partner Attendees (Memastikan struktur 'name' dan 'attendees' terisi) ---
    $partnerAttendeesData = $mom->nama_mitra ?? [];

    $validatedPartnerAttendees = [];
    if (is_array($partnerAttendeesData)) {
        $validatedPartnerAttendees = array_map(function($mitra) {
            if (is_string($mitra)) {
                return ['name' => $mitra, 'attendees' => []];
            }
            if (is_array($mitra) && isset($mitra['name'])) {
                $mitra['attendees'] = isset($mitra['attendees']) && is_array($mitra['attendees']) 
                                     ? $mitra['attendees'] 
                                     : [];
                return $mitra;
            }
            return null;
        }, $partnerAttendeesData);
        
        $validatedPartnerAttendees = array_filter($validatedPartnerAttendees);
    }
    // -----------------------------------------------

    $partnerAttendeesJs = json_encode($validatedPartnerAttendees);
    $agendasJs = json_encode($mom->agendas->pluck('item')->toArray() ?? []);
    $oldAttachmentsJs = json_encode($mom->attachments ?? []);
@endphp

<div class="pt-2">
    {{-- Toast Notification --}}
    <div id="toast" class="hidden fixed top-24 right-5 z-50 items-center gap-3 px-4 py-3 rounded-xl shadow-lg bg-gray-700 border border-gray-600 text-white transition-all duration-500 opacity-0">
         <div class="flex-shrink-0"><i id="toast-icon" class="fa-solid text-lg"></i></div>
         <div id="toast-message" class="text-sm font-medium"></div>
    </div>

    {{-- Header (Konten Header tetap) --}}
    <div class="p-6 md:p-8 rounded-xl shadow-lg bg-gray-800 border-l-4 border-red-500 mb-6">
        <div>
            <h1 class="text-3xl font-bold font-orbitron text-neon-red">Edit MoM</h1>
            <p class="mt-1 text-gray-400 line-clamp-1" title="{{ $mom->title }}">
                Mengubah: {{ $mom->title }}
            </p>
             @if($mom->status_id == 3 && $mom->rejection_comment)
                <div class="mt-2 text-sm text-red-400 bg-red-900/30 border border-red-500/50 rounded-lg p-3">
                    <strong class="font-semibold block"><i class="fa-solid fa-triangle-exclamation mr-1"></i> Alasan Revisi dari Admin:</strong>
                    <span>{{ $mom->rejection_comment }}</span>
                </div>
            @endif
        </div>
    </div>

    {{-- Form Container --}}
    <div class="bg-gray-800 p-6 md:p-8 rounded-2xl shadow-lg border border-gray-700">
        <form id="mom-form" class="space-y-10">
            @csrf
            @method('PATCH')

            {{-- Informasi Rapat (Konten tetap) --}}
            <div class="space-y-6">
                <h2 class="text-lg font-semibold text-white font-orbitron border-b border-gray-700 pb-3">Informasi Rapat</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div><label for="judul" class="block mb-2 text-sm font-medium text-gray-300">Judul Rapat</label><input type="text" name="title" id="judul" value="{{ old('title', $mom->title) }}" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5" required></div>
                    <div><label for="tempat" class="block mb-2 text-sm font-medium text-gray-300">Tempat</label><input type="text" name="location" id="tempat" value="{{ old('location', $mom->location) }}" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5" required></div>
                    <div><label for="pimpinan_rapat" class="block mb-2 text-sm font-medium text-gray-300">Pimpinan Rapat</label><input type="text" name="pimpinan_rapat" id="pimpinan_rapat" value="{{ old('pimpinan_rapat', $mom->pimpinan_rapat) }}" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5" required></div>
                    <div><label for="notulen" class="block mb-2 text-sm font-medium text-gray-300">Notulen</label><input type="text" name="notulen" id="notulen" value="{{ old('notulen', $mom->notulen) }}" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5" required></div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div><label for="tanggal" class="block mb-2 text-sm font-medium text-gray-300">Tanggal</label><input type="date" name="meeting_date" id="tanggal" value="{{ old('meeting_date', $mom->meeting_date->format('Y-m-d')) }}" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5" required></div>
                    <div><label for="waktu_mulai" class="block mb-2 text-sm font-medium text-gray-300">Waktu Mulai</label><input type="time" name="start_time" id="waktu_mulai" value="{{ old('start_time', $mom->start_time) }}" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5" required></div>
                    <div><label for="waktu_selesai" class="block mb-2 text-sm font-medium text-gray-300">Waktu Selesai</label><input type="time" name="end_time" id="waktu_selesai" value="{{ old('end_time', $mom->end_time) }}" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5" required></div>
                </div>
            </div>

            {{-- Peserta Internal & Agenda (Disesuaikan ke mode Unit Dinamis) --}}
            <div class="space-y-6">
                <h2 class="text-lg font-semibold text-white font-orbitron border-b border-gray-700 pb-3">Peserta & Agenda</h2>
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-300">Peserta Rapat (Per Unit/Bagian)</label>
                        <div class="flex flex-col md:flex-row gap-3 items-end">
                            <div class="w-full">
                                <input type="text" id="input-internal-unit" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5" placeholder="Nama Unit/Bagian (Contoh: Unit HC)">
                            </div>
                            <button type="button" id="btn-add-internal-unit" class="px-5 py-2.5 text-sm font-medium text-white btn-neon-red rounded-lg w-full md:w-auto flex-shrink-0">Tambah Unit</button>
                        </div>
                        
                        {{-- CONTAINER INI AKAN DIISI OLEH JAVASCRIPT (renderInternalList) --}}
                        <div id="list-internal-attendees-container" class="space-y-4 mt-4"></div>
                        
                        <p class="mt-1 text-xs text-gray-500">Tambah Unit, lalu masukkan nama-nama peserta dari Unit tersebut.</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-6 pt-6 border-t border-gray-700">
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-300">Agenda</label>
                        <div class="flex gap-2">
                            <input type="text" id="input-agenda" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5" placeholder="Ketik agenda lalu Enter...">
                            <button type="button" id="btn-add-agenda" class="px-5 py-2.5 text-sm font-medium text-white btn-neon-red rounded-lg">Add</button>
                        </div>
                        <ol id="list-agenda" class="mt-3 space-y-2 list-decimal list-inside text-sm text-gray-300"></ol>
                        <p class="mt-1 text-xs text-gray-500">Minimal 1 item Agenda (Wajib).</p>
                    </div>
                </div>
            </div>

            {{-- Peserta dari Mitra (Konten tetap) --}}
            <div class="space-y-6">
                <h2 class="text-lg font-semibold text-white font-orbitron border-b border-gray-700 pb-3">Pihak Luar (Mitra)</h2>
                <div class="flex flex-col md:flex-row gap-4 items-end">
                    <div class="w-full">
                        <label for="input-mitra-nama" class="block mb-2 text-sm font-medium text-gray-300">Nama Mitra/Instansi</label>
                        <input type="text" id="input-mitra-nama" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5" placeholder="Ketik nama Mitra">
                    </div>
                    <button type="button" id="btn-add-mitra" class="px-5 py-2.5 text-sm font-medium text-white btn-neon-red rounded-lg w-full md:w-auto flex-shrink-0">Tambah Mitra</button>
                </div>
                <div id="list-mitra-container" class="space-y-4"></div>
                <p class="mt-1 text-xs text-gray-500">Tambah Mitra dan masukkan nama-nama orang yang hadir di bawahnya.</p>
            </div>

            {{-- Pembahasan (Konten tetap) --}}
            <div>
                <h2 class="text-lg font-semibold text-white font-orbitron border-b border-gray-700 pb-3 mb-6">Pembahasan</h2>
                <div id="pembahasan-editor">{!! $mom->pembahasan ?? '' !!}</div>
                <input type="hidden" name="pembahasan" id="pembahasan-hidden">
            </div>

            {{-- Lampiran (Konten tetap) --}}
            <div>
                <h2 class="text-lg font-semibold text-white font-orbitron border-b border-gray-700 pb-3 mb-6">Lampiran</h2>
                <input class="block w-full text-sm text-gray-400 border border-gray-600 rounded-lg cursor-pointer bg-gray-700 focus:outline-none file:bg-gray-600 file:border-0 file:text-gray-300 file:px-4 file:py-2.5" id="lampiran-input" name="attachments[]" type="file" multiple>
                <p class="mt-1 text-xs text-gray-500">File baru yang diunggah akan ditambahkan. File lama yang dihapus di bawah ini akan dihapus permanen saat update.</p>
                <div id="file-list" class="mt-3 space-y-2"></div>
            </div>

            {{-- Tombol Submit (Konten tetap) --}}
            <div class="flex justify-end gap-4 pt-6 border-t border-gray-700">
                <button type="button" id="btn-submit" class="text-white btn-neon-red btn-pulse font-semibold rounded-lg text-base px-10 py-3 text-center">
                    <i class="fa-solid fa-save mr-2"></i>Update MoM
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

<script>
    // FUNGSI UTILITY: Tampilkan Toast
    const showToast = (message, isError = false) => {
        const toast = document.getElementById("toast");
        const icon = document.getElementById("toast-icon");
        const messageContainer = document.getElementById("toast-message");

        icon.className = isError
            ? 'fa-solid fa-circle-xmark text-red-500 text-lg'
            : 'fa-solid fa-circle-check text-green-500 text-lg';
        messageContainer.textContent = message;

        toast.classList.remove("hidden", "opacity-0");
        toast.classList.add("opacity-100");

        setTimeout(() => {
            toast.classList.remove("opacity-100");
            toast.classList.add("opacity-0");
            setTimeout(() => { toast.classList.add("hidden"); }, 500);
        }, 5000);
    };

    document.addEventListener("DOMContentLoaded", () => {
        const form = document.getElementById('mom-form');
        const btnSubmit = document.getElementById('btn-submit');
        const fileInput = document.getElementById('lampiran-input');

        // Ambil CSRF token
        const csrfTokenElement = document.querySelector('meta[name="csrf-token"]');
        const csrfToken = csrfTokenElement ? csrfTokenElement.getAttribute('content') : '';

        // URL UPDATE (menggunakan ID MoM saat ini)
        const momId = '{{ $mom->version_id }}';
        const updateUrl = `{{ route('moms.update', $mom->version_id) }}`;

        // --- SETUP DATA GLOBAL DENGAN DATA LAMA ---
        const dataStorage = {
            // Menggunakan data yang sudah dikonversi di PHP
            internalAttendees: JSON.parse('{!! $internalAttendeesJs !!}'), 
            agendas: JSON.parse('{!! $agendasJs !!}'),
            partnerAttendees: JSON.parse('{!! $partnerAttendeesJs !!}'), 
            filesToUpload: [],
            oldFiles: JSON.parse('{!! $oldAttachmentsJs !!}'),
            filesToDelete: []
        };
        
        // --- Setup Quill JS ---
        const pembahasanQuill = new Quill('#pembahasan-editor', { theme: 'snow', placeholder: "Tuliskan hasil pembahasan, keputusan, dan poin penting lainnya...", modules: { toolbar: [[{ 'header': [1, 2, false] }], ['bold', 'italic', 'underline'], [{ 'list': 'ordered' }, { 'list': 'bullet' }], ['link'], ['clean']] } });

        // --- FUNGSI MERENDER DAFTAR FILE (Memasukkan file lama dan baru) ---
        const renderFileList = () => {
            const fileListContainer = document.getElementById('file-list');
            fileListContainer.innerHTML = '';

            const oldFilesToDisplay = dataStorage.oldFiles.filter(
                f => !dataStorage.filesToDelete.includes(f.attachment_id)
            );

            const allFiles = [
                ...oldFilesToDisplay.map(f => ({ name: f.file_name, size: f.file_size, type: 'old', id: f.attachment_id })),
                ...dataStorage.filesToUpload.map(f => ({ name: f.name, size: f.size, type: 'new', fileObj: f }))
            ];

            if (allFiles.length > 0) {
                const ul = document.createElement('ul');
                ul.className = 'list-none space-y-2';

                allFiles.forEach((file) => {
                    const li = document.createElement('li');

                    li.className = `flex items-center justify-between p-2 rounded-lg ${file.type === 'old' ? 'bg-gray-700' : 'bg-gray-600'}`; // Menyesuaikan warna dark mode

                    const fileInfo = document.createElement('span');
                    fileInfo.className = 'flex items-center text-sm font-medium truncate text-gray-300';
                    const iconColor = file.type === 'old' ? 'text-red-400' : 'text-red-500'; 
                    const statusText = file.type === 'old' ? '(Lama)' : '(Baru)';

                    fileInfo.innerHTML = `<i class="fa-solid fa-file mr-2 ${iconColor}"></i> <span>${file.name}</span> <span class="ml-2 text-xs text-gray-500">(${(file.size / 1024 / 1024).toFixed(2)} MB) ${statusText}</span>`;

                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.innerHTML = '<i class="fa-solid fa-trash text-red-500 hover:text-red-700"></i>';
                    removeBtn.title = `Hapus file ${file.name} dari daftar`;

                    removeBtn.onclick = () => {
                        if (file.type === 'new') {
                            dataStorage.filesToUpload.splice(dataStorage.filesToUpload.findIndex(f => f.name === file.name), 1);
                        } else {
                            if (!dataStorage.filesToDelete.includes(file.id)) {
                                dataStorage.filesToDelete.push(file.id);
                            }
                        }
                        renderFileList();
                        fileInput.value = null;
                    };

                    li.appendChild(fileInfo);
                    li.appendChild(removeBtn);
                    ul.appendChild(li);
                });
                fileListContainer.appendChild(ul);
            } else {
                fileListContainer.textContent = 'Belum ada file yang dipilih.';
            }
        };

        // --- EVENT LISTENER FILE INPUT ---
        fileInput.addEventListener('change', (event) => {
            const newFiles = Array.from(event.target.files);

            if (newFiles.length > 0) {
                newFiles.forEach(file => {
                    const isDuplicate = [...dataStorage.filesToUpload, ...dataStorage.oldFiles].some(existingFile => existingFile.name === file.name && existingFile.size === file.size);

                    if (!isDuplicate) {
                        dataStorage.filesToUpload.push(file);
                    } else {
                         showToast(`File ${file.name} sudah ada dalam daftar.`, true);
                    }
                });
            }

            fileInput.value = null;
            renderFileList();
        });


        // --- Setup Peserta Internal (Dynamic Unit Input) ---
        function setupInternalAttendees() {
            const inputUnit = document.getElementById('input-internal-unit');
            const btnAddUnit = document.getElementById('btn-add-internal-unit');
            const listInternalContainer = document.getElementById('list-internal-attendees-container');

            const renderInternalList = () => {
                listInternalContainer.innerHTML = '';

                dataStorage.internalAttendees.forEach((unitData, unitIndex) => {
                    const unitDiv = document.createElement('div');
                    // Menggunakan style yang mirip dengan card Mitra
                    unitDiv.className = 'p-4 border border-gray-700 rounded-lg bg-gray-900 shadow-sm space-y-3 unit-card';

                    // Header Unit
                    const header = document.createElement('div');
                    header.className = 'flex items-center justify-between border-b border-gray-700 pb-2';
                    // Menggunakan text-red-400 (neon-red) seperti Mitra
                    header.innerHTML = `<h3 class="text-base font-semibold text-red-400">${unitData.unit}</h3>`;

                    const removeUnitBtn = document.createElement('button');
                    removeUnitBtn.type = 'button';
                    removeUnitBtn.innerHTML = '<i class="fa-solid fa-trash text-red-500 hover:text-red-700 fa-sm"></i>';
                    removeUnitBtn.title = 'Hapus Unit ini beserta pesertanya';
                    removeUnitBtn.onclick = () => {
                        dataStorage.internalAttendees.splice(unitIndex, 1);
                        renderInternalList();
                    };
                    header.appendChild(removeUnitBtn);
                    unitDiv.appendChild(header);

                    // Form Input Peserta untuk Unit ini
                    const attendeeForm = document.createElement('div');
                    attendeeForm.className = 'flex gap-2';
                    attendeeForm.innerHTML = `
                        <input type="text" id="input-peserta-internal-${unitIndex}" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5" placeholder="Nama orang yang hadir">
                        <button type="button" id="btn-add-peserta-internal-${unitIndex}" class="px-4 py-2 text-xs font-medium text-white btn-neon-red rounded-lg flex-shrink-0">Tambah</button>
                    `;
                    unitDiv.appendChild(attendeeForm);

                    // List Peserta Unit
                    const attendeeList = document.createElement('ul');
                    // Ganti list-disc menjadi list-none agar tampilan lebih rapi seperti Mitra
                    attendeeList.className = 'mt-2 space-y-1 list-none text-sm text-gray-300';
                    unitData.attendees.forEach((person, personIndex) => {
                        const li = document.createElement('li');
                        // Menyesuaikan ukuran teks seperti Mitra
                        li.className = 'flex items-center justify-between text-xs md:text-sm';
                        li.textContent = person;

                        const removePersonBtn = document.createElement('button');
                        removePersonBtn.type = 'button';
                        removePersonBtn.innerHTML = '<i class="fa-solid fa-times text-red-400 hover:text-red-600 fa-xs"></i>';
                        removePersonBtn.className = 'ml-4';
                        removePersonBtn.onclick = () => {
                            dataStorage.internalAttendees[unitIndex].attendees.splice(personIndex, 1);
                            renderInternalList();
                        };

                        li.appendChild(removePersonBtn);
                        attendeeList.appendChild(li);
                    });
                    unitDiv.appendChild(attendeeList);
                    listInternalContainer.appendChild(unitDiv);

                    // Tambahkan Listener untuk tombol Tambah Peserta
                    const personInput = document.getElementById(`input-peserta-internal-${unitIndex}`);
                    const personAddBtn = document.getElementById(`btn-add-peserta-internal-${unitIndex}`);

                    const addPerson = () => {
                        const personName = personInput.value.trim();
                        if (personName === '') return;

                        if (unitData.attendees.some(n => n.toLowerCase() === personName.toLowerCase())) {
                            showToast('Peserta ini sudah ditambahkan di unit ini!', true);
                            return;
                        }

                        dataStorage.internalAttendees[unitIndex].attendees.push(personName);
                        personInput.value = '';
                        renderInternalList();
                    };

                    personAddBtn.addEventListener('click', addPerson);
                    personInput.addEventListener('keydown', (e) => {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            addPerson();
                        }
                    });
                });

                // Tampilkan pesan jika belum ada unit
                if (dataStorage.internalAttendees.length === 0) {
                    listInternalContainer.innerHTML = '<p class="text-sm text-gray-500">Silakan tambahkan Unit/Bagian yang hadir.</p>';
                }
            };

            // Tambahkan Unit Baru
            const addUnit = () => {
                const unitName = inputUnit.value.trim();
                if (unitName === '') {
                    showToast('Nama Unit/Bagian wajib diisi!', true);
                    return;
                }

                if (dataStorage.internalAttendees.some(u => u.unit.toLowerCase() === unitName.toLowerCase())) {
                    showToast('Nama Unit/Bagian ini sudah ada!', true);
                    return;
                }

                dataStorage.internalAttendees.push({ unit: unitName, attendees: [] });
                inputUnit.value = '';
                inputUnit.focus();
                renderInternalList();
            };

            if (btnAddUnit) {
                btnAddUnit.addEventListener('click', addUnit);
                inputUnit.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        addUnit();
                    }
                });
            }

            // Panggil saat inisialisasi: Ini yang memastikan data lama dirender
            renderInternalList();
        }
        setupInternalAttendees();

        // --- SETUP AGENDA (RENDER AWAL) ---
        function setupAgendaList() {
            const input = document.getElementById('input-agenda');
            const addButton = document.getElementById('btn-add-agenda');
            const listContainer = document.getElementById('list-agenda');

            const renderList = () => {
                listContainer.innerHTML = '';
                dataStorage.agendas.forEach((item, index) => {
                    const listItem = document.createElement('li');
                    listItem.className = 'flex items-center justify-between text-gray-300';
                    listItem.textContent = item;

                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.innerHTML = '<i class="fa-solid fa-times text-red-500 hover:text-red-700 fa-sm"></i>';
                    removeBtn.className = 'ml-4';
                    removeBtn.onclick = () => {
                        dataStorage.agendas.splice(index, 1);
                        renderList();
                    };
                    listItem.appendChild(removeBtn);
                    listContainer.appendChild(listItem);
                });
            };

            const addItem = () => {
                const value = input.value.trim();
                if (value === '') return;
                dataStorage.agendas.push(value);
                renderList();
                input.value = '';
                input.focus();
            };

            if (addButton) {
                addButton.addEventListener('click', addItem);
                input.addEventListener('keydown', (e) => { if (e.key === 'Enter') { e.preventDefault(); addItem(); } });
            }
            renderList();
        }
        setupAgendaList();


        // --- SETUP PESERTA MITRA (RENDER AWAL) ---
        function setupPartnerAttendees() {
            const inputMitra = document.getElementById('input-mitra-nama');
            const btnAddMitra = document.getElementById('btn-add-mitra');
            const listMitraContainer = document.getElementById('list-mitra-container');

            const renderMitraList = () => {
                listMitraContainer.innerHTML = '';

                dataStorage.partnerAttendees.forEach((mitra, mitraIndex) => {
                    const mitraDiv = document.createElement('div');
                    mitraDiv.className = 'p-4 border border-gray-700 rounded-lg bg-gray-900 shadow-sm space-y-3 unit-card';

                    const header = document.createElement('div');
                    header.className = 'flex items-center justify-between border-b border-gray-700 pb-2';
                    header.innerHTML = `<h3 class="text-base font-semibold text-red-400">${mitra.name}</h3>`;

                    const removeMitraBtn = document.createElement('button');
                    removeMitraBtn.type = 'button';
                    removeMitraBtn.innerHTML = '<i class="fa-solid fa-trash text-red-500 hover:text-red-700 fa-sm"></i>';
                    removeMitraBtn.onclick = () => {
                        dataStorage.partnerAttendees.splice(mitraIndex, 1);
                        renderMitraList();
                    };
                    header.appendChild(removeMitraBtn);
                    mitraDiv.appendChild(header);

                    const attendeeForm = document.createElement('div');
                    attendeeForm.className = 'flex gap-2';
                    attendeeForm.innerHTML = `
                        <input type="text" id="input-peserta-mitra-${mitraIndex}" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5" placeholder="Nama orang yang hadir">
                        <button type="button" id="btn-add-peserta-mitra-${mitraIndex}" class="px-4 py-2 text-xs font-medium text-white btn-neon-red rounded-lg flex-shrink-0">Tambah</button>
                    `;
                    mitraDiv.appendChild(attendeeForm);

                    const attendeeList = document.createElement('ul');
                    // Menggunakan list-none seperti yang terlihat pada gambar Mitra
                    attendeeList.className = 'mt-2 space-y-1 list-none text-sm text-gray-300';
                    
                    const attendeesArray = Array.isArray(mitra.attendees) ? mitra.attendees : [];

                    attendeesArray.forEach((person, personIndex) => {
                        const li = document.createElement('li');
                        li.className = 'flex items-center justify-between text-xs md:text-sm';
                        li.textContent = person;

                        const removePersonBtn = document.createElement('button');
                        removePersonBtn.type = 'button';
                        removePersonBtn.innerHTML = '<i class="fa-solid fa-times text-red-400 hover:text-red-600 fa-xs"></i>';
                        removePersonBtn.className = 'ml-4';
                        removePersonBtn.onclick = () => {
                            dataStorage.partnerAttendees[mitraIndex].attendees.splice(personIndex, 1);
                            renderMitraList();
                        };

                        li.appendChild(removePersonBtn);
                        attendeeList.appendChild(li);
                    });
                    mitraDiv.appendChild(attendeeList);
                    listMitraContainer.appendChild(mitraDiv);

                    const personInput = document.getElementById(`input-peserta-mitra-${mitraIndex}`);
                    const personAddBtn = document.getElementById(`btn-add-peserta-mitra-${mitraIndex}`);

                    const addPerson = () => {
                        const personName = personInput.value.trim();
                        if (personName === '') return;

                        if (mitra.attendees.some(n => n.toLowerCase() === personName.toLowerCase())) {
                            showToast('Peserta ini sudah ditambahkan di mitra ini!', true);
                            return;
                        }

                        dataStorage.partnerAttendees[mitraIndex].attendees.push(personName);
                        personInput.value = '';
                        renderMitraList();
                    };

                    if (personAddBtn) personAddBtn.addEventListener('click', addPerson);
                    if (personInput) personInput.addEventListener('keydown', (e) => {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            addPerson();
                        }
                    });
                });
                
                // Tampilkan pesan jika belum ada mitra
                if (dataStorage.partnerAttendees.length === 0) {
                    listMitraContainer.innerHTML = '<p class="text-sm text-gray-500">Silakan tambahkan Pihak Luar (Mitra) jika ada.</p>';
                }
            };

            const addMitra = () => {
                const mitraName = inputMitra.value.trim();
                if (mitraName === '') {
                    showToast('Nama Mitra wajib diisi!', true);
                    return;
                }

                if (dataStorage.partnerAttendees.some(mitra => mitra.name.toLowerCase() === mitraName.toLowerCase())) {
                    showToast('Nama Mitra ini sudah ada!', true);
                    return;
                }

                dataStorage.partnerAttendees.push({ name: mitraName, attendees: [] });
                inputMitra.value = '';
                inputMitra.focus();
                renderMitraList();
            };

            if (btnAddMitra) {
                btnAddMitra.addEventListener('click', addMitra);
                inputMitra.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        addMitra();
                    }
                });
            }
            renderMitraList();
        }
        setupPartnerAttendees();


        // --- FUNGSI SUBMIT UTAMA (AJAX PATCH) ---
        const submitForm = async () => {
            const formData = new FormData();

            // Tambahkan field method PATCH secara manual
            formData.append('_method', 'PATCH');
            formData.append('_token', csrfToken);

            const simpleFields = ['title', 'location', 'meeting_date', 'start_time', 'end_time', 'pimpinan_rapat', 'notulen'];
            simpleFields.forEach(name => {
                const element = document.querySelector(`[name="${name}"]`);
                if (element) {
                    formData.append(name, element.value);
                }
            });

            // --- VALIDASI DAN PEMBENTUKAN DATA ---
            const pembahasanContent = pembahasanQuill.root.innerHTML.trim();
            if (pembahasanContent.length === 0 || pembahasanContent === '<p><br></p>') {
                showToast('Pembahasan wajib diisi!', true);
                return;
            }
            formData.append('pembahasan', pembahasanContent);
            
            // Cek Peserta Internal & Tambahkan Array JSON dari dataStorage
            let totalInternalAttendees = 0;
            dataStorage.internalAttendees.forEach(unitData => {
                totalInternalAttendees += unitData.attendees.length;
            });

            if (totalInternalAttendees === 0) {
                showToast('Peserta Rapat Internal wajib diisi (minimal 1 Unit/Bagian dengan minimal 1 peserta)!', true);
                return;
            }
            
            // Tambahkan Peserta Internal (Internal Attendees) sebagai JSON string
            formData.append('internal_attendees_json', JSON.stringify(dataStorage.internalAttendees));

            if (dataStorage.agendas.length === 0) {
                 showToast('Agenda Rapat wajib diisi (minimal 1 item)!', true);
                 return;
            }
            dataStorage.agendas.forEach(item => {
                formData.append('agendas[]', item);
            });

            // Tambahkan Array Peserta Mitra (Partner Attendees) sebagai JSON string
            formData.append('partner_attendees_json', JSON.stringify(dataStorage.partnerAttendees));

            // Tambahkan ID file lama yang ditandai untuk dihapus
            if (dataStorage.filesToDelete.length > 0) {
                dataStorage.filesToDelete.forEach(id => {
                    formData.append('files_to_delete[]', id);
                });
            }

            // Tambahkan file BARU ke FormData
            if (dataStorage.filesToUpload.length > 0) {
                dataStorage.filesToUpload.forEach(file => {
                    formData.append('attachments[]', file, file.name);
                });
            }

            // KIRIM DATA
            try {
                const response = await fetch(updateUrl, {
                    method: 'POST', // Spoofing PATCH
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData,
                });

                const isJson = response.headers.get('content-type')?.includes('application/json');
                const data = isJson ? await response.json() : { message: 'Server error atau non-JSON response.', errors: {} };

                if (response.ok) {
                    showToast(data.message || 'MoM berhasil diupdate!', false);
                    setTimeout(() => {
                        window.location.href = `{{ route('draft.index', $mom->version_id) }}`; 
                    }, 1000);
                } else {
                    let errorMessage = 'Gagal menyimpan MoM.';

                    if (response.status === 422 && data.errors) {
                        const firstErrorKey = Object.keys(data.errors)[0];
                        errorMessage = `Validasi Gagal: ${data.errors[firstErrorKey][0]}`;
                    } else if (data.message) {
                        errorMessage = data.message;
                    }

                    showToast(errorMessage, true);
                }
            } catch (error) {
                console.error('Network or Parse Error:', error);
                showToast('Koneksi Gagal atau Error Server.', true);
            }
        };

        // Mengaktifkan listener tombol Submit
        btnSubmit.addEventListener('click', (e) => {
            e.preventDefault();
            submitForm();
        });

        form.addEventListener("submit", (e) => {
            e.preventDefault();
        });

        // Panggil renderFileList saat DOM selesai dimuat untuk menampilkan file lama
        renderFileList();
    });
</script>
@endpush