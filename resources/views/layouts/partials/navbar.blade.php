{{-- ======================================================= --}}
{{--                  NAVBAR USER - FIXED VERSION             --}}
{{-- ======================================================= --}}

<nav class="fixed top-0 z-50 w-full bg-gray-800 border-b border-gray-700 shadow-md">
  <div class="px-3 py-3 lg:px-5 lg:pl-3">
    <div class="flex items-center justify-between">
      {{-- === Left === --}}
      <div class="flex items-center justify-start rtl:justify-end">
        <button
          data-drawer-target="logo-sidebar"
          data-drawer-toggle="logo-sidebar"
          type="button"
          class="inline-flex items-center p-2 text-sm text-gray-400 rounded-lg sm:hidden hover:bg-red-500/20 focus:outline-none focus:ring-2 focus:ring-red-500">
          <i class="fa-solid fa-bars w-6 h-6"></i>
        </button>

        <a href="{{ route('dashboard') }}" class="flex ms-2 items-center text-neon-red">
          <img src="{{ asset('img/LOGO.png') }}" class="h-12 mr-3 logo-neon-glow" alt="TR1 MoMatic Logo" />
          <span class="self-center text-2xl font-bold font-orbitron hidden md:block">MoMatic</span>
        </a>
      </div>

      {{-- === Right === --}}
      <div class="flex items-center">

        {{-- ========== Search ========== --}}
        <div class="relative hidden md:block w-64 lg:w-96 mr-4">
          <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <i class="fa-solid fa-search text-gray-400"></i>
          </div>

          <input
            type="text"
            id="search-navbar"
            class="block w-full p-2 pl-10 text-sm text-white border border-gray-700 rounded-lg bg-gray-900 focus:ring-1 focus:ring-red-500 focus:border-red-500"
            placeholder="Search MoM..." />

          <div id="search-dropdown"
               class="absolute mt-2 w-full bg-gray-800 border border-gray-700 rounded-lg shadow-lg z-50 hidden">
            <div id="search-results"
                 class="max-h-96 overflow-y-auto divide-y divide-gray-700">
              <div class="px-4 py-6 text-center text-gray-400">
                <p>Mulai mengetik untuk mencari...</p>
              </div>
            </div>
          </div>
        </div>

        {{-- ========== Notifications ========== --}}
        @php
          $unreadCount = \App\Http\Controllers\NotificationController::getUnreadCount();
        @endphp

        <button type="button"
                data-dropdown-toggle="user-notification-dropdown"
                id="notification-bell-button"
                class="p-2 mr-3 text-gray-400 rounded-full hover:bg-red-500/20 relative focus:outline-none focus:ring-2 focus:ring-red-500">
          <i class="fa-solid fa-bell fa-lg"></i>

          {{-- Ping & Badge --}}
          <span class="notification-ping absolute top-1 right-1 flex h-3 w-3 {{ $unreadCount > 0 ? '' : 'hidden' }}">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
          </span>
          <span class="notification-badge absolute -top-1 -right-1 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white bg-red-600 rounded-full min-w-[1.25rem] {{ $unreadCount > 0 ? '' : 'hidden' }}">
            {{ $unreadCount > 99 ? '99+' : $unreadCount }}
          </span>
        </button>

        <div id="user-notification-dropdown"
             class="z-50 hidden max-w-sm my-4 overflow-hidden text-base list-none bg-gray-800 divide-y divide-gray-700 rounded-lg shadow-lg border border-gray-700">
          <div class="block px-4 py-2 text-base font-medium text-center text-white bg-gray-900/50">
            Notifications
          </div>

          <div id="notification-list" class="max-h-96 overflow-y-auto">
            <div class="px-4 py-6 text-center text-gray-400">
              <p>Loading notifications...</p>
            </div>
          </div>

          <a href="{{ url('notifications') }}"
             class="block py-2 text-sm font-medium text-center text-white rounded-b-lg bg-gray-900/50 hover:bg-gray-700">
            <div class="inline-flex items-center">
              <i class="fa-solid fa-eye mr-2"></i>View all
            </div>
          </a>
        </div>

        {{-- ========== User dropdown ========== --}}
        <div class="flex items-center ms-3">
          <button type="button"
                  class="flex text-sm bg-gray-800 rounded-full focus:ring-4 focus:ring-red-500/50"
                  data-dropdown-toggle="dropdown-user">
            <img class="w-8 h-8 rounded-full object-cover"
                 src="{{ auth()->user()->avatar_url }}"
                 alt="user photo"
                 onerror="this.src='{{ asset('img/avatar-default.png') }}'">
          </button>

          <div id="dropdown-user"
               class="z-50 hidden my-4 text-base list-none bg-gray-800 divide-y divide-gray-700 rounded-md shadow-lg border border-gray-700">
            <div class="px-4 py-3">
              <p class="text-sm text-white">{{ auth()->user()->name }}</p>
              <p class="text-sm font-medium text-gray-400 truncate">{{ auth()->user()->email }}</p>
            </div>

            <ul class="py-1">
              <li>
                <a href="{{ route('profile.edit') }}"
                   class="block px-4 py-2 text-sm text-gray-400 hover:bg-gray-700 hover:text-white">
                  Profile
                </a>
              </li>
            </ul>
          </div>
        </div>

      </div>
    </div>
  </div>
</nav>

{{-- Search Script --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-navbar');
    const searchDropdown = document.getElementById('search-dropdown');
    const searchResults = document.getElementById('search-results');
    let searchTimeout = null;

    if (searchInput) {
        // Show dropdown on focus
        searchInput.addEventListener('focus', function() {
            if (this.value.trim().length > 0) {
                searchDropdown.classList.remove('hidden');
            }
        });

        // Hide dropdown on click outside
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !searchDropdown.contains(e.target)) {
                searchDropdown.classList.add('hidden');
            }
        });

        // Search on input with debounce
        searchInput.addEventListener('input', function() {
            const query = this.value.trim();

            clearTimeout(searchTimeout);

            if (query.length === 0) {
                searchDropdown.classList.add('hidden');
                searchResults.innerHTML = `
                    <div class="px-4 py-6 text-center text-gray-400">
                        <p>Mulai mengetik untuk mencari...</p>
                    </div>`;
                return;
            }

            if (query.length < 3) {
                searchDropdown.classList.remove('hidden');
                searchResults.innerHTML = `
                    <div class="px-4 py-6 text-center text-gray-400">
                        <i class="fa-solid fa-keyboard text-2xl mb-2 text-gray-600"></i>
                        <p>Ketik minimal 3 karakter...</p>
                    </div>`;
                return;
            }

            // Show loading
            searchDropdown.classList.remove('hidden');
            searchResults.innerHTML = `
                <div class="px-4 py-6 text-center text-gray-400">
                    <div class="inline-block w-8 h-8 border-4 border-gray-700 border-t-red-500 rounded-full animate-spin"></div>
                    <p class="mt-2">Mencari...</p>
                </div>`;

            // Debounce search
            searchTimeout = setTimeout(() => {
                performSearch(query);
            }, 500);
        });

        // Allow Enter key to search
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const query = this.value.trim();
                if (query.length >= 3) {
                    window.location.href = `/draft?search=${encodeURIComponent(query)}`;
                }
            }
        });
    }

    async function performSearch(query) {
        try {
            const response = await fetch(`{{ route('moms.search') }}?q=${encodeURIComponent(query)}`);

            if (!response.ok) {
                throw new Error('Search failed');
            }

            const data = await response.json();
            displayResults(data);

        } catch (error) {
            console.error('Search error:', error);
            searchResults.innerHTML = `
                <div class="px-4 py-6 text-center text-red-400">
                    <i class="fa-solid fa-exclamation-triangle text-2xl mb-2"></i>
                    <p>Gagal melakukan pencarian</p>
                </div>`;
        }
    }

    function displayResults(data) {
        searchResults.innerHTML = '';

        if (data.length === 0) {
            searchResults.innerHTML = `
                <div class="px-4 py-6 text-center text-gray-400">
                    <i class="fa-solid fa-search text-3xl mb-3 text-gray-600"></i>
                    <p class="font-medium">Tidak ada hasil ditemukan</p>
                    <p class="text-xs mt-1 text-gray-500">Coba gunakan kata kunci lain</p>
                </div>`;
            return;
        }

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

            // Status badge
            const statusInfo = getStatusInfo(mom.status);

            const item = document.createElement('a');
            item.href = `/moms/${mom.version_id}`;
            item.className = 'block px-4 py-3 hover:bg-gray-700 transition-colors border-b border-gray-700 last:border-b-0';
            item.innerHTML = `
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 rounded-full bg-red-500/10 flex items-center justify-center">
                            <i class="fa-solid fa-file-lines text-red-400"></i>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between mb-1">
                            <p class="text-sm font-semibold text-white truncate pr-2">
                                ${highlightText(mom.title, searchInput.value)}
                            </p>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium ${statusInfo.bgClass} ${statusInfo.textClass} rounded-full whitespace-nowrap">
                                <span class="w-1.5 h-1.5 rounded-full ${statusInfo.dotClass}"></span>
                                ${statusInfo.label}
                            </span>
                        </div>
                        <p class="text-xs text-gray-400 mb-1">
                            <i class="fa-solid fa-calendar-day mr-1"></i>${createdDate}
                            <span class="mx-1">•</span>
                            <i class="fa-solid fa-clock mr-1"></i>${createdTime}
                        </p>
                        ${mom.location ? `
                            <p class="text-xs text-gray-500 truncate">
                                <i class="fa-solid fa-map-marker-alt mr-1"></i>${mom.location}
                            </p>
                        ` : ''}
                    </div>
                </div>
            `;
            searchResults.appendChild(item);
        });

        // Add "View All" link if there are many results
        if (data.length >= 5) {
            const viewAllLink = document.createElement('a');
            viewAllLink.href = `/draft?search=${encodeURIComponent(searchInput.value)}`;
            viewAllLink.className = 'block px-4 py-3 text-center text-sm font-medium text-red-400 hover:bg-gray-700 hover:text-red-300 transition-colors border-t border-gray-700';
            viewAllLink.innerHTML = `
                <i class="fa-solid fa-arrow-right mr-2"></i>Lihat semua hasil
            `;
            searchResults.appendChild(viewAllLink);
        }
    }

    function getStatusInfo(status) {
        const statusMap = {
            'Disetujui': {
                label: 'Approved',
                dotClass: 'bg-green-500',
                bgClass: 'bg-green-500/10',
                textClass: 'text-green-400'
            },
            'Menunggu': {
                label: 'Pending',
                dotClass: 'bg-yellow-400',
                bgClass: 'bg-yellow-500/10',
                textClass: 'text-yellow-400'
            },
            'Ditolak': {
                label: 'Rejected',
                dotClass: 'bg-red-500',
                bgClass: 'bg-red-500/10',
                textClass: 'text-red-400'
            }
        };

        return statusMap[status] || {
            label: 'Unknown',
            dotClass: 'bg-gray-500',
            bgClass: 'bg-gray-500/10',
            textClass: 'text-gray-400'
        };
    }

    function highlightText(text, query) {
        if (!query) return text;

        const regex = new RegExp(`(${query})`, 'gi');
        return text.replace(regex, '<mark class="bg-red-500/30 text-red-300 px-1 rounded">$1</mark>');
    }
});
</script>

{{-- ========== NOTIFICATION SCRIPT ========== --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Cegah multiple initialization
    if (window.notificationInitialized) {
        console.log('Notification already initialized');
        return;
    }
    window.notificationInitialized = true;

    const notificationBadge = document.querySelector('.notification-badge');
    const notificationPing = document.querySelector('.notification-ping');
    const notificationList = document.getElementById('notification-list');

    function timeAgo(dateString) {
        const now = new Date();
        const past = new Date(dateString);
        const diffInSeconds = Math.floor((now - past) / 1000);

        if (diffInSeconds < 60) return 'Just now';
        if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)} minutes ago`;
        if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)} hours ago`;
        if (diffInSeconds < 2592000) return `${Math.floor(diffInSeconds / 86400)} days ago`;
        return `${Math.floor(diffInSeconds / 2592000)} months ago`;
    }

    async function fetchNotifications() {
        try {
            console.log('Fetching user notifications...');
            const response = await fetch("{{ route('notifications.recent') }}");

            if (!response.ok) {
                console.error('Failed to fetch notifications:', response.status);
                return;
            }

            const data = await response.json();
            console.log('Notification data received:', data);

            // Validasi data
            if (!data || !Array.isArray(data.notifications)) {
                console.error('Invalid notification data format');
                return;
            }

            // Update badge dan ping
            const unreadCount = data.unread_count || 0;

            if (notificationBadge) {
                if (unreadCount > 0) {
                    notificationBadge.textContent = unreadCount > 99 ? '99+' : unreadCount;
                    notificationBadge.classList.remove('hidden');
                } else {
                    notificationBadge.classList.add('hidden');
                }
            }

            if (notificationPing) {
                if (unreadCount > 0) {
                    notificationPing.classList.remove('hidden');
                } else {
                    notificationPing.classList.add('hidden');
                }
            }

            // Update notification list
            if (notificationList) {
                notificationList.innerHTML = '';

                if (data.notifications.length > 0) {
                    data.notifications.forEach(notification => {
                        // Validasi notification object
                        if (!notification || !notification.id) {
                            console.warn('Invalid notification object:', notification);
                            return;
                        }

                        const color = notification.color || 'red';
                        const icon = notification.icon || 'fa-solid fa-bell';

                        // PERBAIKAN: Gunakan created_at_human dari backend, fallback ke timeAgo
                        const timeDisplay = notification.created_at_human || timeAgo(notification.created_at) || 'Just now';

                        const isReadClass = !notification.is_read ? 'bg-red-900/20' : '';

                        const itemHtml = `
                            <a href="/notifications/${notification.id}/read"
                               class="flex px-4 py-3 border-b border-gray-700 hover:bg-gray-700 ${isReadClass}">
                                <div class="flex-shrink-0">
                                    <div class="inline-flex items-center justify-center w-8 h-8 bg-${color}-500/10 rounded-full">
                                        <i class="${icon} text-${color}-400"></i>
                                    </div>
                                </div>
                                <div class="w-full ps-3">
                                    <div class="text-white font-medium text-sm mb-1">
                                        ${notification.title || 'Notification'}
                                    </div>
                                    <div class="text-gray-400 text-sm mb-1.5">
                                        ${notification.message || ''}
                                    </div>
                                    <div class="text-xs text-red-400">
                                        ${timeDisplay}
                                    </div>
                                </div>
                            </a>
                        `;

                        notificationList.insertAdjacentHTML('beforeend', itemHtml);
                    });
                } else {
                    notificationList.innerHTML = `
                        <div class="px-4 py-6 text-center text-gray-400">
                            <p>No new notifications</p>
                        </div>
                    `;
                }
            }
        } catch (error) {
            console.error('Failed to fetch notifications:', error);
            if (notificationList) {
                notificationList.innerHTML = `
                    <div class="px-4 py-6 text-center text-red-400">
                        <p>Failed to load notifications</p>
                    </div>
                `;
            }
        }
    }

    // Fetch on page load
    fetchNotifications();

    // Auto refresh every 60 seconds
    const refreshInterval = setInterval(fetchNotifications, 60000);

    // Cleanup on unload
    window.addEventListener('beforeunload', function() {
        clearInterval(refreshInterval);
        window.notificationInitialized = false;
    });
});
</script>

{{-- ========== LOGOUT CONFIRMATION SCRIPT ========== --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const logoutButton = document.getElementById('logout-button');
    const logoutForm = document.getElementById('logout-form');

    if (logoutButton && logoutForm) {
        logoutButton.addEventListener('click', function (event) {
            event.preventDefault();

            Swal.fire({
                title: 'Anda yakin ingin keluar?',
                text: "Anda akan diarahkan kembali ke halaman login.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Keluar!',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'bg-gray-800 rounded-2xl border border-gray-700',
                    title: 'text-white font-orbitron',
                    htmlContainer: 'text-gray-400',
                    confirmButton: 'btn-neon-red text-white font-semibold px-6 py-2 mr-3 rounded-lg',
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
