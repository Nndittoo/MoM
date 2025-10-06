<nav class="fixed top-0 z-50 w-full bg-component-bg border-b border-border-light dark:bg-dark-component-bg dark:border-border-dark shadow-sm">
    <div class="px-3 py-3 lg:px-5 lg:pl-3">
        <div class="flex items-center justify-between">
            {{-- === Left Section === --}}
            <div class="flex items-center justify-start rtl:justify-end">
                <button data-drawer-target="admin-sidebar" data-drawer-toggle="admin-sidebar"
                    type="button"
                    class="inline-flex items-center p-2 text-sm text-text-secondary rounded-lg sm:hidden hover:bg-primary/10 dark:text-dark-text-secondary dark:hover:bg-primary/20">
                    <i class="fa-solid fa-bars w-6 h-6"></i>
                </button>

                <a href="{{ url('/admin/dashboard') }}" class="flex ms-2 md:me-24 items-center">
                    <img src="{{ asset('img/LOGO_TELKOM.png') }}" class="h-12 mr-3" alt="Telkom Logo" />
                    <span class="self-center text-xl font-semibold sm:text-2xl whitespace-nowrap dark:text-white">
                        Admin Panel
                    </span>
                </a>
            </div>

            {{-- === Right Section === --}}
            <div class="flex items-center">

                {{-- === Notification (Admin) === --}}
                <button type="button"
                    data-dropdown-toggle="admin-notification-dropdown"
                    class="p-2 mr-3 text-text-secondary rounded-full hover:bg-primary/10 relative dark:text-dark-text-secondary dark:hover:bg-primary/20">
                    <i class="fa-solid fa-bell fa-lg"></i>
                    <span class="absolute top-1 right-1 flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                    </span>
                </button>

                <div id="admin-notification-dropdown"
                    class="z-50 hidden max-w-sm my-4 overflow-hidden text-base list-none bg-component-bg divide-y divide-border-light rounded-lg shadow-lg dark:bg-dark-component-bg dark:divide-border-dark">
                    <div class="block px-4 py-2 text-base font-medium text-center text-text-primary bg-body-bg dark:bg-dark-component-bg/50 dark:text-dark-text-primary">
                        Notifications
                        <span class="ml-2 inline-flex items-center justify-center px-2 py-0.5 text-xs font-semibold text-white bg-red-500 rounded-full">
                            2
                        </span>
                    </div>

                    <div>
                        <a href="#" class="flex px-4 py-3 border-b hover:bg-body-bg dark:hover:bg-dark-body-bg dark:border-border-dark">
                            <div class="flex-shrink-0">
                                <div class="inline-flex items-center justify-center w-8 h-8 bg-green-100 rounded-full dark:bg-green-900">
                                    <i class="fa-solid fa-calendar-check text-green-500"></i>
                                </div>
                            </div>
                            <div class="w-full ps-3">
                                <div class="text-sm text-text-secondary dark:text-dark-text-secondary mb-1.5">
                                    <span class="font-semibold text-text-primary dark:text-white">KOM Project NIQE 2025:</span>
                                    Dijadwalkan pada 1 Okt 2025, 08:30.
                                </div>
                                <div class="text-xs text-blue-600 dark:text-blue-500">1 hari yang lalu</div>
                            </div>
                        </a>

                        <a href="#" class="flex px-4 py-3 hover:bg-body-bg dark:hover:bg-dark-body-bg">
                            <div class="flex-shrink-0">
                                <div class="inline-flex items-center justify-center w-8 h-8 bg-yellow-100 rounded-full dark:bg-yellow-900">
                                    <i class="fa-solid fa-file-lines text-yellow-500"></i>
                                </div>
                            </div>
                            <div class="w-full ps-3">
                                <div class="text-sm text-text-secondary dark:text-dark-text-secondary mb-1.5">
                                    <span class="font-semibold text-text-primary dark:text-white">Notulen Siap:</span>
                                    "VALIDASI NEW ORDER MINI OLT" sudah tersedia.
                                </div>
                                <div class="text-xs text-blue-600 dark:text-blue-500">15 September 2025</div>
                            </div>
                        </a>
                    </div>

                    <a href="{{ route('user.notifications') }}"
                        class="block py-2 text-sm font-medium text-center text-text-primary rounded-b-lg bg-body-bg hover:bg-border-light dark:bg-dark-component-bg/50 dark:hover:bg-dark-body-bg dark:text-white">
                        <span class="inline-flex items-center"><i class="fa-solid fa-eye mr-2"></i>View all</span>
                    </a>
                </div>
                {{-- === /Notification (Admin) === --}}

                {{-- === User Dropdown === --}}
                <div class="flex items-center ms-3">
                    <button type="button"
                        class="flex text-sm bg-gray-800 rounded-full focus:ring-4 focus:ring-primary/50"
                        data-dropdown-toggle="dropdown-user">
                        <img class="w-8 h-8 rounded-full"
                            src="https://cdn.pixabay.com/photo/2023/02/18/11/00/icon-7797704_1280.png"
                            alt="user photo">
                    </button>

                    <div class="z-50 hidden my-4 text-base list-none bg-component-bg divide-y divide-border-light rounded-md shadow-lg dark:bg-dark-component-bg dark:divide-border-dark"
                        id="dropdown-user">
                        <div class="px-4 py-3">
                            <p class="text-sm text-text-primary dark:text-dark-text-primary">{{ auth()->user()->name }}</p>
                            <p class="text-sm font-medium text-text-secondary truncate dark:text-dark-text-secondary">{{ auth()->user()->email }}</p>
                        </div>

                        <ul class="py-1">
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="w-full text-left block px-4 py-2 text-sm text-red-600 hover:bg-red-100 dark:text-red-400 dark:hover:bg-red-500/20">
                                        <i class="fa-solid fa-arrow-right-from-bracket mr-2"></i>Sign out
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
                {{-- === /User Dropdown === --}}
            </div>
        </div>
    </div>
</nav>
