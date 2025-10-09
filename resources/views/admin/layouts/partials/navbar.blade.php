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

                <a href="{{ route('admin.dashboard') }}" class="flex ms-2 md:me-24 items-center">
                    <img src="{{ asset('img/LOGO_TELKOM.png') }}" class="h-12 mr-3" alt="Telkom Logo" />
                    <span class="self-center text-xl font-semibold sm:text-2xl whitespace-nowrap dark:text-white">
                        Admin Panel
                    </span>
                </a>
            </div>

            {{-- === Right Section === --}}
            <div class="flex items-center">

                {{-- Notification Dropdown --}}
                <button type="button" data-dropdown-toggle="notification-dropdown" id="notification-bell-button" class="p-2 mr-3 text-text-secondary rounded-full hover:bg-primary/10 relative dark:text-dark-text-secondary dark:hover:bg-primary/20">
                    <i class="fa-solid fa-bell fa-lg"></i>
                    {{-- Indikator Notifikasi akan ditambahkan oleh JS --}}
                </button>
                <div id="notification-dropdown" class="z-50 hidden max-w-sm my-4 overflow-hidden text-base list-none bg-component-bg divide-y divide-border-light rounded-lg shadow-lg dark:bg-dark-component-bg dark:divide-border-dark">
                    <div class="block px-4 py-2 text-base font-medium text-center text-text-primary bg-body-bg dark:bg-dark-component-bg/50 dark:text-dark-text-primary">
                        Notifications
                    </div>
                    {{-- Konten Notifikasi akan diisi oleh JS --}}
                    <div id="notification-items-container">
                        {{-- Placeholder saat loading --}}
                        <div class="p-4 text-center text-sm text-text-secondary dark:text-dark-text-secondary">
                            Loading notifications...
                        </div>
                    </div>
                    <a href="{{ route("admin.notification") }}" class="block py-2 text-sm font-medium text-center text-text-primary rounded-b-lg bg-body-bg hover:bg-border-light dark:bg-dark-component-bg/50 dark:hover:bg-dark-body-bg dark:text-white">
                        <div class="inline-flex items-center">
                            <i class="fa-solid fa-eye mr-2"></i>Lihat semua notifikasi
                        </div>
                    </a>
                </div>

                {{-- User Profile Dropdown --}}
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
            </div>
        </div>
    </div>
</nav>

{{-- SCRIPT UNTUK NOTIFIKASI DINAMIS --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const bellButton = document.getElementById('notification-bell-button');
    const dropdownContainer = document.getElementById('notification-items-container');

    // Template untuk indikator (titik merah)
    const notificationIndicatorTemplate = `
        <span class="absolute top-1 right-1 flex h-3 w-3">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
        </span>
    `;

    // Template untuk satu item notifikasi
    function createNotificationItem(notification) {
        return `
            <a href="${notification.url}" class="flex px-4 py-3 border-b hover:bg-body-bg dark:hover:bg-dark-body-bg dark:border-border-dark ${!notification.is_read ? 'bg-blue-50 dark:bg-blue-900/20' : ''}">
                <div class="flex-shrink-0">
                    <div class="inline-flex items-center justify-center w-8 h-8 bg-${notification.color}-100 rounded-full dark:bg-${notification.color}-900">
                        <i class="${notification.icon} text-${notification.color}-500"></i>
                    </div>
                </div>
                <div class="w-full ps-3">
                    <div class="text-text-secondary text-sm mb-1.5 dark:text-dark-text-secondary">
                        ${notification.message}
                    </div>
                    <div class="text-xs text-blue-600 dark:text-blue-500">${notification.created_at_human}</div>
                </div>
            </a>
        `;
    }

    // Fungsi untuk mengambil dan menampilkan data
    async function fetchNotifications() {
        try {
            const response = await fetch('{{ route("admin.notifications.recent") }}');
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            const data = await response.json();

            // 1. Update Indikator Lonceng
            // Hapus indikator lama jika ada
            const existingIndicator = bellButton.querySelector('span');
            if (existingIndicator) {
                existingIndicator.remove();
            }
            // Tambahkan indikator baru jika ada notif belum dibaca
            if (data.unread_count > 0) {
                bellButton.insertAdjacentHTML('beforeend', notificationIndicatorTemplate);
            }

            // 2. Update Isi Dropdown
            dropdownContainer.innerHTML = ''; // Kosongkan container
            if (data.notifications.length > 0) {
                data.notifications.forEach(notification => {
                    dropdownContainer.innerHTML += createNotificationItem(notification);
                });
            } else {
                dropdownContainer.innerHTML = `
                    <div class="p-4 text-center text-sm text-text-secondary dark:text-dark-text-secondary">
                        Tidak ada notifikasi baru.
                    </div>
                `;
            }
        } catch (error) {
            console.error('Failed to fetch notifications:', error);
            dropdownContainer.innerHTML = `
                <div class="p-4 text-center text-sm text-red-500">
                    Gagal memuat notifikasi.
                </div>
            `;
        }
    }

    fetchNotifications();

});
</script>
