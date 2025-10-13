<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Reset Password - Telkom Indonesia</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-900">

@php
  // Ambil token & email dari berbagai kemungkinan sumber
  $resolvedToken = $token ?? request()->route('token') ?? request('token');
  $resolvedEmail = old('email', $email ?? request('email'));
@endphp

<div class="min-h-screen flex justify-center items-center p-4">
  <div class="max-w-4xl w-full bg-white shadow-xl rounded-2xl flex flex-col md:flex-row h-auto md:h-[560px] overflow-hidden border border-gray-200">

    {{-- Left: Form --}}
    <div class="w-full md:w-1/2 p-6 sm:p-10 flex flex-col justify-center">
      <div class="flex justify-center md:justify-start">
        <img src="{{ asset('img/logo.png') }}" class="w-24" alt="Telkom Logo"/>
      </div>

      <h1 class="text-2xl xl:text-3xl font-extrabold text-gray-800 mt-6">Buat Password Baru</h1>
      <p class="text-sm text-gray-600 mt-2">Masukkan password baru untuk akun Anda.</p>

      {{-- Alerts --}}
      @if (session('status'))
        <div class="mt-4 p-3 rounded bg-green-100 text-green-700">{{ session('status') }}</div>
      @endif

      {{-- Token hilang? Tampilkan peringatan supaya user buka dari link email --}}
      @if (blank($resolvedToken))
        <div class="mt-4 p-3 rounded bg-yellow-100 text-yellow-800 text-sm">
          Link reset tidak valid atau sudah kadaluarsa. Silakan
          <a href="{{ route('password.request') }}" class="underline font-semibold">minta link reset kembali</a>
          dan buka halaman ini dari email yang kami kirim.
        </div>
      @endif

      {{-- Error validasi --}}
      @if ($errors->any())
        <div class="mt-4 p-3 rounded bg-red-100 text-red-700 text-sm">
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

        {{-- Hidden token & email yang akan diproses server --}}
        <input type="hidden" name="token" value="{{ $resolvedToken }}">
        <input type="hidden" name="email" value="{{ $resolvedEmail }}">

        {{-- Email tampilan (readonly) agar user tahu akun yang di-reset --}}
        <input
          type="email"
          value="{{ $resolvedEmail }}"
          readonly
          class="w-full px-6 py-3 rounded-lg font-medium bg-gray-100 text-gray-700 text-sm border border-gray-200"
          placeholder="Email">

        {{-- Password --}}
        <div class="relative">
          <input id="password" type="password" name="password" required
                 class="w-full px-6 py-3 rounded-lg font-medium bg-gray-100 placeholder-gray-500 text-sm focus:outline-none focus:bg-white border border-gray-200"
                 placeholder="Password Baru" autocomplete="new-password">
          <button type="button" id="togglePassword"
                  class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-500">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
          </button>
        </div>

        {{-- Konfirmasi Password --}}
        <div class="relative">
          <input id="password_confirmation" type="password" name="password_confirmation" required
                 class="w-full px-6 py-3 rounded-lg font-medium bg-gray-100 placeholder-gray-500 text-sm focus:outline-none focus:bg-white border border-gray-200"
                 placeholder="Konfirmasi Password Baru" autocomplete="new-password">
          <button type="button" id="togglePasswordConfirm"
                  class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-500">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
          </button>
        </div>

        <button type="submit"
                class="mt-2 tracking-wide font-semibold bg-red-600 text-white w-full py-3 rounded-lg hover:bg-red-700 transition-all duration-300 flex items-center justify-center">
          <svg class="w-5 h-5 -ml-1 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
          </svg>
          Reset Password
        </button>
      </form>
    </div>

    {{-- Right: Image --}}
    <div class="w-full md:w-1/2 hidden md:flex items-center justify-center bg-red-100">
      <div class="w-full bg-cover bg-center h-full"
           style="background-image:url('https://images.unsplash.com/photo-1516321497487-e288fb19713f?q=80&w=2070&auto=format&fit=crop');">
      </div>
    </div>
  </div>
</div>

<script>
  function toggle(id, btnId) {
    const input = document.getElementById(id);
    const btn = document.getElementById(btnId);
    btn.addEventListener('click', () => {
      input.type = input.type === 'password' ? 'text' : 'password';
    });
  }
  toggle('password', 'togglePassword');
  toggle('password_confirmation', 'togglePasswordConfirm');
</script>
</body>
</html>
