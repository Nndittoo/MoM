<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Sign Up - Telkom Indonesia</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50 text-gray-900">
    <div class="min-h-screen flex justify-center items-center p-4">

        <div class="max-w-4xl w-full bg-white shadow-xl rounded-2xl flex flex-col md:flex-row h-auto md:h-[600px] overflow-hidden border border-gray-200">

            {{-- Left: Form --}}
            <div class="w-full md:w-1/2 p-6 sm:p-12 flex flex-col justify-center">
                <div class="flex justify-center md:justify-start">
                    <img src="{{ asset('img/logo.png') }}" class="w-24" alt="Telkom Logo" />
                </div>

                <h1 class="text-2xl xl:text-3xl font-extrabold text-gray-800 mt-6">
                    Create an Account
                </h1>

                {{-- Flash error (umum) --}}
                @if ($errors->any())
                    <div class="mt-4 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700">
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
                                'w-full px-6 py-3 rounded-lg font-medium bg-gray-100 placeholder-gray-500 text-sm focus:outline-none focus:bg-white border',
                                'border-red-400' => $errors->has('name'),
                                'border-gray-200' => !$errors->has('name'),
                            ])
                            type="text" placeholder="Full Name" />
                        @error('name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror

                        {{-- Email --}}
                        <input
                            name="email"
                            value="{{ old('email') }}"
                            @class([
                                'w-full px-6 py-3 mt-4 rounded-lg font-medium bg-gray-100 placeholder-gray-500 text-sm focus:outline-none focus:bg-white border',
                                'border-red-400' => $errors->has('email'),
                                'border-gray-200' => !$errors->has('email'),
                            ])
                            type="email" placeholder="Email" />
                        @error('email')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror

                        {{-- Password --}}
                        <input
                            name="password"
                            @class([
                                'w-full px-6 py-3 mt-4 rounded-lg font-medium bg-gray-100 placeholder-gray-500 text-sm focus:outline-none focus:bg-white border',
                                'border-red-400' => $errors->has('password'),
                                'border-gray-200' => !$errors->has('password'),
                            ])
                            type="password" placeholder="Password" />
                        @error('password')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror

                        {{-- Confirm Password --}}
                        <input
                            name="password_confirmation"
                            class="w-full px-6 py-3 mt-4 rounded-lg font-medium bg-gray-100 placeholder-gray-500 text-sm focus:outline-none focus:bg-white border border-gray-200"
                            type="password" placeholder="Confirm Password" />
                            <div class="text-right mt-2">
                                <a href="{{ route('reset') }}" class="text-sm font-semibold text-gray-600 hover:text-red-600 focus:text-red-700">
                                    Forgot Password?
                                </a>
                            </div>

                        <button type="submit"
                            class="mt-5 tracking-wide font-semibold bg-red-600 text-white w-full py-3 rounded-lg hover:bg-red-700 transition-all duration-300 ease-in-out flex items-center justify-center focus:shadow-outline focus:outline-none">
                            <svg class="w-5 h-5 -ml-2" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                                <circle cx="8.5" cy="7" r="4"/>
                                <path d="M20 8v6M23 11h-6"/>
                            </svg>
                            <span class="ml-2">Create Account</span>
                        </button>

                        <p class="mt-6 text-sm text-gray-600 text-center">
                            Already have an account?
                            <a href="{{ route('login') }}" class="font-semibold text-red-600 hover:text-red-700">
                                Sign In
                            </a>
                        </p>
                    </div>
                </form>
            </div>

            {{-- Right: Image --}}
            <div class="w-full md:w-1/2 hidden md:flex items-center justify-center bg-red-100">
                <div class="w-full bg-cover bg-center h-full"
                     style="background-image: url('https://images.unsplash.com/photo-1516321497487-e288fb19713f?q=80&w=2070&auto=format&fit=crop');">
                </div>
            </div>

        </div>
    </div>
</body>
</html>
