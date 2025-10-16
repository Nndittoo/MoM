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
<div class="pt-2">
    {{-- Header --}}
    <div class="p-6 md:p-8 rounded-xl shadow-lg bg-gray-800 border-l-4 border-red-500 mb-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold font-orbitron text-neon-red">Detail MoM</h1>
                <p class="mt-1 text-gray-400 line-clamp-1" title="{{ $mom->title }}">{{ $mom->title }}</p>
            </div>
            <div class="flex space-x-2 w-full sm:w-auto">
                <a href="{{ route('admin.repository') }}" class="flex-1 sm:flex-initial inline-flex justify-center items-center px-4 py-2 text-sm font-medium text-gray-300 bg-gray-800 border border-gray-700 rounded-lg hover:bg-gray-700">
                    <i class="fa-solid fa-arrow-left mr-2"></i>Kembali
                </a>
                <a href="{{ route('moms.export', $mom->version_id) }}" target="_blank" class="flex-1 sm:flex-initial inline-flex justify-center items-center px-4 py-2 text-sm font-medium text-white btn-neon-red btn-pulse rounded-lg">
                    <i class="fa-solid fa-file-pdf mr-2"></i>Export PDF
                </a>
            </div>
        </div>
    </div>


    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Kolom kiri --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-gray-800 rounded-xl shadow-md p-6 border border-gray-700">
                <h3 class="text-xl font-bold text-white font-orbitron mb-4 border-b border-gray-700 pb-3">Informasi Rapat</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm text-gray-300">
                    <div><p class="text-gray-500 font-semibold">Pimpinan:</p><p>{{ $mom->pimpinan_rapat }}</p></div>
                    <div><p class="text-gray-500 font-semibold">Notulen:</p><p>{{ $mom->notulen }}</p></div>
                    <div class="sm:col-span-2"><p class="text-gray-500 font-semibold">Waktu:</p><p>{{ Carbon::parse($mom->meeting_date)->translatedFormat('l, d M Y') }} | {{ Carbon::parse($mom->start_time)->format('H:i') }} – {{ Carbon::parse($mom->end_time)->format('H:i') }}</p></div>
                    <div class="sm:col-span-2"><p class="text-gray-500 font-semibold">Tempat:</p><p>{{ $mom->location }}</p></div>
                </div>
            </div>
            <div class="bg-gray-800 rounded-xl shadow-md p-6 border border-gray-700">
                <h3 class="text-xl font-bold text-white font-orbitron mb-4 border-b border-gray-700 pb-3">Hasil Pembahasan</h3>
                <div class="prose prose-sm prose-invert max-w-none text-gray-300">{!! $mom->pembahasan !!}</div>
            </div>
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

        {{-- Kolom kanan --}}
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-gray-800 rounded-xl shadow-md p-6 border border-gray-700">
                <h3 class="text-xl font-bold text-white font-orbitron mb-4"><i class="fa-solid fa-users mr-2 text-red-400"></i>Peserta ({{ $totalAttendees }})</h3>
                <ul class="space-y-2 text-sm text-gray-300 list-disc list-inside">
                    @forelse($allAttendeeNames as $attendeeName)
                        <li>{{ $attendeeName }}</li>
                    @empty
                        <span class="italic text-gray-500">Tidak ada peserta tercatat.</span>
                    @endforelse
                </ul>
            </div>
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
            <div class="bg-gray-800 rounded-xl shadow-md p-6 border border-gray-700">
                <h3 class="text-xl font-bold text-white font-orbitron mb-4"><i class="fa-solid fa-bullseye mr-2 text-red-400"></i>Tindak Lanjut</h3>
                <div id="tindak-lanjut-list" class="space-y-3">
                    @forelse($mom->actionItems as $item)
                        <div id="action-item-{{ $item->action_id }}" class="p-3 bg-gray-900 rounded-lg border border-gray-700 flex justify-between items-center">
                            <div>
                                <p class="font-semibold text-sm text-white">{{ $item->item }}</p>
                                <p class="text-xs text-gray-400">Deadline: {{ Carbon::parse($item->due)->translatedFormat('d M Y') }}</p>
                            </div>
                            <button onclick="deleteActionItem({{ $item->action_id }})" class="text-gray-500 hover:text-red-400 transition-colors">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 italic">Tidak ada tindak lanjut.</p>
                    @endforelse
                </div>
                <button data-modal-target="tindak-lanjut-modal" data-modal-toggle="tindak-lanjut-modal" class="mt-4 w-full flex justify-center items-center px-4 py-2 text-sm font-semibold text-white btn-neon-red rounded-lg">
                    <i class="fa-solid fa-plus mr-2"></i>Tambah Tindak Lanjut
                </button>
            </div>
        </div>
    </div>
            {{-- Tindak lanjut --}}
            {{-- Tindak Lanjut --}}


{{-- Modal Tambah --}}
<div id="tindak-lanjut-modal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
    <div class="relative w-full max-w-md bg-gray-900 text-gray-100 rounded-xl shadow-2xl border border-gray-700 animate-fadeIn">
        <div class="flex justify-between items-center p-4 border-b border-gray-700">
            <h3 class="text-lg font-semibold text-white"><i class="fa-solid fa-plus-circle text-neon-red mr-2"></i>Tambah Tindak Lanjut</h3>
            <button type="button" data-modal-toggle="tindak-lanjut-modal" class="text-gray-400 hover:text-white">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form id="tindak-lanjut-form" class="p-4 space-y-4">
            @csrf
            <input type="hidden" name="mom_id" value="{{ $mom->version_id }}">
            <div>
                <label class="block text-sm mb-1">Deskripsi Tugas</label>
                <input type="text" name="item" class="w-full p-2 rounded-lg bg-gray-800 border border-gray-700 focus:border-red-500 focus:ring-1 focus:ring-red-500" required>
            </div>
            <div>
                <label class="block text-sm mb-1">Deadline</label>
                <input type="date" name="due" class="w-full p-2 rounded-lg bg-gray-800 border border-gray-700 focus:border-red-500 focus:ring-1 focus:ring-red-500" required>
            </div>
            <button type="submit"
                class="w-full py-2 rounded-lg bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 text-white font-semibold transition-all duration-300">
                <i class="fa-solid fa-check mr-1"></i> Simpan
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const storeUrl = "{{ route('action_items.store') }}";
    const deleteUrl = "{{ route('action_items.destroy', ':id') }}";
    const listContainer = document.getElementById('tindak-lanjut-list');
    const form = document.getElementById('tindak-lanjut-form');
    const modal = document.getElementById('tindak-lanjut-modal');

    const closeModal = () => {
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    };

    // 🗑️ Hapus Item
    window.deleteActionItem = async function (id) {
        const result = await Swal.fire({
            title: 'Hapus Tindak Lanjut?',
            text: 'Event Google Calendar juga akan dihapus!',
            icon: 'warning',
            customClass: {
                popup: 'bg-gray-800 rounded-2xl border border-gray-700',
                title: 'text-white font-orbitron',
                htmlContainer: 'text-gray-400',
                confirmButton: 'btn-neon-red text-white font-semibold px-6 py-2 mr-4 rounded-lg',
                cancelButton: 'bg-gray-700 text-gray-300 font-semibold px-6 py-2 rounded-lg hover:bg-gray-600 border border-gray-600'
            },
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal',
            buttonsStyling: false
        });

        if (!result.isConfirmed) return;

        const url = deleteUrl.replace(':id', id);

        // Tampilkan loading
        Swal.fire({
            title: 'Menghapus...',
            text: 'Menghapus tindak lanjut dan event Google Calendar',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            },
            customClass: {
                popup: 'bg-gray-800 rounded-2xl border border-gray-700',
                title: 'text-white font-orbitron',
                htmlContainer: 'text-gray-400'
            }
        });

        try {
            const response = await fetch(url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            });

            const data = await response.json();

            if (response.ok) {
                // Animasi hapus
                const itemEl = document.getElementById(`action-item-${id}`);
                if (itemEl) {
                    itemEl.classList.add('opacity-0', 'translate-x-4', 'transition-all', 'duration-300');
                    setTimeout(() => itemEl.remove(), 300);
                }

                // Cek apakah masih ada item
                setTimeout(() => {
                    if (!listContainer.querySelector('.p-3')) {
                        listContainer.innerHTML = '<p class="text-sm text-gray-500 italic">Tidak ada tindak lanjut.</p>';
                    }
                }, 350);

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message || 'Tindak lanjut dan event Google Calendar berhasil dihapus.',
                    timer: 2000,
                    showConfirmButton: false,
                    customClass: {
                        popup: 'bg-gray-800 rounded-2xl border border-gray-700',
                        title: 'text-white font-orbitron',
                        htmlContainer: 'text-gray-400'
                    }
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: data.message || 'Gagal menghapus tindak lanjut.',
                    customClass: {
                        popup: 'bg-gray-800 rounded-2xl border border-gray-700',
                        title: 'text-white font-orbitron',
                        htmlContainer: 'text-gray-400',
                        confirmButton: 'btn-neon-red text-white font-semibold px-6 py-2 rounded-lg'
                    }
                });
            }
        } catch (err) {
            console.error('Delete error:', err);
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Terjadi kesalahan koneksi.',
                customClass: {
                    popup: 'bg-gray-800 rounded-2xl border border-gray-700',
                    title: 'text-white font-orbitron',
                    htmlContainer: 'text-gray-400',
                    confirmButton: 'btn-neon-red text-white font-semibold px-6 py-2 rounded-lg'
                }
            });
        }
    };

    // ➕ Tambah Item
    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        const formData = new FormData(form);

        try {
            const response = await fetch(storeUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': formData.get('_token'),
                    'Accept': 'application/json',
                },
                body: formData,
            });

            const data = await response.json();

            if (response.ok) {
                const item = data.action_item;
                const formattedDate = new Date(item.due).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });

                const newEl = document.createElement('div');
                newEl.id = `action-item-${item.action_id}`;
                newEl.className = 'p-3 bg-gray-800 dark:bg-dark-body-bg rounded-lg flex justify-between items-center border border-gray-700 hover:border-red-500 transition-all duration-300 opacity-0 translate-y-2';
                newEl.innerHTML = `
                    <div>
                        <p class="font-semibold text-sm text-gray-100">${item.item}</p>
                        <p class="text-xs text-gray-400">Deadline: ${formattedDate}</p>
                    </div>
                    <button onclick="deleteActionItem(${item.action_id})" class="text-red-500 hover:text-red-400 transition-all">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                `;

                const emptyMsg = listContainer.querySelector('p.text-text-secondary');
                if (emptyMsg) listContainer.innerHTML = '';
                listContainer.appendChild(newEl);

                // animasi muncul
                setTimeout(() => newEl.classList.remove('opacity-0', 'translate-y-2'), 50);

                form.reset();
                closeModal();

                Swal.fire({
    icon: 'success',
    title: 'Berhasil!',
    text: 'Tindak lanjut berhasil ditambahkan!',
    timer: 2000,
    showConfirmButton: false,
    customClass: {
                        popup: 'bg-gray-800 rounded-2xl border border-gray-700',
                        title: 'text-white font-orbitron',
                        htmlContainer: 'text-gray-400',
                        confirmButton: 'btn-neon-red text-white font-semibold px-6 py-2 mr-4 rounded-lg',
                        cancelButton: 'bg-gray-700 text-gray-300 font-semibold px-6 py-2 rounded-lg hover:bg-gray-600 border border-gray-600'
                    },
}).then(() => {
    location.reload();
});
            } else {
                Swal.fire('Gagal', data.message || 'Validasi gagal.', 'error');
            }
        } catch (err) {
            Swal.fire('Error', 'Terjadi kesalahan koneksi.', 'error');
        }
    });
});
</script>
@endpush
