<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Reset Password - Telkom Indonesia</title>
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

                {{-- Form di sini akan berubah tergantung langkahnya --}}
                <form action="" method="POST" class="w-full mt-6">
                    @csrf

                    {{-- STEP 1: Input Email & Username --}}
                    <div id="step-1">
                        <h1 class="text-2xl xl:text-3xl font-extrabold text-gray-800">
                            Reset Password
                        </h1>
                        <p class="text-sm text-gray-600 mt-2">
                            Masukkan email dan username akun Anda untuk melanjutkan.
                        </p>
                        <div class="mx-auto max-w-xs mt-6">
                            {{-- Email --}}
                            <input
                                name="email"
                                value="{{ old('email') }}"
                                class="w-full px-6 py-3 rounded-lg font-medium bg-gray-100 placeholder-gray-500 text-sm focus:outline-none focus:bg-white border border-gray-200"
                                type="email" placeholder="Email" required/>

                            {{-- Username --}}
                            <input
                                name="uname"
                                value="{{ old('uname') }}"
                                class="w-full px-6 py-3 mt-4 rounded-lg font-medium bg-gray-100 placeholder-gray-500 text-sm focus:outline-none focus:bg-white border border-gray-200"
                                type="text" placeholder="Username" required/>

                            {{-- Tombol Next --}}
                            <button type="button" id="next-btn"
                                class="mt-5 tracking-wide font-semibold bg-red-600 text-white w-full py-3 rounded-lg hover:bg-red-700 transition-all duration-300 ease-in-out flex items-center justify-center focus:shadow-outline focus:outline-none">
                                <span class="ml-2">Selanjutnya</span>
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                            </button>
                        </div>
                    </div>

                    {{-- STEP 2: Input Password Baru (Default-nya tersembunyi) --}}
                    <div id="step-2" class="hidden">
                         <h1 class="text-2xl xl:text-3xl font-extrabold text-gray-800">
                            Buat Password Baru
                        </h1>
                        <p class="text-sm text-gray-600 mt-2">
                            Password baru Anda harus berbeda dari password sebelumnya.
                        </p>
                        <div class="mx-auto max-w-xs mt-6">
                             {{-- New Password --}}
                             <div class="relative">
                                <input id="password" name="password"
                                    class="w-full px-6 py-3 rounded-lg font-medium bg-gray-100 placeholder-gray-500 text-sm focus:outline-none focus:bg-white border border-gray-200"
                                    type="password" placeholder="Password Baru" />
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5">
                                    <svg class="h-6 w-6 text-gray-500 cursor-pointer" id="togglePassword" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </div>
                            </div>

                            {{-- Confirm New Password --}}
                             <div class="relative mt-4">
                                <input id="password_confirmation" name="password_confirmation"
                                    class="w-full px-6 py-3 rounded-lg font-medium bg-gray-100 placeholder-gray-500 text-sm focus:outline-none focus:bg-white border border-gray-200"
                                    type="password" placeholder="Konfirmasi Password Baru" />
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5">
                                     <svg class="h-6 w-6 text-gray-500 cursor-pointer" id="togglePasswordConfirm" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </div>
                            </div>

                            <button type="submit"
                                class="mt-5 tracking-wide font-semibold bg-red-600 text-white w-full py-3 rounded-lg hover:bg-red-700 transition-all duration-300 ease-in-out flex items-center justify-center focus:shadow-outline focus:outline-none">
                                <svg class="w-5 h-5 -ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                <span class="ml-2">Reset Password</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Right: Image (Sama seperti halaman sign up) --}}
            <div class="w-full md:w-1/2 hidden md:flex items-center justify-center bg-red-100">
                <div class="w-full bg-cover bg-center h-full"
                    style="background-image: url('https://images.unsplash.com/photo-1516321497487-e288fb19713f?q=80&w=2070&auto=format&fit=crop');">
                </div>
            </div>

        </div>
    </div>

    <script>
        // Logika untuk beralih antar langkah
        const step1 = document.getElementById('step-1');
        const step2 = document.getElementById('step-2');
        const nextBtn = document.getElementById('next-btn');

        nextBtn.addEventListener('click', () => {
            // Di sini Anda bisa menambahkan validasi untuk email dan username sebelum lanjut
            // Untuk sekarang, kita langsung pindah
            step1.classList.add('hidden');
            step2.classList.remove('hidden');
        });

        // Logika untuk toggle password visibility
        function setupPasswordToggle(inputId, toggleId) {
            const passwordInput = document.getElementById(inputId);
            const toggleButton = document.getElementById(toggleId);

            toggleButton.addEventListener('click', function () {
                // ganti tipe input
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);

                // ganti ikon mata (opsional, tapi UX bagus)
                if (type === 'password') {
                    this.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />`;
                } else {
                    this.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7 1.274-4.057 5.064-7 9.542-7 1.41 0 2.75.364 3.995 1.038M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2 2l20 20" />`;
                }
            });
        }

        setupPasswordToggle('password', 'togglePassword');
        setupPasswordToggle('password_confirmation', 'togglePasswordConfirm');
    </script>

</body>
</html>
