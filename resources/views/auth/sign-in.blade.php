<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign In - Telkom Indonesia</title>

  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
  <!-- Background putih -->
  <div class="min-h-screen bg-white text-gray-900 flex justify-center items-center">

    <!-- Card utama -->
    <div class="max-w-4xl w-full bg-white shadow-xl rounded-3xl flex flex-row h-[600px] overflow-hidden
                border-4 border-gray-200 relative">
      <!-- Border lapisan kedua -->
      <div class="absolute inset-0 rounded-3xl border border-gray-400 pointer-events-none"></div>

      <!-- Left Side Form -->
      <div class="w-1/2 p-6 sm:p-12 flex flex-col justify-center relative z-10">
        <div>
          <img src="LOGO_TELKOM.png" class="w-32 mx-auto" alt="Telkom Logo" />
        </div>
        <div class="mt-12 flex flex-col items-center">
          <h1 class="text-2xl xl:text-3xl font-extrabold text-red-600">
            Sign In
          </h1>
          <div class="w-full flex-1 mt-8">

            <!-- Email Form -->
            <div class="mx-auto max-w-xs">
              <input
                class="w-full px-8 py-4 rounded-lg font-medium bg-gray-100 border border-gray-200 placeholder-gray-500 text-sm focus:outline-none focus:border-red-400 focus:bg-white"
                type="email" placeholder="Email" />
              <input
                class="w-full px-8 py-4 rounded-lg font-medium bg-gray-100 border border-gray-200 placeholder-gray-500 text-sm focus:outline-none focus:border-red-400 focus:bg-white mt-5"
                type="password" placeholder="Password" />
              <a href="{{ route('user.index') }}"
                class="mt-5 tracking-wide font-semibold bg-red-600 text-white w-full py-4 rounded-lg hover:bg-red-700 transition-all duration-300 ease-in-out flex items-center justify-center focus:shadow-outline focus:outline-none">
                <svg class="w-6 h-6 -ml-2" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                  stroke-linejoin="round">
                  <path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" />
                  <circle cx="8.5" cy="7" r="4" />
                  <path d="M20 8v6M23 11h-6" />
                </svg>
                <span class="ml-3">Sign In</span>
              </a>
              <p class="mt-6 text-xs text-gray-600 text-center">
                I agree to abide by
                <a href="#" class="border-b border-gray-500 border-dotted">
                  Terms of Service
                </a>
                and its
                <a href="#" class="border-b border-gray-500 border-dotted">
                  Privacy Policy
                </a>
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Side -->
      <div class="w-1/2 relative flex z-10">
        <!-- Background Image -->
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-90"
          style="background-image: url('telkom2.png');"></div>
        <!-- Overlay merah transparan -->
        <div class="absolute inset-0 bg-red-700 bg-opacity-30"></div>
        <div class="relative w-full flex items-center justify-center">
          <h2 class="text-white font-bold text-3xl drop-shadow-lg">Welcome to MoM</h2>
        </div>
      </div>

    </div>
  </div>
</body>

</html>
