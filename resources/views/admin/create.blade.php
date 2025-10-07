@extends('admin.layouts.app')

@section('title', 'Create MoM | MoM Telkom')

@push('styles')
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
@endpush

@section('content')
<div class="pt-4">
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

                    <div><h2 class="text-base font-semibold text-text-primary dark:text-dark-text-primary border-b border-border-light dark:border-border-dark pb-3 mb-6">Pembahasan</h2><div id="pembahasan-editor"><input type="text" id="input-agenda" class="h-16 bg-body-bg border border-border-light text-text-primary text-sm rounded-lg focus:ring-primary focus:border-primary block w-full p-2.5 dark:bg-dark-component-bg dark:border-border-dark" placeholder="Masukkan pembahasan di sini . ."></div></div>


                    <div><h2 class="text-base font-semibold text-text-primary dark:text-dark-text-primary border-b border-border-light dark:border-border-dark pb-3 mb-6">Lampiran</h2><input class="block w-full text-sm text-text-primary border border-border-light rounded-lg cursor-pointer bg-body-bg dark:text-dark-text-secondary focus:outline-none dark:bg-dark-component-bg dark:border-border-dark" id="lampiran" type="file" multiple><p class="mt-1 text-xs text-text-secondary">PNG, JPG, PDF, DOCX (MAX. 5MB per file).</p></div>

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
        document.addEventListener("DOMContentLoaded", () => {
            // --- Setup Peserta (Pills) ---
            function setupParticipantPills() {
                const input = document.getElementById('input-peserta');
                const addButton = document.getElementById('btn-add-peserta');
                const listContainer = document.getElementById('list-peserta');
                const addItem = () => {
                    const value = input.value.trim();
                    if (value === '') return;
                    const pill = document.createElement('span');
                    pill.className = 'inline-flex items-center gap-x-2 bg-primary/20 text-primary text-sm font-medium px-3 py-1.5 rounded-full dark:bg-primary/30 dark:text-primary';
                    pill.textContent = value;
                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.innerHTML = '<i class="fa-solid fa-times w-3 h-3"></i>';
                    removeBtn.onclick = () => pill.remove();
                    pill.appendChild(removeBtn);
                    listContainer.appendChild(pill);
                    input.value = '';
                    input.focus();
                };
                addButton.addEventListener('click', addItem);
                input.addEventListener('keydown', (e) => { if (e.key === 'Enter') { e.preventDefault(); addItem(); } });
            }
            setupParticipantPills();

            // --- Setup Agenda (List) ---
            function setupAgendaList() {
                const input = document.getElementById('input-agenda');
                const addButton = document.getElementById('btn-add-agenda');
                const listContainer = document.getElementById('list-agenda');
                const addItem = () => {
                    const value = input.value.trim();
                    if (value === '') return;
                    const listItem = document.createElement('li');
                    listItem.className = 'flex items-center justify-between text-text-secondary dark:text-dark-text-secondary';
                    listItem.textContent = value;
                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.innerHTML = '<i class="fa-solid fa-times text-red-500 hover:text-red-700 fa-sm"></i>';
                    removeBtn.className = 'ml-4';
                    removeBtn.onclick = () => listItem.remove();
                    listItem.appendChild(removeBtn);
                    listContainer.appendChild(listItem);
                    input.value = '';
                    input.focus();
                };
                addButton.addEventListener('click', addItem);
                input.addEventListener('keydown', (e) => { if (e.key === 'Enter') { e.preventDefault(); addItem(); } });
            }
            setupAgendaList();

            // --- Setup Action Items (Tindak Lanjut) ---
            function setupActionItems() {
                const taskInput = document.getElementById('input-task');
                const deadlineInput = document.getElementById('input-deadline');
                const addButton = document.getElementById('btn-add-task');
                const listContainer = document.getElementById('list-tindak-lanjut');
                const addItem = () => {
                    const taskValue = taskInput.value.trim();
                    const deadlineValue = deadlineInput.value;
                    if (!taskValue || !deadlineValue) { alert('Deskripsi Task dan Deadline harus diisi!'); return; }
                    const formattedDate = new Date(deadlineValue).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
                    const newItem = document.createElement('div');
                    newItem.className = 'flex justify-between items-center bg-body-bg dark:bg-dark-body-bg p-3 rounded-md border border-border-light dark:border-border-dark';
                    newItem.innerHTML = `<div><p class="font-medium text-text-primary dark:text-dark-text-primary">${taskValue}</p><p class="text-xs text-text-secondary dark:text-dark-text-secondary"><i class="fa-solid fa-flag-checkered fa-xs mr-1 opacity-75"></i>Deadline: ${formattedDate}</p></div><button type="button" class="remove-btn text-red-500 hover:text-red-700 ml-4"><i class="fa-solid fa-trash-alt"></i></button>`;
                    newItem.querySelector('.remove-btn').addEventListener('click', () => newItem.remove());
                    listContainer.appendChild(newItem);
                    taskInput.value = '';
                    deadlineInput.value = '';
                    taskInput.focus();
                };
                addButton.addEventListener('click', addItem);
            }
            setupActionItems();

            // --- Inisialisasi Quill JS ---
            const quill = new Quill('#pembahasan-editor', { theme: 'snow', placeholder: "Tuliskan hasil pembahasan, keputusan, dan poin penting lainnya...", modules: { toolbar: [[{ 'header': [1, 2, false] }], ['bold', 'italic', 'underline'], [{ 'list': 'ordered' }, { 'list': 'bullet' }], ['link'], ['clean']] } });
            document.getElementById('mom-form').addEventListener("submit", (e) => { e.preventDefault(); document.getElementById('pembahasan').value = quill.root.innerHTML; });

            // --- Toast Notification ---
            const btnSubmit = document.getElementById("btn-submit");
            const toast = document.getElementById("toast");
            btnSubmit.addEventListener("click", () => {
                toast.classList.remove("hidden", "opacity-0");
                toast.classList.add("opacity-100");
                setTimeout(() => {
                    toast.classList.remove("opacity-100");
                    toast.classList.add("opacity-0");
                    setTimeout(() => { toast.classList.add("hidden"); }, 500);
                }, 2000);
            });
        });
    </script>
@endpush
