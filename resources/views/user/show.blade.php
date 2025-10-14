@extends('layouts.app')

@section('title', 'Detail MoM | MoM Telkom')

@php
    use Carbon\Carbon;
    use Illuminate\Support\Facades\Auth;

    // Ambil attachment pertama untuk ditampilkan sebagai thumbnail
    $attachment = $mom->attachments->first();
    $imageUrl = $attachment
        ? asset('storage/' . $attachment->file_path)
        : asset('img/lampiran-kosong.png');

    $statusText = $mom->status->status ?? 'Unknown';

    // MENGGABUNGKAN PESERTA DARI KOLOM JSON (Internal dan Mitra)
    $internalNames = [];
    $partnerNames = [];

    // Ambil Peserta Internal (nama_peserta adalah array of units/objects)
    if (is_array($mom->nama_peserta)) {
        foreach ($mom->nama_peserta as $unit) {
            if (is_array($unit['attendees'] ?? null)) {
                $internalNames = array_merge($internalNames, $unit['attendees']);
            }
        }
    }

    // Ambil Peserta Mitra (nama_mitra adalah array of partner/objects)
    if (is_array($mom->nama_mitra)) {
        foreach ($mom->nama_mitra as $mitra) {
            if (is_array($mitra['attendees'] ?? null)) {
                $partnerNames = array_merge($partnerNames, $mitra['attendees']);
            }
        }
    }

    // Gabungkan, hapus duplikasi, dan filter agar hanya string yang tersisa
    $allAttendees = array_unique(array_merge($internalNames, $partnerNames));
    $allAttendees = array_filter($allAttendees, fn($name) => is_string($name) && !empty($name));

    $totalAttendees = count($allAttendees);
    // ---------------------------------------------------------------------------
@endphp

@section('content')
<div class="pt-2">
    {{-- PERUBAHAN: Header halaman disesuaikan dengan tema --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center sm:justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold font-orbitron text-neon-red">Detail MoM</h1>
            <p class="mt-1 text-gray-400 line-clamp-1" title="{{ $mom->title }}">{{ $mom->title }}</p>
        </div>
        <div class="flex space-x-2 mt-4 sm:mt-0 w-full sm:w-auto">
            <a href="{{ route('draft.index') }}" class="flex-1 sm:flex-initial inline-flex justify-center items-center px-4 py-2 text-sm font-medium text-gray-300 bg-gray-800 border border-gray-700 rounded-lg hover:bg-gray-700">
                <i class="fa-solid fa-arrow-left mr-2"></i>Kembali
            </a>
            <a href="{{ route('moms.export', $mom->version_id) }}" target="_blank"
               class="flex-1 sm:flex-initial inline-flex justify-center items-center px-4 py-2 text-sm font-medium text-white btn-neon-red btn-pulse rounded-lg">
                <i class="fa-solid fa-file-pdf mr-2"></i>Export PDF
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Kolom Kiri: Informasi Utama, Pembahasan & Lampiran --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- PERUBAHAN: Card Informasi Rapat --}}
            <div class="bg-gray-800 rounded-xl shadow-md p-6 border border-gray-700">
                <h3 class="text-xl font-bold text-white font-orbitron mb-4 border-b border-gray-700 pb-3">Informasi Rapat</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm text-gray-300">
                    <div><p class="text-gray-500 font-semibold">Pimpinan:</p><p>{{ $mom->pimpinan_rapat }}</p></div>
                    <div><p class="text-gray-500 font-semibold">Notulen:</p><p>{{ $mom->notulen }}</p></div>
                    <div class="sm:col-span-2"><p class="text-gray-500 font-semibold">Waktu:</p><p>{{ Carbon::parse($mom->meeting_date)->translatedFormat('l, d M Y') }} | {{ Carbon::parse($mom->start_time)->format('H:i') }} – {{ Carbon::parse($mom->end_time)->format('H:i') }}</p></div>
                    <div class="sm:col-span-2"><p class="text-gray-500 font-semibold">Tempat:</p><p>{{ $mom->location }}</p></div>
                </div>
            </div>

            {{-- PERUBAHAN: Card Hasil Pembahasan --}}
            <div class="bg-gray-800 rounded-xl shadow-md p-6 border border-gray-700">
                <h3 class="text-xl font-bold text-white font-orbitron mb-4 border-b border-gray-700 pb-3">Hasil Pembahasan</h3>
                {{-- Class 'prose-invert' dari Tailwind akan otomatis menyesuaikan style teks untuk mode gelap --}}
                <div class="prose prose-sm prose-invert max-w-none text-gray-300">
                    {!! $mom->pembahasan !!}
                </div>
            </div>

            {{-- PERUBAHAN: Card Lampiran --}}
            <div class="bg-gray-800 rounded-xl shadow-md p-6 border border-gray-700">
                <h3 class="text-xl font-bold text-white font-orbitron mb-4 border-b border-gray-700 pb-3">Lampiran</h3>
                @if($mom->attachments->isNotEmpty())
                    @if($attachment && str_starts_with($attachment->mime_type, 'image/'))
                        <a href="{{ $imageUrl }}" target="_blank">
                            <img src="{{ $imageUrl }}" alt="Lampiran Rapat" class="w-full rounded-lg max-w-md mb-4 border border-gray-700 hover:opacity-80 transition">
                        </a>
                    @endif
                    <ul class="space-y-2 text-sm">
                        @foreach($mom->attachments as $attachment)
                        <li>
                            <a href="{{ asset('storage/' . $attachment->file_path) }}" target="_blank" class="text-red-400 hover:underline hover:text-red-300 flex items-center">
                                <i class="fa-solid fa-paperclip mr-2"></i> {{ $attachment->file_name }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-gray-500 italic">Tidak ada lampiran terlampir.</p>
                @endif
            </div>
        </div>

        {{-- Kolom Kanan: Peserta, Agenda, & Tindak Lanjut --}}
        <div class="lg:col-span-1 space-y-6">

            {{-- PERUBAHAN: Card Peserta --}}
            <div class="bg-gray-800 rounded-xl shadow-md p-6 border border-gray-700">
                <h3 class="text-xl font-bold text-white font-orbitron mb-4"><i class="fa-solid fa-users mr-2 text-red-400"></i>Peserta ({{ $totalAttendees }})</h3>
                <ul class="space-y-2 text-sm text-gray-300 list-disc list-inside">
                    @forelse($allAttendees as $attendeeName)
                        <li>{{ $attendeeName }}</li>
                    @empty
                        <span class="italic text-gray-500">Tidak ada peserta tercatat.</span>
                    @endforelse
                </ul>
            </div>

            {{-- PERUBAHAN: Card Agenda --}}
            <div class="bg-gray-800 rounded-xl shadow-md p-6 border border-gray-700">
                <h3 class="text-xl font-bold text-white font-orbitron mb-4"><i class="fa-solid fa-list-check mr-2 text-red-400"></i>Agenda</h3>
                <ol class="space-y-2 text-sm text-gray-300 list-decimal list-inside">
                    @forelse($mom->agendas as $agenda)
                        <li>{{ $agenda->item }}</li>
                    @empty
                        <span class="italic text-gray-500">Tidak ada agenda tercatat.</span>
                    @endforelse
                </ol>
            </div>

            {{-- PERUBAHAN: Card Tindak Lanjut --}}
            <div class="bg-gray-800 rounded-xl shadow-md p-6 border border-gray-700">
                <h3 class="text-xl font-bold text-white font-orbitron mb-4"><i class="fa-solid fa-bullseye mr-2 text-red-400"></i>Tindak Lanjut</h3>
                <div id="tindak-lanjut-list" class="space-y-3">
                    @forelse($mom->actionItems as $item)
                        <div class="p-3 bg-gray-900 rounded-lg border border-gray-700">
                            <p class="font-semibold text-sm text-white">{{ $item->item }}</p>
                            <p class="text-xs text-gray-400">Deadline: {{ Carbon::parse($item->due)->translatedFormat('d M Y') }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 italic">Tidak ada tindak lanjut yang dicatat.</p>
                    @endforelse
                </div>
                <button data-modal-target="tindak-lanjut-modal" data-modal-toggle="tindak-lanjut-modal" class="mt-4 w-full flex justify-center items-center px-4 py-2 text-sm font-semibold text-white bg-green-600 rounded-lg hover:bg-green-700">
                    <i class="fa-solid fa-plus mr-2"></i>Tambah Tindak Lanjut
                </button>
            </div>
        </div>
    </div>
</div>

{{-- PERUBAHAN: Modal Form Tambah Tindak Lanjut --}}
<div id="tindak-lanjut-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <div class="relative bg-gray-800 rounded-lg shadow border border-gray-700">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-600">
                <h3 class="text-lg font-semibold text-white font-orbitron">Tambah Tindak Lanjut Baru</h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-600 hover:text-white rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-toggle="tindak-lanjut-modal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/></svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <form id="tindak-lanjut-form" class="p-4 md:p-5">
                @csrf
                <input type="hidden" name="mom_id" value="{{ $mom->version_id }}">
                <div class="grid gap-4 mb-4 grid-cols-1">
                    <div>
                        <label for="task-description" class="block mb-2 text-sm font-medium text-white">Deskripsi Tugas</label>
                        <input type="text" name="item" id="task-description" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5" placeholder="Contoh: Finalisasi desain UI/UX" required>
                    </div>
                    <div>
                        <label for="task-deadline" class="block mb-2 text-sm font-medium text-white">Deadline</label>
                        <input type="date" name="due" id="task-deadline" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5" required>
                    </div>
                </div>
                {{-- Tombol submit disesuaikan dengan tema --}}
                <button type="submit" class="text-white inline-flex items-center btn-pulse font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                    <i class="fa-solid fa-plus me-1 -ms-1 w-5 h-5"></i>
                    Tambahkan Tugas
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Tambahkan bullet di dalam pembahasan */
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
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('tindak-lanjut-form');
        const listContainer = document.getElementById('tindak-lanjut-list');
        const storeActionItemUrl = "{{ route('action_items.store') }}";

        const modalElement = document.getElementById('tindak-lanjut-modal');
        let modalInstance = null;

        if (typeof Flowbite !== 'undefined' && Flowbite.Modal) {
             modalInstance = new Flowbite.Modal(modalElement);
        }

        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(form);
            const description = formData.get('item');
            const deadline = formData.get('due');

            if (!description || !deadline) {
                alert('Mohon isi Deskripsi Tugas dan Deadline.');
                return;
            }

            try {
                const response = await fetch(storeActionItemUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': formData.get('_token'),
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: formData,
                });

                const data = await response.json();

                if (response.ok) {
                    const formattedDate = new Date(deadline + 'T00:00:00').toLocaleDateString('id-ID', {
                        day: '2-digit', month: 'short', year: 'numeric'
                    });

                    const newItem = document.createElement('div');
                    newItem.className = 'p-3 bg-body-bg dark:bg-dark-body-bg rounded-lg';
                    newItem.innerHTML = `
                        <p class="font-semibold text-sm">${description}</p>
                        <p class="text-xs text-text-secondary">Deadline: ${formattedDate}</p>
                    `;

                    const emptyMessage = listContainer.querySelector('.text-text-secondary');
                    if (emptyMessage && listContainer.children.length === 1 && emptyMessage.textContent.includes('Tidak ada tindak lanjut')) {
                        listContainer.innerHTML = '';
                    }

                    listContainer.appendChild(newItem);

                    form.reset();
                    if (modalInstance) modalInstance.hide();

                    window.location.reload();

                } else {
                    let errorMessage = data.message || 'Error server saat menambahkan tugas.';
                    if (response.status === 422 && data.errors) {
                        errorMessage = 'Validasi Gagal: ' +
                                            (data.errors.item ? data.errors.item[0] + ' ' : '') +
                                            (data.errors.due ? data.errors.due[0] + ' ' : '') +
                                            (data.errors.mom_id ? data.errors.mom_id[0] : '');
                    }
                    alert('Gagal menyimpan tugas: ' + errorMessage);
                }
            } catch (error) {
                console.error('Network Error:', error);
                alert('Terjadi kesalahan koneksi.');
            }
        });
    });
</script>
@endpush
