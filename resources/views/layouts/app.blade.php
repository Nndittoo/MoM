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
</body>
</html>
