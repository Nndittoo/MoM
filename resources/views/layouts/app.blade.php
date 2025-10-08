<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MoM Telkom')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet"/>
    <link rel="icon" type="image/png" href="{{ asset('img/LOGO_TELKOM.png') }}"/>

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        'primary': '#DC2626', // Red-600
                        'primary-dark': '#B91C1C', // Red-700
                        'body-bg': '#F9FAFB', // Gray-50
                        'dark-body-bg': '#111827', // Gray-900
                        'component-bg': '#ffffff', // White
                        'dark-component-bg': '#1F2937', // Gray-800
                        'text-primary': '#1F2937', // Gray-800
                        'dark-text-primary': '#F3F4F6', // Gray-100
                        'text-secondary': '#6B7280', // Gray-500
                        'dark-text-secondary': '#9CA3AF', // Gray-400
                        'border-light': '#E5E7EB', // Gray-200
                        'border-dark': '#374151', // Gray-700
                    }
                }
            }
        }
    </script>
    <style>
        .text-gradient { color: #DC2626; }
        .bg-gradient-primary { background-color: #DC2626; }
    </style>
    @stack('styles')
</head>
<body class="bg-body-bg dark:bg-dark-body-bg">

    @include('layouts.partials.navbar')

    @include('layouts.partials.sidebar')

    <main class="p-4 sm:ml-64">
        @yield('content')
    </main>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
    @stack('scripts')
<script>
        // Fungsi untuk mengubah format waktu (helper)
        function timeAgo(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const seconds = Math.round((now - date) / 1000);
            const minutes = Math.round(seconds / 60);
            if (minutes < 1) return `baru saja`;
            if (minutes < 60) return `${minutes} menit lalu`;
            const hours = Math.round(minutes / 60);
            if (hours < 24) return `${hours} jam lalu`;
            const days = Math.round(hours / 24);
            return `${days} hari lalu`;
        }

        // Fungsi utama untuk mengambil dan merender notifikasi
        async function fetchNotifications() {
            try {
                // Panggil route yang memberikan data notifikasi terbaru dalam format JSON
                const response = await fetch("{{ route('notifications.recent') }}");
                if (!response.ok) return; // Jika gagal, hentikan

                const data = await response.json();

                const notificationBadge = document.querySelector('.notification-badge');
                const notificationPing = document.querySelector('.notification-ping');
                const notificationList = document.getElementById('notification-list');

                // Update badge jumlah notifikasi
                if (data.unread_count > 0) {
                    notificationBadge.textContent = data.unread_count > 99 ? '99+' : data.unread_count;
                    notificationBadge.classList.remove('hidden');
                    notificationPing.classList.remove('hidden');
                } else {
                    notificationBadge.classList.add('hidden');
                    notificationPing.classList.add('hidden');
                }

                // Kosongkan daftar notifikasi yang ada
                notificationList.innerHTML = '';

                // Tampilkan notifikasi baru
                if (data.notifications.length > 0) {
                    data.notifications.forEach(notification => {
                        const isReadClass = !notification.is_read ? 'bg-blue-50 dark:bg-blue-900/20' : '';
                        let color = 'gray', icon = 'fa-bell';
                        if(notification.type === 'created') { color = 'blue'; icon = 'fa-file-circle-plus'; }

                        const notificationUrl = `/notifications/${notification.id}/read`;
                        const itemHtml = `
                        <a href="${notificationUrl}" class="flex px-4 py-3 border-b hover:bg-body-bg dark:hover:bg-dark-body-bg dark:border-border-dark ${isReadClass}">
                            <div class="flex-shrink-0">
                                <div class="inline-flex items-center justify-center w-8 h-8 bg-${color}-100 rounded-full dark:bg-${color}-900">
                                    <i class="fa-solid ${icon} text-${color}-500"></i>
                                </div>
                            </div>
                            <div class="w-full ps-3">
                                <div class="text-text-secondary text-sm mb-1.5 dark:text-dark-text-secondary">
                                    <span class="font-semibold text-text-primary dark:text-white">${notification.title}</span>
                                    <p class="text-xs mt-1">${notification.message}</p>
                                </div>
                                <div class="text-xs text-blue-600 dark:text-blue-500">${timeAgo(notification.created_at)}</div>
                            </div>
                        </a>`;
                        notificationList.insertAdjacentHTML('beforeend', itemHtml);
                    });
                } else {
                    notificationList.innerHTML = `<div class="px-4 py-6 text-center text-text-secondary dark:text-dark-text-secondary"><p>Belum ada notifikasi</p></div>`;
                }
            } catch (error) {
                console.error('Gagal mengambil notifikasi:', error);
            }
        }

        // Jadikan fungsi ini global agar bisa dipanggil dari mana saja
        window.fetchNotifications = fetchNotifications;

        // Panggil saat halaman pertama kali dimuat
        document.addEventListener('DOMContentLoaded', fetchNotifications);
    </script>

</body>
</html>
