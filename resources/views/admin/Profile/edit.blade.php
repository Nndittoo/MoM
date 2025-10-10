@extends('admin.layouts.app')

@section('title','Edit Profile (Admin)')

@section('content')
<div class="mt-4 max-w-4xl">
    @if (session('success'))
        <div class="mb-4 p-3 rounded bg-green-100 text-green-700">{{ session('success') }}</div>
    @endif
    @error('avatar')
        <div class="mb-4 p-3 rounded bg-red-100 text-red-700">{{ $message }}</div>
    @enderror

    <div class="bg-component-bg dark:bg-dark-component-bg rounded-lg shadow p-6">
        <h1 class="text-2xl font-semibold text-text-primary dark:text-white mb-6">Edit Profile (Admin)</h1>

        <div class="flex items-center gap-6">
            <img src="{{ $user->avatar_url }}" class="w-24 h-24 rounded-full object-cover border" alt="avatar">
            <div>
                <p class="text-text-primary dark:text-white font-medium">{{ $user->name }}</p>
                <p class="text-text-secondary dark:text-dark-text-secondary text-sm">{{ $user->email }}</p>
            </div>
        </div>

        <form action="{{ route('profile.photo.update') }}" method="POST" enctype="multipart/form-data" class="mt-6 space-y-4">
            @csrf
            <input type="file" name="avatar" accept="image/*"
                   class="block w-full text-sm file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-primary file:text-white hover:file:bg-primary/90">
            <button class="px-4 py-2 rounded bg-primary text-white hover:bg-primary/90" type="submit">Upload Foto</button>
        </form>

        @if($user->avatar)
        <form action="{{ route('profile.photo.delete') }}" method="POST" class="mt-4">
            @csrf @method('DELETE')
            <button class="px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700" type="submit">Hapus Foto</button>
        </form>
        @endif
    </div>
</div>
@endsection
