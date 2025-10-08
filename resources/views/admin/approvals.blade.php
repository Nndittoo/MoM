@extends('admin.layouts.app')

@section('title', 'Persetujuan MoM')

@section('content')

{{-- TOAST NOTIFIKASI --}}
<div id="toast" class="hidden fixed top-5 right-5 z-50 items-center gap-3 px-4 py-3 rounded-xl shadow-lg bg-white dark:bg-dark-component-bg text-text-primary dark:text-dark-text-primary transition-all duration-500 opacity-0">
    <div class="flex-shrink-0"><i class="fa-solid fa-circle-check text-green-500 text-lg"></i></div>
    <div class="text-sm font-medium" id="toast-message">Notifikasi</div>
</div>

<div class="space-y-6">
    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-text-primary dark:text-dark-text-primary">Persetujuan MoM</h1>
            <p class="mt-1 text-text-secondary dark:text-dark-text-secondary">Review dan kelola MoM yang menunggu persetujuan.</p>
        </div>
    </div>

    {{-- DAFTAR MOM --}}
    <div class="space-y-4">
        @php $pendingMoms = $pendingMoms ?? []; @endphp

        @forelse ($pendingMoms as $mom)
        @php
            $momId = $mom->version_id ?? $mom->id ?? null;
            $creatorName = $mom->creator->name ?? 'N/A';
            $createdAt = $mom->created_at ? $mom->created_at->format('d M Y') : 'N/A';
        @endphp

        <div class="bg-component-bg dark:bg-dark-component-bg shadow rounded-lg p-5 border-l-4 border-yellow-500">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div class="flex-grow">
                    <a href="{{ url('/moms/' . $momId) }}" class="font-bold text-lg text-text-primary dark:text-dark-text-primary hover:underline">{{ $mom->title }}</a>
                    <p class="text-sm text-text-secondary dark:text-dark-text-secondary mt-1">
                        Diajukan oleh: <span class="font-medium">{{ $creatorName }}</span> pada {{ $createdAt }}
                    </p>
                </div>

                <div class="flex items-center gap-2 w-full sm:w-auto">
                    {{-- APPROVE --}}
                    <button
                        data-mom-id="{{ $momId }}"
                        data-mom-title="{{ $mom->title }}"
                        data-approve-url="{{ $momId ? route('admin.approvals.approve', ['mom' => $momId]) : '#' }}"
                        class="approve-btn w-1/2 sm:w-auto flex justify-center items-center px-4 py-2 text-sm font-semibold text-white bg-green-600 rounded-lg hover:bg-green-700">
                        <i class="fa-solid fa-check mr-2"></i>Approve
                    </button>

                    {{-- REJECT --}}
                    <button
                        data-modal-target="rejection-modal" data-modal-toggle="rejection-modal"
                        data-mom-id="{{ $momId }}"
                        data-mom-title="{{ $mom->title }}"
                        data-reject-url="{{ $momId ? route('admin.approvals.reject', ['mom' => $momId]) : '#' }}"
                        class="reject-btn w-1/2 sm:w-auto flex justify-center items-center px-4 py-2 text-sm font-semibold text-white bg-red-600 rounded-lg hover:bg-red-700">
                        <i class="fa-solid fa-times mr-2"></i>Reject
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-10 bg-component-bg dark:bg-dark-component-bg rounded-lg shadow-md">
            <i class="fa-solid fa-check-circle text-4xl text-green-500"></i>
            <p class="mt-4 text-lg font-semibold text-text-primary dark:text-dark-text-primary">Tidak Ada MoM untuk Direview</p>
            <p class="text-sm text-text-secondary dark:text-dark-text-secondary">Semua MoM yang masuk sudah selesai ditindaklanjuti.</p>
        </div>
        @endforelse
    </div>
</div>

{{-- MODAL PENOLAKAN --}}
<div id="rejection-modal" tabindex="-1" aria-hidden="true"
    class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-lg max-h-full">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Alasan Penolakan</h3>
                <button type="button" data-modal-hide="rejection-modal"
                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
                    <i class="fa-solid fa-times"></i>
                </button>
            </div>
            <form id="rejection-form" class="p-4 md:p-5">
                @csrf
                <p class="text-sm text-gray-600 dark:text-gray-300 mb-2">Anda menolak MoM berjudul:</p>
                <p id="modal-mom-title" class="font-bold text-gray-800 dark:text-white mb-4"></p>

                <input type="hidden" id="modal-mom-id" name="mom_id">

                <div>
                    <label for="rejection-comment" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Komentar Revisi</label>
                    <textarea id="rejection-comment" name="comment" rows="4"
                        class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary focus:border-primary dark:bg-gray-600 dark:border-gray-500"
                        placeholder="Jelaskan bagian mana yang perlu diperbaiki oleh user..." required></textarea>
                </div>

                <button type="submit"
                    class="mt-4 text-white inline-flex items-center bg-red-600 hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:focus:ring-red-800">
                    Kirim Penolakan
                </button>
            </form>
        </div>
    </div>
</div>
@endsection



@push('scripts')
<script>
/* TOAST */
const showToast = (message, isError = false) => {
    const toast = document.getElementById("toast");
    const icon = toast.querySelector('i');
    const msg = document.getElementById("toast-message");

    icon.className = isError
        ? 'fa-solid fa-circle-xmark text-red-500 text-lg'
        : 'fa-solid fa-circle-check text-green-500 text-lg';
    msg.textContent = message;

    toast.classList.remove("hidden", "opacity-0");
    toast.classList.add("opacity-100");

    setTimeout(() => {
        toast.classList.remove("opacity-100");
        toast.classList.add("opacity-0");
        setTimeout(() => toast.classList.add("hidden"), 500);
    }, 3000);
};

/* AJAX HANDLER */
const handleAjaxAction = async (url, method, data = null, successCallback = () => {}, actionName = 'aksi') => {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content
        || document.querySelector('input[name="_token"]')?.value || '';

    try {
        const response = await fetch(url, {
            method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: data ? JSON.stringify(data) : null,
        });

        const result = await response.json();

        if (response.ok) {
            showToast(`Berhasil ${actionName} diproses.`);
            successCallback();
        } else {
            const msg = result.message || `Gagal ${actionName}.`;
            showToast(msg, true);
            console.error('AJAX Error:', result);
        }
    } catch (err) {
        showToast(`Gagal ${actionName}. Periksa koneksi Anda.`, true);
        console.error(err);
    }
};

/* CLOSE MODAL (VERSI FIX BACKDROP) */
const closeRejectionModal = () => {
    const modal = document.getElementById('rejection-modal');

    // Tutup modal
    modal.classList.add('hidden');
    modal.setAttribute('aria-hidden', 'true');

    // ====== HAPUS SEMUA BACKDROP ======
    const backdrops = [
        '.modal-backdrop',
        '.fixed.inset-0.bg-gray-900',
        '.bg-gray-900.bg-opacity-50',
        '.bg-opacity-50',
        '.dark\\:bg-opacity-80',
        '.overflow-y-auto > div.fixed.inset-0',
        '[data-modal-backdrop]'
    ];
    backdrops.forEach(selector => {
        document.querySelectorAll(selector).forEach(el => el.remove());
    });

    // ====== RESET BODY STYLE ======
    document.body.classList.remove('modal-open', 'overflow-hidden');
    document.body.style.overflow = 'auto';
    document.body.style.pointerEvents = 'auto';
    document.body.removeAttribute('data-modal-open');
};

/* EVENT LISTENER */
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('rejection-modal');

    // Buka modal penolakan
    document.querySelectorAll('.reject-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            modal.querySelector('#modal-mom-id').value = btn.dataset.momId;
            modal.querySelector('#modal-mom-title').textContent = btn.dataset.momTitle;
            modal.querySelector('#rejection-form').dataset.url = btn.dataset.rejectUrl;
        });
    });

    // Tutup modal dengan tombol X
    document.querySelector('[data-modal-hide="rejection-modal"]').addEventListener('click', closeRejectionModal);

    // Submit penolakan
    document.getElementById('rejection-form').addEventListener('submit', e => {
        e.preventDefault();
        const form = e.target;
        const momId = form.querySelector('#modal-mom-id').value;
        const comment = form.querySelector('#rejection-comment').value;
        const url = form.dataset.url;

        // ========================================================
        // PERUBAHAN DI SINI
        // ========================================================
        const success = () => {
            // Muat ulang halaman untuk menampilkan daftar MoM yang terbaru
            window.location.reload();
        };

        handleAjaxAction(url, 'POST', { comment }, success, 'menolak MoM');
    });

    // Approve MoM
    document.querySelectorAll('.approve-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const url = btn.dataset.approveUrl;
            const momTitle = btn.dataset.momTitle;
            const card = btn.closest('.bg-component-bg');
            if (confirm(`Apakah Anda yakin ingin menyetujui MoM "${momTitle}"?`)) {
                const success = () => {
                    card.style.transition = 'opacity 0.5s ease';
                    card.style.opacity = '0';
                    setTimeout(() => card.remove(), 500);
                };
                handleAjaxAction(url, 'POST', null, success, 'menyetujui MoM');
            }
        });
    });
});
</script>
@endpush
