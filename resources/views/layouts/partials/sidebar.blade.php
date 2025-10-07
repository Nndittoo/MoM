<aside id="logo-sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform -translate-x-full bg-component-bg border-r border-border-light sm:translate-x-0 dark:bg-dark-component-bg dark:border-border-dark" aria-label="Sidebar">
    <div class="h-full px-4 pb-6 overflow-y-auto flex flex-col justify-between">
        <ul class="space-y-2 font-medium">
            <li>
                <a href="{{ url('/user') }}" class="flex items-center p-3 rounded-lg group transition {{ request()->is('dashboard') || request()->is('user') ? 'bg-primary/20 text-primary font-semibold' : 'text-text-secondary dark:text-dark-text-secondary hover:bg-primary/10 dark:hover:bg-primary/20 hover:text-primary' }}">
                    <i class="fa-solid fa-chart-pie w-5 h-5"></i>
                    <span class="ms-3">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="{{ url('/draft') }}" class="flex items-center p-3 rounded-lg group transition {{ request()->is('draft') ? 'bg-primary/20 text-primary font-semibold' : 'text-text-secondary dark:text-dark-text-secondary hover:bg-primary/10 dark:hover:bg-primary/20 hover:text-primary' }}">
                    <i class="fa-solid fa-note-sticky w-5 h-5"></i>
                    <span class="ms-3">Draft MoM</span>
                </a>
            </li>
            <li>
                <a href="{{ url('/create') }}" class="flex items-center p-3 rounded-lg group transition {{ request()->is('create') ? 'bg-primary/20 text-primary font-semibold' : 'text-text-secondary dark:text-dark-text-secondary hover:bg-primary/10 dark:hover:bg-primary/20 hover:text-primary' }}">
                    <i class="fa-solid fa-notes-medical w-5 h-5"></i>
                    <span class="ms-3">Create MoM</span>
                </a>
            </li>
            <li>
                <a href="{{ url('/reminder') }}" class="flex items-center p-3 rounded-lg group transition relative {{ request()->is('reminder') ? 'bg-primary/20 text-primary font-semibold' : 'text-text-secondary dark:text-dark-text-secondary hover:bg-primary/10 dark:hover:bg-primary/20 hover:text-primary' }}">
                    <i class="fa-solid fa-bell w-5 h-5"></i>
                    <span class="ms-3">Reminder</span>
                    <span class="absolute right-3 inline-flex items-center justify-center w-6 h-6 text-xs font-medium text-white bg-red-500 rounded-full">3</span>
                </a>
            </li>
            <li>
                <a href="{{ url('/calendar') }}" class="flex items-center p-3 rounded-lg group transition {{ request()->is('calendar') ? 'bg-primary/20 text-primary font-semibold' : 'text-text-secondary dark:text-dark-text-secondary hover:bg-primary/10 dark:hover:bg-primary/20 hover:text-primary' }}">
                    <i class="fa-solid fa-calendar-alt w-5 h-5"></i>
                    <span class="ms-3">Calendar</span>
                </a>
            </li>
        </ul>
        <div class="border-t border-border-light dark:border-border-dark pt-4">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                    class="w-full flex items-center gap-5 p-3 rounded-lg text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-500/20 group transition font-medium">
                    <i class="fa-solid fa-arrow-right-from-bracket w-3 h-3"></i>
                    <p>Log Out</p>
                </button>
            </form>
        </div>
    </div>
</aside>
