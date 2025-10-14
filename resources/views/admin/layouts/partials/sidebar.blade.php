<aside id="admin-sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform -translate-x-full bg-gray-800 border-r border-gray-700 sm:translate-x-0" aria-label="Sidebar">
    <div class="h-full px-4 pb-6 overflow-y-auto flex flex-col justify-between">
        <ul class="space-y-2 font-medium">
            {{-- Dashboard --}}
            <li>
                <a href="{{ route('admin.dashboard') }}" class="flex items-center p-3 rounded-lg group transition
                    {{ request()->routeIs('admin.dashboard*')
                        ? 'bg-red-500/10 text-white font-semibold border-l-4 border-red-500'
                        : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                    <i class="fa-solid fa-tachometer-alt w-5 h-5 {{ request()->routeIs('admin.dashboard*') ? 'text-red-400' : '' }}"></i>
                    <span class="ms-3">Dashboard</span>
                </a>
            </li>

            {{-- Persetujuan MoM --}}
            <li>
                <a href="{{ route('admin.approvals.index') }}" class="flex items-center p-3 rounded-lg group transition relative
                    {{ request()->routeIs('admin.approvals.*')
                        ? 'bg-red-500/10 text-white font-semibold border-l-4 border-red-500'
                        : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                    <i class="fa-solid fa-check-to-slot w-5 h-5 {{ request()->routeIs('admin.approvals.*') ? 'text-red-400' : '' }}"></i>
                    <span class="ms-3">Persetujuan MoM</span>
                    @if(isset($pendingApprovalsCount) && $pendingApprovalsCount > 0)
                        <span class="absolute right-3 inline-flex items-center justify-center w-6 h-6 text-xs font-bold text-white bg-red-500 rounded-full">
                            {{ $pendingApprovalsCount > 99 ? '99+' : $pendingApprovalsCount }}
                        </span>
                    @endif
                </a>
            </li>

            {{-- MoM Repository --}}
            <li>
                <a href="{{ route('admin.repository') }}" class="flex items-center p-3 rounded-lg group transition
                    {{ request()->routeIs('admin.repository*')
                        ? 'bg-red-500/10 text-white font-semibold border-l-4 border-red-500'
                        : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                    <i class="fa-solid fa-box-archive w-5 h-5 {{ request()->routeIs('admin.repository*') ? 'text-red-400' : '' }}"></i>
                    <span class="ms-3">MoM</span>
                </a>
            </li>

            {{-- Calendar --}}
            <li>
                <a href="{{ route('admin.calendars') }}" class="flex items-center p-3 rounded-lg group transition
                    {{ request()->routeIs('admin.calendars*')
                        ? 'bg-red-500/10 text-white font-semibold border-l-4 border-red-500'
                        : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                    <i class="fa-solid fa-calendar w-5 h-5 {{ request()->routeIs('admin.calendars*') ? 'text-red-400' : '' }}"></i>
                    <span class="ms-3">Calendar</span>
                </a>
            </li>

            {{-- Task --}}
            <li>
                <a href="{{ route('admin.task') }}" class="flex items-center p-3 rounded-lg group transition relative
                    {{ request()->routeIs('admin.task*')
                        ? 'bg-red-500/10 text-white font-semibold border-l-4 border-red-500'
                        : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                    <i class="fa-solid fa-tasks w-5 h-5 {{ request()->routeIs('admin.task*') ? 'text-red-400' : '' }}"></i>
                    <span class="ms-3">Task</span>
                    @if(isset($onGoingTasksCount) && $onGoingTasksCount > 0)
                        <span class="absolute right-3 inline-flex items-center justify-center w-6 h-6 text-xs font-bold text-white bg-red-500 rounded-full">
                            {{ $onGoingTasksCount > 99 ? '99+' : $onGoingTasksCount }}
                        </span>
                    @endif
                </a>
            </li>

            {{-- Notifications --}}
            <li>
                <a href="{{ route('admin.notification') }}" class="flex items-center p-3 rounded-lg group transition relative
                    {{ request()->routeIs('admin.notification*')
                        ? 'bg-red-500/10 text-white font-semibold border-l-4 border-red-500'
                        : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                    <i class="fa-solid fa-bell w-5 h-5 {{ request()->routeIs('admin.notification*') ? 'text-red-400' : '' }}"></i>
                    <span class="ms-3">Notifications</span>
                    @if(isset($adminNotificationCount) && $adminNotificationCount > 0)
                        <span class="absolute right-3 inline-flex items-center justify-center w-6 h-6 text-xs font-bold text-white bg-red-500 rounded-full">
                            {{ $adminNotificationCount > 99 ? '99+' : $adminNotificationCount }}
                        </span>
                    @endif
                </a>
            </li>

            {{-- User Management --}}
            <li>
                <a href="{{ route('admin.users') }}" class="flex items-center p-3 rounded-lg group transition
                    {{ request()->routeIs('admin.users*')
                        ? 'bg-red-500/10 text-white font-semibold border-l-4 border-red-500'
                        : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                    <i class="fa-solid fa-users w-5 h-5 {{ request()->routeIs('admin.users*') ? 'text-red-400' : '' }}"></i>
                    <span class="ms-3">Manajemen Pengguna</span>
                </a>
            </li>
        </ul>

        <div class="border-t border-gray-700 pt-4">
            <form id="logout-form-admin" method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="button" id="logout-button-admin"
                        class="w-full flex items-center p-3 rounded-lg text-red-400 hover:bg-red-500/20 group transition font-medium">
                    <i class="fa-solid fa-arrow-right-from-bracket w-5 h-5"></i>
                    <span class="ms-3">Log Out</span>
                </button>
            </form>
        </div>
    </div>
</aside>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const logoutButton = document.getElementById('logout-button-admin');
        const logoutForm = document.getElementById('logout-form-admin');

        if (logoutButton) {
            logoutButton.addEventListener('click', function (event) {
                event.preventDefault(); // Mencegah form submit langsung

                Swal.fire({
                    title: 'Anda yakin ingin keluar?',
                    text: "Sesi admin Anda akan diakhiri.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Keluar!',
                    cancelButtonText: 'Batal',
                    customClass: {
                        popup: 'bg-gray-800 rounded-2xl border border-gray-700',
                        title: 'text-white font-orbitron',
                        htmlContainer: 'text-gray-400',
                        confirmButton: 'btn-neon-red text-white font-semibold px-6 py-2 mr-5 rounded-lg',
                        cancelButton: 'bg-gray-700 text-gray-300 font-semibold px-6 py-2 rounded-lg hover:bg-gray-600 border border-gray-600'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        logoutForm.submit();
                    }
                });
            });
        }
    });
</script>
@endpush
