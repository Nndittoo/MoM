@extends('layouts.app')

@section('title', 'Calendar | TR1 MoMatic')

@section('content')
<div class="pt-2">
    {{-- Header Halaman dengan Animasi Shimmer --}}
    <div class="p-6 md:p-8 rounded-xl shadow-lg bg-gray-800 border-l-4 border-red-500 mb-6 relative overflow-hidden shimmer-bg">
        <h1 class="text-3xl font-bold font-orbitron text-neon-red relative z-10">Task Calendar</h1>
        <p class="mt-1 text-gray-400 relative z-10">Lihat semua deadline tugas dari setiap MoM dalam format kalender.</p>
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
                {{-- Tombol Filter Status
                <div id="status-filters" class="flex items-center space-x-1 p-1 bg-gray-900 rounded-lg">
                    <button data-status="all" class="filter-btn px-3 py-1 text-sm rounded-md bg-red-600 text-white">All</button>
                    <button data-status="ongoing" class="filter-btn px-3 py-1 text-sm rounded-md text-gray-400 hover:bg-gray-700">On Going</button>
                    <button data-status="today" class="filter-btn px-3 py-1 text-sm rounded-md text-gray-400 hover:bg-gray-700">Due Today</button>
                    <button data-status="overdue" class="filter-btn px-3 py-1 text-sm rounded-md text-gray-400 hover:bg-gray-700">Overdue</button>
                </div> --}}
            </div>
            <div id="eventListContainer" class="space-y-4">
                <div id="noEventPlaceholder" class="text-center py-10 bg-gray-800/50 rounded-2xl border border-dashed border-gray-700">
                    <img src="{{ asset("img/LOGO.png") }}" alt="No Events" class="w-16 h-16 mx-auto opacity-30">
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
        let events = @json($events);

        const calendarTitle = document.getElementById("calendarTitle");
        const calendarGrid = document.getElementById("calendarGrid");
        const eventList = document.getElementById("eventList");
        const eventListTitle = document.getElementById("eventListTitle");
        const noEventPlaceholder = document.getElementById("noEventPlaceholder");

        const today = new Date();
        today.setHours(0, 0, 0, 0); // Normalisasi tanggal hari ini
        let currentMonth = today.getMonth();
        let currentYear = today.getFullYear();
        const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

        async function fetchEvents(month, year) {
            try {
                // Tampilkan loading state sederhana
                eventListTitle.textContent = `Loading jadwal untuk ${monthNames[month]}...`;
                eventList.innerHTML = '';
                noEventPlaceholder.style.display = 'none';

                const response = await fetch(`{{ route('calendar.events') }}?month=${month + 1}&year=${year}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                });

                if (!response.ok) throw new Error('Failed to fetch events');

                events = await response.json();
                renderCalendar(month, year);
            } catch (error) {
                console.error('Error fetching events:', error);
                events = {};
                renderCalendar(month, year); // Render kalender kosong jika error
            }
        }


        function renderCalendar(month, year) {
            calendarGrid.innerHTML = "";
            const firstDay = new Date(year, month).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();

            calendarTitle.textContent = `${monthNames[month]} ${year}`;

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
                    dayNumber.className = 'flex items-center justify-center h-8 w-8 rounded-full bg-red-600 text-white font-semibold';
                }

                if (hasEvent) {
                    const eventCount = events[dateStr].length;
                    const eventDot = document.createElement('div');
                    eventDot.className = `absolute bottom-1.5 h-1.5 w-1.5 rounded-full ${isToday ? 'bg-white' : 'bg-red-500'}`;
                    dayCell.appendChild(eventDot);
                    dayCell.title = `${eventCount} task${eventCount > 1 ? 's' : ''} on this day`;

                    dayCell.addEventListener('click', () => {
                        const eventSection = document.getElementById('eventListTitle');
                        eventSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    });
                }

                dayCell.appendChild(dayNumber);
                calendarGrid.appendChild(dayCell);
            }
            renderMonthlyEvents(month, year);
        }

        function renderMonthlyEvents(month, year) {
            eventListTitle.textContent = `Jadwal Bulan ${monthNames[month]}`;
            eventList.innerHTML = "";

            const monthlyEvents = Object.keys(events)
                .sort((a, b) => new Date(a) - new Date(b))
                .flatMap(date => events[date].map(event => ({ ...event, date })));

            if (monthlyEvents.length > 0) {
                noEventPlaceholder.style.display = 'none';
                eventList.style.display = 'block';

                monthlyEvents.forEach(event => {
                    const eventDate = new Date(event.date + 'T00:00:00');
                    const day = eventDate.getDate();
                    const deadlineDate = new Date(event.deadline + 'T00:00:00');

                    let deadlineClass = 'text-red-400';
                    if (deadlineDate < today) {
                        deadlineClass = 'text-gray-500 line-through'; // Overdue
                    } else if (deadlineDate.getTime() === today.getTime()) {
                        deadlineClass = 'text-yellow-400 font-bold animate-pulse'; // Today
                    }

                    const listItem = document.createElement('li');
                    listItem.className = "flex items-start gap-4 p-4 bg-gray-800 rounded-lg shadow-sm hover:shadow-md hover:bg-gray-700/50 transition-all";
                    listItem.innerHTML = `
                        <div class="flex-shrink-0 h-12 w-12 flex flex-col items-center justify-center bg-red-500/10 rounded-lg">
                            <span class="text-xl font-bold text-red-400">${String(day).padStart(2, '0')}</span>
                        </div>
                        <div class="flex-grow">
                            <p class="font-semibold text-white">${event.task}</p>
                            <p class="text-sm text-gray-400">${event.momTitle}</p>
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
            }
        }

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

        document.getElementById("prevMonth").addEventListener("click", () => navigate(-1));
        document.getElementById("nextMonth").addEventListener("click", () => navigate(1));
        document.getElementById("goToday").addEventListener("click", () => {
            currentMonth = new Date().getMonth();
            currentYear = new Date().getFullYear();
            fetchEvents(currentMonth, currentYear);
        });

        // Render awal
        renderCalendar(currentMonth, currentYear);
    });
</script>
@endpush
