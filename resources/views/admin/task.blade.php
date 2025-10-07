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

        {{-- Filter dan Search Section --}}
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 p-5 dark:from-primary/20 rounded-2xl shadow-lg border border-border-light dark:border-border-dark backdrop-blur">
            <div class="relative w-full sm:w-80">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="fa-solid fa-magnifying-glass text-gray-400"></i>
                </div>
                <input type="text" id="task-search" placeholder="Cari tugas atau MoM..."
                    class="bg-body-bg border border-border-light text-text-primary text-sm rounded-lg focus:ring-2 focus:ring-primary focus:border-primary block w-full pl-10 p-2.5 dark:bg-dark-body-bg dark:border-border-dark transition-all duration-300">
            </div>

            <div class="w-full sm:w-auto">
                <select id="status-filter"
                    class="bg-body-bg border border-border-light text-text-primary text-sm rounded-lg focus:ring-2 focus:ring-primary focus:border-primary block w-full p-2.5 dark:bg-dark-body-bg dark:border-border-dark transition-all duration-300">
                    <option selected value="all">Semua Status</option>
                    <option value="on_going">On Going</option>
                    <option value="done">Done</option>
                </select>
            </div>
        </div>

        {{-- Daftar Tugas --}}
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @php
                $tasks = [
                    ['id' => 1, 'task_title' => 'Plan survei ulang lokasi RAP058', 'mom_title' => 'Evaluasi Progress Project OTN TR1', 'deadline' => now()->subDays(1)->toDateString(), 'status' => 'on_going'],
                    ['id' => 2, 'task_title' => 'Follow-up status OSP KIS768', 'mom_title' => 'Evaluasi Progress Project OTN TR1', 'deadline' => now()->addDays(2)->toDateString(), 'status' => 'on_going'],
                    ['id' => 3, 'task_title' => 'Finalisasi Desain UI/UX', 'mom_title' => 'Brainstorming Fitur Baru Aplikasi', 'deadline' => now()->addDays(10)->toDateString(), 'status' => 'done'],
                    ['id' => 4, 'task_title' => 'Backup Data Server Utama', 'mom_title' => 'Rapat Maintenance Bulanan', 'deadline' => now()->addDays(7)->toDateString(), 'status' => 'on_going'],
                ];
            @endphp

            @foreach ($tasks as $task)
                @php
                    $deadline = \Carbon\Carbon::parse($task['deadline']);
                    $isOverdue = $deadline->isPast() && $task['status'] != 'done';
                    $isDueSoon = !$isOverdue && $deadline->diffInDays(now()) < 3 && $task['status'] != 'done';
                    $borderColor = $isOverdue ? 'border-red-500' : ($isDueSoon ? 'border-yellow-500' : 'border-transparent');
                @endphp

                <div class="relative group bg-component-bg dark:bg-dark-component-bg rounded-xl border-l-4 {{ $borderColor }} shadow-md hover:shadow-2xl transition-all duration-300 hover:-translate-y-1 overflow-hidden">
                    {{-- Card Header --}}
                    <div class="p-5">
                        <div class="flex justify-between items-start">
                            <h2 class="text-lg font-semibold text-text-primary dark:text-dark-text-primary group-hover:text-primary transition-colors duration-300">
                                {{ $task['task_title'] }}
                            </h2>
                            <a href="{{ url('/details') }}" class="text-xs font-medium text-primary hover:underline whitespace-nowrap ml-4">
                                Lihat MoM <i class="fa-solid fa-arrow-right fa-xs"></i>
                            </a>
                        </div>

                        {{-- Card Info --}}
                        <div class="flex items-center space-x-3 mt-3 text-sm text-text-secondary dark:text-dark-text-secondary">
                            <span title="Asal MoM" class="flex items-center"><i class="fa-solid fa-file-lines mr-2 text-primary"></i>{{ $task['mom_title'] }}</span>
                            <span title="Deadline"
                                class="flex items-center {{ $isOverdue ? 'text-red-500 font-semibold' : ($isDueSoon ? 'text-yellow-500 font-semibold' : '') }}">
                                <i class="fa-solid fa-calendar-day mr-2"></i>{{ $deadline->translatedFormat('d F Y') }}
                            </span>
                        </div>
                    </div>

                    {{-- Card Footer --}}
                    <div class="px-5 py-4 bg-body-bg/50 dark:bg-dark-body-bg/40 border-t border-border-light dark:border-border-dark flex items-center justify-between">
                        <div id="status-display-{{ $task['id'] }}" class="flex items-center gap-3">
                            @if ($task['status'] == 'done')
                                <span class="status-badge flex items-center gap-1 text-xs font-semibold px-3 py-1 rounded-full bg-green-100 text-green-700">
                                    <i class="fa-solid fa-circle-check"></i> Done
                                </span>
                            @else
                                <span class="status-badge flex items-center gap-1 text-xs font-semibold px-3 py-1 rounded-full bg-yellow-100 text-yellow-700">
                                    <i class="fa-solid fa-spinner animate-spin-slow"></i> On Going
                                </span>
                            @endif
                            <button class="change-status-btn text-xs font-medium text-blue-600 hover:text-blue-800 transition-colors" data-task-id="{{ $task['id'] }}">
                                <i class="fa-solid fa-pen-to-square mr-1"></i>Ubah
                            </button>
                        </div>

                        {{-- Status Form --}}
                        <form id="status-form-{{ $task['id'] }}" class="hidden items-center gap-2 w-full sm:w-auto" data-task-id="{{ $task['id'] }}">
                            <select class="status-select bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2">
                                <option value="on_going" {{ $task['status'] == 'on_going' ? 'selected' : '' }}>On Going</option>
                                <option value="done" {{ $task['status'] == 'done' ? 'selected' : '' }}>Done</option>
                            </select>
                            <button type="submit"
                                class="save-button px-4 py-2 text-xs font-medium text-center text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-all duration-300">
                                Simpan
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.change-status-btn').forEach(button => {
        button.addEventListener('click', function() {
            const taskId = this.dataset.taskId;
            document.getElementById(`status-display-${taskId}`).classList.add('hidden');
            const form = document.getElementById(`status-form-${taskId}`);
            form.classList.remove('hidden');
            form.classList.add('flex');
        });
    });

    document.querySelectorAll('form[id^="status-form-"]').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const taskId = this.dataset.taskId;
            const newStatus = this.querySelector('.status-select').value;
            const statusDisplay = document.getElementById(`status-display-${taskId}`);
            const statusBadge = statusDisplay.querySelector('.status-badge');

            alert(`Status untuk tugas #${taskId} diubah menjadi "${newStatus}" (Simulasi)`);

            // Update tampilan badge
            statusBadge.classList.remove('bg-yellow-100', 'text-yellow-700', 'bg-green-100', 'text-green-700');
            if (newStatus === 'done') {
                statusBadge.innerHTML = `<i class="fa-solid fa-circle-check"></i> Done`;
                statusBadge.classList.add('bg-green-100', 'text-green-700');
            } else {
                statusBadge.innerHTML = `<i class="fa-solid fa-spinner animate-spin-slow"></i> On Going`;
                statusBadge.classList.add('bg-yellow-100', 'text-yellow-700');
            }

            this.classList.add('hidden');
            this.classList.remove('flex');
            statusDisplay.classList.remove('hidden');
        });
    });
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
</style>
@endpush
