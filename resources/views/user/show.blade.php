@extends('layouts.app')

@section('title', 'Detail MoM | MoM Telkom')

@php
    use Carbon\Carbon;
    use Illuminate\Support\Facades\Auth;

    $attachment = $mom->attachments->first();
    $imageUrl = $attachment
        ? asset('storage/' . $attachment->file_path)
        : asset('img/lampiran-kosong.png');

    $statusText = $mom->status->status ?? 'Unknown';

    // Logika Peserta (sama seperti sebelumnya)
    $internalData = is_array($mom->nama_peserta) ? $mom->nama_peserta : json_decode($mom->nama_peserta ?? '[]', true);
    $partnerData = isset($mom->partner_attendees) && is_array($mom->partner_attendees) ? $mom->partner_attendees : json_decode($mom->partner_attendees ?? '[]', true);
    $allAttendeeContainers = array_merge($internalData, $partnerData);
    $allAttendeeNames = [];
    foreach ($allAttendeeContainers as $container) {
        if (isset($container['attendees']) && is_array($container['attendees'])) {
            $validAttendees = array_filter($container['attendees'], fn($name) => is_string($name) && !empty($name));
            $allAttendeeNames = array_merge($allAttendeeNames, $validAttendees);
        }
    }
    $allAttendeeNames = array_unique($allAttendeeNames);
    $totalAttendees = count($allAttendeeNames);
@endphp

@push('styles')
<style>
    /* Styling khusus agar TinyMCE Read-Only terlihat menyatu */
    .tox-tinymce-readonly {
        border: 1px solid #4B5563 !important; /* Border abu-abu */
        border-radius: 0.5rem !important;
        background-color: #1F2937 !important; /* Background card */
    }
    /* Sembunyikan status bar bawah editor */
    .tox-statusbar { display: none !important; }
</style>
@endpush

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
            {{-- Info rapat --}}
            <div class="bg-gray-800 rounded-xl shadow-md p-6 border border-gray-700">
                <h3 class="text-xl font-bold text-white font-orbitron mb-4 border-b border-gray-700 pb-3">Informasi Rapat</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm text-gray-300">
                    <div><p class="text-gray-500 font-semibold">Pimpinan:</p><p>{{ $mom->pimpinan_rapat }}</p></div>
                    <div><p class="text-gray-500 font-semibold">Notulen:</p><p>{{ $mom->notulen }}</p></div>
                    <div class="sm:col-span-2"><p class="text-gray-500 font-semibold">Waktu:</p><p>{{ Carbon::parse($mom->meeting_date)->translatedFormat('l, d M Y') }} | {{ Carbon::parse($mom->start_time)->format('H:i') }} – {{ Carbon::parse($mom->end_time)->format('H:i') }}</p></div>
                    <div class="sm:col-span-2"><p class="text-gray-500 font-semibold">Tempat:</p><p>{{ $mom->location }}</p></div>
                </div>
            </div>

            {{-- Card Hasil Pembahasan (TINYMCE READ-ONLY) --}}
            <div class="bg-gray-800 rounded-xl shadow-md p-6 border border-gray-700">
                <h3 class="text-xl font-bold text-white font-orbitron mb-4 border-b border-gray-700 pb-3">Hasil Pembahasan</h3>

                {{-- Gunakan Textarea yang akan diubah menjadi TinyMCE --}}
                <textarea id="pembahasan-viewer" class="hidden">
                    {!! $mom->pembahasan !!}
                </textarea>

            </div>

            {{-- Lampiran --}}
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

        {{-- Kolom kanan (Peserta, Agenda, Tindak Lanjut) --}}
        <div class="lg:col-span-1 space-y-6">
            {{-- ... (Kode Card Kanan TETAP SAMA seperti sebelumnya) ... --}}
            <div class="bg-gray-800 rounded-xl shadow-md p-6 border border-gray-700">
                <h3 class="text-xl font-bold text-white font-orbitron mb-4"><i class="fa-solid fa-users mr-2 text-red-400"></i>Peserta ({{ $totalAttendees }})</h3>
                <ul class="space-y-2 text-sm text-gray-300 list-disc list-inside">
                    @forelse($allAttendeeNames as $attendeeName) <li>{{ $attendeeName }}</li> @empty <span class="italic text-gray-500">Tidak ada peserta.</span> @endforelse
                </ul>
            </div>
            <div class="bg-gray-800 rounded-xl shadow-md p-6 border border-gray-700">
                <h3 class="text-xl font-bold text-white font-orbitron mb-4"><i class="fa-solid fa-list-check mr-2 text-red-400"></i>Agenda</h3>
                <ol class="space-y-2 text-sm text-gray-300 list-decimal list-inside">
                    @forelse($mom->agendas as $agenda) <li>{{ $agenda->item }}</li> @empty <span class="italic text-gray-500">Tidak ada agenda.</span> @endforelse
                </ol>
            </div>
            <div class="bg-gray-800 rounded-xl shadow-md p-6 border border-gray-700">
                <h3 class="text-xl font-bold text-white font-orbitron mb-4"><i class="fa-solid fa-bullseye mr-2 text-red-400"></i>Tindak Lanjut</h3>
                <div id="tindak-lanjut-list" class="space-y-3">
                    @forelse($mom->actionItems as $item)
                        <div id="action-item-{{ $item->action_id }}" class="p-3 bg-gray-900 rounded-lg border border-gray-700 flex justify-between items-center">
                            <div><p class="font-semibold text-sm text-white">{{ $item->item }}</p><p class="text-xs text-gray-400">Deadline: {{ Carbon::parse($item->due)->translatedFormat('d M Y') }}</p></div>
                            <button onclick="deleteActionItem({{ $item->action_id }})" class="text-gray-500 hover:text-red-400 transition-colors"><i class="fa-solid fa-trash"></i></button>
                        </div>
                    @empty <p class="text-sm text-gray-500 italic">Tidak ada tindak lanjut.</p> @endforelse
                </div>
                <button data-modal-target="tindak-lanjut-modal" data-modal-toggle="tindak-lanjut-modal" class="mt-4 w-full flex justify-center items-center px-4 py-2 text-sm font-semibold text-white btn-neon-red rounded-lg">
                    <i class="fa-solid fa-plus mr-2"></i>Tambah Tindak Lanjut
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Tambah Tindak Lanjut (TETAP SAMA) --}}
<div id="tindak-lanjut-modal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
    <div class="relative w-full max-w-md bg-gray-800 text-gray-100 rounded-xl shadow-2xl border border-gray-700">
        <div class="flex justify-between items-center p-4 border-b border-gray-700">
            <h3 class="text-lg font-semibold text-white font-orbitron">Tambah Tindak Lanjut</h3>
            <button type="button" data-modal-hide="tindak-lanjut-modal" class="text-gray-400 bg-transparent hover:bg-gray-600 hover:text-white rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form id="tindak-lanjut-form" class="p-4 space-y-4">
            @csrf
            <input type="hidden" name="mom_id" value="{{ $mom->version_id }}">
            <div><label class="block mb-2 text-sm font-medium text-gray-300">Deskripsi Tugas</label><input type="text" name="item" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5" required></div>
            <div><label class="block mb-2 text-sm font-medium text-gray-300">Deadline</label><input type="date" name="due" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5" required></div>
            <button type="submit" class="text-white inline-flex items-center btn-neon-red font-medium rounded-lg text-sm px-5 py-2.5 text-center"><i class="fa-solid fa-plus mr-1"></i> Tambahkan</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js" referrerpolicy="origin"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
{{-- Script AJAX Anda yang sudah ada untuk tindak lanjut --}}
<script>

        tinymce.init({
            selector: '#pembahasan-viewer', // Target textarea
            skin: 'oxide-dark',            // Tema Gelap
            content_css: 'dark',
            menubar: false,                // Sembunyikan Menu
            toolbar: false,                // Sembunyikan Toolbar
            readonly: 1,                   // MODE BACA SAJA (PENTING!)
            height: 400,                   // Tinggi tetap agar rapi
            plugins: 'table autolink',     // Plugin dasar agar tabel/link ter-render
            content_style: `
                body {
                    font-family: 'Inter', sans-serif;
                    background-color: #1F2937; /* bg-gray-800 agar menyatu dengan card */
                    color: #D1D5DB; /* text-gray-300 */
                    font-size: 0.875rem;
                    padding: 1rem;
                }
                /* Styling Tabel di dalam Viewer */
                table { border-collapse: collapse; width: 100%; margin: 10px 0; }
                td, th { border: 1px solid #6B7280; padding: 8px; }
                a { color: #EF4444; text-decoration: underline; }
            `,
            setup: function (editor) {
                // Hapus border fokus biru saat diklik karena ini cuma viewer
                editor.on('init', function () {
                    editor.getBody().setAttribute('contenteditable', false);
                    // Tambahkan kelas khusus ke container agar bisa distyling CSS tambahan jika perlu
                    editor.getContainer().classList.add('tox-tinymce-readonly');
                });
            }
        });

    document.addEventListener('DOMContentLoaded', function () {
        // ... (Paste kode script JavaScript tindak lanjut Anda yang sudah diperbarui di sini) ...
        // Pastikan menggunakan versi yang me-refresh halaman setelah tambah data
        const storeActionItemUrl = "{{ route('action_items.store') }}";
        const deleteActionItemUrl = "{{ route('action_items.destroy', ':actionItem') }}";
        const listContainer = document.getElementById('tindak-lanjut-list');
        const form = document.getElementById('tindak-lanjut-form');

        const showAlert = (title, message, isError = false) => {
            Swal.fire({
                title: title, text: message, icon: isError ? 'error' : 'success',
                background: '#1F2937', color: '#F9FAFB', confirmButtonText: 'OK',
                customClass: { confirmButton: 'btn-neon-red text-white font-semibold px-6 py-2 rounded-lg' }, buttonsStyling: false
            });
        };

        window.deleteActionItem = function (actionItemId) {
            Swal.fire({
                title: 'Anda yakin?', text: "Tindak lanjut ini akan dihapus permanen!", icon: 'warning',
                showCancelButton: true, confirmButtonText: 'Ya, Hapus!', cancelButtonText: 'Batal',
                customClass: { popup: 'bg-gray-800 border-gray-700', title: 'text-white font-orbitron', htmlContainer: 'text-gray-400', confirmButton: 'btn-neon-red text-white px-6 py-2 rounded-lg', cancelButton: 'bg-gray-700 text-gray-300 px-6 py-2 rounded-lg' }, buttonsStyling: false
            }).then(async (result) => {
                if (result.isConfirmed) {
                    const url = deleteActionItemUrl.replace(':actionItem', actionItemId);
                    try {
                        const response = await fetch(url, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' } });
                        if (response.ok) { window.location.reload(); }
                        else { const data = await response.json(); showAlert('Gagal!', data.message || 'Gagal menghapus.', true); }
                    } catch (error) { showAlert('Error!', 'Terjadi kesalahan koneksi.', true); }
                }
            });
        };

        form.addEventListener('submit', async function (e) {
            e.preventDefault();
            const formData = new FormData(form);
            try {
                const response = await fetch(storeActionItemUrl, { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, body: formData });
                const data = await response.json();
                if (response.ok) { window.location.reload(); }
                else { showAlert('Gagal!', data.message || 'Gagal menambahkan.', true); }
            } catch (error) { showAlert('Error!', 'Terjadi kesalahan koneksi.', true); }
        });
    });
</script>
@endpush
