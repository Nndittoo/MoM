<nav class="fixed top-0 z-50 w-full bg-gray-800 border-b border-gray-700 shadow-md">
    <div class="px-3 py-3 lg:px-5 lg:pl-3">
        <div class="flex items-center justify-between">
            {{-- === Left Section === --}}
            <div class="flex items-center justify-start rtl:justify-end">
                <button
                    data-drawer-target="admin-sidebar"
                    data-drawer-toggle="admin-sidebar"
                    type="button"
                    class="inline-flex items-center p-2 text-sm text-gray-400 rounded-lg sm:hidden hover:bg-red-500/20 focus:outline-none focus:ring-2 focus:ring-red-500">
                    <i class="fa-solid fa-bars w-6 h-6"></i>
                </button>

                <a href="{{ route('admin.dashboard') }}" class="flex ms-2 items-center">
                    <img src="{{ asset('img/LOGO.png') }}" class="logo-neon-glow h-10 mr-3" alt="TR1 MoMatic Logo" />
                    <span class="self-center text-xl font-bold font-orbitron text-neon-red hidden sm:block">
                        Admin Panel
                    </span>
                </a>
            </div>

            {{-- === Right Section === --}}
            <div class="flex items-center">
                {{-- Notification Dropdown --}}
                <button type="button" data-dropdown-toggle="notification-dropdown" id="notification-bell-button" class="p-2 mr-3 text-gray-400 rounded-full hover:bg-red-500/20 relative focus:outline-none focus:ring-2 focus:ring-red-500">
                    <i class="fa-solid fa-bell fa-lg"></i>
                    {{-- Indikator Notifikasi akan ditambahkan oleh JS --}}
                </button>
                <div id="notification-dropdown" class="z-50 hidden max-w-sm my-4 overflow-hidden text-base list-none bg-gray-800 divide-y divide-gray-700 rounded-lg shadow-lg border border-gray-700">
                    <div class="block px-4 py-2 text-base font-medium text-center text-white bg-gray-900/50">
                        Notifications
                    </div>
                    <div id="notification-items-container" class="max-h-96 overflow-y-auto">
                        <div class="p-4 text-center text-sm text-gray-400">
                            Loading notifications...
                        </div>
                    </div>
                    <a href="{{ route("admin.notification") }}" class="block py-2 text-sm font-medium text-center text-white rounded-b-lg bg-gray-900/50 hover:bg-gray-700">
                        <div class="inline-flex items-center">
                            <i class="fa-solid fa-eye mr-2"></i>Lihat semua notifikasi
                        </div>
                    </a>
                </div>

                {{-- User Profile Dropdown --}}
                @auth
                <div class="flex items-center ms-3">
                    <button type="button" class="flex text-sm bg-gray-800 rounded-full focus:ring-4 focus:ring-red-500/50" data-dropdown-toggle="dropdown-user">
                        <img class="w-8 h-8 rounded-full object-cover" src="{{ auth()->user()->avatar_url }}" alt="avatar" onerror="this.src='{{ asset('img/avatar-default.png') }}'">
                    </button>
                    <div id="dropdown-user" class="z-50 hidden my-4 text-base list-none bg-gray-800 divide-y divide-gray-700 rounded-md shadow-lg border border-gray-700">
                        <div class="px-4 py-3">
                            <p class="text-sm text-white">{{ auth()->user()->name }}</p>
                            <p class="text-sm font-medium text-gray-400 truncate">{{ auth()->user()->email }}</p>
                        </div>
                        <ul class="py-1">
                            <li>
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-400 hover:bg-gray-700 hover:text-white">
                                    Profile
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                @endauth
            </div>
        </div>
    </div>
</nav>

{{-- SCRIPT UNTUK NOTIFIKASI DINAMIS (DENGAN PENYESUAIAN TEMA) --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const bellButton = document.getElementById('notification-bell-button');
    const dropdownContainer = document.getElementById('notification-items-container');

    const notificationIndicatorTemplate = `
        <span class="absolute top-1 right-1 flex h-3 w-3">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
        </span>
    `;

    function createNotificationItem(notification) {
        // Gunakan warna merah sebagai default jika tidak ada, dan sesuaikan warna teks
        const color = notification.color || 'red';
        return `
            <a href="${notification.url}" class="flex px-4 py-3 border-b border-gray-700 hover:bg-gray-700 ${!notification.is_read ? 'bg-red-900/20' : ''}">
                <div class="flex-shrink-0">
                    <div class="inline-flex items-center justify-center w-8 h-8 bg-${color}-500/10 rounded-full">
                        <i class="${notification.icon} text-${color}-400"></i>
                    </div>
                </div>
                <div class="w-full ps-3">
                    <div class="text-gray-400 text-sm mb-1.5">
                        ${notification.message}
                    </div>
                    <div class="text-xs text-red-400">${notification.created_at_human}</div>
                </div>
            </a>
        `;
    }

    async function fetchNotifications() {
        try {
            const response = await fetch('{{ route("admin.notifications.recent") }}');
            if (!response.ok) throw new Error('Network response was not ok');
            const data = await response.json();

            // 1. Update Indikator Lonceng
            const existingIndicator = bellButton.querySelector('span');
            if (existingIndicator) existingIndicator.remove();
            if (data.unread_count > 0) {
                bellButton.insertAdjacentHTML('beforeend', notificationIndicatorTemplate);
            }

            // 2. Update Isi Dropdown
            dropdownContainer.innerHTML = '';
            if (data.notifications.length > 0) {
                data.notifications.forEach(notification => {
                    dropdownContainer.innerHTML += createNotificationItem(notification);
                });
            } else {
                dropdownContainer.innerHTML = `<div class="p-4 text-center text-sm text-gray-500">Tidak ada notifikasi baru.</div>`;
            }
        } catch (error) {
            console.error('Failed to fetch notifications:', error);
            dropdownContainer.innerHTML = `<div class="p-4 text-center text-sm text-red-500">Gagal memuat notifikasi.</div>`;
        }
    }

    fetchNotifications();
});
</script>
