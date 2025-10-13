<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - TR1 MoMatic</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Inter:wght@400;600;700;800;900&display=swap');

        body {
            font-family: 'Inter', sans-serif; /* Fallback to Inter */
        }
        .font-orbitron {
            font-family: 'Orbitron', sans-serif;
        }
        /* Custom styles for glow/shadow effects similar to the logo */
        .text-neon-red {
            color: #EF4444; /* Tailwind red-500 */
            text-shadow: 0 0 5px rgba(239, 68, 68, 0.7), 0 0 10px rgba(239, 68, 68, 0.5);
        }
        .btn-neon-red {
            background-color: #EF4444; /* Tailwind red-500 */
            box-shadow: 0 0 8px rgba(239, 68, 68, 0.6);
            transition: all 0.3s ease-in-out;
        }
        .btn-neon-red:hover {
            background-color: #DC2626; /* Tailwind red-600 */
            box-shadow: 0 0 12px rgba(239, 68, 68, 0.8);
        }
        .input-glow:focus {
            border-color: #DC2626; /* red-600 */
            box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.3); /* subtle red glow */
            background-color: #374151; /* gray-700 */
        }
    </style>
</head>

<body class="bg-gray-900 text-gray-100 font-inter">
    <div class="min-h-screen flex justify-center items-center p-4">

        <div class="max-w-4xl w-full bg-gray-800 shadow-2xl rounded-2xl flex flex-row h-full overflow-hidden border border-gray-700">



            <div class="w-1/2 hidden md:flex items-center justify-center bg-gray-900 relative">
                <div class="absolute inset-0 bg-cover bg-center opacity-20" style="background-image: url('{{ asset("img/LOGO.png") }}'); background-size: contain; background-repeat: no-repeat; background-position: center;">
                </div>

                <div class="relative z-10 p-8 text-center">
                    <h2 class="text-4xl font-extrabold text-white font-orbitron mb-4 text-shadow-lg">
                        Turn Talk into Action.
                    </h2>
                    <p class="text-lg text-gray-300">
                        Capture every key point and drive your projects forward with precision.
                    </p>
                </div>
            </div>
<div class="w-full md:w-1/2 p-6 sm:p-12 flex flex-col justify-center">

                <div class="flex justify-center">
                    <img src="{{ asset("img/LOGO.png") }}" class="w-[200px] md:w-[250px]" alt="TR1 MoMatic Logo" />
                </div>

                <div class="mt-8 flex flex-col items-center">
                    <div class="w-full max-w-xs md:max-w-none flex-1">
                        <h1 class="text-3xl xl:text-4xl font-extrabold text-center md:text-left text-neon-red font-orbitron">
                            Sign In
                        </h1>
                        @if (session('success'))
                        <div class="mt-4 rounded-lg border border-green-500 bg-green-900 p-3 text-sm text-green-300">
                            {{ session('success') }}
                        </div>
                        @endif
                        <form action="{{ route('login.post') }}" method="POST" class="mx-auto max-w-xs mt-8">
                            @csrf

                            <input
                                name="email"
                                value="{{ old('email') }}"
                                class="w-full px-6 py-3 rounded-lg font-medium bg-gray-700 border border-gray-600 placeholder-gray-400 text-gray-100 text-sm focus:outline-none input-glow"
                                type="email" placeholder="Email" required />

                            <input
                                name="password"
                                class="w-full px-6 py-3 rounded-lg font-medium bg-gray-700 border border-gray-600 placeholder-gray-400 text-gray-100 text-sm focus:outline-none input-glow mt-5"
                                type="password" placeholder="Password" required />

                                <div class="text-right mt-2">
                                <a href="{{ route('password.request') }}" class="text-sm font-semibold text-gray-600 hover:text-red-600 focus:text-red-700">
                                    Forgot Password?
                                </a>
                            </div>

                            @if ($errors->any())
                                <div class="mt-3 text-red-400 text-sm text-center">
                                    {{ $errors->first() }}
                                </div>
                            @endif

                            <button type="submit"
                                class="mt-5 tracking-wide font-semibold btn-neon-red text-white w-full py-3 rounded-lg hover:bg-red-700 transition-all duration-300 ease-in-out flex items-center justify-center focus:shadow-outline focus:outline-none">
                                <svg class="w-6 h-6 -ml-2" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" />
                                    <circle cx="8.5" cy="7" r="4" />
                                    <path d="M20 8v6M23 11h-6" />
                                </svg>
                                <span class="ml-3">Sign In</span>
                            </button>
                        </form>

                        <p class="mt-6 text-sm text-gray-400 text-center">
                            Don't have an account yet?
                            <a href="{{ route("register") }}" class="font-semibold text-neon-red hover:text-red-500">
                                Sign Up
                            </a>
                        </p>

                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
