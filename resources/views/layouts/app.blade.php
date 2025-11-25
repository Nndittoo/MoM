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

            .ql-snow .ql-stroke { stroke: #9CA3AF; }
            .ql-snow .ql-fill { fill: #9CA3AF; }
            .ql-snow .ql-picker { color: #9CA3AF; }
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

        <script type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/9.22.0/firebase-app.js";
        import { getMessaging, getToken, onMessage } from "https://www.gstatic.com/firebasejs/9.22.0/firebase-messaging.js";

        const firebaseConfig = {
            apiKey: "{{ config('services.firebase.api_key') }}",
            authDomain: "{{ config('services.firebase.auth_domain') }}",
            projectId: "{{ config('services.firebase.project_id') }}",
            messagingSenderId: "{{ config('services.firebase.messaging_sender_id') }}",
            appId: "{{ config('services.firebase.app_id') }}"
        };

        const vapidKey = "{{ config('services.firebase.vapid_key') }}";

        const app = initializeApp(firebaseConfig);
        const messaging = getMessaging(app);

        async function registerDeviceForPush() {
        try {
            // register service worker
            const swRegistration = await navigator.serviceWorker.register('/firebase-messaging-sw.js');

            // minta permission
            const permission = await Notification.requestPermission();
            if (permission !== 'granted') return console.log('Permission not granted');

            // ambil token FCM
            const currentToken = await getToken(messaging, { vapidKey, serviceWorkerRegistration: swRegistration });
            if (currentToken) {
                // kirim ke server
                await fetch("{{ route('device-tokens.store') }}", {
                    method: 'POST',
                    headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({ token: currentToken, platform: 'web' })
                });
            }
            } catch (err) {
                console.error('Unable to get permission to notify.', err);
            }
            }

            // optional: handle message while page is in foreground
            onMessage(messaging, (payload) => {
            console.log('Message received. ', payload);
            // you can show a small in-app popup or browser Notification
            if (Notification.permission === 'granted') {
                new Notification(payload.notification.title, {
                body: payload.notification.body
                });
            }
            });

            // panggil function ketika halaman terbuka atau user klik ikon enable
            if ('serviceWorker' in navigator && 'Notification' in window) {
            // bisa panggil registerDeviceForPush() dengan tombol "Enable Notifications" atau otomatis setelah login
            registerDeviceForPush();
            }
        </script>
    </body>
</html>
