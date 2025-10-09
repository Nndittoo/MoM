@extends('layouts.app')

@section('title', 'Edit MoM | MoM Telkom')

@push('styles')
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
@endpush

@section('content')

{{-- Inisialisasi Data dari PHP ke JavaScript --}}
@php
    // Data JSON untuk Peserta dan Agenda (diambil dari Model dengan Casting)
    $manualAttendeesJs = json_encode($mom->nama_peserta ?? []);
    $partnerAttendeesJs = json_encode($mom->nama_mitra ?? []);
    $agendasJs = json_encode($mom->agendas->pluck('item')->toArray() ?? []);
    
    // Data Attachment Lama
    $oldAttachmentsJs = json_encode($mom->attachments ?? []);
@endphp

<div class="pt-16">
    {{-- Toast Notification --}}
    <div id="toast" class="hidden fixed top-24 right-5 z-50 items-center gap-3 px-4 py-3 rounded-xl shadow-lg bg-white dark:bg-dark-component-bg border border-border-light dark:border-border-dark text-text-primary dark:text-dark-text-primary transition-all duration-500 opacity-0">
        <div class="flex-shrink-0"><i class="fa-solid fa-circle-check text-green-500 text-lg"></i></div>
        <div class="text-sm font-medium" id="toast-message">Notifikasi</div>
    </div>

    {{-- Header --}}
    <div class="flex flex-col md:flex-row items-center justify-between p-6 md:p-8 overflow-hidden rounded-lg shadow-md bg-component-bg dark:bg-dark-component-bg border-l-4 border-primary mb-6">
        <div class="flex items-center space-x-4">
            <div>
                <h1 class="text-3xl font-bold text-text-primary dark:text-dark-text-primary">Edit MoM: {{ $mom->title }}</h1>
                <p class="mt-1 text-text-secondary dark:text-dark-text-secondary">
                    Ubah dan perbarui notulensi rapat ini. Status saat ini: {{ $mom->status->status ?? 'N/A' }}
                    @if($mom->status_id == 3 && $mom->rejection_comment)
                        <span class="text-red-500 font-medium block mt-1">
                            Alasan Revisi: {{ $mom->rejection_comment }}
                        </span>
                    @endif
                </p>
            </div>
        </div>
    </div>

    {{-- Form Container --}}
    <div class="bg-component-bg dark:bg-dark-component-bg p-6 md:p-8 rounded-2xl shadow-lg">
        <form id="mom-form" class="space-y-10">
            @csrf 
            {{-- Method field diperlukan untuk Laravel meniru PATCH --}}
            @method('PATCH')

            {{-- Informasi Rapat --}}
            <div class="space-y-6">
                <h2 class="text-base font-semibold text-text-primary dark:text-dark-text-primary border-b border-border-light dark:border-border-dark pb-3">Informasi Rapat</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div><label for="judul" class="block mb-2 text-sm font-medium">Judul Rapat</label><input type="text" name="title" id="judul" value="{{ $mom->title ?? '' }}" class="bg-body-bg border border-border-light text-text-primary text-sm rounded-lg focus:ring-primary focus:border-primary block w-full p-2.5 dark:bg-dark-component-bg dark:border-border-dark" placeholder="Contoh: Rapat Progres Proyek Q3" required></div>
                    <div><label for="tempat" class="block mb-2 text-sm font-medium">Tempat</label><input type="text" name="location" id="tempat" value="{{ $mom->location ?? '' }}" class="bg-body-bg border border-border-light text-text-primary text-sm rounded-lg focus:ring-primary focus:border-primary block w-full p-2.5 dark:bg-dark-component-bg dark:border-border-dark" placeholder="Contoh: Ruang Rapat Lt. 5 / Online" required></div>

                    <div>
                        <label for="pimpinan_rapat" class="block mb-2 text-sm font-medium">Pimpinan Rapat</label>
                        <input type="text" name="pimpinan_rapat" id="pimpinan_rapat" value="{{ $mom->pimpinan_rapat ?? '' }}" class="bg-body-bg border border-border-light text-text-primary text-sm rounded-lg focus:ring-primary focus:border-primary block w-full p-2.5 dark:bg-dark-component-bg dark:border-border-dark" placeholder="Nama Pimpinan" required>
                    </div>

                    <div>
                        <label for="notulen" class="block mb-2 text-sm font-medium">Notulen</label>
                        <input type="text" name="notulen" id="notulen" value="{{ $mom->notulen ?? '' }}" class="bg-body-bg border border-border-light text-text-primary text-sm rounded-lg focus:ring-primary focus:border-primary block w-full p-2.5 dark:bg-dark-component-bg dark:border-border-dark" placeholder="Nama Notulen" required>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div><label for="tanggal" class="block mb-2 text-sm font-medium">Tanggal</label><input type="date" name="meeting_date" id="tanggal" value="{{ $mom->meeting_date->format('Y-m-d') ?? '' }}" class="bg-body-bg border border-border-light text-text-primary text-sm rounded-lg focus:ring-primary focus:border-primary block w-full p-2.5 dark:bg-dark-component-bg dark:border-border-dark" required></div>
                    <div><label for="waktu_mulai" class="block mb-2 text-sm font-medium">Waktu Mulai</label><input type="time" name="start_time" id="waktu_mulai" value="{{ $mom->start_time ?? '' }}" class="bg-body-bg border border-border-light text-text-primary text-sm rounded-lg focus:ring-primary focus:border-primary block w-full p-2.5 dark:bg-dark-component-bg dark:border-border-dark" required></div>
                    <div><label for="waktu_selesai" class="block mb-2 text-sm font-medium">Waktu Selesai</label><input type="time" name="end_time" id="waktu_selesai" value="{{ $mom->end_time ?? '' }}" class="bg-body-bg border border-border-light text-text-primary text-sm rounded-lg focus:ring-primary focus:border-primary block w-full p-2.5 dark:bg-dark-component-bg dark:border-border-dark" required></div>
                </div>
            </div>

            {{-- Peserta & Agenda --}}
            <div class="space-y-6">
                <h2 class="text-base font-semibold text-text-primary dark:text-dark-text-primary border-b border-border-light dark:border-border-dark pb-3">Peserta & Agenda</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                    {{-- PESERTA INTERNAL --}}
                    <div>
                        <label class="block mb-2 text-sm font-medium">Peserta Rapat</label>
                        <div class="flex gap-2">
                            <input type="text" id="input-peserta-manual" class="bg-body-bg border border-border-light text-text-primary text-sm rounded-lg focus:ring-primary focus:border-primary block w-full p-2.5 dark:bg-dark-component-bg dark:border-border-dark" placeholder="Nama Peserta">
                            <button type="button" id="btn-add-peserta-manual" class="px-4 py-2 text-sm font-medium text-white bg-gradient-primary rounded-lg hover:opacity-90">Add</button>
                        </div>
                        <div id="list-peserta-manual" class="flex flex-wrap gap-2 mt-3"></div>
                        <p class="mt-1 text-xs text-text-secondary">Ketik nama peserta satu per satu.</p>
                    </div>

                    {{-- AGENDA --}}
                    <div>
                        <label class="block mb-2 text-sm font-medium">Agenda</label>
                        <div class="flex gap-2">
                            <input type="text" id="input-agenda" class="bg-body-bg border border-border-light text-text-primary text-sm rounded-lg focus:ring-primary focus:border-primary block w-full p-2.5 dark:bg-dark-component-bg dark:border-border-dark" placeholder="Ketik agenda lalu Enter...">
                            <button type="button" id="btn-add-agenda" class="px-4 py-2 text-sm font-medium text-white bg-gradient-primary rounded-lg hover:opacity-90">Add</button>
                        </div>
                        <ol id="list-agenda" class="mt-3 space-y-2 list-decimal list-inside text-sm"></ol>
                        <p class="mt-1 text-xs text-text-secondary">Minimal 1 item Agenda (Wajib).</p>
                    </div>
                </div>
            </div>

            {{-- Peserta dari Mitra --}}
            <div class="space-y-6">
                <h2 class="text-base font-semibold text-text-primary dark:text-dark-text-primary border-b border-border-light dark:border-border-dark pb-3">Pihak yang Akan Menandatangani MoM</h2>

                <div class="flex flex-col md:flex-row gap-4 items-end">
                    <div class="w-full">
                        <label for="input-mitra-nama" class="block mb-2 text-sm font-medium">Nama Mitra (Contoh: PT TIF)</label>
                        <input type="text" id="input-mitra-nama" class="bg-body-bg border border-border-light text-text-primary text-sm rounded-lg focus:ring-primary focus:border-primary block w-full p-2.5 dark:bg-dark-component-bg dark:border-border-dark" placeholder="Ketik nama Mitra">
                    </div>
                    <button type="button" id="btn-add-mitra" class="px-4 py-3 text-sm font-medium text-white bg-gradient-primary rounded-lg hover:opacity-90 w-full md:w-auto flex-shrink-0">Tambah Mitra</button>
                </div>

                <div id="list-mitra-container" class="space-y-4"></div>
                <p class="mt-1 text-xs text-text-secondary">Tambah Mitra dan masukkan nama-nama orang yang hadir di bawahnya.</p>
            </div>

            {{-- Pembahasan --}}
            <div>
                <h2 class="text-base font-semibold text-text-primary dark:text-dark-text-primary border-b border-border-light dark:border-border-dark pb-3 mb-6">Pembahasan</h2>
                <div id="pembahasan-editor">
                    {!! $mom->pembahasan ?? '' !!}
                </div>
                <input type="hidden" name="pembahasan" id="pembahasan-hidden">
            </div>

            {{-- Lampiran --}}
            <div>
                <h2 class="text-base font-semibold text-text-primary dark:text-dark-text-primary border-b border-border-light dark:border-border-dark pb-3 mb-6">Lampiran</h2>
                <input class="block w-full text-sm text-text-primary border border-border-light rounded-lg cursor-pointer bg-body-bg dark:text-dark-text-secondary focus:outline-none dark:bg-dark-component-bg dark:border-border-dark" id="lampiran-input" name="attachments[]" type="file" multiple>
                <p class="mt-1 text-xs text-text-secondary">PNG, JPG, PDF, DOCX (MAX. 10MB). File lama yang dihapus di bawah ini akan dihapus permanen saat update.</p>

                {{-- LIST FILE LAMA DAN BARU --}}
                <div id="file-list" class="mt-3 space-y-1 text-sm text-text-secondary dark:text-dark-text-secondary"></div>
            </div>

            {{-- Submit Button (Diubah menjadi Update MoM) --}}
            <div class="flex justify-end gap-4 pt-6 border-t border-border-light dark:border-border-dark">
                <button type="button" id="btn-submit" class="text-white bg-gradient-primary hover:opacity-90 font-medium rounded-lg text-sm px-8 py-2.5 text-center">Update MoM</button>
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
        const icon = toast.querySelector('i');
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

        // URL UPDATE (menggunakan ID MoM saat ini)
        const momId = '{{ $mom->version_id }}';
        const updateUrl = `/moms/${momId}`; 

        // --- SETUP DATA GLOBAL DENGAN DATA LAMA ---
        const dataStorage = {
            manualAttendees: JSON.parse('{!! $manualAttendeesJs !!}'), 
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
            
            // Filter file lama: hanya tampilkan yang TIDAK ada di filesToDelete
            const oldFilesToDisplay = dataStorage.oldFiles.filter(
                f => !dataStorage.filesToDelete.includes(f.attachment_id)
            );
            
            const allFiles = [
                ...oldFilesToDisplay.map(f => ({ 
                    name: f.file_name, 
                    size: f.file_size, 
                    type: 'old', 
                    id: f.attachment_id 
                })),
                ...dataStorage.filesToUpload.map(f => ({ 
                    name: f.name, 
                    size: f.size, 
                    type: 'new', 
                    fileObj: f 
                }))
            ];
            
            if (allFiles.length > 0) {
                const ul = document.createElement('ul');
                ul.className = 'list-none space-y-2';

                allFiles.forEach((file, index) => {
                    const li = document.createElement('li');
                    
                    li.className = `flex items-center justify-between p-2 rounded-lg ${file.type === 'old' ? 'bg-yellow-50 dark:bg-gray-800' : 'bg-gray-100 dark:bg-gray-700'}`;

                    const fileInfo = document.createElement('span');
                    fileInfo.className = 'flex items-center text-sm font-medium truncate';
                    const iconColor = file.type === 'old' ? 'text-yellow-600' : 'text-primary';
                    const statusText = file.type === 'old' ? '(Lama)' : '(Baru)';

                    fileInfo.innerHTML = `<i class="fa-solid fa-file mr-2 ${iconColor}"></i> <span>${file.name}</span> <span class="ml-2 text-xs text-text-secondary dark:text-dark-text-secondary">(${(file.size / 1024 / 1024).toFixed(2)} MB) ${statusText}</span>`;

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

        // --- SETUP PESERTA INTERNAL (RENDER AWAL) ---
        function setupManualParticipantPills() {
            const input = document.getElementById('input-peserta-manual');
            const addButton = document.getElementById('btn-add-peserta-manual');
            const listContainer = document.getElementById('list-peserta-manual');

            const renderList = () => {
                listContainer.innerHTML = '';
                dataStorage.manualAttendees.forEach((name, index) => {
                    const pill = document.createElement('span');
                    pill.className = 'inline-flex items-center gap-x-2 bg-primary/20 text-primary text-sm font-medium px-3 py-1.5 rounded-full dark:bg-primary/30 dark:text-primary';

                    pill.textContent = name;

                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.innerHTML = '<i class="fa-solid fa-times w-3 h-3"></i>';
                    removeBtn.onclick = () => {
                        dataStorage.manualAttendees.splice(index, 1);
                        renderList();
                    };

                    pill.appendChild(removeBtn);
                    listContainer.appendChild(pill);
                });
            };

            const addItem = () => {
                const name = input.value.trim();
                if (!name) return;

                if (dataStorage.manualAttendees.some(n => n.toLowerCase() === name.toLowerCase())) {
                    showToast('Peserta ini sudah ditambahkan!', true);
                    return;
                }

                dataStorage.manualAttendees.push(name);
                input.value = '';
                input.focus();
                renderList();
            };

            if (addButton) {
                addButton.addEventListener('click', addItem);
                input.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        addItem();
                    }
                });
            }
            renderList();
        }
        setupManualParticipantPills();


        // --- SETUP AGENDA (RENDER AWAL) ---
        function setupAgendaList() {
            const input = document.getElementById('input-agenda');
            const addButton = document.getElementById('btn-add-agenda');
            const listContainer = document.getElementById('list-agenda');

            const renderList = () => {
                listContainer.innerHTML = '';
                dataStorage.agendas.forEach((item, index) => {
                    const listItem = document.createElement('li');
                    listItem.className = 'flex items-center justify-between text-text-secondary dark:text-dark-text-secondary';
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
                    mitraDiv.className = 'p-4 border border-border-light dark:border-border-dark rounded-lg bg-body-bg dark:bg-dark-component-bg shadow-sm space-y-3';

                    const header = document.createElement('div');
                    header.className = 'flex items-center justify-between border-b border-border-light dark:border-border-dark pb-2';
                    header.innerHTML = `<h3 class="text-base font-semibold text-primary">${mitra.name}</h3>`;

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
                        <input type="text" id="input-peserta-mitra-${mitraIndex}" class="bg-white border border-border-light text-text-primary text-sm rounded-lg focus:ring-primary focus:border-primary block w-full p-2.5 dark:bg-dark-body-bg dark:border-border-dark" placeholder="Nama orang yang hadir">
                        <button type="button" id="btn-add-peserta-mitra-${mitraIndex}" class="px-4 py-2 text-xs font-medium text-white bg-primary rounded-lg hover:opacity-90 flex-shrink-0">Tambah</button>
                    `;
                    mitraDiv.appendChild(attendeeForm);

                    const attendeeList = document.createElement('ul');
                    attendeeList.className = 'mt-2 space-y-1 list-disc list-inside text-sm text-text-secondary dark:text-dark-text-secondary';
                    mitra.attendees.forEach((person, personIndex) => {
                        const li = document.createElement('li');
                        li.className = 'flex items-center justify-between';
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
            formData.append('_token', '{{ csrf_token() }}');
            
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

            if (dataStorage.manualAttendees.length === 0) {
                 showToast('Peserta Rapat Internal wajib diisi (minimal 1 peserta)!', true);
                 return;
            }
            dataStorage.manualAttendees.forEach(name => {
                formData.append('attendees_manual[]', name);
            });

            if (dataStorage.agendas.length === 0) {
                 showToast('Agenda Rapat wajib diisi (minimal 1 item)!', true);
                 return;
            }
            dataStorage.agendas.forEach(item => {
                formData.append('agendas[]', item);
            });

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
                // FIX SINTAKSIS: Pastikan fetch() dan await terstruktur dengan benar
                const response = await fetch(updateUrl, {
                    method: 'POST', // Spoofing PATCH
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: formData,
                });

                const isJson = response.headers.get('content-type')?.includes('application/json');
                const data = isJson ? await response.json() : { message: 'Server error atau non-JSON response.', errors: {} };

                if (response.ok) {
                    showToast(data.message || 'MoM berhasil diupdate!', false);
                    setTimeout(() => {
                        window.location.href = `/moms/${momId}/edit`; // Refresh halaman edit setelah update
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