<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lupa Password | TR1 MoMatic</title>
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
<body class="min-h-screen flex items-center justify-center bg-gray-900 p-4">

    <div class="w-full max-w-md">
        {{-- Logo --}}
        <div class="flex justify-center mb-8">
            <a href="{{ route('login') }}">
                <img src="{{ asset('img/LOGO.png') }}" class="h-48" alt="TR1 MoMatic Logo">
            </a>
        </div>

    {{-- Card Form --}}
        <div class="bg-gray-800 p-8 rounded-2xl shadow-lg border border-gray-700">
            <h1 class="text-2xl font-bold font-orbitron text-neon-red mb-1 text-center">Lupa Password</h1>
            <p class="text-center text-gray-400 text-sm mb-6">
                Masukkan email Anda dan kami akan mengirimkan link untuk mereset password.
            </p>

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                {{-- Notifikasi Status/Error --}}
                @if (session('status'))
                    <div class="mb-4 p-3 rounded-lg bg-green-900/50 text-green-300 text-sm border border-green-700">{{ session('status') }}</div>
                @endif
                @error('email')
                    <div class="mb-4 p-3 rounded-lg bg-red-900/50 text-red-300 text-sm border border-red-700">{{ $message }}</div>
                @enderror

                <label for="email" class="sr-only">Email</label>
                <input class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-4 py-3 mb-4 focus:ring-red-500 focus:border-red-500"
                       type="email"
                       name="email"
                       id="email"
                       placeholder="Masukkan email Anda"
                       required
                       autofocus>

                <button class="w-full btn-neon-red btn-pulse text-white font-semibold py-3 rounded-lg">
                    Kirim Link Reset
                </button>
            </form>

            {{-- Link Kembali ke Login --}}
            <div class="text-center mt-6">
                <a href="{{ route('login') }}" class="text-sm text-gray-400 hover:text-red-400 hover:underline">
                    <i class="fa-solid fa-arrow-left fa-xs mr-1"></i> Kembali ke Login
                </a>
            </div>
        </div>
    </div>
</body>
</html>
