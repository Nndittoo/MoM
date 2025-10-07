@extends('admin.layouts.app')

@section('title', 'Persetujuan MoM')

@section('content')

<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-text-primary dark:text-dark-text-primary">Persetujuan MoM</h1>
            <p class="mt-1 text-text-secondary dark:text-dark-text-secondary">Review dan kelola MoM yang menunggu persetujuan.</p>
        </div>
    </div>

    {{-- Daftar MoM Menunggu Persetujuan --}}
    <div class="space-y-4">
        @forelse ($pendingMoms as $mom)
        <div class="bg-component-bg dark:bg-dark-component-bg shadow rounded-lg p-5 border-l-4 border-yellow-500">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div class="flex-grow">
                    <a href="{{ url('/moms/' . $mom->version_id) }}" class="font-bold text-lg text-text-primary dark:text-dark-text-primary hover:underline">{{ $mom->title }}</a>
                    <p class="text-sm text-text-secondary dark:text-dark-text-secondary mt-1">
                        Diajukan oleh: <span class="font-medium">{{ $mom->creator->name ?? 'N/A' }}</span> pada {{ $mom->created_at->format('d M Y') }}
                    </p>
                </div>

                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <button data-mom-id="{{ $mom->version_id }}"
                        data-mom-title="{{ $mom->title }}"
                        data-approve-url="{{ $mom->version_id ? route('admin.approvals.approve', ['mom' => $mom->version_id]) : '#' }}"
                        class="approve-btn w-1/2 sm:w-auto flex justify-center items-center px-4 py-2 text-sm font-semibold text-white bg-green-600 rounded-lg hover:bg-green-700">
                        <i class="fa-solid fa-check mr-2"></i>Approve
                    </button>

                    <button data-modal-target="rejection-modal" data-modal-toggle="rejection-modal"
                        data-mom-id="{{ $mom->version_id }}"
                        data-mom-title="{{ $mom->title }}"
                        data-reject-url="{{ $mom->version_id ? route('admin.approvals.reject', ['mom' => $mom->version_id]) : '#' }}"
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

{{-- Modal Form Penolakan --}}
<div id="rejection-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-lg max-h-full">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Alasan Penolakan</h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="rejection-modal">
                    <i class="fa-solid fa-times"></i><span class="sr-only">Close modal</span>
                </button>
            </div>
            <form id="rejection-form" class="p-4 md:p-5">
                @csrf
                <p class="text-sm text-gray-600 dark:text-gray-300 mb-2">Anda menolak MoM berjudul:</p>
                <p id="modal-mom-title" class="font-bold text-gray-800 dark:text-white mb-4"></p>

                <input type="hidden" id="modal-mom-id" name="mom_id">

                <div>
                    <label for="rejection-comment" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Komentar Revisi</label>
                    <textarea id="rejection-comment" name="comment" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary focus:border-primary dark:bg-gray-600 dark:border-gray-500" placeholder="Jelaskan bagian mana yang perlu diperbaiki oleh user..." required></textarea>
                </div>

                <button type="submit" class="mt-4 text-white inline-flex items-center bg-red-600 hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:focus:ring-red-800">
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
    const rejectionModal = document.getElementById('rejection-modal');
    const csrfToken = document.querySelector('meta[name="csrf-token"]') 
        ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') 
        : '';

    const handleAjaxAction = (url, method, body, successCallback, errorTitle) => {
        if (url === '#') {
            console.error(`Gagal ${errorTitle.toLowerCase()}: URL tidak valid. MoM mungkin tidak memiliki ID yang valid.`);
            alert(`Gagal ${errorTitle.toLowerCase()}. Data MoM tidak lengkap.`);
            return;
        }

        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: body ? JSON.stringify(body) : null
        })
        .then(response => {
            if (!response.ok) {
                return response.text().then(text => {
                    throw new Error(text || `HTTP error! Status: ${response.status}`);
                });
            }
            return response.json();
        })
        .then(data => {
            alert(data.message);
            successCallback();
        })
        .catch(error => {
            console.error(`${errorTitle}:`, error);
            alert(`Gagal ${errorTitle.toLowerCase()}. Pastikan CSRF token sudah benar dan rute POST dapat dijangkau. Lihat console untuk detail error.`);
        });
    };

    // Tombol reject -> buka modal
    document.querySelectorAll('.reject-btn').forEach(button => {
        button.addEventListener('click', function() {
            const momId = this.dataset.momId;
            const momTitle = this.dataset.momTitle;
            const rejectUrl = this.dataset.rejectUrl;

            document.getElementById('rejection-form').dataset.url = rejectUrl;
            rejectionModal.querySelector('#modal-mom-id').value = momId;
            rejectionModal.querySelector('#modal-mom-title').textContent = momTitle;
        });
    });

    // Submit form penolakan
    document.getElementById('rejection-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const form = this;
        const momId = form.querySelector('#modal-mom-id').value;
        const comment = form.querySelector('#rejection-comment').value;
        const url = form.dataset.url;

        const successCallback = () => {
            const modalElement = document.getElementById('rejection-modal');
            try {
                const instance = window.Flowbite?.getInstance?.('rejection-modal');
                if (instance) {
                    instance.hide();
                } else {
                    modalElement.classList.add('hidden');
                }
            } catch (e) {
                modalElement.classList.add('hidden');
            }

            // Pastikan body bisa discroll lagi
            document.body.classList.remove('overflow-hidden');

            // Hapus backdrop setelah delay
            setTimeout(() => {
                document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
            }, 300);

            form.reset();

            // Hapus kartu MoM
            const card = document.querySelector(`.reject-btn[data-mom-id="${momId}"]`).closest('.bg-component-bg');
            if (card) {
                card.style.transition = 'opacity 0.5s ease';
                card.style.opacity = '0';
                setTimeout(() => card.remove(), 500);
            }
        };

        handleAjaxAction(url, 'POST', { comment: comment }, successCallback, 'menolak MoM');
    });

    // Tombol approve
    document.querySelectorAll('.approve-btn').forEach(button => {
        button.addEventListener('click', function() {
            const momId = this.dataset.momId;
            const card = this.closest('.bg-component-bg');
            const momTitle = card.querySelector('a').textContent;
            const url = this.dataset.approveUrl;

            if (confirm(`Apakah Anda yakin ingin menyetujui MoM "${momTitle}"?`)) {
                const successCallback = () => {
                    card.style.transition = 'opacity 0.5s ease';
                    card.style.opacity = '0';
                    setTimeout(() => card.remove(), 500);
                };

                handleAjaxAction(url, 'POST', null, successCallback, 'menyetujui MoM');
            }
        });
    });

    // Jaga-jaga: hapus backdrop tertinggal
    setInterval(() => {
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        document.body.classList.remove('overflow-hidden');
    }, 1000);
});
</script>
@endpush
