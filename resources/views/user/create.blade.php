@extends('layouts.app')

@section('title', 'Create MoM | MoM Telkom')

@push('styles')
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
@endpush

@section('content')
<div class="pt-16">
    {{-- Toast Notification --}}
    <div id="toast" class="hidden fixed top-24 right-5 z-50 items-center gap-3 px-4 py-3 rounded-xl shadow-lg bg-white dark:bg-dark-component-bg border border-border-light dark:border-border-dark text-text-primary dark:text-dark-text-primary transition-all duration-500 opacity-0">
        <div class="flex-shrink-0"><i class="fa-solid fa-circle-check text-green-500 text-lg"></i></div>
        <div class="text-sm font-medium">MoM berhasil di submit!</div>
    </div>

    {{-- Header --}}
    <div class="flex flex-col md:flex-row items-center justify-between p-6 md:p-8 overflow-hidden rounded-lg shadow-md bg-component-bg dark:bg-dark-component-bg border-l-4 border-primary mb-6">
        <div class="flex items-center space-x-4">
            <div>
                <h1 class="text-3xl font-bold text-text-primary dark:text-dark-text-primary">Create New MoM</h1>
                <p class="mt-1 text-text-secondary dark:text-dark-text-secondary">
                    Buat notulensi rapat baru dengan mengisi form di bawah ini.
                </p>
            </div>
        </div>
    </div>

    {{-- Form Container --}}
    <div class="bg-component-bg dark:bg-dark-component-bg p-6 md:p-8 rounded-2xl shadow-lg">
        <form id="mom-form" class="space-y-10">
            @csrf 
            
            {{-- Informasi Rapat --}}
            <div class="space-y-6">
                <h2 class="text-base font-semibold text-text-primary dark:text-dark-text-primary border-b border-border-light dark:border-border-dark pb-3">Informasi Rapat</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div><label for="judul" class="block mb-2 text-sm font-medium">Judul Rapat</label><input type="text" name="title" id="judul" class="bg-body-bg border border-border-light text-text-primary text-sm rounded-lg focus:ring-primary focus:border-primary block w-full p-2.5 dark:bg-dark-component-bg dark:border-border-dark" placeholder="Contoh: Rapat Progres Proyek Q3" required></div>
                    <div><label for="tempat" class="block mb-2 text-sm font-medium">Tempat</label><input type="text" name="location" id="tempat" class="bg-body-bg border border-border-light text-text-primary text-sm rounded-lg focus:ring-primary focus:border-primary block w-full p-2.5 dark:bg-dark-component-bg dark:border-border-dark" placeholder="Contoh: Ruang Rapat Lt. 5 / Online" required></div>
                    
                    {{-- Input manual pimpinan rapat --}}
                    <div>
                        <label for="pimpinan_rapat" class="block mb-2 text-sm font-medium">Pimpinan Rapat</label>
                        <input type="text" name="pimpinan_rapat" id="pimpinan_rapat" class="bg-body-bg border border-border-light text-text-primary text-sm rounded-lg focus:ring-primary focus:border-primary block w-full p-2.5 dark:bg-dark-component-bg dark:border-border-dark" placeholder="Nama Pimpinan" required>
                    </div>
                    
                    {{-- Input manual notulen --}}
                    <div>
                        <label for="notulen" class="block mb-2 text-sm font-medium">Notulen</label>
                        <input type="text" name="notulen" id="notulen" class="bg-body-bg border border-border-light text-text-primary text-sm rounded-lg focus:ring-primary focus:border-primary block w-full p-2.5 dark:bg-dark-component-bg dark:border-border-dark" placeholder="Nama Notulen" required>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div><label for="tanggal" class="block mb-2 text-sm font-medium">Tanggal</label><input type="date" name="meeting_date" id="tanggal" class="bg-body-bg border border-border-light text-text-primary text-sm rounded-lg focus:ring-primary focus:border-primary block w-full p-2.5 dark:bg-dark-component-bg dark:border-border-dark" required></div>
                    <div><label for="waktu_mulai" class="block mb-2 text-sm font-medium">Waktu Mulai</label><input type="time" name="start_time" id="waktu_mulai" class="bg-body-bg border border-border-light text-text-primary text-sm rounded-lg focus:ring-primary focus:border-primary block w-full p-2.5 dark:bg-dark-component-bg dark:border-border-dark" required></div>
                    <div><label for="waktu_selesai" class="block mb-2 text-sm font-medium">Waktu Selesai</label><input type="time" name="end_time" id="waktu_selesai" class="bg-body-bg border border-border-light text-text-primary text-sm rounded-lg focus:ring-primary focus:border-primary block w-full p-2.5 dark:bg-dark-component-bg dark:border-border-dark" required></div>
                </div>
            </div>

            {{-- Peserta & Agenda --}}
            <div class="space-y-6">
                <h2 class="text-base font-semibold text-text-primary dark:text-dark-text-primary border-b border-border-light dark:border-border-dark pb-3">Peserta & Agenda</h2>
                
                {{-- Peserta rapat --}}
                <div class="grid grid-cols-1 gap-8">
                    <div>
                        <label class="block mb-2 text-sm font-medium">Peserta Rapat (Per Unit/Bagian)</label>
                        <div class="flex flex-col md:flex-row gap-4 items-end">
                            <div class="w-full">
                                <input type="text" id="input-internal-unit" class="bg-body-bg border border-border-light text-text-primary text-sm rounded-lg focus:ring-primary focus:border-primary block w-full p-2.5 dark:bg-dark-component-bg dark:border-border-dark" placeholder="Nama Unit/Bagian (Contoh: Unit HC)">
                            </div>
                            <button type="button" id="btn-add-internal-unit" class="px-4 py-3 text-sm font-medium text-white bg-gradient-primary rounded-lg hover:opacity-90 w-full md:w-auto flex-shrink-0">Tambah Unit</button>
                        </div>
                        
                        <div id="list-internal-attendees-container" class="space-y-4 mt-4">
                        </div>
                        <p class="mt-1 text-xs text-text-secondary">Tambah Unit, lalu masukkan nama-nama peserta dari Unit tersebut.</p>
                    </div>
                </div>
                
                {{-- Agenda (Add Button + JS List) --}}
                <div class="grid grid-cols-1 gap-8 pt-6 border-t border-border-light dark:border-border-dark">
                    <div>
                        <label class="block mb-2 text-sm font-medium">Agenda</label>
                        <div class="flex gap-2">
                            <input type="text" id="input-agenda" class="bg-body-bg border border-border-light text-text-primary text-sm rounded-lg focus:ring-primary focus:border-primary block w-full px-3 py-3 dark:bg-dark-component-bg dark:border-border-dark" placeholder="Ketik agenda lalu Enter...">
                            
                            <button type="button" id="btn-add-agenda" class="px-4 py-3 text-sm font-medium text-white bg-gradient-primary rounded-lg hover:opacity-90">Add</button>
                        </div>
                        <ol id="list-agenda" class="mt-3 space-y-2 list-decimal list-inside text-sm"></ol>
                        <p class="mt-1 text-xs text-text-secondary">Minimal 1 item Agenda (Wajib).</p>
                    </div>
                </div>
            </div>

            {{-- Pihak Luar (Mitra) --}}
            <div class="space-y-6">
                <h2 class="text-base font-semibold text-text-primary dark:text-dark-text-primary border-b border-border-light dark:border-border-dark pb-3">Pihak Luar (Yang Akan Menandatangani MoM)</h2>
                
                {{-- Form untuk menambah Mitra Baru --}}
                <div class="flex flex-col md:flex-row gap-4 items-end">
                    <div class="w-full">
                        <label for="input-mitra-nama" class="block mb-2 text-sm font-medium">Nama Mitra/Instansi (Contoh: PT TIF)</label>
                        <input type="text" id="input-mitra-nama" class="bg-body-bg border border-border-light text-text-primary text-sm rounded-lg focus:ring-primary focus:border-primary block w-full p-2.5 dark:bg-dark-component-bg dark:border-border-dark" placeholder="Ketik nama Mitra">
                    </div>
                    <button type="button" id="btn-add-mitra" class="px-4 py-3 text-sm font-medium text-white bg-gradient-primary rounded-lg hover:opacity-90 w-full md:w-auto flex-shrink-0">Tambah Pihak Luar</button>
                </div>

                {{-- Container untuk daftar Mitra yang sudah ditambahkan --}}
                <div id="list-mitra-container" class="space-y-4">
                </div>
                <p class="mt-1 text-xs text-text-secondary">Tambah Mitra dan masukkan nama-nama orang yang hadir di bawahnya.</p>
            </div>

            {{-- Pembahasan --}}
            <div>
                <h2 class="text-base font-semibold text-text-primary dark:text-dark-text-primary border-b border-border-light dark:border-border-dark pb-3 mb-6">Pembahasan</h2>
                <div id="pembahasan-editor"></div>
                <input type="hidden" name="pembahasan" id="pembahasan-hidden"> {{-- Hidden input diisi oleh JS --}}
            </div>

            {{-- Lampiran --}}
            <div>
                <h2 class="text-base font-semibold text-text-primary dark:text-dark-text-primary border-b border-border-light dark:border-border-dark pb-3 mb-6">Lampiran</h2>
                {{-- Input file dengan ID 'lampiran-input' agar mudah diolah JS --}}
                <input class="block w-full text-sm text-text-primary border border-border-light rounded-lg cursor-pointer bg-body-bg dark:text-dark-component-bg dark:border-border-dark" id="lampiran-input" name="attachments[]" type="file" multiple>
                <p class="mt-1 text-xs text-text-secondary">PNG, JPG, PDF, DOCX (MAX. 10MB). Dapat mengunggah lebih dari 1 file.</p>
                
                {{-- LIST FILE YANG DIPILIH --}}
                <div id="file-list" class="mt-3 space-y-1 text-sm text-text-secondary dark:text-dark-text-secondary">
                    {{-- File names will be listed here by JS --}}
                </div>
            </div>

            {{-- Submit Button --}}
            <div class="flex justify-end gap-4 pt-6 border-t border-border-light dark:border-border-dark">
                <button type="button" id="btn-submit" class="text-white bg-gradient-primary hover:opacity-90 font-medium rounded-lg text-sm px-8 py-2.5 text-center">Submit MoM</button>
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
        const messageContainer = toast.querySelector('div.text-sm.font-medium');
        
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

        if (!form || !btnSubmit || !fileInput) {
            console.error("Elemen form, submit button, atau file input tidak ditemukan.");
            return;
        }
        
        // --- SETUP DATA GLOBAL ---
        const dataStorage = {
            internalAttendees: [], 
            agendas: [], 
            partnerAttendees: [], 
            filesToUpload: []
        };

        // --- FUNGSI MERENDER DAFTAR FILE ---
        const renderFileList = () => {
            const fileListContainer = document.getElementById('file-list');
            fileListContainer.innerHTML = '';
            const files = dataStorage.filesToUpload;

            if (files.length > 0) {
                const ul = document.createElement('ul');
                ul.className = 'list-none space-y-2'; 
                
                files.forEach((file, index) => {
                    const li = document.createElement('li');
                    li.className = 'flex items-center justify-between p-2 rounded-lg bg-gray-100 dark:bg-gray-700';

                    const fileInfo = document.createElement('span');
                    fileInfo.className = 'flex items-center text-sm font-medium truncate';
                    fileInfo.innerHTML = `<i class="fa-solid fa-file mr-2 text-primary"></i> <span>${file.name}</span> <span class="ml-2 text-xs text-text-secondary dark:text-dark-text-secondary">(${(file.size / 1024 / 1024).toFixed(2)} MB)</span>`;

                    // Tombol Hapus 
                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.innerHTML = '<i class="fa-solid fa-xmark text-red-500 hover:text-red-700"></i>';
                    removeBtn.title = 'Hapus file ini dari daftar upload';
                    
                    removeBtn.onclick = () => {
                        dataStorage.filesToUpload.splice(index, 1);
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

        // --- EVENT LISTENER UNTUK FILE INPUT (Menggabungkan file) ---
        fileInput.addEventListener('change', (event) => {
            const newFiles = Array.from(event.target.files);
            
            if (newFiles.length > 0) {
                newFiles.forEach(file => {
                    // Cek duplikasi (berdasarkan nama dan ukuran)
                    if (!dataStorage.filesToUpload.some(existingFile => existingFile.name === file.name && existingFile.size === file.size)) {
                        dataStorage.filesToUpload.push(file);
                    }
                });
            }

            // Reset input file
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
                    unitDiv.className = 'p-4 border border-border-light dark:border-border-dark rounded-lg bg-body-bg dark:bg-dark-component-bg shadow-sm space-y-3';
                    
                    // Header Unit
                    const header = document.createElement('div');
                    header.className = 'flex items-center justify-between border-b border-border-light dark:border-border-dark pb-2';
                    header.innerHTML = `<h3 class="text-base font-semibold text-primary">${unitData.unit}</h3>`;
                    
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
                        <input type="text" id="input-peserta-internal-${unitIndex}" class="bg-white border border-border-light text-text-primary text-sm rounded-lg focus:ring-primary focus:border-primary block w-full p-2.5 dark:bg-dark-body-bg dark:border-border-dark" placeholder="Nama orang yang hadir">
                        <button type="button" id="btn-add-peserta-internal-${unitIndex}" class="px-4 py-2 text-xs font-medium text-primary border border-primary rounded-lg hover:bg-primary/10 flex-shrink-0">Tambah</button>
                    `;
                    unitDiv.appendChild(attendeeForm);
                    
                    // List Peserta Unit
                    const attendeeList = document.createElement('ul');
                    attendeeList.className = 'mt-2 space-y-1 list-disc list-inside text-sm text-text-secondary dark:text-dark-text-secondary';
                    unitData.attendees.forEach((person, personIndex) => {
                        const li = document.createElement('li');
                        li.className = 'flex items-center justify-between';
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
                        
                        // Cek duplikasi di unit yang sama
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
                    listInternalContainer.innerHTML = '<p class="text-sm text-text-secondary dark:text-dark-text-secondary">Silakan tambahkan Unit/Bagian yang hadir.</p>';
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
            
            // Panggil saat inisialisasi
            renderInternalList();
        }
        setupInternalAttendees();


        // --- Setup Agenda (List) ---
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
        }
        setupAgendaList();
        
        
        // --- Setup Peserta dari Mitra (Dynamic Input) ---
        function setupPartnerAttendees() {
            const inputMitra = document.getElementById('input-mitra-nama');
            const btnAddMitra = document.getElementById('btn-add-mitra');
            const listMitraContainer = document.getElementById('list-mitra-container');

            const renderMitraList = () => {
                listMitraContainer.innerHTML = '';
                
                dataStorage.partnerAttendees.forEach((mitra, mitraIndex) => {
                    const mitraDiv = document.createElement('div');
                    mitraDiv.className = 'p-4 border border-border-light dark:border-border-dark rounded-lg bg-body-bg dark:bg-dark-component-bg shadow-sm space-y-3';
                    
                    // Header Mitra
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

                    // Form Input Peserta untuk Mitra ini
                    const attendeeForm = document.createElement('div');
                    attendeeForm.className = 'flex gap-2';
                    attendeeForm.innerHTML = `
                        <input type="text" id="input-peserta-mitra-${mitraIndex}" class="bg-white border border-border-light text-text-primary text-sm rounded-lg focus:ring-primary focus:border-primary block w-full p-2.5 dark:bg-dark-body-bg dark:border-border-dark" placeholder="Nama orang yang hadir">
                        <button type="button" id="btn-add-peserta-mitra-${mitraIndex}" class="px-4 py-2 text-xs font-medium text-primary border border-primary rounded-lg hover:bg-primary/10 flex-shrink-0">Tambah</button>
                    `;
                    mitraDiv.appendChild(attendeeForm);
                    
                    // List Peserta Mitra
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
                    
                    // Tambahkan Listener untuk tombol Tambah Peserta
                    const personInput = document.getElementById(`input-peserta-mitra-${mitraIndex}`);
                    const personAddBtn = document.getElementById(`btn-add-peserta-mitra-${mitraIndex}`);
                    
                    const addPerson = () => {
                        const personName = personInput.value.trim();
                        if (personName === '') return;
                        
                        // Cek duplikasi di mitra yang sama
                        if (mitra.attendees.some(n => n.toLowerCase() === personName.toLowerCase())) {
                            showToast('Peserta ini sudah ditambahkan di mitra ini!', true);
                            return;
                        }

                        dataStorage.partnerAttendees[mitraIndex].attendees.push(personName);
                        personInput.value = '';
                        renderMitraList();
                    };

                    personAddBtn.addEventListener('click', addPerson);
                    personInput.addEventListener('keydown', (e) => { 
                        if (e.key === 'Enter') { 
                            e.preventDefault(); 
                            addPerson(); 
                        } 
                    });
                });

                // Tampilkan pesan jika belum ada mitra
                if (dataStorage.partnerAttendees.length === 0) {
                    listMitraContainer.innerHTML = '<p class="text-sm text-text-secondary dark:text-dark-text-secondary">Silakan tambahkan Pihak Luar (Mitra) jika ada.</p>';
                }
            };

            // Tambahkan Mitra Baru
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


        // --- Inisialisasi Quill JS ---
        const pembahasanQuill = new Quill('#pembahasan-editor', { theme: 'snow', placeholder: "Tuliskan hasil pembahasan, keputusan, dan poin penting lainnya...", modules: { toolbar: [[{ 'header': [1, 2, false] }], ['bold', 'italic', 'underline'], [{ 'list': 'ordered' }, { 'list': 'bullet' }], ['link'], ['clean']] } });
        

        // --- FUNGSI SUBMIT UTAMA (AJAX) ---
        const submitForm = async () => {
            const formData = new FormData(); 
            
            // Mengambil field non-admin
            const simpleFields = ['title', 'location', 'meeting_date', 'start_time', 'end_time', 'pimpinan_rapat', 'notulen']; 
            simpleFields.forEach(name => {
                const element = document.querySelector(`[name="${name}"]`);
                if (element) {
                    formData.append(name, element.value);
                }
            });
            formData.append('_token', '{{ csrf_token() }}');

            // Validasi Input Pimpinan dan Notulen Manual
            if (!document.getElementById('pimpinan_rapat').value.trim() || !document.getElementById('notulen').value.trim()) {
                showToast('Nama Pimpinan Rapat dan Notulen wajib diisi!', true);
                return;
            }

            // Ambil Konten Pembahasan (dari Quill)
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

            // Tambahkan Array Agenda
            if (dataStorage.agendas.length === 0) {
                showToast('Agenda Rapat wajib diisi (minimal 1 item)!', true);
                return;
            }
            dataStorage.agendas.forEach(item => {
                formData.append('agendas[]', item);
            });
            
            // Tambahkan Array Peserta Mitra (Partner Attendees) sebagai JSON string
            formData.append('partner_attendees_json', JSON.stringify(dataStorage.partnerAttendees));

            // Tambahkan file-file dari dataStorage ke FormData
            if (dataStorage.filesToUpload.length > 0) {
                dataStorage.filesToUpload.forEach(file => {
                    formData.append('attachments[]', file, file.name);
                });
            }

            
            // Kirim data ke backend
            
            try {
                const response = await fetch('{{ route('moms.store') }}', { 
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken, 
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData, 
                });

                const isJson = response.headers.get('content-type')?.includes('application/json');
                const data = isJson ? await response.json() : { message: 'Server error or non-JSON response.', errors: {} };

                if (response.ok) {
                    showToast('MoM berhasil di submit!', false);
                    
                    // --- REDIRECT KE user/draft ---
                    setTimeout(() => {
                        window.location.href = "{{ route('draft.index') }}"; 
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
        
        // Nonaktifkan submit form default agar tidak terjadi reload
        form.addEventListener("submit", (e) => { 
            e.preventDefault(); 
        }); 
        
        // Panggil renderFileList saat DOM selesai dimuat (untuk inisialisasi teks)
        renderFileList(); 
    });
</script>
@endpush