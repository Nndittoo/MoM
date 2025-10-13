@extends('admin.layouts.app')

@section('title', 'Detail MoM | MoM Telkom')

@php
    use Carbon\Carbon;
    use Illuminate\Support\Facades\Auth;

    $attachment = $mom->attachments->first();
    $imageUrl = $attachment
        ? asset('storage/' . $attachment->file_path)
        : asset('img/lampiran-kosong.png');

    $statusText = $mom->status->status ?? 'Unknown';
    
    // Ambil data peserta internal dan eksternal
    $internalData = is_array($mom->nama_peserta) ? $mom->nama_peserta : json_decode($mom->nama_peserta ?? '[]', true);
    $partnerData = isset($mom->partner_attendees) && is_array($mom->partner_attendees) ? $mom->partner_attendees : json_decode($mom->partner_attendees ?? '[]', true); 

    // Gabungkan semua container unit/mitra
    $allAttendeeContainers = array_merge($internalData, $partnerData);
    
    $allAttendeeNames = [];
    
    // Ekstrak hanya nama-nama peserta ke dalam array flat
    foreach ($allAttendeeContainers as $container) {
        if (isset($container['attendees']) && is_array($container['attendees'])) {
            // Filter untuk memastikan hanya string yang diambil
            $validAttendees = array_filter($container['attendees'], fn($name) => is_string($name) && !empty($name));
            $allAttendeeNames = array_merge($allAttendeeNames, $validAttendees);
        }
    }
    
    // Hilangkan duplikasi dan hitung total
    $allAttendeeNames = array_unique($allAttendeeNames);
    $totalAttendees = count($allAttendeeNames);
    
@endphp

@section('content')
<div class="p-4 rounded-lg mt-14">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center sm:justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-text-primary dark:text-dark-text-primary">Detail Minute of Meeting</h1>
            <p class="mt-1 text-text-secondary dark:text-dark-text-secondary">{{ $mom->title }}</p>
        </div>
        <div class="flex space-x-2 mt-4 sm:mt-0 w-full sm:w-auto">
            <a href="{{ route('admin.repository') }}" class="flex-1 sm:flex-initial inline-flex justify-center items-center px-4 py-2 text-sm font-medium text-text-secondary bg-component-bg border border-border-light rounded-lg hover:bg-body-bg dark:bg-dark-component-bg dark:text-dark-text-secondary dark:border-border-dark dark:hover:bg-dark-body-bg">
                <i class="fa-solid fa-arrow-left mr-2"></i>Kembali
            </a>

            <a href="{{ route('moms.export', $mom->version_id) }}" target="_blank" class="flex-1 sm:flex-initial inline-flex justify-center items-center px-4 py-2 text-sm font-medium text-white bg-gradient-primary rounded-lg hover:opacity-90">
                <i class="fa-solid fa-file-pdf mr-2"></i>Export
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Kolom kiri --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Info rapat --}}
            <div class="bg-component-bg dark:bg-dark-component-bg rounded-lg shadow-md p-6">
                <h3 class="text-xl font-bold mb-4 border-b pb-3">Informasi Rapat</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div><p><strong>Pimpinan:</strong></p><p>{{ $mom->pimpinan_rapat }}</p></div>
                    <div><p><strong>Notulen:</strong></p><p>{{ $mom->notulen }}</p></div>
                    <div>
                        <p><strong>Waktu:</strong></p>
                        <p>{{ Carbon::parse($mom->meeting_date)->translatedFormat('l, d M Y') }} | {{ Carbon::parse($mom->start_time)->format('H:i') }} – {{ Carbon::parse($mom->end_time)->format('H:i') }}</p>
                    </div>
                    <div><p><strong>Tempat:</strong></p><p>{{ $mom->location }}</p></div>
                </div>
            </div>

            {{-- Card Hasil Pembahasan --}}
            <div class="bg-component-bg dark:bg-dark-component-bg rounded-lg shadow-md p-6">
                <h3 class="text-xl font-bold text-text-primary dark:text-dark-text-primary mb-4 border-b dark:border-border-dark pb-3">Hasil Pembahasan</h3>
                <div class="prose dark:prose-invert max-w-none text-sm">
                    {!! $mom->pembahasan !!}
                </div>
            </div>

            {{-- Lampiran --}}
            <div class="bg-component-bg dark:bg-dark-component-bg rounded-lg shadow-md p-6">
                <h3 class="text-xl font-bold mb-4 border-b pb-3">Lampiran</h3>
                @if($mom->attachments->isNotEmpty())
                    @if($attachment && str_starts_with($attachment->mime_type, 'image/'))
                        <img src="{{ $imageUrl }}" alt="Lampiran Rapat" class="w-full rounded-lg max-w-md mb-4">
                    @endif
                    <ul class="space-y-2 text-sm">
                        @foreach($mom->attachments as $attachment)
                        <li>
                            <a href="{{ asset('storage/' . $attachment->file_path) }}" target="_blank" class="text-primary hover:underline flex items-center">
                                <i class="fa-solid fa-paperclip mr-2"></i> {{ $attachment->file_name }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-text-secondary">Tidak ada lampiran.</p>
                @endif
            </div>
        </div>

        {{-- Kolom kanan --}}
        <div class="lg:col-span-1 space-y-6">

            {{-- Peserta --}}
            <div class="bg-component-bg dark:bg-dark-component-bg rounded-lg shadow-md p-6">
                <h3 class="text-xl font-bold mb-4"><i class="fa-solid fa-users mr-2"></i>Peserta ({{ $totalAttendees }})</h3>
                <ul class="space-y-2 text-sm list-disc list-inside">
                    
                    @forelse($allAttendeeNames as $attendeeName) 
                        <li>{{ $attendeeName }}</li>
                    @empty
                        <span class="italic text-text-secondary">Tidak ada peserta tercatat.</span>
                    @endforelse
                </ul>
            </div>

            {{-- Agenda --}}
            <div class="bg-component-bg dark:bg-dark-component-bg rounded-lg shadow-md p-6">
                <h3 class="text-xl font-bold mb-4"><i class="fa-solid fa-list-check mr-2"></i>Agenda</h3>
                <ol class="space-y-2 text-sm list-decimal list-inside">
                    @foreach($mom->agendas as $agenda)
                        <li>{{ $agenda->item }}</li>
                    @endforeach
                </ol>
            </div>

            {{-- Tindak lanjut --}}
            <div class="bg-component-bg dark:bg-dark-component-bg rounded-lg shadow-md p-6">
                <h3 class="text-xl font-bold mb-4"><i class="fa-solid fa-bullseye mr-2"></i>Tindak Lanjut</h3>
                <div id="tindak-lanjut-list" class="space-y-3">
                    @forelse($mom->actionItems as $item)
                        <div id="action-item-{{ $item->action_id }}" class="p-3 bg-body-bg dark:bg-dark-body-bg rounded-lg flex justify-between items-center">
                            <div>
                                <p class="font-semibold text-sm">{{ $item->item }}</p>
                                <p class="text-xs text-text-secondary">Deadline: {{ Carbon::parse($item->due)->translatedFormat('d M Y') }}</p>
                            </div>
                            <button onclick="deleteActionItem({{ $item->action_id }})" class="text-red-500 hover:text-red-700">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    @empty
                        <p class="text-sm text-text-secondary">Tidak ada tindak lanjut.</p>
                    @endforelse
                </div>

                {{-- Tombol Tambah --}}
                <button data-modal-target="tindak-lanjut-modal" data-modal-toggle="tindak-lanjut-modal" class="mt-4 w-full flex justify-center items-center px-4 py-2 text-sm font-semibold text-white bg-green-600 rounded-lg hover:bg-green-700">
                    <i class="fa-solid fa-plus mr-2"></i>Tambah Tindak Lanjut
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Tambah --}}
<div id="tindak-lanjut-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed inset-0 z-50 justify-center items-center w-full h-full">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <div class="flex items-center justify-between p-4 border-b dark:border-gray-600">
                <h3 class="text-lg font-semibold">Tambah Tindak Lanjut Baru</h3>
                <button type="button" data-modal-toggle="tindak-lanjut-modal" class="text-gray-400 hover:text-gray-900 dark:hover:text-white">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <form id="tindak-lanjut-form" class="p-4">
                @csrf
                <input type="hidden" name="mom_id" value="{{ $mom->version_id }}">
                <div class="mb-4">
                    <label for="task-description" class="block text-sm font-medium">Deskripsi Tugas</label>
                    <input type="text" name="item" id="task-description" class="w-full border rounded-lg p-2" required>
                </div>
                <div class="mb-4">
                    <label for="task-deadline" class="block text-sm font-medium">Deadline</label>
                    <input type="date" name="due" id="task-deadline" class="w-full border rounded-lg p-2" required>
                </div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    <i class="fa-solid fa-plus mr-1"></i> Tambahkan
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Menambahkan bullet di dalam pembahasan */
    .prose ul {
        list-style-type: disc;
        margin-left: 1.5rem;
        padding-left: 1rem;
    }

    .prose ol {
        list-style-type: decimal;
        margin-left: 1.5rem;
        padding-left: 1rem;
    }

    .prose li {
        margin-bottom: 0.25rem;
    }

    .prose ul li::marker {
        color: var(--tw-prose-bullets, #6b7280);
    }

    .dark .prose ul li::marker {
        color: #d1d5db;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const storeActionItemUrl = "{{ route('action_items.store') }}";
    const deleteActionItemUrl = "{{ route('action_items.destroy', ':actionItem') }}"; 
    const listContainer = document.getElementById('tindak-lanjut-list');
    const form = document.getElementById('tindak-lanjut-form');
    const modalElement = document.getElementById('tindak-lanjut-modal');
    
    // Inisialisasi Flowbite Modal
    let modalInstance = null;
    if (typeof Flowbite !== 'undefined' && Flowbite.Modal) {
         modalInstance = new Flowbite.Modal(modalElement);
    }

    // Fungsi Kritis: Menyembunyikan modal dan membersihkan DOM dari backdrop/overlay
    const hideModalAndClean = () => {
        // --- 1. Sembunyikan Modal ---
        if (modalInstance) {
            modalInstance.hide();
        } else {
            // Fallback manual jika Flowbite API gagal/tidak diinisialisasi
            modalElement.classList.add('hidden');
            // Hapus kelas display: block/flex yang mungkin ditambahkan Flowbite
            modalElement.style.display = 'none';
        }
        
        // --- 2. Bersihkan BODY ---
        // Hapus kelas yang mengunci scroll
        document.body.classList.remove('overflow-hidden'); 
        
        // --- 3. Hapus ELEMEN BACKDROP DINDAMIS ---
        // Flowbite modern menggunakan class 'modal-backdrop' pada elemen div yang dibuatnya secara dinamis
        const backdrop = document.querySelector('.modal-backdrop');
        if (backdrop) {
            backdrop.remove();
        }
        // Jika Anda menggunakan versi Flowbite lama atau ada konfigurasi custom, 
        // Flowbite mungkin juga meninggalkan elemen dengan atribut 'modal-backdrop'
        document.querySelector('[modal-backdrop]')?.remove();
        // ====================================================
    };

    // Fungsi Delete (tidak diubah)
    window.deleteActionItem = async function (actionItemId) {
        if (!confirm("Yakin ingin menghapus tindak lanjut ini?")) return;
        const url = deleteActionItemUrl.replace(':actionItem', actionItemId);

        try {
            const response = await fetch(url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 
                    'Accept': 'application/json',
                },
            });

            if (response.ok) {
                document.getElementById(`action-item-${actionItemId}`)?.remove();
                
                if (listContainer.children.length === 0) {
                    listContainer.innerHTML = '<p class="text-sm text-text-secondary">Tidak ada tindak lanjut.</p>';
                }
            } else {
                const data = await response.json();
                alert(data.message || 'Gagal menghapus tindak lanjut.');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan koneksi saat menghapus.');
        }
    };

    // Fungsi Tambah
    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        const formData = new FormData(form);

        try {
            const response = await fetch(storeActionItemUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': formData.get('_token'), 
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: formData,
            });

            const data = await response.json(); 

            if (response.ok) {
                const newId = data.action_item.action_id; 
                const newItem = document.createElement('div');
                const dateOptions = { day: '2-digit', month: 'short', year: 'numeric' };
                const formattedDate = new Date(formData.get('due') + 'T00:00:00').toLocaleDateString('id-ID', dateOptions); 
                
                newItem.className = 'p-3 bg-body-bg dark:bg-dark-body-bg rounded-lg flex justify-between items-center';
                newItem.id = `action-item-${newId}`;
                newItem.innerHTML = `
                    <div>
                        <p class="font-semibold text-sm">${formData.get('item')}</p>
                        <p class="text-xs text-text-secondary">Deadline: ${formattedDate}</p>
                    </div>
                    <button onclick="deleteActionItem(${newId})" class="text-red-500 hover:text-red-700">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                `;
                
                const emptyMessage = listContainer.querySelector('.text-text-secondary');
                if (emptyMessage && listContainer.children.length === 1 && emptyMessage.textContent.includes('Tidak ada tindak lanjut')) {
                    listContainer.innerHTML = '';
                }
                
                listContainer.appendChild(newItem);
                form.reset();
                
                // Gunakan fungsi clean up
                hideModalAndClean();
                
            } else {
                let errorMessage = data.message || 'Error server saat menambahkan tugas.';
                if (response.status === 422 && data.errors) {
                    errorMessage = 'Validasi Gagal: ' + 
                                 (data.errors.item ? data.errors.item[0] + ' ' : '') + 
                                 (data.errors.due ? data.errors.due[0] + ' ' : '') +
                                 (data.errors.mom_id ? data.errors.mom_id[0] : '');
                }
                alert('Gagal menambahkan tindak lanjut:\n' + errorMessage);
            }
        } catch (error) {
            console.error('Network Error:', error);
            alert('Terjadi kesalahan koneksi.');
        }
    });
});
</script>
@endpush