<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') | MoM Telkom</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet"/>
    <link rel="icon" type="image/png" href="{{ asset('img/LOGO_TELKOM.png') }}"/>


    {{-- Konfigurasi Tailwind --}}
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        'primary': '#DC2626',
                        'primary-dark': '#B91C1C',
                        'body-bg': '#F9FAFB',
                        'dark-body-bg': '#111827',
                        'component-bg': '#ffffff',
                        'dark-component-bg': '#1F2937',
                        'text-primary': '#1F2937',
                        'dark-text-primary': '#F3F4F6',
                        'text-secondary': '#6B7280',
                        'dark-text-secondary': '#9CA3AF',
                        'border-light': '#E5E7EB',
                        'border-dark': '#374151',
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

    {{-- Memuat Navbar Admin --}}
    @include('admin.layouts.partials.navbar')

    {{-- Memuat Sidebar Admin --}}
    @include('admin.layouts.partials.sidebar')

    {{-- Konten Utama Halaman --}}
    <main class="p-4 sm:ml-64">
        <div class="pt-14">
             @yield('content')
        </div>
    </main>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
    @stack('scripts')
</body>
</html>
