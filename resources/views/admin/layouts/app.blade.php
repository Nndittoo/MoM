<!DOCTYPE html>
<html lang="id" class="dark">
    {{-- Mode gelap diaktifkan secara default --}}
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'Admin Dashboard') | TR1 MoMatic</title>

        {{-- PERUBAHAN: Ikon diganti dengan logo TR1 MoMatic --}}
        <link rel="icon" type="image/png" href="{{ asset('img/LOGO.png') }}"/>

        <link
            rel="stylesheet"
            href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <script src="https://cdn.tailwindcss.com"></script>
        <link
            href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css"
            rel="stylesheet"/>

        {{-- PERUBAHAN: Konfigurasi Tailwind dihapus dari sini, kita gunakan kelas standar --}}

        {{-- PERUBAHAN: Semua gaya kustom disatukan di sini --}}
        <style>
            .ql-toolbar { border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem; background-color: #1F2937; border-color: #374151 !important; }
            .ql-container { border-bottom-left-radius: 0.5rem; border-bottom-right-radius: 0.5rem; background-color: #374151; border-color: #374151 !important; color: #D1D5DB; }
            .ql-editor.ql-blank::before { color: #9CA3AF !important; font-style: normal !important; }
            .ql-snow .ql-stroke { stroke: #9CA3AF; }
            .ql-snow .ql-picker-label { color: #9CA3AF; }
            /* Import Font Futuristik */
            @import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@700&family=Inter:wght@400;500;600;700&display=swap');

            body {
                font-family: 'Inter', sans-serif;
            }

            @keyframes spin-slow {
                from {
                    transform: rotate(0deg);
                }
                to {
                    transform: rotate(360deg);
                }
            }
            .animate-spin-slow {
                animation: spin-slow 2s linear infinite;
            }

            @keyframes slide-in {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
            .animate-slide-in {
                animation: slide-in 0.3s ease-out;
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

            .font-orbitron {
                font-family: 'Orbitron', sans-serif;
            }

            /* Pola latar belakang futuristik untuk area konten */
            .main-content-bg {
                background-color: #111827;
                /* gray-900 */
                background-image: radial-gradient(rgba(239, 68, 68, 0.1) 1px, transparent 1px);
                background-size: 30px 30px;
            }

            /* Kelas-kelas helper untuk tema neon-red */
            .text-neon-red {
                color: #EF4444;
                /* red-500 */
                text-shadow: 0 0 5px rgba(239, 68, 68, 0.7);
            }

            .btn-neon-red {
                background-color: #EF4444;
                /* red-500 */
                box-shadow: 0 0 8px rgba(239, 68, 68, 0.6);
                transition: all 0.3s ease-in-out;
            }
            .btn-neon-red:hover {
                background-color: #DC2626;
                /* red-600 */
                box-shadow: 0 0 12px rgba(239, 68, 68, 0.8), 0 0 20px rgba(239, 68, 68, 0.5);
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
        </style>

        @stack('styles')
    </head>
    <body class="bg-gray-900">

        @include('admin.layouts.partials.navbar')
        @include('admin.layouts.partials.sidebar')

        {{-- Konten Utama Halaman dengan Latar Berpola --}}
        <main class="p-4 sm:ml-64 main-content-bg min-h-screen">
            <div class="pt-14">
                @yield('content')
            </div>
        </main>

        <script
            src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
        {{-- Di dalam file admin/layouts/app.blade.php --}}
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        @stack('scripts')
    </body>
</html>
