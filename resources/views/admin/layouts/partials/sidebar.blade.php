<aside id="admin-sidebar"
    class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform -translate-x-full bg-component-bg border-r border-border-light sm:translate-x-0 dark:bg-dark-component-bg dark:border-border-dark"
    aria-label="Sidebar">
    <div class="h-full px-4 pb-6 overflow-y-auto flex flex-col justify-between">
        <ul class="space-y-2 font-medium">
            <li>
                <a href="{{ route('admin.dashboard') }}"
                   class="flex items-center p-3 rounded-lg group transition
                   {{ request()->routeIs('admin.dashboard') ? 'bg-primary/20 text-primary font-semibold'
                                                                : 'text-text-secondary dark:text-dark-text-secondary hover:bg-primary/10' }}">
                    <i class="fa-solid fa-tachometer-alt w-5 h-5"></i>
                    <span class="ms-3">Dashboard</span>
                </a>
            </li>

            {{-- PERSETUJUAN MOM (DYNAMIC COUNT) --}}
            <li>
                <a href="{{ route('admin.approvals.index') }}"
                   class="flex items-center p-3 rounded-lg group transition relative
                   {{ request()->routeIs('admin.approvals') ? 'bg-primary/20 text-primary font-semibold'
                                                                : 'text-text-secondary dark:text-dark-text-secondary hover:bg-primary/10' }}">
                    <i class="fa-solid fa-check-to-slot w-5 h-5"></i>
                    <span class="ms-3">Persetujuan MoM</span>

                    @php
                        // Ensure the variable has a fallback value of 0
                        $count = $pendingApprovalsCount ?? 0;
                    @endphp

                    @if(isset($count))
                        <span class="absolute right-3 inline-flex items-center justify-center w-6 h-6 text-xs font-medium text-white bg-red-500 rounded-full"
                              style="{{ $count === 0 ? 'opacity: 0.5;' : '' }}">
                            {{ $count }}
                        </span>
                    @endif
                </a>
            </li>

            <li>
                <a href="{{ route('admin.repository') }}"
                   class="flex items-center p-3 rounded-lg group transition
                   {{ request()->routeIs('admin.repository') ? 'bg-primary/20 text-primary font-semibold'
                                                              : 'text-text-secondary dark:text-dark-text-secondary hover:bg-primary/10' }}">
                    <i class="fa-solid fa-box-archive w-5 h-5"></i>
                    <span class="ms-3">MoM</span>
                </a>
            </li>

            <li>
                <a href="{{ route('admin.calendars') }}"
                   class="flex items-center p-3 rounded-lg group transition
                   {{ request()->routeIs('admin.calendars') ? 'bg-primary/20 text-primary font-semibold' : 'text-text-secondary dark:text-dark-text-secondary hover:bg-primary/10' }}">
                    <i class="fa-solid fa-calendar w-5 h-5"></i>
                    <span class="ms-3">Calendar</span>
                </a>
            </li>

            <li>
                <a href="{{ route('admin.task') }}"
                   class="flex items-center p-3 rounded-lg group transition
                   {{ request()->routeIs('admin.task') ? 'bg-primary/20 text-primary font-semibold' : 'text-text-secondary dark:text-dark-text-secondary hover:bg-primary/10' }}">
                    <i class="fa-solid fa-tasks w-5 h-5"></i>
                    <span class="ms-3">Task</span>
                </a>
            </li>

            <li>
                <a href="{{ route("admin.notification") }}"
                class="flex items-center p-3 rounded-lg group transition relative {{ request()->is('notification') ? 'bg-primary/20 text-primary font-semibold' : 'text-text-secondary dark:text-dark-text-secondary hover:bg-primary/10' }}">
                    <i class="fa-solid fa-bell w-5 h-5"></i>
                    <span class="ms-3 flex-1 whitespace-nowrap">Notifications</span>
                    {{-- Badge Notifikasi Dinamis --}}
                    @if(isset($adminNotificationCount) && $adminNotificationCount > 0)
                        <span class="inline-flex items-center justify-center w-6 h-6 ms-3 text-sm font-medium text-white bg-red-500 rounded-full">
                            {{ $adminNotificationCount }}
                        </span>
                    @endif
                </a>
            </li>

            <li>
                <a href="{{ route("admin.users") }}"
                   class="flex items-center p-3 rounded-lg group transition
                   {{ request()->routeIs('admin.users') ? 'bg-primary/20 text-primary font-semibold' : 'text-text-secondary dark:text-dark-text-secondary hover:bg-primary/10' }}">
                    <i class="fa-solid fa-users w-5 h-5"></i>
                    <span class="ms-3">Manajemen Pengguna</span>
                </a>
            </li>
        </ul>

        <div class="border-t border-border-light dark:border-border-dark pt-4">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full flex items-center gap-5 p-3 rounded-lg text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-500/20 group transition font-medium">
                    <i class="fa-solid fa-arrow-right-from-bracket w-3 h-3"></i>
                    <p>Log Out</p>
                </button>
            </form>
        </div>
    </div>
</aside>
