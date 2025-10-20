<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Reset Password | TR1 MoMatic</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    {{-- Style untuk font dan animasi --}}
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@700&family=Inter:wght@400;500;600&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .font-orbitron { font-family: 'Orbitron', sans-serif; }
        .text-neon-red {
            color: #EF4444;
            text-shadow: 0 0 5px rgba(239, 68, 68, 0.7);
        }
        .btn-neon-red {
            background-color: #EF4444;
            box-shadow: 0 0 8px rgba(239, 68, 68, 0.6);
            transition: all 0.3s ease-in-out;
        }
        .btn-neon-red:hover {
            background-color: #DC2626;
            box-shadow: 0 0 12px rgba(239, 68, 68, 0.8), 0 0 20px rgba(239, 68, 68, 0.5);
        }
        .btn-pulse {
            animation: pulse-animation 2s infinite;
        }
        @keyframes pulse-animation {
            0% { box-shadow: 0 0 8px rgba(239, 68, 68, 0.6); }
            50% { box-shadow: 0 0 20px rgba(239, 68, 68, 0.9); }
            100% { box-shadow: 0 0 8px rgba(239, 68, 68, 0.6); }
        }
    </style>
</head>
<body class="bg-gray-900 text-gray-100">

@php
    // Ambil token & email dari berbagai kemungkinan sumber
    $resolvedToken = $token ?? request()->route('token') ?? request('token');
    $resolvedEmail = old('email', $email ?? request('email'));
@endphp

<div class="min-h-screen flex justify-center items-center p-4">
    <div class="max-w-4xl w-full bg-gray-800 shadow-2xl rounded-2xl flex flex-col md:flex-row h-auto overflow-hidden border border-gray-700">

        {{-- Left: Form --}}
        <div class="w-full md:w-1/2 p-6 sm:p-10 flex flex-col justify-center">
            <div class="flex justify-center">
                <img src="{{ asset('img/LOGO.png') }}" class="w-48" alt="TR1 MoMatic Logo"/>
            </div>

            <h1 class="text-2xl xl:text-3xl font-bold font-orbitron text-neon-red mt-6">Buat Password Baru</h1>
            <p class="text-sm text-gray-400 mt-2">Masukkan password baru untuk akun Anda.</p>

            {{-- Alerts --}}
            @if (session('status'))
                <div class="mt-4 p-3 rounded-lg bg-green-900/50 text-green-300 border border-green-700 text-sm">{{ session('status') }}</div>
            @endif
            @if (blank($resolvedToken))
                <div class="mt-4 p-3 rounded-lg bg-yellow-900/50 text-yellow-300 border border-yellow-700 text-sm">
                    Link reset tidak valid atau sudah kadaluarsa. Silakan
                    <a href="{{ route('password.request') }}" class="underline font-semibold hover:text-yellow-200">minta link reset kembali</a>.
                </div>
            @endif
            @if ($errors->any())
                <div class="mt-4 p-3 rounded-lg bg-red-900/50 text-red-300 border border-red-700 text-sm">
                    <ul class="list-disc ml-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Form --}}
            <form method="POST" action="{{ route('password.store') }}" class="mt-6 space-y-4">
                @csrf
                <input type="hidden" name="token" value="{{ $resolvedToken }}">
                <input type="hidden" name="email" value="{{ $resolvedEmail }}">

                <input type="email" value="{{ $resolvedEmail }}" readonly class="w-full px-6 py-3 rounded-lg font-medium bg-gray-700 text-gray-400 text-sm border border-gray-600 cursor-not-allowed">

                <div class="relative">
                    <input id="password" type="password" name="password" required class="w-full px-6 py-3 rounded-lg font-medium bg-gray-700 border border-gray-600 text-white placeholder-gray-400 text-sm focus:outline-none focus:ring-1 focus:ring-red-500 focus:border-red-500" placeholder="Password Baru" autocomplete="new-password">
                    <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </div>

                <div class="relative">
                    <input id="password_confirmation" type="password" name="password_confirmation" required class="w-full px-6 py-3 rounded-lg font-medium bg-gray-700 border border-gray-600 text-white placeholder-gray-400 text-sm focus:outline-none focus:ring-1 focus:ring-red-500 focus:border-red-500" placeholder="Konfirmasi Password Baru" autocomplete="new-password">
                    <button type="button" id="togglePasswordConfirm" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </div>

                <button type="submit" class="mt-2 w-full py-3 rounded-lg btn-neon-red btn-pulse text-white font-semibold flex items-center justify-center">
                    <i class="fa-solid fa-key mr-2"></i>
                    Reset Password
                </button>
            </form>
        </div>

        {{-- Right: Image --}}
        <div class="w-full md:w-1/2 hidden md:flex items-center justify-center bg-gray-900 relative">
            <div class="absolute inset-0 bg-cover bg-center opacity-10" style="background-image: url('{{ asset("img/LOGO.png") }}'); background-size: 80%; background-repeat: no-repeat;"></div>
            <div class="relative z-10 p-8 text-center">
                <h2 class="text-4xl font-extrabold text-white font-orbitron mb-4">Secure Your Access</h2>
                <p class="text-lg text-gray-300">Buat password yang kuat untuk melindungi akun Anda.</p>
            </div>
        </div>
    </div>
</div>

<script>
    function toggle(inputId, buttonId) {
        const input = document.getElementById(inputId);
        const button = document.getElementById(buttonId);
        const icon = button.querySelector('i');

        button.addEventListener('click', () => {
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    }
    toggle('password', 'togglePassword');
    toggle('password_confirmation', 'togglePasswordConfirm');
</script>

</body>
</html>
