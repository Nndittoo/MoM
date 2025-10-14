@extends('admin.layouts.app')

@section('title', 'Manajemen Tugas | MoM Telkom')

@push('styles')
<style>
    /* Animasi fade-in untuk kartu tugas */
    .task-card {
        opacity: 0;
        transform: translateY(15px);
        animation: fadeInSlideUp 0.5s ease-out forwards;
    }
    @keyframes fadeInSlideUp { to { opacity: 1; transform: translateY(0); } }
</style>
@endpush

@section('content')
<div class="pt-2">
    <div class="space-y-6">
        {{-- Header Halaman --}}
        <div class="p-6 md:p-8 rounded-xl shadow-lg bg-gray-800 border-l-4 border-red-500">
            <h1 class="text-3xl font-bold font-orbitron text-neon-red">Manajemen Tugas</h1>
            <p class="mt-1 text-gray-400">Pantau dan kelola semua tugas dari berbagai MoM dalam satu tampilan.</p>
        </div>

        {{-- Pesan Sukses (jika ada) --}}
        @if(session('success'))
        <div class="p-4 bg-green-900/50 border-l-4 border-green-500 text-green-300 rounded-lg">
            <p class="font-medium">{{ session('success') }}</p>
        </div>
        @endif

        {{-- Kartu Statistik --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-gray-800 rounded-xl shadow-md p-4 border border-gray-700">
                <div class="flex items-center justify-between"><p class="text-sm text-gray-400">Total Tasks</p><i class="fa-solid fa-tasks fa-2x text-blue-500 opacity-20"></i></div>
                <p class="text-2xl font-bold text-blue-400 mt-1">{{ $stats['total'] }}</p>
            </div>
            <div class="bg-gray-800 rounded-xl shadow-md p-4 border border-gray-700">
                <div class="flex items-center justify-between"><p class="text-sm text-gray-400">On Going</p><i class="fa-solid fa-spinner fa-2x text-yellow-500 opacity-20"></i></div>
                <p class="text-2xl font-bold text-yellow-400 mt-1">{{ $stats['mendatang'] }}</p>
            </div>
            <div class="bg-gray-800 rounded-xl shadow-md p-4 border border-gray-700">
                <div class="flex items-center justify-between"><p class="text-sm text-gray-400">Completed</p><i class="fa-solid fa-check-circle fa-2x text-green-500 opacity-20"></i></div>
                <p class="text-2xl font-bold text-green-400 mt-1">{{ $stats['selesai'] }}</p>
            </div>
            <div class="bg-gray-800 rounded-xl shadow-md p-4 border border-gray-700">
                <div class="flex items-center justify-between"><p class="text-sm text-gray-400">Overdue</p><i class="fa-solid fa-exclamation-triangle fa-2x text-red-500 opacity-20"></i></div>
                <p class="text-2xl font-bold text-red-400 mt-1">{{ $stats['overdue'] }}</p>
            </div>
        </div>

        {{-- Filter dan Search --}}
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 p-4 rounded-xl shadow-lg border border-gray-700 bg-gray-800">
            <div class="relative w-full sm:w-80">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none"><i class="fa-solid fa-magnifying-glass text-gray-400"></i></div>
                <input type="text" id="task-search" placeholder="Cari tugas atau MoM..." class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full pl-10 p-2.5">
            </div>
            <div class="flex gap-3 w-full sm:w-auto">
                <select id="status-filter" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full sm:w-48 p-2.5">
                    <option value="all">Semua Status</option>
                    <option value="mendatang">On Going</option>
                    <option value="selesai">Done</option>
                    <option value="overdue">Overdue</option> {{-- Filter baru --}}
                </select>
                <button id="reset-filter" class="px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition" title="Reset Filter">
                    <i class="fa-solid fa-rotate-right"></i>
                </button>
            </div>
        </div>

        {{-- Daftar Tugas --}}
        <div id="tasks-container" class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @forelse($tasks as $index => $task)
                @php
                    $deadline = \Carbon\Carbon::parse($task->due);
                    $today = now()->startOfDay();
                    $deadlineDate = $deadline->startOfDay();

                    $isOverdue = $deadlineDate->lt($today) && $task->status == 'mendatang';
                    $isDueSoon = !$isOverdue && $deadlineDate->gte($today) && $deadlineDate->diffInDays($today) < 3 && $task->status == 'mendatang';
                    $borderColor = $isOverdue ? 'border-red-500' : ($isDueSoon ? 'border-yellow-500' : 'border-gray-700');
                @endphp
                <div class="task-card relative group bg-gray-800 rounded-xl border-l-4 {{ $borderColor }} shadow-md hover:shadow-2xl hover:shadow-red-500/10 transition-all duration-300 hover:-translate-y-1 overflow-hidden"
                     style="animation-delay: {{ $index * 50 }}ms;"
                     data-task-id="{{ $task->action_id }}" data-task-title="{{ strtolower($task->item) }}" data-mom-title="{{ strtolower($task->mom->title ?? '') }}"
                     data-status="{{ $isOverdue ? 'overdue' : $task->status }}">
                    <div class="p-5">
                        <div class="flex justify-between items-start">
                            <h2 class="text-lg font-semibold text-white group-hover:text-red-400 transition-colors duration-300">{{ $task->item }}</h2>
                            <a href="{{ route('admin.moms.show', $task->mom_id) }}" class="text-xs font-medium text-red-400 hover:underline whitespace-nowrap ml-4">
                                Lihat MoM <i class="fa-solid fa-arrow-right fa-xs"></i>
                            </a>
                        </div>

                        {{-- Card Info --}}
                        <div class="flex flex-col space-y-2 mt-3 text-sm text-gray-400">
                            <span title="Asal MoM" class="flex items-center">
                                <i class="fa-solid fa-file-lines w-4 mr-2 text-gray-500"></i>
                                {{ \Str::limit($task->mom->title ?? 'N/A', 40) }}
                            </span>
                            <span title="Deadline"
                                class="flex items-center {{ $isOverdue ? 'text-red-500 font-semibold' : ($isDueSoon ? 'text-yellow-500 font-semibold' : '') }}">
                                <i class="fa-solid fa-calendar-day w-4 mr-2 text-gray-500"></i>
                                {{ $deadline->translatedFormat('d F Y') }}
                                @if($isOverdue)
                                    @php
                                        $daysOverdue = now()->startOfDay()->diffInDays($deadline);
                                    @endphp
                                    <span class="ml-2 px-2 py-0.5 text-xs bg-red-100 text-red-700 rounded-full dark:bg-red-900 dark:text-red-300">
                                        Terlambat {{ $daysOverdue }} hari
                                    </span>
                                @elseif($task->status == 'mendatang')
                                    @php
                                        $daysRemaining = now()->startOfDay()->diffInDays($deadline);
                                    @endphp
                                    @if($daysRemaining == 0)
                                        <span class="ml-2 px-2 py-0.5 text-xs bg-red-100 text-red-700 rounded-full dark:bg-red-900 dark:text-red-300">
                                            Hari ini
                                        </span>
                                    @else
                                        <span class="ml-2 px-2 py-0.5 text-xs {{ $isDueSoon ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300' : 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300' }}">
                                            {{ $daysRemaining }} hari lagi
                                        </span>
                                    @endif
                                @endif
                            </span>
                        </div>
                    </div>
                    <div class="px-5 py-3 bg-gray-900/50 border-t border-gray-700 flex items-center justify-between">
                        <div id="status-display-{{ $task->action_id }}" class="flex items-center gap-3">
                            @if($task->status == 'selesai')
                                <span class="status-badge flex items-center gap-1 text-xs font-semibold px-3 py-1 rounded-full bg-green-900/50 text-green-400"><i class="fa-solid fa-circle-check"></i> Done</span>
                            @elseif($isOverdue)
                                <span class="status-badge flex items-center gap-1 text-xs font-semibold px-3 py-1 rounded-full bg-red-900/50 text-red-400"><i class="fa-solid fa-triangle-exclamation"></i> Overdue</span>
                            @else
                                <span class="status-badge flex items-center gap-1 text-xs font-semibold px-3 py-1 rounded-full bg-yellow-900/50 text-yellow-400"><i class="fa-solid fa-spinner"></i> On Going</span>
                            @endif
                        </div>
                        <button class="change-status-btn text-xs font-medium text-blue-400 hover:text-blue-300 transition-colors" data-task-id="{{ $task->action_id }}"><i class="fa-solid fa-pen-to-square mr-1"></i>Ubah</button>
                        <form id="status-form-{{ $task->action_id }}" class="hidden items-center gap-2 w-full sm:w-auto status-form" data-task-id="{{ $task->action_id }}">
                            @csrf
                            <select class="status-select bg-gray-700 border border-gray-600 text-white text-xs rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2">
                                <option value="mendatang" {{ $task->status == 'mendatang' ? 'selected' : '' }}>On Going</option>
                                <option value="selesai" {{ $task->status == 'selesai' ? 'selected' : '' }}>Done</option>
                                <option value="terlambat" {{ $task->status == 'terlambat' ? 'selected' : '' }}>Overdue</option>
                            </select>
                            <button type="submit" class="save-button px-3 py-2 text-xs font-medium text-center text-white bg-blue-600 rounded-lg hover:bg-blue-700"><i class="fa-solid fa-check"></i></button>
                            <button type="button" class="cancel-button px-3 py-2 text-xs font-medium text-center text-gray-300 bg-gray-600 rounded-lg hover:bg-gray-500"><i class="fa-solid fa-times"></i></button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-16 bg-gray-800/50 rounded-xl border border-dashed border-gray-700">
                    <i class="fa-solid fa-inbox text-6xl text-gray-700 mb-4"></i>
                    <p class="text-gray-500">Tidak ada tugas ditemukan</p>
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $tasks->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Change status button
    document.addEventListener('click', function(e) {
        if (e.target.closest('.change-status-btn')) {
            const button = e.target.closest('.change-status-btn');
            const taskId = button.dataset.taskId;
            document.getElementById(`status-display-${taskId}`).classList.add('hidden');
            const form = document.getElementById(`status-form-${taskId}`);
            form.classList.remove('hidden');
            form.classList.add('flex');
        }
    });

    // Cancel button
    document.addEventListener('click', function(e) {
        if (e.target.closest('.cancel-button')) {
            const button = e.target.closest('.cancel-button');
            const form = button.closest('form');
            const taskId = form.dataset.taskId;

            form.classList.add('hidden');
            form.classList.remove('flex');
            document.getElementById(`status-display-${taskId}`).classList.remove('hidden');
        }
    });

    // Submit status form
    document.addEventListener('submit', function(e) {
        if (e.target.classList.contains('status-form')) {
            e.preventDefault();

            const form = e.target;
            const taskId = form.dataset.taskId;
            const newStatus = form.querySelector('.status-select').value;
            const statusDisplay = document.getElementById(`status-display-${taskId}`);
            const statusBadge = statusDisplay.querySelector('.status-badge');

            // Show loading
            const submitBtn = form.querySelector('.save-button');
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i>Saving...';
            submitBtn.disabled = true;

            // Send AJAX request
            fetch(`/admin/task/${taskId}/update-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ status: newStatus })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update badge - remove all status classes
                    statusBadge.classList.remove(
                        'bg-yellow-100', 'text-yellow-700', 'dark:bg-yellow-900', 'dark:text-yellow-300',
                        'bg-green-100', 'text-green-700', 'dark:bg-green-900', 'dark:text-green-300',
                        'bg-red-100', 'text-red-700', 'dark:bg-red-900', 'dark:text-red-300'
                    );

                    // Update badge based on new status
                    if (newStatus === 'selesai') {
                        statusBadge.innerHTML = `<i class="fa-solid fa-circle-check"></i> Done`;
                        statusBadge.classList.add('bg-green-100', 'text-green-700', 'dark:bg-green-900', 'dark:text-green-300');
                    } else if (newStatus === 'terlambat') {
                        statusBadge.innerHTML = `<i class="fa-solid fa-exclamation-triangle"></i> Overdue`;
                        statusBadge.classList.add('bg-red-100', 'text-red-700', 'dark:bg-red-900', 'dark:text-red-300');
                    } else {
                        statusBadge.innerHTML = `<i class="fa-solid fa-spinner animate-spin-slow"></i> On Going`;
                        statusBadge.classList.add('bg-yellow-100', 'text-yellow-700', 'dark:bg-yellow-900', 'dark:text-yellow-300');
                    }

                    // Update card data attribute and border
                    const card = form.closest('.task-card');
                    card.dataset.status = newStatus;

                    // Update border color
                    card.classList.remove('border-red-500', 'border-yellow-500', 'border-transparent');
                    if (newStatus === 'terlambat') {
                        card.classList.add('border-red-500');
                    } else {
                        card.classList.add('border-transparent');
                    }

                    // Show success message
                    showNotification('Status berhasil diupdate!', 'success');

                    // Hide form, show display
                    form.classList.add('hidden');
                    form.classList.remove('flex');
                    statusDisplay.classList.remove('hidden');

                    // Reload stats after short delay
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showNotification(data.message || 'Gagal mengupdate status', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Terjadi kesalahan saat mengupdate status', 'error');
            })
            .finally(() => {
                submitBtn.innerHTML = originalBtnText;
                submitBtn.disabled = false;
            });
        }
    });

    // Search functionality
    const searchInput = document.getElementById('task-search');
    const statusFilter = document.getElementById('status-filter');

    function filterTasks() {
        const searchTerm = searchInput.value.toLowerCase();
        const statusValue = statusFilter.value;
        const cards = document.querySelectorAll('.task-card');

        cards.forEach(card => {
            const taskTitle = card.dataset.taskTitle;
            const momTitle = card.dataset.momTitle;
            const cardStatus = card.dataset.status;

            const matchesSearch = searchTerm === '' ||
                                taskTitle.includes(searchTerm) ||
                                momTitle.includes(searchTerm);

            const matchesStatus = statusValue === 'all' || cardStatus === statusValue;

            if (matchesSearch && matchesStatus) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    searchInput.addEventListener('input', debounce(filterTasks, 300));
    statusFilter.addEventListener('change', filterTasks);

    // Reset filter
    document.getElementById('reset-filter').addEventListener('click', function() {
        searchInput.value = '';
        statusFilter.value = 'all';
        filterTasks();
    });

    // Utility functions
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    function showNotification(message, type = 'success') {
        const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-slide-in`;
        notification.innerHTML = `
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
                <span>${message}</span>
            </div>
        `;
        document.body.appendChild(notification);

        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
});
</script>
@endpush
