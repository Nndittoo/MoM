<nav class="fixed top-0 z-50 w-full bg-component-bg border-b border-border-light dark:bg-dark-component-bg dark:border-border-dark shadow-sm">
    <div class="px-3 py-3 lg:px-5 lg:pl-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center justify-start rtl:justify-end">
                <button
                    data-drawer-target="logo-sidebar"
                    data-drawer-toggle="logo-sidebar"
                    type="button"
                    class="inline-flex items-center p-2 text-sm text-text-secondary rounded-lg sm:hidden hover:bg-primary/10 dark:text-dark-text-secondary dark:hover:bg-primary/20">
                    <i class="fa-solid fa-bars w-6 h-6"></i>
                </button>

                <a href="{{ url('/') }}" class="flex ms-2 md:me-24 items-center">
                    <img src="{{ asset('img/LOGO_TELKOM.png') }}" class="h-12 mr-3" alt="Telkom Logo" />
                </a>
            </div>

            <div class="flex items-center">
                {{-- Search --}}
                <div class="relative hidden md:block w-64 lg:w-96 mr-4">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i class="fa-solid fa-search text-text-secondary"></i>
                    </div>

                    <input
                        type="text"
                        id="search-navbar"
                        class="block w-full p-2 pl-10 text-sm text-text-primary border border-border-light rounded-lg bg-body-bg focus:ring-primary focus:border-primary dark:bg-dark-component-bg dark:border-border-dark"
                        placeholder="Search MoM...">

                    {{-- Dropdown hasil pencarian --}}
                    <div id="search-dropdown"
                        class="absolute mt-2 w-full bg-component-bg dark:bg-dark-component-bg border border-border-light dark:border-border-dark rounded-lg shadow-lg z-50 hidden">
                        <div class="max-h-96 overflow-y-auto divide-y divide-border-light dark:divide-border-dark" id="search-results">
                            <div class="px-4 py-6 text-center text-text-secondary dark:text-dark-text-secondary">
                                <p>Mulai mengetik untuk mencari...</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Notifications --}}
                <button type="button" data-dropdown-toggle="notification-dropdown"
                    class="p-2 mr-3 text-text-secondary rounded-full hover:bg-primary/10 relative dark:text-dark-text-secondary dark:hover:bg-primary/20">
                    <i class="fa-solid fa-bell fa-lg"></i>
                    @php
                        $unreadCount = \App\Http\Controllers\NotificationController::getUnreadCount();
                    @endphp
                    {{-- Ping Indicator --}}
                    <span class="notification-ping absolute top-1 right-1 flex h-3 w-3 {{ $unreadCount > 0 ? '' : 'hidden' }}">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                    </span>
                    {{-- Badge Count --}}
                    <span class="notification-badge absolute -top-1 -right-1 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white bg-red-600 rounded-full min-w-[1.25rem] {{ $unreadCount > 0 ? '' : 'hidden' }}">
                        {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                    </span>
                </button>

                <div id="notification-dropdown"
                    class="z-50 hidden max-w-sm my-4 overflow-hidden text-base list-none bg-component-bg divide-y divide-border-light rounded-lg shadow-lg dark:bg-dark-component-bg dark:divide-border-dark">
                    {{-- ... header dropdown ... --}}

                    {{-- KOSONGKAN BAGIAN INI --}}
                    <div id="notification-list" class="max-h-96 overflow-y-auto">
                        {{-- Loop PHP di sini dihapus. JavaScript akan mengisi area ini. --}}
                        {{-- Anda bisa menambahkan skeleton loading di sini jika mau --}}
                        <div class="px-4 py-6 text-center text-text-secondary dark:text-dark-text-secondary">
                            <p>Loading notifications...</p>
                        </div>
                    </div>

                    <a href="{{ url('notifications') }}"
                    class="block py-2 text-sm font-medium text-center text-text-primary rounded-b-lg bg-body-bg hover:bg-border-light dark:bg-dark-component-bg/50 dark:hover:bg-dark-body-bg dark:text-white">
                        <div class="inline-flex items-center">
                            <i class="fa-solid fa-eye mr-2"></i>View all
                        </div>
                    </a>
                </div>

                {{-- User dropdown --}}
                <div class="flex items-center ms-3">
                    <button type="button"
                            class="flex text-sm bg-gray-800 rounded-full focus:ring-4 focus:ring-primary/50"
                            data-dropdown-toggle="dropdown-user">
                        <img class="w-8 h-8 rounded-full"
                             src="https://cdn.pixabay.com/photo/2023/02/18/11/00/icon-7797704_1280.png"
                             alt="user photo">
                    </button>

                    <div id="dropdown-user"
                         class="z-50 hidden my-4 text-base list-none bg-component-bg divide-y divide-border-light rounded-md shadow-lg dark:bg-dark-component-bg dark:divide-border-dark">
                        <div class="px-4 py-3">
                            <p class="text-sm text-text-primary dark:text-dark-text-primary">{{ auth()->user()->name }}</p>
                            <p class="text-sm font-medium text-text-secondary truncate dark:text-dark-text-secondary">{{ auth()->user()->email }}</p>
                        </div>

                        <ul class="py-1">
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                            class="w-full text-left block px-4 py-2 text-sm text-text-secondary hover:bg-body-bg dark:hover:bg-dark-body-bg">
                                        Sign out
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-navbar');
    const dropdown = document.getElementById('search-dropdown');
    const resultsContainer = document.getElementById('search-results');
    let debounceTimer;

    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        const query = this.value.trim();

        if (query.length < 2) {
            dropdown.classList.add('hidden');
            return;
        }

        debounceTimer = setTimeout(() => {
            fetch(`/api/search-moms?search=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    resultsContainer.innerHTML = '';

                    if (data.length === 0) {
                        resultsContainer.innerHTML = `
                            <div class="px-4 py-6 text-center text-text-secondary dark:text-dark-text-secondary">
                                <p>Tidak ada hasil ditemukan</p>
                            </div>`;
                    } else {
                        data.forEach(mom => {
                            const createdDate = new Date(mom.created_at).toLocaleDateString('id-ID', {
                                day: '2-digit',
                                month: 'long',
                                year: 'numeric'
                            });

                            const createdTime = new Date(mom.created_at).toLocaleTimeString('id-ID', {
                                hour: '2-digit',
                                minute: '2-digit'
                            });

                            const item = document.createElement('div');
                            item.className = 'block px-4 py-3 hover:bg-primary/10 dark:hover:bg-primary/20 transition cursor-pointer';
                            item.innerHTML = `
                                <a href="/moms/${mom.version_id}" class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                                            <i class="fa-solid fa-file-alt text-blue-500 text-lg"></i>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-text-primary dark:text-dark-text-primary truncate">
                                            ${mom.title}
                                        </p>
                                        <p class="text-xs text-text-secondary dark:text-dark-text-secondary">
                                            ${createdDate}, ${createdTime}
                                        </p>
                                    </div>
                                </a>
                            `;
                            resultsContainer.appendChild(item);
                        });
                    }

                    dropdown.classList.remove('hidden');
                })
                .catch(err => {
                    console.error(err);
                    dropdown.classList.add('hidden');
                });
        }, 300);
    });

    // Klik di luar dropdown -> sembunyikan
    document.addEventListener('click', function(e) {
        if (!dropdown.contains(e.target) && !searchInput.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });
});
</script>
