@extends('layouts.app')

@section('title','Edit Profile | TR1 MoMatic')

@section('content')
<div class="pt-2">
    {{-- Header Halaman --}}
    <div class="p-6 md:p-8 rounded-xl shadow-lg bg-gray-800 border-l-4 border-red-500 mb-6">
        <h1 class="text-3xl font-bold font-orbitron text-neon-red">Edit Profile</h1>
        <p class="mt-1 text-gray-400">Kelola informasi akun dan foto profil Anda.</p>
    </div>

    @if (session('success'))
        <div class="mb-4 p-3 rounded-lg bg-green-900/50 text-green-300 text-sm border border-green-700">{{ session('success') }}</div>
    @endif
    @error('avatar')
        <div class="mb-4 p-3 rounded-lg bg-red-900/50 text-red-300 text-sm border border-red-700">{{ $message }}</div>
    @enderror

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Kolom Kiri: Foto Profil --}}
        <div class="lg:col-span-1">
            <div class="bg-gray-800 rounded-2xl shadow-lg border border-gray-700 p-6">
                <h2 class="text-xl font-semibold text-white font-orbitron border-b border-gray-700 pb-4 mb-6">Foto Profil</h2>

                <div class="flex flex-col items-center gap-4">
                    <img src="{{ $user->avatar_url }}" class="w-32 h-32 rounded-full object-cover ring-2 ring-gray-600" alt="avatar">
                    <div class="text-center">
                        <p class="text-white font-medium text-lg">{{ $user->name }}</p>
                        <p class="text-gray-400 text-sm">{{ $user->email }}</p>
                    </div>
                </div>

                <form action="{{ route('profile.photo.update') }}" method="POST" enctype="multipart/form-data" class="mt-6 space-y-4">
                    @csrf
                    <label for="avatar-input" class="sr-only">Pilih foto</label>
                    <input type="file" name="avatar" id="avatar-input" accept="image/*"
                           class="block w-full text-sm text-gray-400 border border-gray-600 rounded-lg cursor-pointer bg-gray-700 focus:outline-none
                                  file:mr-4 file:py-2 file:px-4 file:rounded-l-lg file:border-0
                                  file:bg-red-600 file:text-white hover:file:bg-red-700 file:font-semibold">
                    <button class="w-full px-4 py-2 rounded-lg btn-neon-red text-white" type="submit">Upload Foto</button>
                </form>

                @if($user->avatar)
                <form id="delete-photo-form" action="{{ route('profile.photo.delete') }}" method="POST" class="mt-4">
                    @csrf
                    @method('DELETE')
                    <button id="delete-photo-button" type="button" class="w-full px-4 py-2 rounded-lg bg-transparent border border-red-500 text-red-400 hover:bg-red-500/10">Hapus Foto</button>
                </form>
                @endif
            </div>
        </div>

        {{-- Kolom Kanan: Informasi Akun & Ubah Password --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Kartu Informasi Akun --}}
            <div class="bg-gray-800 rounded-2xl shadow-lg border border-gray-700 p-6">
                <h2 class="text-xl font-semibold text-white font-orbitron border-b border-gray-700 pb-4 mb-6">Informasi Akun</h2>
                <form action="#" method="POST" class="space-y-4"> {{-- Ganti '#' dengan route update info Anda --}}
                    @csrf
                    {{-- @method('PATCH') --}}
                    <div>
                        <label for="name" class="block mb-2 text-sm font-medium text-gray-300">Nama Lengkap</label>
                        <input type="text" id="name" name="name" disabled value="{{ old('name', $user->name) }}" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5" required>
                    </div>
                    <div>
                        <label for="email" class="block mb-2 text-sm font-medium text-gray-300">Alamat Email</label>
                        <input type="email" id="email" name="email" disabled value="{{ old('email', $user->email) }}" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5" required>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const deleteButton = document.getElementById('delete-photo-button');
        const deleteForm = document.getElementById('delete-photo-form');

        if (deleteButton) {
            deleteButton.addEventListener('click', function (event) {
                event.preventDefault();

                Swal.fire({
                    title: 'Anda yakin?',
                    text: "Foto profil Anda akan dihapus secara permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    customClass: {
                        popup: 'bg-gray-800 rounded-2xl border border-gray-700',
                        title: 'text-white font-orbitron',
                        htmlContainer: 'text-gray-400',
                        confirmButton: 'btn-neon-red text-white font-semibold px-6 py-2 mr-4 rounded-lg',
                        cancelButton: 'bg-gray-700 text-gray-300 font-semibold px-6 py-2 rounded-lg hover:bg-gray-600 border border-gray-600'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        deleteForm.submit();
                    }
                });
            });
        }
    });
</script>
@endpush
