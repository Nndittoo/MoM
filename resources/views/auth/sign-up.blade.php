<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Sign Up - TR1 MoMatic</title>
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

        <div class="max-w-4xl w-full bg-gray-800 shadow-2xl rounded-2xl flex flex-col md:flex-row h-auto overflow-hidden border border-gray-700">

            {{-- Left: Form --}}
            <div class="w-full md:w-1/2 p-6 sm:p-12 flex flex-col justify-center">
                <div class="flex justify-center"> {{-- Logo centered for all screen sizes --}}
                    <img src="{{ asset("img/LOGO.png") }}" class="w-[200px] md:w-[250px]" alt="TR1 MoMatic Logo" />
                </div>

                <h1 class="text-3xl xl:text-4xl font-extrabold text-center md:text-left text-neon-red font-orbitron mt-6">
                    Create an Account
                </h1>

                {{-- Flash error (umum) --}}
                @if ($errors->any())
                    <div class="mt-4 rounded-lg border border-red-500 bg-red-900 p-3 text-sm text-red-300">
                        Periksa kembali isian Anda.
                    </div>
                @endif

                <form action="{{ route('register.post') }}" method="POST" class="w-full mt-6">
                    @csrf
                    <div class="mx-auto max-w-xs">
                        {{-- Name --}}
                        <input
                            name="name"
                            value="{{ old('name') }}"
                            @class([
                                'w-full px-6 py-3 rounded-lg font-medium bg-gray-700 placeholder-gray-400 text-gray-100 text-sm focus:outline-none input-glow border',
                                'border-red-500' => $errors->has('name'),
                                'border-gray-600' => !$errors->has('name'),
                            ])
                            type="text" placeholder="Full Name" required />
                        @error('name')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror

                        {{-- Email --}}
                        <input
                            name="email"
                            value="{{ old('email') }}"
                            @class([
                                'w-full px-6 py-3 mt-4 rounded-lg font-medium bg-gray-700 placeholder-gray-400 text-gray-100 text-sm focus:outline-none input-glow border',
                                'border-red-500' => $errors->has('email'),
                                'border-gray-600' => !$errors->has('email'),
                            ])
                            type="email" placeholder="Email" required />
                        @error('email')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror

                        {{-- Password --}}
                        <input
                            name="password"
                            @class([
                                'w-full px-6 py-3 mt-4 rounded-lg font-medium bg-gray-700 placeholder-gray-400 text-gray-100 text-sm focus:outline-none input-glow border',
                                'border-red-500' => $errors->has('password'),
                                'border-gray-600' => !$errors->has('password'),
                            ])
                            type="password" placeholder="Password" required />
                        @error('password')
                            <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                        @enderror

                        {{-- Confirm Password --}}
                        <input
                            name="password_confirmation"
                            class="w-full px-6 py-3 mt-4 rounded-lg font-medium bg-gray-700 placeholder-gray-400 text-gray-100 text-sm focus:outline-none input-glow border border-gray-600"
                            type="password" placeholder="Confirm Password" required />

                        {{-- Link Forgot Password dihapus karena ini halaman register --}}
                        {{-- <div class="text-right mt-2">
                            <a href="{{ route('reset') }}" class="text-sm font-semibold text-gray-400 hover:text-neon-red focus:text-neon-red">
                                Forgot Password?
                            </a>
                        </div> --}}

                        <button type="submit"
                            class="mt-5 tracking-wide font-semibold btn-neon-red text-white w-full py-3 rounded-lg flex items-center justify-center focus:shadow-outline focus:outline-none">
                            <svg class="w-5 h-5 -ml-2" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                                <circle cx="8.5" cy="7" r="4"/>
                                <path d="M20 8v6M23 11h-6"/>
                            </svg>
                            <span class="ml-2">Create Account</span>
                        </button>

                        <p class="mt-6 text-sm text-gray-400 text-center">
                            Already have an account?
                            <a href="{{ route('login') }}" class="font-semibold text-neon-red hover:text-red-500">
                                Sign In
                            </a>
                        </p>
                    </div>
                </form>
            </div>

            {{-- Right: Image/Slogan --}}
            <div class="w-full md:w-1/2 hidden md:flex items-center justify-center bg-gray-900 relative">
                <div class="absolute inset-0 bg-cover bg-center opacity-20" style="background-image: url('{{ asset("img/LOGO.png") }}'); background-size: contain; background-repeat: no-repeat; background-position: center;">
                </div>
                <div class="relative z-10 p-8 text-center">
                    <h2 class="text-4xl font-extrabold text-white font-orbitron mb-4 text-shadow-lg">
                        Start Your Productivity Journey.
                    </h2>
                    <p class="text-lg text-gray-300">
                        Join TR1 MoMatic to streamline your meetings and boost team efficiency.
                    </p>
                </div>
            </div>

        </div>
    </div>
</body>
</html>
