@extends('admin.layouts.app')

@section('title', 'Manajemen Pengguna')

@php
    use Carbon\Carbon;

    $totalUsers   = $users->count();
    $totalAdmins  = $users->where('role', 'admin')->count();
    $newThisMonth = $users->filter(function($u){
        return $u->created_at && Carbon::parse($u->created_at)->isCurrentMonth();
    })->count();
@endphp

@section('content')
<div class="pt-2 space-y-6">

    {{-- Notifikasi Sukses/Error --}}
    @if (session('success'))
        <div class="p-4 rounded-lg bg-green-900/50 text-green-300 border border-green-700">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="p-4 rounded-lg bg-red-900/50 text-red-300 border border-red-700">
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Header Halaman --}}
    <div class="p-6 md:p-8 rounded-xl shadow-lg bg-gray-800 border-l-4 border-red-500">
        <h1 class="text-3xl font-bold font-orbitron text-neon-red">Manajemen Pengguna</h1>
        <p class="mt-1 text-gray-400">Tambah, lihat, dan kelola pengguna yang terdaftar di sistem.</p>
    </div>

    {{-- Kartu Statistik Pengguna --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="p-6 rounded-xl bg-gray-800 shadow-md border border-gray-700">
            <div class="flex items-center justify-between"><p class="text-md text-gray-400">Total Pengguna</p><i class="fa-solid fa-users fa-2x text-blue-500 opacity-20"></i></div>
            <p class="text-3xl font-bold text-blue-400 mt-1">{{ $totalUsers }}</p>
        </div>
        <div class="p-6 rounded-xl bg-gray-800 shadow-md border border-gray-700">
            <div class="flex items-center justify-between"><p class="text-md text-gray-400">Jumlah Admin</p><i class="fa-solid fa-user-shield fa-2x text-green-500 opacity-20"></i></div>
            <p class="text-3xl font-bold text-green-400 mt-1">{{ $totalAdmins }}</p>
        </div>
        <div class="p-6 rounded-xl bg-gray-800 shadow-md border border-gray-700">
            <div class="flex items-center justify-between"><p class="text-md text-gray-400">Pengguna Baru (Bulan Ini)</p><i class="fa-solid fa-user-plus fa-2x text-gray-500 opacity-20"></i></div>
            <p class="text-3xl font-bold text-white mt-1">{{ $newThisMonth }}</p>
        </div>
    </div>

    {{-- Tabel Manajemen Pengguna --}}
    <div class="bg-gray-800 shadow-md sm:rounded-lg overflow-hidden border border-gray-700">
        <div class="p-4 border-b border-gray-700 flex flex-col sm:flex-row items-center justify-between gap-4">
            <h5 class="text-xl font-bold text-white font-orbitron">Daftar Pengguna</h5>
            <div class="flex items-center gap-4 w-full sm:w-auto">
                <div class="relative w-full sm:w-64">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none"><i class="fa-solid fa-search text-gray-400"></i></div>
                    <input type="text" id="user-search" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full pl-10 p-2.5" placeholder="Cari nama atau email...">
                </div>
                <div class="relative">
                    <button id="filter-dropdown-button" type="button" class="w-full sm:w-auto flex items-center justify-center py-2.5 px-4 text-sm font-medium text-gray-300 bg-gray-800 rounded-lg border border-gray-700 hover:bg-gray-700 focus:z-10 focus:ring-2 focus:ring-red-500">
                        <i class="fa-solid fa-filter w-4 h-4 me-2"></i>
                        <span id="filter-label">Filter Role</span>
                        <i class="fa-solid fa-chevron-down w-2.5 h-2.5 ms-2.5"></i>
                    </button>
                    <div id="filter-dropdown" class="z-10 hidden absolute right-0 mt-2 bg-gray-800 divide-y divide-gray-700 rounded-lg shadow-lg w-44 border border-gray-700">
                        <ul class="py-1 text-sm text-gray-200">
                            <li><a href="#" data-role="all" class="block px-4 py-2 hover:bg-gray-700">Semua</a></li>
                            <li><a href="#" data-role="admin" class="block px-4 py-2 hover:bg-gray-700">Admin</a></li>
                            <li><a href="#" data-role="user" class="block px-4 py-2 hover:bg-gray-700">User</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-400" id="users-table">
                <thead class="text-xs uppercase bg-gray-700/50">
                    <tr>
                        <th scope="col" class="px-6 py-3">Nama Pengguna</th>
                        <th scope="col" class="px-6 py-3">Email</th>
                        <th scope="col" class="px-6 py-3">Role</th>
                        <th scope="col" class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($users as $user)
                    <tr class="border-b border-gray-700" data-role="{{ $user->role }}">
                        <td class="px-6 py-4 font-medium text-white whitespace-nowrap">{{ $user->name }}</td>
                        <td class="px-6 py-4">{{ $user->email }}</td>
                        <td class="px-6 py-4">
                            <form class="flex items-center gap-2" method="POST" action="{{ route('admin.users.updateRole', $user->id) }}">
                                @csrf
                                <select name="role" class="role-dropdown bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-36 p-2.5">
                                    <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="user"  {{ $user->role === 'user'  ? 'selected' : '' }}>User</option>
                                </select>
                                <button type="submit" class="save-button hidden px-4 py-2 text-xs font-medium text-center text-white bg-blue-600 rounded-lg hover:bg-blue-700">Simpan</button>
                            </form>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if(auth()->id() !== $user->id)
                                <form class="delete-user-form" method="POST" action="{{ route('admin.users.destroy', $user->id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="font-medium text-red-500 hover:underline">Hapus</button>
                                </form>
                            @else
                                <span class="text-xs text-gray-500 italic">Akun Anda</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-6 py-8 text-center text-gray-500">Belum ada pengguna.</td></tr>
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
    document.querySelectorAll('.delete-user-form').forEach(form => {
        form.addEventListener('submit', function(event) {
            event.preventDefault(); // Mencegah form submit langsung
            const userName = this.closest('tr').querySelector('td:first-child').textContent.trim();

            Swal.fire({
                title: 'Anda yakin?',
                html: `Pengguna "<strong>${userName}</strong>" akan dihapus secara permanen!`,
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
                    form.submit(); // Lanjutkan submit form jika dikonfirmasi
                }
            });
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
