<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Notifikasi | MoM</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet"/>

  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          colors: {
            'primary': '#50b5ff',
            'primary-dark': '#5983fb',
            'body-bg': '#f7f8fa',
            'dark-body-bg': '#15181e',
            'component-bg': '#ffffff',
            'dark-component-bg': '#272d3b',
            'text-primary': '#272d3b',
            'dark-text-primary': '#e0e0e0',
            'text-secondary': '#8a92a6',
            'dark-text-secondary': '#a0a0a0',
            'border-light': '#efefef',
            'border-dark': '#3d414c'
          }
        }
      }
    }
  </script>
  <style>
    .text-gradient {
      background: linear-gradient(to right, #50b5ff 0%, #5983fb 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }
    .bg-gradient-primary {
      background-image: linear-gradient(to right, #50b5ff 0%, #5983fb 100%);
    }
    .aspect-square {
      aspect-ratio: 1 / 1;
    }
  </style>
</head>
<body class="bg-body-bg dark:bg-dark-body-bg">

  <!-- Navbar -->
  <nav class="fixed top-0 z-50 w-full bg-component-bg border-b border-border-light dark:bg-dark-component-bg dark:border-border-dark shadow-sm">
    <div class="px-3 py-3 lg:px-5 lg:pl-3">
      <div class="flex items-center justify-between">
        <div class="flex items-center justify-start rtl:justify-end">
          <button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar" aria-controls="logo-sidebar"
            type="button"
            class="inline-flex items-center p-2 text-sm text-text-secondary rounded-lg sm:hidden hover:bg-primary/10 focus:outline-none focus:ring-2 focus:ring-primary/20 dark:text-dark-text-secondary dark:hover:bg-primary/20 dark:focus:ring-primary/50">
            <span class="sr-only">Open sidebar</span>
            <i class="fa-solid fa-bars w-6 h-6"></i>
          </button>
          <a href="#" class="flex ms-2 md:me-24 items-center">
            <img src="LOGO_TELKOM.png" class="h-8 mr-2" alt="Telkom Logo">
            <span class="self-center text-xl font-semibold sm:text-2xl whitespace-nowrap dark:text-white">MoM</span>
          </a>
        </div>
        <div class="flex items-center">
          <div class="relative hidden md:block w-64 lg:w-96 mr-4">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
              <i class="fa-solid fa-search text-text-secondary"></i>
            </div>
            <input type="text" id="search-navbar"
              class="block w-full p-2 pl-10 text-sm text-text-primary border border-border-light rounded-lg bg-body-bg focus:ring-primary focus:border-primary dark:bg-dark-component-bg dark:border-border-dark dark:placeholder-dark-text-secondary dark:text-white"
              placeholder="Search...">
          </div>
          <button type="button"
            class="p-2 mr-3 text-text-secondary rounded-full hover:bg-primary/10 relative dark:text-dark-text-secondary dark:hover:bg-primary/20">
            <i class="fa-solid fa-bell fa-lg"></i>
            <span
              class="absolute inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 rounded-full -top-1 -right-1">3</span>
          </button>
        </div>
      </div>
    </div>
  </nav>

  <!-- Sidebar -->
  <aside id="logo-sidebar"
    class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform -translate-x-full bg-component-bg border-r border-border-light sm:translate-x-0 dark:bg-dark-component-bg dark:border-border-dark"
    aria-label="Sidebar">
    <div class="h-full px-3 pb-4 overflow-y-auto">
      <ul class="space-y-2 font-medium">
        <li>
          <a href="#"
            class="flex items-center p-2 text-text-primary rounded-lg dark:text-dark-text-primary hover:bg-primary/10 dark:hover:bg-primary/20">
            <i class="fa-solid fa-chart-pie w-6 h-6"></i>
            <span class="ms-3">Dashboard</span>
          </a>
        </li>
        <li>
          <a href="#"
            class="flex items-center p-2 text-text-primary rounded-lg dark:text-dark-text-primary hover:bg-primary/10 dark:hover:bg-primary/20">
            <i class="fa-solid fa-calendar-days w-6 h-6"></i>
            <span class="ms-3">Calendar</span>
          </a>
        </li>
        <li>
          <a href="#"
            class="flex items-center p-2 text-text-primary rounded-lg dark:text-dark-text-primary bg-primary/10 dark:bg-primary/20">
            <i class="fa-solid fa-bell w-6 h-6"></i>
            <span class="ms-3">Notifikasi</span>
          </a>
        </li>
      </ul>
    </div>
  </aside>

  <!-- Main Content -->
  <div class="p-4 sm:ml-64 mt-20">
    <div class="p-6 bg-white dark:bg-dark-component-bg rounded-lg shadow-md">
      <h1 class="text-2xl font-bold mb-6">Notifikasi</h1>

      <!-- Notification Cards -->
      <div class="space-y-4">

        <!-- Anggota Baru (sekarang paling atas) -->
        <div class="p-4 bg-white border border-border-light rounded-lg shadow-sm dark:bg-dark-component-bg dark:border-border-dark flex items-start gap-3">
          <i class="fa-solid fa-user-plus text-blue-500 text-xl"></i>
          <div>
            <h2 class="font-semibold text-text-primary dark:text-dark-text-primary">Anggota Baru Bergabung</h2>
            <p class="text-sm text-text-secondary dark:text-dark-text-secondary">Yamin baru saja bergabung ke dalam grup MoM kamu.</p>
            <span class="text-xs text-gray-500">5 jam yang lalu</span>
          </div>
        </div>

        <!-- KOM (sekarang nomor 2) -->
        <div class="p-4 bg-white border border-border-light rounded-lg shadow-sm dark:bg-dark-component-bg dark:border-border-dark flex items-start gap-3">
          <i class="fa-solid fa-calendar-check text-green-500 text-xl"></i>
          <div>
            <h2 class="font-semibold text-text-primary dark:text-dark-text-primary">KOM (Kick Off Meeting) Project NIQE 2025</h2>
            <p class="text-sm text-text-secondary dark:text-dark-text-secondary">
                 Time: Oct 1, 2025 08.30 am <br>
            </p>
            <span class="text-xs text-gray-500">1 hari 40 menit yang lalu</span>
          </div>
        </div>

        <!-- Notulen tetap nomor 3 -->
        <div class="p-4 bg-white border border-border-light rounded-lg shadow-sm dark:bg-dark-component-bg dark:border-border-dark flex items-start gap-3">
          <i class="fa-solid fa-file-lines text-yellow-500 text-xl"></i>
          <div>
            <h2 class="font-semibold text-text-primary dark:text-dark-text-primary">Notulen Siap</h2>
            <p class="text-sm text-text-secondary dark:text-dark-text-secondary">
                Notulen rapat “VALIDASI NEW ORDER MINI OLT PLATFORM ZTE (14 September 2025)” sudah tersedia dan bisa diunduh.</p>
            <span class="text-xs text-gray-500">15 September 2025</span>
          </div>
        </div>

      </div>
    </div>
  </div>

</body>
</html>
