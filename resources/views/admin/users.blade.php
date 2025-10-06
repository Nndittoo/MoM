@extends('admin.layouts.app')

@section('title', 'Manajemen Pengguna')

@section('content')
<div class="mt-4 space-y-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-text-primary dark:text-dark-text-primary">Manajemen Pengguna</h1>
            <p class="mt-1 text-text-secondary dark:text-dark-text-secondary">Tambah, lihat, dan kelola pengguna yang terdaftar di sistem.</p>
        </div>
    </div>

    {{-- Kartu Statistik Pengguna --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="flex items-center justify-between p-6 rounded-lg bg-component-bg dark:bg-dark-component-bg shadow-md">
            <div><p class="text-3xl font-bold text-blue-500">34</p><p class="text-md text-text-secondary mt-1">Total Pengguna</p></div>
            <div class="flex items-center justify-center h-16 w-16 rounded-full bg-blue-100 dark:bg-blue-500/20"><i class="fa-solid fa-users fa-xl text-blue-500"></i></div>
        </div>
        <div class="flex items-center justify-between p-6 rounded-lg bg-component-bg dark:bg-dark-component-bg shadow-md">
            <div><p class="text-3xl font-bold text-green-500">3</p><p class="text-md text-text-secondary mt-1">Jumlah Admin</p></div>
            <div class="flex items-center justify-center h-16 w-16 rounded-full bg-green-100 dark:bg-green-500/20"><i class="fa-solid fa-user-shield fa-xl text-green-500"></i></div>
        </div>
        <div class="flex items-center justify-between p-6 rounded-lg bg-component-bg dark:bg-dark-component-bg shadow-md">
            <div><p class="text-3xl font-bold text-text-primary dark:text-dark-text-primary">8</p><p class="text-md text-text-secondary mt-1">Pengguna Baru (Bulan Ini)</p></div>
            <div class="flex items-center justify-center h-16 w-16 rounded-full bg-gray-100 dark:bg-gray-700"><i class="fa-solid fa-user-plus fa-xl text-gray-500"></i></div>
        </div>
    </div>

    {{-- Tabel Manajemen Pengguna --}}
    <div class="bg-component-bg dark:bg-dark-component-bg shadow-md sm:rounded-lg overflow-hidden">
        <div class="p-4 border-b dark:border-border-dark flex flex-col sm:flex-row items-center justify-between gap-4">
            <h5 class="text-xl font-bold text-text-primary dark:text-white">Daftar Pengguna</h5>
            <div class="flex items-center gap-4 w-full sm:w-auto">
                {{-- Search Bar --}}
                <div class="relative w-full sm:w-64">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none"><i class="fa-solid fa-search text-gray-400"></i></div>
                    <input type="text" id="user-search" class="bg-body-bg border border-border-light text-text-primary text-sm rounded-lg focus:ring-primary focus:border-primary block w-full pl-10 p-2.5 dark:bg-dark-component-bg dark:border-border-dark" placeholder="Cari nama atau email...">
                </div>
                {{-- Filter Dropdown --}}
                <div>
                    <button id="filter-dropdown-button" data-dropdown-toggle="filter-dropdown" class="w-full sm:w-auto flex items-center justify-center py-2.5 px-4 text-sm font-medium text-text-primary focus:outline-none bg-component-bg rounded-lg border border-border-light hover:bg-body-bg dark:bg-dark-component-bg dark:text-dark-text-secondary dark:border-border-dark" type="button">
                        <i class="fa-solid fa-filter w-4 h-4 me-2"></i>Filter Role
                        <i class="fa-solid fa-chevron-down w-2.5 h-2.5 ms-2.5"></i>
                    </button>
                    <div id="filter-dropdown" class="z-10 hidden bg-component-bg divide-y divide-border-light rounded-lg shadow-md w-44 dark:bg-dark-component-bg">
                        <ul class="py-1 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="filter-dropdown-button">
                            <li><a href="#" class="block px-4 py-2 hover:bg-body-bg dark:hover:bg-dark-body-bg">Semua</a></li>
                            <li><a href="#" class="block px-4 py-2 hover:bg-body-bg dark:hover:bg-dark-body-bg">Admin</a></li>
                            <li><a href="#" class="block px-4 py-2 hover:bg-body-bg dark:hover:bg-dark-body-bg">User</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-text-secondary dark:text-dark-text-secondary">
                <thead class="text-xs uppercase bg-body-bg dark:bg-dark-component-bg/50">
                    <tr>
                        <th scope="col" class="px-6 py-3">Nama Pengguna</th>
                        <th scope="col" class="px-6 py-3">Role</th>
                        <th scope="col" class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b dark:border-border-dark">
                        <td class="px-6 py-4"><div class="font-medium text-text-primary dark:text-white">Neil Sims</div><div class="text-xs text-text-secondary">neil.sims@telkom.co.id</div></td>
                        <td class="px-6 py-4"><form class="flex items-center gap-2"><select class="role-dropdown bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"><option value="Admin" selected>Admin</option><option value="User">User</option></select><button type="submit" class="save-button hidden px-3 py-2 text-xs font-medium text-center text-white bg-blue-600 rounded-lg hover:bg-blue-700">Simpan</button></form></td>
                        <td class="px-6 py-4 text-center"><a href="#" class="font-medium text-red-500 hover:underline">Hapus</a></td>
                    </tr>
                    <tr class="border-b dark:border-border-dark">
                        <td class="px-6 py-4"><div class="font-medium text-text-primary dark:text-white">Bonnie Green</div><div class="text-xs text-text-secondary">bonnie@telkom.co.id</div></td>
                        <td class="px-6 py-4"><form class="flex items-center gap-2"><select class="role-dropdown bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"><option value="Admin">Admin</option><option value="User" selected>User</option></select><button type="submit" class="save-button hidden px-3 py-2 text-xs font-medium text-center text-white bg-blue-600 rounded-lg hover:bg-blue-700">Simpan</button></form></td>
                        <td class="px-6 py-4 text-center"><a href="#" class="font-medium text-red-500 hover:underline">Hapus</a></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Logika untuk menampilkan tombol "Simpan" saat role diubah
        const roleDropdowns = document.querySelectorAll('.role-dropdown');
        roleDropdowns.forEach(dropdown => {
            const initialRole = dropdown.value;
            const saveButton = dropdown.parentElement.querySelector('.save-button');
            dropdown.addEventListener('change', function () {
                if (this.value !== initialRole) {
                    saveButton.classList.remove('hidden');
                } else {
                    saveButton.classList.add('hidden');
                }
            });
            dropdown.parentElement.addEventListener('submit', function(e) {
                e.preventDefault();
                const newRole = dropdown.value;
                console.log(`Mengupdate role menjadi: ${newRole}`);
                alert(`Role telah diubah menjadi ${newRole}. (Simulasi)`);
                saveButton.classList.add('hidden');
            });
        });

        // Logika untuk search bar (simulasi frontend)
        const searchInput = document.getElementById('user-search');
        searchInput.addEventListener('keyup', function() {
            const filter = searchInput.value.toUpperCase();
            const table = document.querySelector('table');
            const tr = table.getElementsByTagName('tr');

            for (let i = 1; i < tr.length; i++) { // Mulai dari 1 untuk skip header
                const tdName = tr[i].getElementsByTagName('td')[0];
                if (tdName) {
                    const txtValue = tdName.textContent || tdName.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
        });
    });
</script>
@endpush