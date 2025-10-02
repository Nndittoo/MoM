<nav class="fixed top-0 z-50 w-full bg-component-bg border-b border-border-light dark:bg-dark-component-bg dark:border-border-dark shadow-sm">
    <div class="px-3 py-3 lg:px-5 lg:pl-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center justify-start rtl:justify-end">
                <button data-drawer-target="admin-sidebar" data-drawer-toggle="admin-sidebar" type="button" class="inline-flex items-center p-2 text-sm text-text-secondary rounded-lg sm:hidden hover:bg-primary/10 dark:text-dark-text-secondary dark:hover:bg-primary/20">
                    <i class="fa-solid fa-bars w-6 h-6"></i>
                </button>
                <a href="{{ url('/admin/dashboard') }}" class="flex ms-2 md:me-24 items-center">
                    <img src="{{ asset('img/LOGO_TELKOM.png') }}" class="h-12 mr-3" alt="Telkom Logo" />
                    <span class="self-center text-xl font-semibold sm:text-2xl whitespace-nowrap dark:text-white">Admin Panel</span>
                </a>
            </div>
            <div class="flex items-center">
                {{-- User Profile Dropdown --}}
                <div class="flex items-center ms-3">
                    <button type="button" class="flex text-sm bg-gray-800 rounded-full focus:ring-4 focus:ring-primary/50" data-dropdown-toggle="dropdown-user">
                        <img class="w-8 h-8 rounded-full" src="https://flowbite.com/docs/images/people/profile-picture-5.jpg" alt="user photo">
                    </button>
                    <div class="z-50 hidden my-4 text-base list-none bg-component-bg divide-y divide-border-light rounded-md shadow-lg dark:bg-dark-component-bg dark:divide-border-dark" id="dropdown-user">
                        <div class="px-4 py-3">
                            <p class="text-sm text-text-primary dark:text-dark-text-primary">Admin Name</p>
                            <p class="text-sm font-medium text-text-secondary truncate dark:text-dark-text-secondary">admin@telkom.co.id</p>
                        </div>
                        <ul class="py-1">
                            <li><a href="#" class="block px-4 py-2 text-sm text-text-secondary hover:bg-body-bg">Sign out</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>
