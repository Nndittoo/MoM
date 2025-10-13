<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Lupa Password</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex items-center justify-center bg-gray-50">
  <form method="POST" action="{{ route('password.email') }}" class="bg-white p-6 rounded shadow w-full max-w-md">
    @csrf
    <h1 class="text-xl font-semibold mb-4">Lupa Password</h1>

    @if (session('status'))
      <div class="mb-3 p-3 rounded bg-green-100 text-green-700">{{ session('status') }}</div>
    @endif

    @error('email')
      <div class="mb-3 p-3 rounded bg-red-100 text-red-700">{{ $message }}</div>
    @enderror

    <input class="w-full border rounded px-3 py-2 mb-3"
           type="email"
           name="email"
           placeholder="Masukkan email Anda"
           required>

    <button class="w-full bg-red-600 text-white py-2 rounded hover:bg-red-700">
      Kirim Link Reset
    </button>
  </form>
</body>
</html>
