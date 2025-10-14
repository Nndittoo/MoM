@extends('admin.layouts.app')

@section('title', 'Calendar | Admin MoM Telkom')

@push('styles')
<style>
    /* Animasi fade-in untuk daftar event */
    .event-item {
        opacity: 0;
        transform: translateY(10px);
        animation: fadeInSlideUp 0.5s ease-out forwards;
    }
    @keyframes fadeInSlideUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
@endpush

@section('content')
<div class="pt-2">
    {{-- Header Halaman --}}
    <div class="p-6 md:p-8 rounded-xl shadow-lg bg-gray-800 border-l-4 border-red-500 mb-6">
        <h1 class="text-3xl font-bold font-orbitron text-neon-red">Task Calendar</h1>
        <p class="mt-1 text-gray-400">Lihat semua deadline tugas dari setiap MoM dalam format kalender.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Kartu Kalender --}}
        <div class="lg:col-span-1 bg-gray-800 rounded-2xl shadow-md p-6 h-fit border border-gray-700">
            <div class="flex justify-between items-center mb-6">
                <div class="flex items-center gap-4">
                    <h2 id="calendarTitle" class="text-xl font-semibold text-white font-orbitron w-32 text-center"></h2>
                    <div class="flex items-center gap-2">
                        <button id="prevMonth" class="h-8 w-8 flex items-center justify-center text-gray-400 hover:bg-gray-700 rounded-full transition-colors">
                            <i class="fa-solid fa-chevron-left"></i>
                        </button>
                        <button id="nextMonth" class="h-8 w-8 flex items-center justify-center text-gray-400 hover:bg-gray-700 rounded-full transition-colors">
                            <i class="fa-solid fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
                <button id="goToday" class="px-4 py-1.5 text-sm font-medium text-gray-300 border border-gray-600 rounded-lg hover:bg-gray-700">
                    Today
                </button>
            </div>
            <div class="grid grid-cols-7 gap-2 text-center text-sm font-medium text-gray-500 mb-3">
                <div>Sun</div><div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div>
            </div>
            <div id="calendarGrid" class="grid grid-cols-7 gap-2 text-sm"></div>
        </div>

        {{-- Daftar Event dengan Filter --}}
        <div class="lg:col-span-2">
            <div class="flex flex-col sm:flex-row justify-between sm:items-center mb-4 gap-4">
                <h3 id="eventListTitle" class="text-lg font-semibold text-white font-orbitron"></h3>
                {{-- Tombol Filter Status --}}
                <div id="statusFilters" class="flex items-center space-x-1 p-1 bg-gray-900 rounded-lg">
                    <button data-status="all" class="filter-btn px-3 py-1 text-sm rounded-md bg-red-600 text-white">All</button>
                    <button data-status="ongoing" class="filter-btn px-3 py-1 text-sm rounded-md text-gray-400 hover:bg-gray-700">On Going</button>
                    <button data-status="today" class="filter-btn px-3 py-1 text-sm rounded-md text-gray-400 hover:bg-gray-700">Due Today</button>
                    <button data-status="overdue" class="filter-btn px-3 py-1 text-sm rounded-md text-gray-400 hover:bg-gray-700">Overdue</button>
                </div>
            </div>
            <div id="eventListContainer" class="space-y-4">
                <div id="noEventPlaceholder" class="text-center py-10 bg-gray-800/50 rounded-2xl border border-dashed border-gray-700">
                    <img src="{{ asset("img/LOGO.png") }}" alt="No Events" class="h-28 mx-auto opacity-30">
                    <p class="mt-4 text-sm text-gray-500">Tidak ada jadwal untuk bulan ini.</p>
                </div>
                <ul id="eventList" class="space-y-4"></ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Data dari backend
    let events = @json($events);
    let currentFilter = 'all';

    const calendarTitle = document.getElementById("calendarTitle");
    const calendarGrid = document.getElementById("calendarGrid");
    const eventList = document.getElementById("eventList");
    const eventListTitle = document.getElementById("eventListTitle");
    const noEventPlaceholder = document.getElementById("noEventPlaceholder");
    const statusFilters = document.getElementById("statusFilters");

    const today = new Date();
    let currentMonth = today.getMonth();
    let currentYear = today.getFullYear();

    const monthNames = [
        "January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
    ];

    // Ambil data event dari backend
    async function fetchEvents(month, year) {
        try {
            const response = await fetch(`{{ route('admin.calendars.events') }}?month=${month + 1}&year=${year}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) throw new Error('Failed to fetch events');

            events = await response.json();
            renderCalendar(month, year);
        } catch (error) {
            console.error('Error fetching events:', error);
            events = {};
            renderCalendar(month, year);
        }
    }

    // Render kalender
    function renderCalendar(month, year) {
        calendarGrid.innerHTML = "";
        const firstDay = new Date(year, month).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();

        calendarTitle.textContent = `${monthNames[month]} ${year}`;

        // Kosongkan sel sebelum tanggal 1
        for (let i = 0; i < firstDay; i++) {
            calendarGrid.innerHTML += `<div></div>`;
        }

        for (let d = 1; d <= daysInMonth; d++) {
            const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
            const isToday = d === today.getDate() && month === today.getMonth() && year === today.getFullYear();
            const hasEvent = events[dateStr] && events[dateStr].length > 0;

            const dayCell = document.createElement('div');
            dayCell.className = 'relative flex items-center justify-center p-2 rounded-full transition-colors aspect-square text-gray-300 cursor-pointer hover:bg-gray-700';
            const dayNumber = document.createElement('span');
            dayNumber.textContent = d;

            if (isToday) {
                dayNumber.className = 'flex items-center justify-center h-8 w-8 rounded-full bg-red-600 text-white font-semibold animate-pulse';
            }

            if (hasEvent) {
                const eventDot = document.createElement('div');
                eventDot.className = `absolute bottom-1.5 h-1.5 w-1.5 rounded-full ${isToday ? 'bg-white' : 'bg-red-500'} animate-bounce`;
                dayCell.appendChild(eventDot);
                dayCell.title = `${events[dateStr].length} task(s) on this day`;
                dayCell.addEventListener('click', () => {
                    document.getElementById('eventListTitle').scrollIntoView({ behavior: 'smooth', block: 'start' });
                });
            }

            dayCell.appendChild(dayNumber);
            calendarGrid.appendChild(dayCell);
        }

        renderMonthlyEvents(month, year);
    }

    // Render daftar event bulanan
    function renderMonthlyEvents(month, year) {
        eventListTitle.textContent = `Jadwal Bulan ${monthNames[month]}`;
        eventList.innerHTML = "";

        let monthlyEvents = Object.keys(events)
            .sort((a, b) => new Date(a) - new Date(b))
            .flatMap(date => events[date].map(event => ({ ...event, date })));

        // Terapkan filter
        if (currentFilter !== 'all') {
            monthlyEvents = monthlyEvents.filter(event => {
                const deadlineDate = new Date(event.deadline + 'T00:00:00');
                const todayStart = new Date(today.toDateString()); // normalize
                if (currentFilter === 'overdue') return deadlineDate < todayStart;
                if (currentFilter === 'today') return deadlineDate.getTime() === todayStart.getTime();
                if (currentFilter === 'ongoing') return deadlineDate > todayStart;
                return false;
            });
        }

        if (monthlyEvents.length > 0) {
            noEventPlaceholder.style.display = 'none';
            eventList.style.display = 'block';
            monthlyEvents.forEach((event, index) => {
                const day = new Date(event.date + 'T00:00:00').getDate();
                const deadlineDate = new Date(event.deadline + 'T00:00:00');

                let deadlineClass = 'text-red-400'; // Default On Going
                if (deadlineDate < today) deadlineClass = 'text-gray-500 line-through'; // Overdue
                else if (deadlineDate.getDate() === today.getDate() &&
                         deadlineDate.getMonth() === today.getMonth() &&
                         deadlineDate.getFullYear() === today.getFullYear()) {
                    deadlineClass = 'text-yellow-400 font-bold animate-pulse'; // Today
                }

                const listItem = document.createElement('li');
                listItem.className = "event-item flex items-start gap-4 p-4 bg-gray-800 rounded-lg shadow-sm hover:bg-gray-700/50 transition-all";
                listItem.style.animationDelay = `${index * 50}ms`;
                listItem.innerHTML = `
                    <div class="flex-shrink-0 h-12 w-12 flex flex-col items-center justify-center bg-red-500/10 rounded-lg">
                        <span class="text-xl font-bold text-red-400">${String(day).padStart(2, '0')}</span>
                    </div>
                    <div class="flex-grow">
                        <p class="font-semibold text-white">${event.task}</p>
                        <a href="/admin/moms/${event.mom_id}" class="text-sm text-gray-400 hover:text-red-400 hover:underline">${event.momTitle}</a>
                        <div class="flex items-center text-xs text-gray-500 mt-1.5">
                            <i class="fa-solid fa-calendar-plus fa-xs mr-1.5"></i>
                            <span>Dibuat: ${event.createdDate.split('-').reverse().join('/')}</span>
                        </div>
                    </div>
                    <div class="text-right text-sm flex-shrink-0">
                        <p class="font-medium text-gray-500">Deadline</p>
                        <p class="${deadlineClass} font-semibold">${event.deadline.split('-').reverse().join('/')}</p>
                    </div>
                `;
                eventList.appendChild(listItem);
            });
        } else {
            noEventPlaceholder.style.display = 'block';
            eventList.style.display = 'none';
            noEventPlaceholder.querySelector('p').textContent =
                currentFilter !== 'all'
                    ? `Tidak ada tugas dengan status "${currentFilter}".`
                    : `Tidak ada jadwal untuk bulan ini.`;
        }
    }

    // Navigasi bulan
    function navigate(offset) {
        currentMonth += offset;
        if (currentMonth < 0) {
            currentMonth = 11;
            currentYear--;
        } else if (currentMonth > 11) {
            currentMonth = 0;
            currentYear++;
        }
        fetchEvents(currentMonth, currentYear);
    }

    // Event listener tombol filter
    statusFilters.addEventListener('click', (e) => {
        if (e.target.classList.contains('filter-btn')) {
            currentFilter = e.target.dataset.status;
            statusFilters.querySelectorAll('.filter-btn').forEach(btn => {
                btn.classList.remove('bg-red-600', 'text-white');
                btn.classList.add('text-gray-400', 'hover:bg-gray-700');
            });
            e.target.classList.add('bg-red-600', 'text-white');
            e.target.classList.remove('text-gray-400', 'hover:bg-gray-700');
            renderMonthlyEvents(currentMonth, currentYear);
        }
    });

    // Tombol navigasi
    document.getElementById("prevMonth").addEventListener("click", () => navigate(-1));
    document.getElementById("nextMonth").addEventListener("click", () => navigate(1));
    document.getElementById("goToday").addEventListener("click", () => {
        currentMonth = today.getMonth();
        currentYear = today.getFullYear();
        fetchEvents(currentMonth, currentYear);
    });

    // Render pertama
    renderCalendar(currentMonth, currentYear);
});
</script>
@endpush
