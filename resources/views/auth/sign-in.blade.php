<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - Telkom Indonesia</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
    <div class="min-h-screen bg-gray-50 text-gray-900 flex justify-center items-center p-4">

        <div class="max-w-4xl w-full bg-white shadow-xl rounded-2xl flex flex-row h-[600px] overflow-hidden border border-gray-200">

            <div class="w-full md:w-1/2 p-6 sm:p-12 flex flex-col justify-center">
                <div class="flex justify-center md:justify-start">
                    <img src="{{ asset("img/logo.png") }}" class="w-32" alt="Telkom Logo" />
                </div>
                <div class="mt-10 flex flex-col items-center">
                    <div class="w-full flex-1">
                        <h1 class="text-2xl xl:text-3xl font-extrabold text-center md:text-left text-gray-800">
                            Sign In
                        </h1>

                        <form action="{{ route('login.post') }}" method="POST" class="mx-auto max-w-xs mt-8">
                            @csrf

                            <input
                                name="email"
                                value="{{ old('email') }}"
                                class="w-full px-6 py-3 rounded-lg font-medium bg-gray-100 border border-gray-200 placeholder-gray-500 text-sm focus:outline-none focus:border-red-400 focus:bg-white"
                                type="email" placeholder="Email" required />

                            <input
                                name="password"
                                class="w-full px-6 py-3 rounded-lg font-medium bg-gray-100 border border-gray-200 placeholder-gray-500 text-sm focus:outline-none focus:border-red-400 focus:bg-white mt-5"
                                type="password" placeholder="Password" required />

                            @if ($errors->any())
                                <div class="mt-3 text-red-500 text-sm text-center">
                                    {{ $errors->first() }}
                                </div>
                            @endif

                            <button type="submit"
                                class="mt-5 tracking-wide font-semibold bg-red-600 text-white w-full py-3 rounded-lg hover:bg-red-700 transition-all duration-300 ease-in-out flex items-center justify-center focus:shadow-outline focus:outline-none">
                                <svg class="w-6 h-6 -ml-2" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" />
                                    <circle cx="8.5" cy="7" r="4" />
                                    <path d="M20 8v6M23 11h-6" />
                                </svg>
                                <span class="ml-3">Sign In</span>
                            </button>
                        </form>

                        <p class="mt-6 text-sm text-gray-600 text-center">
                            Don't have an account yet?
                            <a href="{{ route("signup") }}" class="font-semibold text-red-600 hover:text-red-700">
                                Sign Up
                            </a>
                        </p>

                    </div>
                </div>
            </div>

            <div class="w-1/2 hidden md:flex items-center justify-center">
                 <div class="w-full bg-cover bg-center h-full" style="background-image: url('{{ asset("img/telkom2.png") }}')">
                 </div>
            </div>

        </div>
    </div>
</body>

</html>
