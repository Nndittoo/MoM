@extends('admin.layouts.app')

@section('title', 'Persetujuan MoM | TR1 MoMatic')

@push('styles')
<style>
    /* Animasi fade-in untuk kartu MoM */
    .approval-card {
        opacity: 0;
        transform: scale(0.98);
        animation: fadeInScaleUp 0.5s ease-out forwards;
    }
    @keyframes fadeInScaleUp { to { opacity: 1; transform: scale(1); } }

    /* Animasi keluar kartu */
    .fade-out {
        opacity: 0 !important;
        transform: scale(0.95) !important;
        transition: opacity 0.5s ease, transform 0.5s ease;
    }
</style>
@endpush

@section('content')
<div class="pt-2">
    {{-- Toast Notification --}}
    <div id="toast" class="hidden fixed top-24 right-5 z-50 flex items-center gap-3 px-4 py-3 rounded-xl shadow-lg bg-gray-700 border border-gray-600 text-white transition-all duration-500 opacity-0">
        <i></i><span class="text-sm"></span>
    </div>

    {{-- Header --}}
    <div class="p-6 md:p-8 rounded-xl shadow-lg bg-gray-800 border-l-4 border-red-500 mb-6">
        <h1 class="text-3xl font-bold font-orbitron text-neon-red">Persetujuan MoM</h1>
        <p class="mt-1 text-gray-400">Review dan kelola MoM yang menunggu persetujuan dari para pengguna.</p>
    </div>

    {{-- Daftar MoM --}}
    <div class="space-y-4">
        @forelse ($pendingMoms as $index => $mom)
            @php
                $momId = $mom->version_id ?? $mom->id ?? null;
                $creatorName = $mom->creator->name ?? 'N/A';
                $createdAt = $mom->created_at ? $mom->created_at->format('d M Y') : 'N/A';
            @endphp

            <div class="approval-card bg-gray-800 shadow rounded-lg p-5 border-l-4 border-yellow-500 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4"
                 style="animation-delay: {{ $index * 100 }}ms;"
                 id="mom-card-{{ $momId }}">
                <div class="flex-grow">
                    <a href="{{ $momId ? route('admin.moms.show', $momId) : '#' }}" class="font-bold text-lg text-white hover:underline hover:text-red-400 transition-colors">{{ $mom->title }}</a>
                    <p class="text-sm text-gray-400 mt-1">
                        Diajukan oleh: <span class="font-medium text-gray-300">{{ $creatorName }}</span> pada {{ $createdAt }}
                    </p>
                </div>

                <div class="flex items-center gap-2 w-full sm:w-auto flex-shrink-0">
                    <button
                        data-mom-id="{{ $momId }}"
                        data-mom-title="{{ $mom->title }}"
                        data-approve-url="{{ $momId ? route('admin.approvals.approve', ['mom' => $momId]) : '#' }}"
                        class="approve-btn w-1/2 sm:w-auto flex justify-center items-center px-4 py-2 text-sm font-semibold text-white bg-green-600 rounded-lg hover:bg-green-700 transition-transform hover:scale-105">
                        <i class="fa-solid fa-check mr-2"></i>Approve
                    </button>

                    <button
                        data-modal-target="rejection-modal" data-modal-toggle="rejection-modal"
                        data-mom-id="{{ $momId }}"
                        data-mom-title="{{ $mom->title }}"
                        data-reject-url="{{ $momId ? route('admin.approvals.reject', ['mom' => $momId]) : '#' }}"
                        class="reject-btn w-1/2 sm:w-auto flex justify-center items-center px-4 py-2 text-sm font-semibold text-white btn-neon-red rounded-lg transition-transform hover:scale-105">
                        <i class="fa-solid fa-times mr-2"></i>Reject
                    </button>
                </div>
            </div>
        @empty
            <div class="text-center py-16 bg-gray-800/50 rounded-xl border border-dashed border-gray-700">
                <i class="fa-solid fa-check-circle fa-4x text-green-500 opacity-50"></i>
                <p class="mt-4 text-lg text-gray-500">Tidak Ada MoM untuk Direview</p>
                <p class="text-sm text-gray-600">Semua MoM yang masuk sudah selesai ditindaklanjuti.</p>
            </div>
        @endforelse
    </div>
</div>

{{-- MODAL PENOLAKAN --}}
<div id="rejection-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-lg max-h-full">
        <div class="relative bg-gray-800 rounded-lg shadow border border-gray-700">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-600">
                <h3 class="text-lg font-semibold text-white font-orbitron">Alasan Penolakan</h3>
                <button type="button" data-modal-hide="rejection-modal" class="close-modal text-gray-400 bg-transparent hover:bg-gray-600 hover:text-white rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
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
                <button type="submit" id="sendReject" class="mt-4 text-white inline-flex items-center btn-neon-red focus:ring-4 focus:outline-none font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                    Kirim Penolakan
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const showToast = (message, isError = false) => {
        const toast = document.getElementById("toast");
        const iconEl = toast.querySelector('i');
        const msgEl = toast.querySelector('span');
        iconEl.className = isError ? 'fa-solid fa-circle-xmark text-red-500 text-lg' : 'fa-solid fa-circle-check text-green-500 text-lg';
        msgEl.textContent = message;
        toast.classList.remove("hidden", "opacity-0");
        toast.classList.add("opacity-100");
        setTimeout(() => {
            toast.classList.remove("opacity-100");
            toast.classList.add("opacity-0");
            setTimeout(() => toast.classList.add("hidden"), 500);
        }, 2500);
    };

    const handleAjaxAction = async (url, method, data = null) => {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const response = await fetch(url, {
            method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: data ? JSON.stringify(data) : null,
        });
        const result = await response.json();
        if (!response.ok) throw result;
        return result;
    };

    document.addEventListener('DOMContentLoaded', () => {
        const modal = document.getElementById('rejection-modal');
        const rejectionForm = document.getElementById('rejection-form');
        const approveButtons = document.querySelectorAll('.approve-btn');
        const rejectButtons = document.querySelectorAll('.reject-btn');
        const closeButtons = document.querySelectorAll('.close-modal');

        // Memastikan hilangkan overlay hitam setelah modal ditutup manual
        const clearBodyLock = () => document.body.classList.remove('overflow-hidden');

        closeButtons.forEach(btn => {
            btn.addEventListener('click', clearBodyLock);
        });

        approveButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                const url = btn.dataset.approveUrl;
                const momTitle = btn.dataset.momTitle;
                const momId = btn.dataset.momId;

                Swal.fire({
                    title: 'Konfirmasi Persetujuan',
                    html: `Anda yakin ingin menyetujui MoM berjudul "<strong>${momTitle}</strong>"?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Setujui!',
                    cancelButtonText: 'Batal',
                    customClass: {
                        popup: 'bg-gray-800 rounded-2xl border border-gray-700',
                        title: 'text-white font-orbitron',
                        htmlContainer: 'text-gray-400',
                        confirmButton: 'bg-green-600 text-white font-semibold px-6 py-2 mr-4 rounded-lg hover:bg-green-700',
                        cancelButton: 'bg-gray-700 text-gray-300 font-semibold px-6 py-2 rounded-lg hover:bg-gray-600 border border-gray-600'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        handleAjaxAction(url, 'POST')
                            .then(response => {
                                showToast(response.message || 'MoM berhasil disetujui.');
                                const card = document.getElementById(`mom-card-${momId}`);
                                if (card) {
                                    card.classList.add('fade-out');
                                    setTimeout(() => card.remove(), 500);
                                }
                            })
                            .catch(() => showToast('Gagal menyetujui MoM.', true));
                    }
                }).then(() =>{
                    location.reload();
                });
            });
        });

        rejectButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                modal.querySelector('#modal-mom-id').value = btn.dataset.momId;
                modal.querySelector('#modal-mom-title').textContent = btn.dataset.momTitle;
                rejectionForm.setAttribute('data-url', btn.dataset.rejectUrl);
            });
        });

        rejectionForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const url = rejectionForm.getAttribute('data-url');
    const momId = modal.querySelector('#modal-mom-id').value;
    const comment = document.getElementById('rejection-comment').value.trim();

    if (!comment) {
        return Swal.fire({
            icon: 'warning',
            title: 'Komentar Kosong!',
            text: 'Harap isi komentar revisi sebelum menolak MoM.',
            confirmButtonColor: '#facc15', // kuning
        });
    }

    try {
        const response = await handleAjaxAction(url, 'POST', { comment });

        await Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: response.message || 'MoM berhasil ditolak.',
            showConfirmButton: false,
            timer: 3000,
            background: '#1f2937', // bg dark gray
            color: '#f3f4f6', // teks abu muda
            iconColor: '#facc15', // kuning sesuai tema
        });

        // Tutup modal dan hilangkan overlay
        modal.classList.add('hidden');
        clearBodyLock();

        // Hapus kartu MoM
        const card = document.getElementById(`mom-card-${momId}`);
        if (card) {
            card.classList.add('fade-out');
            setTimeout(() => card.remove(), 400);
        }

        // Reset textarea
        document.getElementById('rejection-comment').value = '';

        // Refresh halaman (tetap di posisi yang sama)
        const scrollY = window.scrollY;
        location.reload();
        window.scrollTo(0, scrollY);

    } catch (error) {
        console.error('Gagal menolak MoM:', error);
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: 'Terjadi kesalahan saat menolak MoM. Silakan coba lagi.',
            confirmButtonColor: '#ef4444', // merah
            background: '#1f2937',
            color: '#f3f4f6',
        });
    }
});

    });
</script>
@endpush
