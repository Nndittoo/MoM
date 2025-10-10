@extends('admin.layouts.app')

@section('title', 'Manajemen Pengguna')

@section('content')
@php
    use Carbon\Carbon;

    $totalUsers   = $users->count();
    $totalAdmins  = $users->where('role', 'admin')->count();
    $newThisMonth = $users->filter(function($u){
        return $u->created_at && Carbon::parse($u->created_at)->isCurrentMonth();
    })->count();
@endphp

<div class="mt-4 space-y-6">

    {{-- Flash message --}}
    @if (session('success'))
        <div class="p-4 rounded-lg bg-green-50 text-green-700 border border-green-200">
            {{ session('success') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="p-4 rounded-lg bg-red-50 text-red-700 border border-red-200">
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Header --}}
    <div class="flex flex-col md:flex-row items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-text-primary dark:text-dark-text-primary">Manajemen Pengguna</h1>
            <p class="mt-1 text-text-secondary dark:text-dark-text-secondary">
                Tambah, lihat, dan kelola pengguna yang terdaftar di sistem.
            </p>
        </div>
    </div>

    {{-- Kartu Statistik Pengguna --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="flex items-center justify-between p-6 rounded-lg bg-component-bg dark:bg-dark-component-bg shadow-md">
            <div>
                <p class="text-3xl font-bold text-blue-500">{{ $totalUsers }}</p>
                <p class="text-md text-text-secondary mt-1">Total Pengguna</p>
            </div>
            <div class="flex items-center justify-center h-16 w-16 rounded-full bg-blue-100 dark:bg-blue-500/20">
                <i class="fa-solid fa-users fa-xl text-blue-500"></i>
            </div>
        </div>
        <div class="flex items-center justify-between p-6 rounded-lg bg-component-bg dark:bg-dark-component-bg shadow-md">
            <div>
                <p class="text-3xl font-bold text-green-500">{{ $totalAdmins }}</p>
                <p class="text-md text-text-secondary mt-1">Jumlah Admin</p>
            </div>
            <div class="flex items-center justify-center h-16 w-16 rounded-full bg-green-100 dark:bg-green-500/20">
                <i class="fa-solid fa-user-shield fa-xl text-green-500"></i>
            </div>
        </div>
        <div class="flex items-center justify-between p-6 rounded-lg bg-component-bg dark:bg-dark-component-bg shadow-md">
            <div>
                <p class="text-3xl font-bold text-text-primary dark:text-dark-text-primary">{{ $newThisMonth }}</p>
                <p class="text-md text-text-secondary mt-1">Pengguna Baru (Bulan Ini)</p>
            </div>
            <div class="flex items-center justify-center h-16 w-16 rounded-full bg-gray-100 dark:bg-gray-700">
                <i class="fa-solid fa-user-plus fa-xl text-gray-500"></i>
            </div>
        </div>
    </div>

    {{-- Tabel Manajemen Pengguna --}}
    <div class="bg-component-bg dark:bg-dark-component-bg shadow-md sm:rounded-lg overflow-hidden">
        <div class="p-4 border-b dark:border-border-dark flex flex-col sm:flex-row items-center justify-between gap-4">
            <h5 class="text-xl font-bold text-text-primary dark:text-white">Daftar Pengguna</h5>
            <div class="flex items-center gap-4 w-full sm:w-auto">
                {{-- Search Bar --}}
                <div class="relative w-full sm:w-64">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i class="fa-solid fa-search text-gray-400"></i>
                    </div>
                    <input
                        type="text"
                        id="user-search"
                        class="bg-body-bg border border-border-light text-text-primary text-sm rounded-lg focus:ring-primary focus:border-primary block w-full pl-10 p-2.5 dark:bg-dark-component-bg dark:border-border-dark"
                        placeholder="Cari nama atau email...">
                </div>
                {{-- Filter Dropdown --}}
                <div class="relative">
                    <button id="filter-dropdown-button"
                            type="button"
                            class="w-full sm:w-auto flex items-center justify-center py-2.5 px-4 text-sm font-medium text-text-primary focus:outline-none bg-component-bg rounded-lg border border-border-light hover:bg-body-bg dark:bg-dark-component-bg dark:text-dark-text-secondary dark:border-border-dark">
                        <i class="fa-solid fa-filter w-4 h-4 me-2"></i>
                        <span id="filter-label">Filter Role</span>
                        <i class="fa-solid fa-chevron-down w-2.5 h-2.5 ms-2.5"></i>
                    </button>
                    <div id="filter-dropdown"
                         class="z-10 hidden absolute right-0 mt-2 bg-component-bg divide-y divide-border-light rounded-lg shadow-md w-44 dark:bg-dark-component-bg">
                        <ul class="py-1 text-sm text-gray-700 dark:text-gray-200">
                            <li><a href="#" data-role="all" class="block px-4 py-2 hover:bg-body-bg dark:hover:bg-dark-body-bg">Semua</a></li>
                            <li><a href="#" data-role="admin" class="block px-4 py-2 hover:bg-body-bg dark:hover:bg-dark-body-bg">Admin</a></li>
                            <li><a href="#" data-role="user"  class="block px-4 py-2 hover:bg-body-bg dark:hover:bg-dark-body-bg">User</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-text-secondary dark:text-dark-text-secondary" id="users-table">
                <thead class="text-xs uppercase bg-body-bg dark:bg-dark-component-bg/50">
                    <tr>
                        <th scope="col" class="px-6 py-3">Nama Pengguna</th>
                        <th scope="col" class="px-6 py-3">Email</th>
                        <th scope="col" class="px-6 py-3">Role</th>
                        <th scope="col" class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($users as $user)
                    <tr class="border-b dark:border-border-dark" data-role="{{ $user->role }}">
                        <td class="px-6 py-4 font-medium text-text-primary dark:text-white">
                            {{ $user->name }}
                        </td>
                        <td class="px-6 py-4">{{ $user->email }}</td>

                        <td class="px-6 py-4">
                            <form class="flex items-center gap-2"
                                  method="POST"
                                  action="{{ route('admin.users.updateRole', $user->id) }}">
                                @csrf
                                <select name="role"
                                        class="role-dropdown bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-36 p-2.5">
                                    <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="user"  {{ $user->role === 'user'  ? 'selected' : '' }}>User</option>
                                </select>

                                <button type="submit"
                                        class="save-button hidden px-3 py-2 text-xs font-medium text-center text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                                    Simpan
                                </button>
                            </form>
                        </td>

                        <td class="px-6 py-4 text-center">
                            @if(auth()->id() !== $user->id)
                                <form method="POST"
                                      action="{{ route('admin.users.destroy', $user->id) }}"
                                      onsubmit="return confirm('Hapus pengguna {{ $user->name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="font-medium text-red-500 hover:underline">
                                        Hapus
                                    </button>
                                </form>
                            @else
                                <span class="text-xs text-gray-400">Akun sendiri</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-text-secondary">
                            Belum ada pengguna.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Tampilkan tombol "Simpan" saat role berubah
    document.querySelectorAll('.role-dropdown').forEach(function (dropdown) {
        const saveBtn = dropdown.closest('form').querySelector('.save-button');
        const initial = dropdown.value;

        dropdown.addEventListener('change', function () {
            if (this.value !== initial) {
                saveBtn.classList.remove('hidden');
            } else {
                saveBtn.classList.add('hidden');
            }
        });
    });

    // Toggle dropdown Filter
    const filterBtn = document.getElementById('filter-dropdown-button');
    const filterMenu = document.getElementById('filter-dropdown');
    const filterLabel = document.getElementById('filter-label');
    filterBtn.addEventListener('click', () => {
        filterMenu.classList.toggle('hidden');
    });
    // Tutup dropdown jika klik di luar
    document.addEventListener('click', (e) => {
        if (!filterBtn.contains(e.target) && !filterMenu.contains(e.target)) {
            filterMenu.classList.add('hidden');
        }
    });

    // Filter by role (client-side)
    filterMenu.querySelectorAll('a[data-role]').forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const role = link.getAttribute('data-role'); // all|admin|user
            filterLabel.textContent = role === 'all' ? 'Filter Role' : `Role: ${role}`;
            filterMenu.classList.add('hidden');

            document.querySelectorAll('#users-table tbody tr').forEach(tr => {
                const r = tr.getAttribute('data-role');
                tr.style.display = (role === 'all' || r === role) ? '' : 'none';
            });
        });
    });

    // Search (client-side)
    const searchInput = document.getElementById('user-search');
    searchInput.addEventListener('keyup', function() {
        const term = this.value.toLowerCase();
        document.querySelectorAll('#users-table tbody tr').forEach(tr => {
            const name  = tr.children[0]?.innerText?.toLowerCase() ?? '';
            const email = tr.children[1]?.innerText?.toLowerCase() ?? '';
            tr.style.display = (name.includes(term) || email.includes(term)) ? '' : 'none';
        });
    });
});
</script>
@endpush
