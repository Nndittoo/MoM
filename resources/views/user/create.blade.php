@extends('layouts.app')

@section('title', 'Create MoM | MoM Telkom')

@push('styles')
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    
@endpush

@section('content')
<div class="pt-16">
    {{-- Toast Notification --}}
    <div id="toast" class="hidden fixed top-24 right-5 z-50 items-center gap-3 px-4 py-3 rounded-xl shadow-lg bg-white dark:bg-dark-component-bg border border-border-light dark:border-border-dark text-text-primary dark:text-dark-text-primary transition-all duration-500 opacity-0">
        <div class="flex-shrink-0"><i class="fa-solid fa-circle-check text-green-500 text-lg"></i></div>
        <div class="text-sm font-medium">MoM berhasil disubmit!</div>
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
            @csrf {{-- Pastikan CSRF token disertakan --}}

            {{-- Informasi Rapat --}}
            <div class="space-y-6">
                <h2 class="text-base font-semibold text-text-primary dark:text-dark-text-primary border-b border-border-light dark:border-border-dark pb-3">Informasi Rapat</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div><label for="judul" class="block mb-2 text-sm font-medium">Judul Rapat</label><input type="text" name="title" id="judul" class="bg-body-bg border border-border-light text-text-primary text-sm rounded-lg focus:ring-primary focus:border-primary block w-full p-2.5 dark:bg-dark-component-bg dark:border-border-dark" placeholder="Contoh: Rapat Progres Proyek Q3" required></div>
                    <div><label for="tempat" class="block mb-2 text-sm font-medium">Tempat</label><input type="text" name="location" id="tempat" class="bg-body-bg border border-border-light text-text-primary text-sm rounded-lg focus:ring-primary focus:border-primary block w-full p-2.5 dark:bg-dark-component-bg dark:border-border-dark" placeholder="Contoh: Ruang Rapat Lt. 5 / Online" required></div>
                    
                    {{-- DROPDOWN PIMPINAN (Leader ID) --}}
                    <div>
                        <label for="leader_id" class="block mb-2 text-sm font-medium">Pimpinan Rapat</label>
                        <select name="leader_id" id="leader_id" class="bg-body-bg border border-border-light text-text-primary text-sm rounded-lg focus:ring-primary focus:border-primary block w-full p-2.5 dark:bg-dark-component-bg dark:border-border-dark" required>
                            <option value="">-- Pilih Pimpinan --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} (ID: {{ $user->id }})</option>
                            @endforeach
                        </select>
                    </div>
                    
                    {{-- DROPDOWN NOTULEN (Notulen ID) --}}
                    <div>
                        <label for="notulen_id" class="block mb-2 text-sm font-medium">Notulen</label>
                        <select name="notulen_id" id="notulen_id" class="bg-body-bg border border-border-light text-text-primary text-sm rounded-lg focus:ring-primary focus:border-primary block w-full p-2.5 dark:bg-dark-component-bg dark:border-border-dark" required>
                            <option value="">-- Pilih Notulen --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} (ID: {{ $user->id }})</option>
                            @endforeach
                        </select>
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
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    
                    {{-- DROPDOWN INTERAKTIF PESERTA --}}
                    <div>
                        <label class="block mb-2 text-sm font-medium">Peserta Rapat</label>
                        <div class="flex gap-2">
                            <select id="select-peserta" class="bg-body-bg border border-border-light text-text-primary text-sm rounded-lg focus:ring-primary focus:border-primary block w-full p-2.5 dark:bg-dark-component-bg dark:border-border-dark">
                                <option value="">-- Pilih Peserta --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" data-name="{{ $user->name }}">{{ $user->name }} (ID: {{ $user->id }})</option>
                                @endforeach
                            </select>
                            <button type="button" id="btn-add-peserta" class="px-4 py-2 text-sm font-medium text-white bg-gradient-primary rounded-lg hover:opacity-90">Add</button>
                        </div>
                        <div id="list-peserta" class="flex flex-wrap gap-2 mt-3"></div>
                        <p class="mt-1 text-xs text-text-secondary">Pilih minimal satu peserta (Wajib).</p>
                    </div>

                    {{-- AGENDA (Add Button + JS List) --}}
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

            {{-- Pembahasan --}}
            <div>
                <h2 class="text-base font-semibold text-text-primary dark:text-dark-text-primary border-b border-border-light dark:border-border-dark pb-3 mb-6">Pembahasan</h2>
                <div id="pembahasan-editor"></div>
                <input type="hidden" name="pembahasan" id="pembahasan-hidden"> {{-- Hidden input diisi oleh JS --}}
            </div>

            {{-- Tindak Lanjut (Action Items) - Dihilangkan dari HTML --}}
            

            {{-- Lampiran --}}
            <div>
                <h2 class="text-base font-semibold text-text-primary dark:text-dark-text-primary border-b border-border-light dark:border-border-dark pb-3 mb-6">Lampiran</h2>
                <input class="block w-full text-sm text-text-primary border border-border-light rounded-lg cursor-pointer bg-body-bg dark:text-dark-text-secondary focus:outline-none dark:bg-dark-component-bg dark:border-border-dark" id="lampiran" name="attachments[]" type="file" multiple>
                <p class="mt-1 text-xs text-text-secondary">PNG, JPG, PDF, DOCX (MAX. 5MB per file).</p>
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
        
        // Cek jika meta tag CSRF tidak ada (diperlukan untuk Fetch API)
        const csrfTokenElement = document.querySelector('meta[name="csrf-token"]');
        const csrfToken = csrfTokenElement ? csrfTokenElement.getAttribute('content') : '';

        if (!form || !btnSubmit) {
            console.error("Elemen form atau submit button tidak ditemukan.");
            return;
        }
        
        // --- SETUP DATA GLOBAL (Action Items dihilangkan) ---
        const dataStorage = {
            pesertaIDs: [], 
            agendas: [], 
            // actionItems: [] dihapus dari dataStorage
        };

        // --- Setup Peserta (Dropdown + Pills) ---
        function setupParticipantPills() {
            const select = document.getElementById('select-peserta');
            const addButton = document.getElementById('btn-add-peserta');
            const listContainer = document.getElementById('list-peserta');
            
            const renderList = () => {
                listContainer.innerHTML = '';
                dataStorage.pesertaIDs.forEach(item => {
                    const pill = document.createElement('span');
                    pill.className = 'inline-flex items-center gap-x-2 bg-primary/20 text-primary text-sm font-medium px-3 py-1.5 rounded-full dark:bg-primary/30 dark:text-primary';
                    
                    const selectedOption = select.querySelector(`option[value="${item.id}"]`);
                    const userName = selectedOption ? selectedOption.textContent.trim() : `User ID ${item.id}`;

                    pill.textContent = userName;
                    
                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.innerHTML = '<i class="fa-solid fa-times w-3 h-3"></i>';
                    removeBtn.onclick = () => {
                        dataStorage.pesertaIDs = dataStorage.pesertaIDs.filter(idObj => idObj.id !== item.id);
                        renderList();
                    };
                    
                    pill.appendChild(removeBtn);
                    listContainer.appendChild(pill);
                });
            };

            const addItem = () => {
                const id = select.value;
                if (!id) return;
                
                if (dataStorage.pesertaIDs.some(item => item.id == id)) { 
                     showToast('Peserta ini sudah ditambahkan!', true);
                     return;
                }
                
                dataStorage.pesertaIDs.push({id: id});
                select.value = ''; 
                renderList();
            };

            if (addButton) {
                addButton.addEventListener('click', addItem);
            }
        }
        setupParticipantPills();


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
        

        // --- Inisialisasi Quill JS ---
        const pembahasanQuill = new Quill('#pembahasan-editor', { theme: 'snow', placeholder: "Tuliskan hasil pembahasan, keputusan, dan poin penting lainnya...", modules: { toolbar: [[{ 'header': [1, 2, false] }], ['bold', 'italic', 'underline'], [{ 'list': 'ordered' }, { 'list': 'bullet' }], ['link'], ['clean']] } });
        

        // --- FUNGSI SUBMIT UTAMA (AJAX) ---
        const submitForm = async () => {
            const formData = new FormData(form);

            // Data Form Sederhana & Format Tanggal
            const meetingDateRaw = document.getElementById('tanggal').value;
            if (meetingDateRaw) {
                formData.set('meeting_date', meetingDateRaw);
            }

            // Ambil Konten Pembahasan (dari Quill)
            const pembahasanContent = pembahasanQuill.root.innerHTML.trim();
            if (pembahasanContent.length === 0 || pembahasanContent === '<p><br></p>') {
                showToast('Pembahasan wajib diisi!', true);
                return;
            }
            formData.set('pembahasan', pembahasanContent);

            // Cek Peserta & Tambahkan Array dari dataStorage
            if (dataStorage.pesertaIDs.length === 0) {
                 showToast('Peserta Rapat wajib diisi (minimal 1 peserta)!', true);
                 return;
            }
            // Hapus field yang mungkin ada dari form HTML
            formData.delete('attendees[]'); 
            dataStorage.pesertaIDs.forEach(item => {
                formData.append('attendees[]', item.id);
            });

            // Tambahkan Array Agenda
            if (dataStorage.agendas.length === 0) {
                 showToast('Agenda Rapat wajib diisi (minimal 1 item)!', true);
                 return;
            }
            formData.delete('agendas[]'); 
            dataStorage.agendas.forEach(item => {
                formData.append('agendas[]', item);
            });

            
            // Kirim data ke backend
            
            try {
                const response = await fetch('{{ route('moms.store') }}', { 
                    method: 'POST',
                    headers: {
                        // Kirim CSRF hanya di header
                        'X-CSRF-TOKEN': csrfToken, 
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData, 
                });

                const data = await response.json();

                if (response.ok) {
                    showToast('MoM berhasil disubmit!', false);
                    form.reset(); 
                    
                    // Reset custom data dan tampilan
                    dataStorage.pesertaIDs = [];
                    dataStorage.agendas = [];
                    // dataStorage.actionItems tidak perlu direset
                    document.getElementById('list-peserta').innerHTML = ''; // Clear pills
                    document.getElementById('list-agenda').innerHTML = '';
                    // document.getElementById('list-tindak-lanjut').innerHTML = ''; dihapus
                    pembahasanQuill.root.innerHTML = '';
                } else {
                    let errorMessage = 'Gagal menyimpan MoM.';
                    
                    // Coba tangani error validasi 422
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
                // Menangkap error jika respons bukan JSON (e.g. HTML login page dari 302/419)
                showToast('Koneksi Gagal atau Error Server.', true);
            }
        };

        // Mengaktifkan listener tombol Submit
        btnSubmit.addEventListener('click', (e) => {
            e.preventDefault();
            // Panggil fungsi submitForm yang sudah diperbaiki
            submitForm();
        });
        
        // Nonaktifkan submit form default agar tidak terjadi reload
        form.addEventListener("submit", (e) => { 
            e.preventDefault(); 
        }); 
    });
</script>
@endpush