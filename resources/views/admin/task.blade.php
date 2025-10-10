@extends('admin.layouts.app')

@section('title', 'Manajemen Tugas | MoM Telkom')

@section('content')
<div class="pt-4">
    <div class="space-y-8">
        {{-- Header --}}
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between">
            <div>
                <h1 class="text-4xl font-extrabold text-text-primary dark:text-dark-text-primary tracking-tight">
                    Manajemen Tugas
                </h1>
                <p class="mt-2 text-text-secondary dark:text-dark-text-secondary text-base">
                    Pantau dan kelola semua tugas dari berbagai MoM dalam satu tampilan yang terorganisir.
                </p>
            </div>
        </div>

        {{-- Success Message --}}
        @if(session('success'))
        <div class="p-4 bg-green-100 dark:bg-green-900/20 border-l-4 border-green-500 text-green-700 dark:text-green-400 rounded">
            <p class="font-medium">{{ session('success') }}</p>
        </div>
        @endif

        {{-- Statistics Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div class="bg-component-bg dark:bg-dark-component-bg rounded-lg shadow-md p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-text-secondary">Total Tasks</p>
                        <p class="text-2xl font-bold text-primary">{{ $stats['total'] }}</p>
                    </div>
                    <i class="fa-solid fa-tasks fa-2x text-primary opacity-20"></i>
                </div>
            </div>

            <div class="bg-component-bg dark:bg-dark-component-bg rounded-lg shadow-md p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-text-secondary">On Going</p>
                        <p class="text-2xl font-bold text-yellow-500">{{ $stats['mendatang'] }}</p>
                    </div>
                    <i class="fa-solid fa-spinner fa-2x text-yellow-500 opacity-20"></i>
                </div>
            </div>

            <div class="bg-component-bg dark:bg-dark-component-bg rounded-lg shadow-md p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-text-secondary">Completed</p>
                        <p class="text-2xl font-bold text-green-500">{{ $stats['selesai'] }}</p>
                    </div>
                    <i class="fa-solid fa-check-circle fa-2x text-green-500 opacity-20"></i>
                </div>
            </div>

            <div class="bg-component-bg dark:bg-dark-component-bg rounded-lg shadow-md p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-text-secondary">Overdue</p>
                        <p class="text-2xl font-bold text-red-500">{{ $stats['overdue'] }}</p>
                    </div>
                    <i class="fa-solid fa-exclamation-triangle fa-2x text-red-500 opacity-20"></i>
                </div>
            </div>
        </div>

        {{-- Filter dan Search Section --}}
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 p-5 rounded-2xl shadow-lg border border-border-light dark:border-border-dark backdrop-blur bg-component-bg dark:bg-dark-component-bg">
            <div class="relative w-full sm:w-80">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="fa-solid fa-magnifying-glass text-gray-400"></i>
                </div>
                <input type="text" id="task-search" placeholder="Cari tugas atau MoM..."
                    class="bg-body-bg border border-border-light text-text-primary text-sm rounded-lg focus:ring-2 focus:ring-primary focus:border-primary block w-full pl-10 p-2.5 dark:bg-dark-body-bg dark:border-border-dark transition-all duration-300">
            </div>

            <div class="flex gap-3 w-full sm:w-auto">
                <select id="status-filter"
                    class="bg-body-bg border border-border-light text-text-primary text-sm rounded-lg focus:ring-2 focus:ring-primary focus:border-primary block w-full sm:w-48 p-2.5 dark:bg-dark-body-bg dark:border-border-dark transition-all duration-300">
                    <option value="all">Semua Status</option>
                    <option value="mendatang">On Going</option>
                    <option value="selesai">Done</option>
                </select>

                <button id="reset-filter" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                    <i class="fa-solid fa-rotate-right"></i>
                </button>
            </div>
        </div>

        {{-- Daftar Tugas --}}
        <div id="tasks-container" class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @forelse($tasks as $task)
                @php
                    $deadline = \Carbon\Carbon::parse($task->due);
                    $isOverdue = $deadline->isPast() && $task->status == 'mendatang';
                    $isDueSoon = !$isOverdue && $deadline->diffInDays(now()) < 3 && $task->status == 'mendatang';
                    $borderColor = $isOverdue ? 'border-red-500' : ($isDueSoon ? 'border-yellow-500' : 'border-transparent');
                @endphp

                <div class="task-card relative group bg-component-bg dark:bg-dark-component-bg rounded-xl border-l-4 {{ $borderColor }} shadow-md hover:shadow-2xl transition-all duration-300 hover:-translate-y-1 overflow-hidden"
                     data-task-id="{{ $task->action_id }}"
                     data-task-title="{{ strtolower($task->item) }}"
                     data-mom-title="{{ strtolower($task->mom->title ?? '') }}"
                     data-status="{{ $task->status }}">
                    {{-- Card Header --}}
                    <div class="p-5">
                        <div class="flex justify-between items-start">
                            <h2 class="text-lg font-semibold text-text-primary dark:text-dark-text-primary group-hover:text-primary transition-colors duration-300">
                                {{ $task->item }}
                            </h2>
                            <a href="{{ route('admin.moms.show', $task->mom_id) }}"
                               class="text-xs font-medium text-primary hover:underline whitespace-nowrap ml-4">
                                Lihat MoM <i class="fa-solid fa-arrow-right fa-xs"></i>
                            </a>
                        </div>

                        {{-- Card Info --}}
                        <div class="flex flex-col space-y-2 mt-3 text-sm text-text-secondary dark:text-dark-text-secondary">
                            <span title="Asal MoM" class="flex items-center">
                                <i class="fa-solid fa-file-lines mr-2 text-primary"></i>
                                {{ \Str::limit($task->mom->title ?? 'N/A', 40) }}
                            </span>
                            <span title="Deadline"
                                class="flex items-center {{ $isOverdue ? 'text-red-500 font-semibold' : ($isDueSoon ? 'text-yellow-500 font-semibold' : '') }}">
                                <i class="fa-solid fa-calendar-day mr-2"></i>
                                {{ $deadline->translatedFormat('d F Y') }}
                                @if($isOverdue)
                                    <span class="ml-2 px-2 py-0.5 text-xs bg-red-100 text-red-700 rounded-full">Overdue</span>
                                @elseif($isDueSoon)
                                    <span class="ml-2 px-2 py-0.5 text-xs bg-yellow-100 text-yellow-700 rounded-full">Soon</span>
                                @endif
                            </span>
                        </div>
                    </div>

                    {{-- Card Footer --}}
                    <div class="px-5 py-4 bg-body-bg/50 dark:bg-dark-body-bg/40 border-t border-border-light dark:border-border-dark flex items-center justify-between">
                        <div id="status-display-{{ $task->action_id }}" class="flex items-center gap-3">
                            @if($task->status == 'selesai')
                                <span class="status-badge flex items-center gap-1 text-xs font-semibold px-3 py-1 rounded-full bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300">
                                    <i class="fa-solid fa-circle-check"></i> Done
                                </span>
                            @else
                                <span class="status-badge flex items-center gap-1 text-xs font-semibold px-3 py-1 rounded-full bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300">
                                    <i class="fa-solid fa-spinner animate-spin-slow"></i> On Going
                                </span>
                            @endif
                            <button class="change-status-btn text-xs font-medium text-blue-600 hover:text-blue-800 transition-colors"
                                    data-task-id="{{ $task->action_id }}">
                                <i class="fa-solid fa-pen-to-square mr-1"></i>Ubah
                            </button>
                        </div>

                        {{-- Status Form --}}
                        <form id="status-form-{{ $task->action_id }}"
                              class="hidden items-center gap-2 w-full sm:w-auto status-form"
                              data-task-id="{{ $task->action_id }}">
                            @csrf
                            <select class="status-select bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="mendatang" {{ $task->status == 'mendatang' ? 'selected' : '' }}>On Going</option>
                                <option value="selesai" {{ $task->status == 'selesai' ? 'selected' : '' }}>Done</option>
                            </select>
                            <button type="submit"
                                class="save-button px-4 py-2 text-xs font-medium text-center text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-all duration-300">
                                <i class="fa-solid fa-check mr-1"></i>
                            </button>
                            <button type="button"
                                    class="cancel-button px-4 py-2 text-xs font-medium text-center text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-all duration-300">
                                <i class="fa-solid fa-times"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12 bg-component-bg dark:bg-dark-component-bg rounded-lg">
                    <i class="fa-solid fa-inbox text-6xl text-gray-400 mb-4"></i>
                    <p class="text-text-secondary dark:text-dark-text-secondary">Tidak ada tugas ditemukan</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
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
                    // Update badge
                    statusBadge.classList.remove('bg-yellow-100', 'text-yellow-700', 'bg-green-100', 'text-green-700',
                                               'dark:bg-yellow-900', 'dark:text-yellow-300', 'dark:bg-green-900', 'dark:text-green-300');

                    if (newStatus === 'selesai') {
                        statusBadge.innerHTML = `<i class="fa-solid fa-circle-check"></i> Done`;
                        statusBadge.classList.add('bg-green-100', 'text-green-700', 'dark:bg-green-900', 'dark:text-green-300');
                    } else {
                        statusBadge.innerHTML = `<i class="fa-solid fa-spinner animate-spin-slow"></i> On Going`;
                        statusBadge.classList.add('bg-yellow-100', 'text-yellow-700', 'dark:bg-yellow-900', 'dark:text-yellow-300');
                    }

                    // Update card data attribute
                    const card = form.closest('.task-card');
                    card.dataset.status = newStatus;

                    // Show success message
                    showNotification('Status berhasil diupdate!', 'success');

                    // Hide form, show display
                    form.classList.add('hidden');
                    form.classList.remove('flex');
                    statusDisplay.classList.remove('hidden');

                    // Reload stats
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showNotification('Gagal mengupdate status', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Terjadi kesalahan', 'error');
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

<style>
@keyframes spin-slow {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}
.animate-spin-slow {
  animation: spin-slow 2s linear infinite;
}

@keyframes slide-in {
  from {
    transform: translateX(100%);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}
.animate-slide-in {
  animation: slide-in 0.3s ease-out;
}
</style>
@endpush
