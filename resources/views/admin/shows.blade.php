@extends('admin.layouts.app')

@section('title', 'Review MoM | MoM Telkom')

@section('content')
<div class="p-4 rounded-lg mt-3">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center sm:justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-text-primary dark:text-dark-text-primary">Review Minute of Meeting</h1>
            <p class="mt-1 text-text-secondary dark:text-dark-text-secondary">EVALUASI PROGRESS PROJECT OTN TR1</p>
        </div>
        <div class="flex space-x-2 mt-4 sm:mt-0 w-full sm:w-auto">
            <a href="{{ url()->previous() }}" class="flex-1 sm:flex-initial inline-flex justify-center items-center px-4 py-2 text-sm font-medium text-text-secondary bg-component-bg border border-border-light rounded-lg hover:bg-body-bg dark:bg-dark-component-bg dark:text-dark-text-secondary dark:border-border-dark dark:hover:bg-dark-body-bg">
                <i class="fa-solid fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>
    </div>

    {{-- Tombol Aksi Approve & Reject --}}
    <div class="bg-yellow-100 dark:bg-yellow-500/20 border-l-4 border-yellow-500 text-yellow-700 dark:text-yellow-300 p-4 rounded-lg mb-6 flex flex-col sm:flex-row justify-between items-center gap-4">
        <p class="text-sm font-medium text-center sm:text-left">MoM ini sedang menunggu persetujuan Anda. Silakan review konten di bawah sebelum mengambil tindakan.</p>
        <div class="flex items-center gap-2 w-full sm:w-auto flex-shrink-0">
            <button class="approve-btn w-1/2 sm:w-auto flex justify-center items-center px-4 py-2 text-sm font-semibold text-white bg-green-600 rounded-lg hover:bg-green-700">
                <i class="fa-solid fa-check mr-2"></i>Approve
            </button>
            <button data-modal-target="rejection-modal" data-modal-toggle="rejection-modal" data-mom-id="101" data-mom-title="EVALUASI PROGRESS PROJECT OTN TR1" class="reject-btn w-1/2 sm:w-auto flex justify-center items-center px-4 py-2 text-sm font-semibold text-white bg-red-600 rounded-lg hover:bg-red-700">
                <i class="fa-solid fa-times mr-2"></i>Reject
            </button>
        </div>
    </div>

    {{-- Konten Detail dalam Bentuk Card --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            {{-- Card Informasi Rapat --}}
            <div class="bg-component-bg dark:bg-dark-component-bg rounded-lg shadow-md p-6">
                <h3 class="text-xl font-bold text-text-primary dark:text-dark-text-primary mb-4 border-b dark:border-border-dark pb-3">Informasi Rapat</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div><p class="text-text-secondary"><strong>Pimpinan:</strong></p><p>PED TR1</p></div>
                    <div><p class="text-text-secondary"><strong>Notulen:</strong></p><p>M. Hanif A</p></div>
                    <div><p class="text-text-secondary"><strong>Waktu:</strong></p><p>Senin, 15 Sep 2025 | 09.00 – 10.50</p></div>
                    <div><p class="text-text-secondary"><strong>Tempat:</strong></p><p>Meeting Zoom</p></div>
                </div>
            </div>

            {{-- Card Hasil Pembahasan --}}
            <div class="bg-component-bg dark:bg-dark-component-bg rounded-lg shadow-md p-6">
                 <h3 class="text-xl font-bold text-text-primary dark:text-dark-text-primary mb-4 border-b dark:border-border-dark pb-3">Hasil Pembahasan</h3>
                <div class="prose dark:prose-invert max-w-none text-sm">
                    <pre class="font-sans whitespace-pre-wrap bg-transparent p-0">
1. rap058
   - osp berstatus drop dan perlu survei ulang terkait perizinan warga.
   - remark: request mengubah lokasi ke sidamanik desa sibontuan.
2. kis768
   - osp berstatus drop dan perlu survei ulang.
   - status osp matdel.
</pre>
                </div>
            </div>
            <div class="bg-component-bg dark:bg-dark-component-bg rounded-lg shadow-md p-6">
                <h3 class="text-xl font-bold text-text-primary dark:text-dark-text-primary mb-4 border-b dark:border-border-dark pb-3">Lampiran</h3>
                <img src="{{ asset('img/lampiran.png') }}" alt="Lampiran Rapat" class="w-full rounded-lg">
            </div>
        </div>

        {{-- Kolom Kanan --}}
        <div class="space-y-6">
            <div class="bg-component-bg dark:bg-dark-component-bg rounded-lg shadow-md p-6">
                <h3 class="text-xl font-bold text-text-primary dark:text-dark-text-primary mb-4"><i class="fa-solid fa-users mr-2"></i>Peserta</h3>
                <ul class="space-y-2 text-sm list-disc list-inside">
                    <li>Bg @satriasitorus</li>
                    <li>Anggota Tim A</li>
                    <li>Anggota Tim B</li>
                </ul>
            </div>
            <div class="bg-component-bg dark:bg-dark-component-bg rounded-lg shadow-md p-6">
                <h3 class="text-xl font-bold text-text-primary dark:text-dark-text-primary mb-4"><i class="fa-solid fa-list-check mr-2"></i>Agenda</h3>
                <ul class="space-y-2 text-sm list-disc list-inside">
                    <li>Evaluasi Progress Project OTN TR1</li>
                </ul>
            </div>
        </div>
    </div>
</div>

{{-- Modal Form Penolakan (Sama seperti halaman approvals.blade.php) --}}
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
                <p id="modal-mom-title" class="font-bold text-gray-800 dark:text-white mb-4"></p>
                <input type="hidden" id="modal-mom-id" name="mom_id">
                <div>
                    <label for="rejection-comment" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Komentar Revisi</label>
                    <textarea id="rejection-comment" name="comment" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary focus:border-primary dark:bg-gray-600 dark:border-gray-500" placeholder="Jelaskan bagian mana yang perlu diperbaiki oleh user..." required></textarea>
                </div>
                <button type="submit" class="mt-4 text-white inline-flex items-center bg-red-600 hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:focus:ring-red-800">Kirim Penolakan</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Logika untuk mengisi data ke modal penolakan
    const rejectionModal = document.getElementById('rejection-modal');
    document.querySelector('.reject-btn').addEventListener('click', function() {
        const momId = this.dataset.momId;
        const momTitle = this.dataset.momTitle;
        rejectionModal.querySelector('#modal-mom-id').value = momId;
        rejectionModal.querySelector('#modal-mom-title').textContent = `Anda akan menolak MoM: "${momTitle}"`;
    });

    // Logika untuk submit form penolakan (simulasi)
    document.getElementById('rejection-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const momId = this.querySelector('#modal-mom-id').value;
        const comment = this.querySelector('#rejection-comment').value;

        console.log(`Menolak MoM ID: ${momId} dengan komentar: "${comment}"`);
        alert(`MoM #${momId} telah ditolak dengan komentar revisi.`);
        const modal = Flowbite.getInstance('rejection-modal');
        modal.hide();
        // Redirect atau update UI setelah reject
    });

    // Logika untuk tombol approve (simulasi)
    document.querySelector('.approve-btn').addEventListener('click', function() {
        if (confirm('Apakah Anda yakin ingin menyetujui MoM ini?')) {
            console.log('Menyetujui MoM...');
            alert('MoM telah disetujui.');
            // Redirect atau update UI setelah approve
        }
    });
});
</script>
@endpush
