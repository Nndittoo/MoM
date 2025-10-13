{{-- ======================================================= --}}
{{--                 SIDEBAR BLADE YANG DIPERBARUI           --}}
{{-- ======================================================= --}}

<aside id="logo-sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform -translate-x-full bg-gray-800 border-r border-gray-700 sm:translate-x-0" aria-label="Sidebar">
    <div class="h-full px-4 pb-6 overflow-y-auto flex flex-col justify-between">
        <ul class="space-y-2 font-medium">
            {{-- PERUBAHAN BESAR: Styling link aktif dan hover yang baru --}}
            <li>
                <a href="{{ url('/user') }}" class="flex items-center p-3 rounded-lg group transition
                    {{ request()->is('dashboard') || request()->is('user')
                        ? 'bg-red-500/10 text-white font-semibold border-l-4 border-red-500'
                        : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                    <i class="fa-solid fa-chart-pie w-5 h-5 {{ request()->is('dashboard') || request()->is('user') ? 'text-red-400' : '' }}"></i>
                    <span class="ms-3">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="{{ url('/draft') }}" class="flex items-center p-3 rounded-lg group transition
                    {{ request()->is('draft')
                        ? 'bg-red-500/10 text-white font-semibold border-l-4 border-red-500'
                        : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                    <i class="fa-solid fa-note-sticky w-5 h-5 {{ request()->is('draft') ? 'text-red-400' : '' }}"></i>
                    <span class="ms-3">Draft MoM</span>
                </a>
            </li>
            <li>
                <a href="{{ url('/create') }}" class="flex items-center p-3 rounded-lg group transition
                    {{ request()->is('create')
                        ? 'bg-red-500/10 text-white font-semibold border-l-4 border-red-500'
                        : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                    <i class="fa-solid fa-notes-medical w-5 h-5 {{ request()->is('create') ? 'text-red-400' : '' }}"></i>
                    <span class="ms-3">Create MoM</span>
                </a>
            </li>
            <li>
                <a href="{{ url('/reminder') }}" class="flex items-center p-3 rounded-lg group transition relative
                    {{ request()->is('reminder')
                        ? 'bg-red-500/10 text-white font-semibold border-l-4 border-red-500'
                        : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                    <i class="fa-solid fa-bell w-5 h-5 {{ request()->is('reminder') ? 'text-red-400' : '' }}"></i>
                    <span class="ms-3">Reminder</span>
                    @if(isset($reminderCount) && $reminderCount > 0)
                    <span class="absolute right-3 inline-flex items-center justify-center min-w-[1.5rem] h-6 px-1.5 text-xs font-bold text-white bg-red-500 rounded-full">
                        {{ $reminderCount > 99 ? '99+' : $reminderCount }}
                    </span>
                    @endif
                </a>
            </li>
            <li>
                <a href="{{ url('/calendar') }}" class="flex items-center p-3 rounded-lg group transition
                    {{ request()->is('calendar')
                        ? 'bg-red-500/10 text-white font-semibold border-l-4 border-red-500'
                        : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                    <i class="fa-solid fa-calendar-alt w-5 h-5 {{ request()->is('calendar') ? 'text-red-400' : '' }}"></i>
                    <span class="ms-3">Calendar</span>
                </a>
            </li>
        </ul>
        <div class="border-t border-gray-700 pt-4">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                        class="w-full flex items-center p-3 rounded-lg text-red-400 hover:bg-red-500/20 group transition font-medium">
                    <i class="fa-solid fa-arrow-right-from-bracket w-5 h-5"></i>
                    <span class="ms-3">Log Out</span>
                </button>
            </form>
        </div>
    </div>
</aside>
