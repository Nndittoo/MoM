<!DOCTYPE html>
{{-- Menggunakan 'class="dark"' untuk memaksakan mode gelap secara default --}}
<html lang="en" class="dark">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        {{-- PERUBAHAN: Judul dan Favicon disesuaikan dengan brand TR1 MoMatic --}}
        <title>@yield('title', 'TR1 MoMatic')</title>
        <link rel="icon" type="image/png" href="{{ asset('img/LOGO.png') }}"/>

        {{-- Link ke Font Awesome dan Flowbite --}}
        <link
            rel="stylesheet"
            href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link
            href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css"
            rel="stylesheet"/>

        {{-- Script Tailwind CSS --}}
        <script src="https://cdn.tailwindcss.com"></script>

        {{-- PERUBAHAN: Konfigurasi Tailwind dihapus dari sini, karena kita akan menggunakan kelas standar --}}
        {{-- <script> tailwind.config = { ... } </script> --}}

        {{-- PERUBAHAN: Semua gaya kustom disatukan di sini --}}
        <style>
            /* Import Font Futuristik */
            @import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@700&family=Inter:wght@400;500;600;700&display=swap');

            /* Default font untuk body */
            body {
                font-family: 'Inter', sans-serif;
            }

            .text-neon-red {
                color: #EF4444 !important;
                /* Tambahkan !important di sini */
                text-shadow: 0 0 5px rgba(239, 68, 68, 0.7);
            }

            .btn-pulse {
                animation: pulse-animation 2s infinite;
            }
            @keyframes pulse-animation {
                0% {
                    box-shadow: 0 0 8px rgba(239, 68, 68, 0.6);
                }
                50% {
                    box-shadow: 0 0 20px rgba(239, 68, 68, 0.9);
                }
                100% {
                    box-shadow: 0 0 8px rgba(239, 68, 68, 0.6);
                }
            }

            /* Kelas helper untuk font judul */
            .font-orbitron {
                font-family: 'Orbitron', sans-serif;
            }

            @keyframes logo-glow-pulse {
                from {
                    filter: drop-shadow(0 0 4px rgba(239, 68, 68, 0.7)) drop-shadow(0 0 8px rgba(239, 68, 68, 0.5));
                }
                to {
                    filter: drop-shadow(0 0 8px rgba(239, 68, 68, 1)) drop-shadow(0 0 16px rgba(239, 68, 68, 0.7));
                }
            }

            /* Class yang akan kita terapkan pada gambar logo */
            .logo-neon-glow {
                /* Menerapkan animasi yang sudah kita definisikan */
                animation: logo-glow-pulse 2.5s infinite alternate ease-in-out;
            }

            /* Pola latar belakang futuristik untuk area konten */
            .main-content-bg {
                background-color: #111827;
                /* gray-900 */
                background-image: radial-gradient(rgba(239, 68, 68, 0.1) 1px, transparent 1px);
                background-size: 30px 30px;
            }

            /* Custom Scrollbar untuk tema gelap */
            ::-webkit-scrollbar {
                width: 8px;
            }
            ::-webkit-scrollbar-track {
                background: #1F2937;
            }
            /* gray-800 */
            ::-webkit-scrollbar-thumb {
                background: #4B5563;
                border-radius: 10px;
            }
            /* gray-600 */
            ::-webkit-scrollbar-thumb:hover {
                background: #EF4444;
            }
            /* red-500 */

            /* Animasi Shimmer untuk Header */
            .shimmer-bg::after {
                content: '';
                position: absolute;
                top: 0;
                left: -150%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(239, 68, 68, 0.1), transparent);
                animation: shimmer 3s infinite linear;
            }
            @keyframes shimmer {
                from {
                    left: -100%;
                }
                to {
                    left: 100%;
                }
            }

            /* Animasi untuk daftar event */
            .event-item {
                opacity: 0;
                transform: translateY(10px);
                animation: fadeInSlideUp 0.5s ease-out forwards;
            }
            @keyframes fadeInSlideUp {
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .btn-neon-red {
            background-color: #EF4444;
            box-shadow: 0 0 8px rgba(239, 68, 68, 0.6);
            transition: all 0.3s ease-in-out;
            }
            .btn-neon-red:hover {
                background-color: #DC2626;
                box-shadow: 0 0 12px rgba(239, 68, 68, 0.8), 0 0 20px rgba(239, 68, 68, 0.5);
            }

            .ql-toolbar { border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem; background-color: #e4e4e4; border-color: #374151 !important; }
            .ql-container { border-bottom-left-radius: 0.5rem; border-bottom-right-radius: 0.5rem; background-color: #374151; border-color: #374151 !important; color: #D1D5DB; }
            .ql-editor.ql-blank::before { color: #9CA3AF !important; font-style: normal !important; }
            .ql-snow .ql-stroke { stroke: #9CA3AF; }
            .ql-snow .ql-picker-label { color: #9CA3AF; }
        </style>

        @stack('styles')
    </head>

    {{-- PERUBAHAN: Body menggunakan warna dasar dari tema gelap --}}
    <body class="bg-gray-900">

        {{-- Include Navbar dan Sidebar yang sudah diperbarui --}}
        @include('layouts.partials.navbar') @include('layouts.partials.sidebar')

        {{-- Main content area dengan latar belakang berpola --}}
        <main class="p-4 sm:ml-64 main-content-bg min-h-screen">
            {{-- Memberi padding-top agar konten tidak tertutup navbar --}}
            <div class="mt-14">
                @yield('content')
            </div>
        </main>

        <script
            src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
            {{-- Di dalam file layouts/app.blade.php --}}
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        @stack('scripts')

        {{-- PERUBAHAN: Script notifikasi disesuaikan dengan styling tema baru --}}
        <script>
            function timeAgo(dateString) {
                // ... (logika timeAgo tetap sama)
            }

            async function fetchNotifications() {
                try {
                    const response = await fetch("{{ route('notifications.recent') }}");
                    if (!response.ok)
                        return;

                    const data = await response.json();
                    const notificationBadge = document.querySelector('.notification-badge');
                    const notificationPing = document.querySelector('.notification-ping');
                    const notificationList = document.getElementById('notification-list');

                    // ... (logika update badge tetap sama) ...

                    notificationList.innerHTML = ''; // Kosongkan daftar

                    if (data.notifications.length > 0) {
                        data
                            .notifications
                            .forEach(notification => {
                                const isReadClass = !notification.is_read
                                    ? 'bg-red-900/20'
                                    : '';

                                // Ikon dan warna diseragamkan dengan tema merah
                                const color = 'red';
                                let icon = 'fa-bell';
                                if (notification.type === 'created') {
                                    icon = 'fa-file-circle-plus';
                                }

                                const notificationUrl = `/notifications/${notification.id}/read`;
                                const itemHtml = `
                        <a href="${notificationUrl}" class="flex px-4 py-3 border-b border-gray-700 hover:bg-gray-700 ${isReadClass}">
                            <div class="flex-shrink-0">
                                <div class="inline-flex items-center justify-center w-8 h-8 bg-${color}-500/10 rounded-full">
                                    <i class="fa-solid ${icon} text-${color}-500"></i>
                                </div>
                            </div>
                            <div class="w-full ps-3">
                                <div class="text-gray-400 text-sm mb-1.5">
                                    <span class="font-semibold text-white">${notification.title}</span>
                                    <p class="text-xs mt-1">${notification.message}</p>
                                </div>
                                <div class="text-xs text-red-400">${timeAgo(
                                    notification.created_at
                                )}</div>
                            </div>
                        </a>`;
                                notificationList.insertAdjacentHTML('beforeend', itemHtml);
                            });
                    } else {
                        notificationList.innerHTML = `<div class="px-4 py-6 text-center text-gray-400"><p>Belum ada notifikasi</p></div>`;
                    }
                } catch (error) {
                    console.error('Gagal mengambil notifikasi:', error);
                }
            }

            window.fetchNotifications = fetchNotifications;
            document.addEventListener('DOMContentLoaded', fetchNotifications);
        </script>
        <script>
    document.addEventListener('DOMContentLoaded', function () {
        const logoutButton = document.getElementById('logout-button');
        const logoutForm = document.getElementById('logout-form');

        if (logoutButton) {
            logoutButton.addEventListener('click', function (event) {
                // Mencegah form dari submit langsung
                event.preventDefault();

                // Tampilkan SweetAlert2 dengan tema kustom
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
                    buttonsStyling: false // Penting untuk menggunakan kelas kustom
                }).then((result) => {
                    // Jika pengguna menekan tombol "Ya, Keluar!"
                    if (result.isConfirmed) {
                        // Submit form logout
                        logoutForm.submit();
                    }
                });
            });
        }
    });
</script>
    </body>
</html>
