@extends('layouts.app')

@section('title', 'Review MoM | TR1 MoMatic')

@php
    // Asumsikan variabel $mom sudah di-pass dari controller
@endphp

@section('content')
<div class="pt-2">
    {{-- Header Halaman --}}
    <div class="p-6 md:p-8 rounded-xl shadow-lg bg-gray-800 border-l-4 border-red-500 mb-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold font-orbitron text-neon-red">Review MoM</h1>
                <p class="mt-1 text-gray-400 line-clamp-1" title="{{ $mom->title ?? 'Judul MoM' }}">{{ $mom->title ?? 'Judul MoM' }}</p>
            </div>
            <a href="{{ url()->previous() }}" class="flex-shrink-0 inline-flex justify-center items-center px-4 py-2 text-sm font-medium text-gray-300 bg-gray-800 border border-gray-700 rounded-lg hover:bg-gray-700">
                <i class="fa-solid fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>
    </div>

    {{-- Banner Aksi Approve & Reject --}}
    <div class="bg-yellow-900/50 border-l-4 border-yellow-500 text-yellow-300 p-4 rounded-lg mb-6 flex flex-col sm:flex-row justify-between items-center gap-4">
        <p class="text-sm font-medium text-center sm:text-left">
            <i class="fa-solid fa-info-circle mr-1"></i> MoM ini sedang menunggu persetujuan Anda.
        </p>
        <div class="flex items-center gap-2 w-full sm:w-auto flex-shrink-0">
            <button class="approve-btn w-1/2 sm:w-auto flex justify-center items-center px-4 py-2 text-sm font-semibold text-white bg-green-600 rounded-lg hover:bg-green-700">
                <i class="fa-solid fa-check mr-2"></i>Approve
            </button>
            <button data-modal-target="rejection-modal" data-modal-toggle="rejection-modal"
                    data-mom-id="{{ $mom->version_id ?? 'N/A' }}"
                    data-mom-title="{{ $mom->title ?? 'Judul MoM' }}"
                    class="reject-btn w-1/2 sm:w-auto flex justify-center items-center px-4 py-2 text-sm font-semibold text-white btn-neon-red rounded-lg">
                <i class="fa-solid fa-times mr-2"></i>Reject
            </button>
        </div>
    </div>

    {{-- Konten Detail MoM --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-gray-800 rounded-xl shadow-md p-6 border border-gray-700">
                <h3 class="text-xl font-bold text-white font-orbitron mb-4 border-b border-gray-700 pb-3">Informasi Rapat</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm text-gray-300">
                    <div><p class="text-gray-500 font-semibold">Pimpinan:</p><p>{{ $mom->pimpinan_rapat ?? 'N/A' }}</p></div>
                    <div><p class="text-gray-500 font-semibold">Notulen:</p><p>{{ $mom->notulen ?? 'N/A' }}</p></div>
                    <div class="sm:col-span-2"><p class="text-gray-500 font-semibold">Waktu:</p><p>{{ isset($mom->meeting_date) ? \Carbon\Carbon::parse($mom->meeting_date)->translatedFormat('l, d M Y') : 'N/A' }} | {{ isset($mom->start_time) ? \Carbon\Carbon::parse($mom->start_time)->format('H:i') : '' }} – {{ isset($mom->end_time) ? \Carbon\Carbon::parse($mom->end_time)->format('H:i') : '' }}</p></div>
                    <div class="sm:col-span-2"><p class="text-gray-500 font-semibold">Tempat:</p><p>{{ $mom->location ?? 'N/A' }}</p></div>
                </div>
            </div>
            <div class="bg-gray-800 rounded-xl shadow-md p-6 border border-gray-700">
                <h3 class="text-xl font-bold text-white font-orbitron mb-4 border-b border-gray-700 pb-3">Hasil Pembahasan</h3>
                <div class="prose prose-sm prose-invert max-w-none text-gray-300">{!! $mom->pembahasan ?? '<p class="italic text-gray-500">Tidak ada pembahasan.</p>' !!}</div>
            </div>
            <div class="bg-gray-800 rounded-xl shadow-md p-6 border border-gray-700">
                <h3 class="text-xl font-bold text-white font-orbitron mb-4 border-b border-gray-700 pb-3">Lampiran</h3>
                @if(isset($mom->attachments) && $mom->attachments->isNotEmpty())
                    @php $firstAttachment = $mom->attachments->first(); @endphp
                    @if($firstAttachment && str_starts_with($firstAttachment->mime_type, 'image/'))
                        <a href="{{ asset('storage/' . $firstAttachment->file_path) }}" target="_blank"><img src="{{ asset('storage/' . $firstAttachment->file_path) }}" alt="Lampiran Rapat" class="w-full rounded-lg max-w-md mb-4 border border-gray-700 hover:opacity-80 transition"></a>
                    @endif
                    <ul class="space-y-2 text-sm">
                        @foreach($mom->attachments as $attachment)
                        <li><a href="{{ asset('storage/' . $attachment->file_path) }}" target="_blank" class="text-red-400 hover:underline hover:text-red-300 flex items-center"><i class="fa-solid fa-paperclip mr-2"></i> {{ $attachment->file_name }}</a></li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-gray-500 italic">Tidak ada lampiran terlampir.</p>
                @endif
            </div>
        </div>

        <div class="lg:col-span-1 space-y-6">
            <div class="bg-gray-800 rounded-xl shadow-md p-6 border border-gray-700">
                <h3 class="text-xl font-bold text-white font-orbitron mb-4"><i class="fa-solid fa-users mr-2 text-red-400"></i>Peserta</h3>
                <ul class="space-y-2 text-sm text-gray-300 list-disc list-inside">
                    {{-- Ganti dengan loop data asli peserta Anda --}}
                    <li>Peserta 1</li>
                    <li>Peserta 2</li>
                </ul>
            </div>
            <div class="bg-gray-800 rounded-xl shadow-md p-6 border border-gray-700">
                <h3 class="text-xl font-bold text-white font-orbitron mb-4"><i class="fa-solid fa-list-check mr-2 text-red-400"></i>Agenda</h3>
                <ol class="space-y-2 text-sm text-gray-300 list-decimal list-inside">
                    @forelse($mom->agendas ?? [] as $agenda)
                        <li>{{ $agenda->item }}</li>
                    @empty
                        <span class="italic text-gray-500">Tidak ada agenda.</span>
                    @endforelse
                </ol>
            </div>
        </div>
    </div>
</div>

{{-- MODAL PENOLAKAN --}}
<div id="rejection-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-lg max-h-full">
        <div class="relative bg-gray-800 rounded-lg shadow border border-gray-700">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-600">
                <h3 class="text-lg font-semibold text-white font-orbitron">Alasan Penolakan</h3>
                <button type="button" data-modal-hide="rejection-modal" class="text-gray-400 bg-transparent hover:bg-gray-600 hover:text-white rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                    <i class="fa-solid fa-times"></i>
                </button>
            </div>
            <form id="rejection-form" class="p-4 md:p-5">
                @csrf
                <p class="text-sm text-gray-400 mb-2">Anda akan menolak MoM berjudul:</p>
                <p id="modal-mom-title" class="font-bold text-white mb-4"></p>
                <input type="hidden" id="modal-mom-id" name="mom_id">
                <div>
                    <label for="rejection-comment" class="block mb-2 text-sm font-medium text-gray-300">Komentar Revisi (Wajib)</label>
                    <textarea id="rejection-comment" name="comment" rows="4"
                        class="block p-2.5 w-full text-sm text-white bg-gray-700 rounded-lg border border-gray-600 focus:ring-red-500 focus:border-red-500"
                        placeholder="Jelaskan bagian mana yang perlu diperbaiki oleh user..." required></textarea>
                </div>
                <button type="submit" class="mt-4 text-white inline-flex items-center btn-neon-red focus:ring-4 focus:outline-none font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                    Kirim Penolakan
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const approveBtn = document.querySelector('.approve-btn');
    const rejectBtn = document.querySelector('.reject-btn');
    const rejectionForm = document.getElementById('rejection-form');

    const approveUrl = "{{ isset($mom) ? route('admin.approvals.approve', $mom->version_id) : '#' }}";
    const rejectUrl = "{{ isset($mom) ? route('admin.approvals.reject', $mom->version_id) : '#' }}";
    const momTitle = "{{ $mom->title ?? 'Judul MoM' }}";

    // Handler untuk tombol Approve
    approveBtn.addEventListener('click', () => {
        Swal.fire({
            title: 'Konfirmasi Persetujuan',
            html: `Anda yakin ingin menyetujui MoM "<strong>${momTitle}</strong>"?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Setujui!',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'bg-gray-800 rounded-2xl border border-gray-700',
                title: 'text-white font-orbitron',
                htmlContainer: 'text-gray-400',
                confirmButton: 'bg-green-600 text-white font-semibold px-6 py-2 rounded-lg hover:bg-green-700',
                cancelButton: 'bg-gray-700 text-gray-300 font-semibold px-6 py-2 rounded-lg hover:bg-gray-600 border border-gray-600'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                // Submit form untuk approve
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = approveUrl;
                form.innerHTML = '@csrf';
                document.body.appendChild(form);
                form.submit();
            }
        });
    });

    // Handler untuk tombol Reject (mengisi modal)
    rejectBtn.addEventListener('click', function() {
        const momId = this.dataset.momId;
        const mTitle = this.dataset.momTitle;
        rejectionForm.querySelector('#modal-mom-id').value = momId;
        rejectionForm.querySelector('#modal-mom-title').textContent = mTitle;
    });

    // Handler untuk submit form penolakan
    rejectionForm.addEventListener('submit', function(e) {
        e.preventDefault();
        this.action = rejectUrl;
        this.method = 'POST';
        this.submit();
    });
});
</script>
@endpush
