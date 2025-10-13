{{-- ======================================================= --}}
{{--                  NAVBAR BLADE YANG DIPERBARUI           --}}
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

        {{-- PERUBAHAN: Logo diubah ke TR1 MoMatic --}}
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

          {{-- PERUBAHAN: Styling input search --}}
          <input
            type="text"
            id="search-navbar"
            class="block w-full p-2 pl-10 text-sm text-white border border-gray-700 rounded-lg bg-gray-900 focus:ring-1 focus:ring-red-500 focus:border-red-500"
            placeholder="Search MoM..." />

          {{-- PERUBAHAN: Styling dropdown search --}}
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
                class="p-2 mr-3 text-gray-400 rounded-full hover:bg-red-500/20 relative focus:outline-none focus:ring-2 focus:ring-red-500">
          <i class="fa-solid fa-bell fa-lg"></i>

          {{-- Ping & Badge (sudah merah, jadi tidak perlu diubah) --}}
          <span class="notification-ping absolute top-1 right-1 flex h-3 w-3 {{ $unreadCount > 0 ? '' : 'hidden' }}">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
          </span>
          <span class="notification-badge absolute -top-1 -right-1 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white bg-red-600 rounded-full min-w-[1.25rem] {{ $unreadCount > 0 ? '' : 'hidden' }}">
            {{ $unreadCount > 99 ? '99+' : $unreadCount }}
          </span>
        </button>

        <div id="user-notification-dropdown"
             class="z-50 hidden max-w-sm my-4 overflow-hidden text-base list-none bg-gray-800 divide-y divide-gray-700 rounded-lg shadow-lg">
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
               class="z-50 hidden my-4 text-base list-none bg-gray-800 divide-y divide-gray-700 rounded-md shadow-lg">
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
              <li>
                <form method="POST" action="{{ route('logout') }}">
                  @csrf
                  <button type="submit"
                     class="w-full text-left block px-4 py-2 text-sm text-red-400 hover:bg-red-500/20 hover:text-red-300">
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

{{-- ===== JS: Search dropdown (PERUBAHAN Styling) ===== --}}
<script>
// ... (Bagian logika JS Anda tetap sama)
// PERUBAHAN hanya di dalam render hasil pencarian untuk menyesuaikan styling ikon
// Ganti bagian `item.innerHTML` di dalam `forEach` dengan kode di bawah ini:

/*
  ...
  data.forEach(mom => {
    // ... (logika tanggal Anda)

    const item = document.createElement('div');
    item.className = 'block px-4 py-3 hover:bg-gray-700 transition cursor-pointer';
    item.innerHTML = `
      <a href="/moms/${mom.version_id}" class="flex items-center space-x-3">
        <div class="flex-shrink-0">
          {{-- Ikon search result disesuaikan dengan tema merah --}}
          <div class="w-10 h-10 rounded-full bg-red-500/10 flex items-center justify-center">
            <i class="fa-solid fa-file-alt text-red-500 text-lg"></i>
          </div>
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-sm font-medium text-white truncate">${mom.title}</p>
          <p class="text-xs text-gray-400">${createdDate}, ${createdTime}</p>
        </div>
      </a>`;
    resultsContainer.appendChild(item);
  });
  ...
*/
</script>
