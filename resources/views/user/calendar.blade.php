@extends('layouts.app')

@section('title', 'Calendar | MoM Telkom')

@section('content')
<div class="pt-14">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row items-center justify-between p-6 md:p-8 overflow-hidden rounded-lg shadow-md bg-component-bg dark:bg-dark-component-bg border-l-4 border-primary mb-6">
        <div class="flex items-center space-x-4">
            <div>
                <h1 class="text-3xl font-bold text-text-primary dark:text-dark-text-primary">Calendar</h1>
                <p class="mt-1 text-text-secondary dark:text-dark-text-secondary">Lihat deadline dari task setiap MoM.</p>
            </div>
        </div>
    </div>

    {{-- Calendar Grid and Event List --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-component-bg dark:bg-dark-component-bg rounded-2xl shadow-md p-6 h-fit">
            <div class="flex justify-between items-center mb-6">
                <div class="flex items-center gap-4">
                    <h2 id="calendarTitle" class="text-xl font-semibold text-text-primary dark:text-dark-text-primary w-32 text-center"></h2>
                    <div class="flex items-center gap-2">
                        <button id="prevMonth" class="h-8 w-8 flex items-center justify-center text-text-secondary dark:text-dark-text-secondary hover:bg-body-bg dark:hover:bg-dark-body-bg rounded-full transition-colors">
                            <i class="fa-solid fa-chevron-left"></i>
                        </button>
                        <button id="nextMonth" class="h-8 w-8 flex items-center justify-center text-text-secondary dark:text-dark-text-secondary hover:bg-body-bg dark:hover:bg-dark-body-bg rounded-full transition-colors">
                            <i class="fa-solid fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
                <button id="goToday" class="px-4 py-1.5 text-sm font-medium text-text-primary border border-border-light rounded-lg hover:bg-body-bg dark:text-dark-text-primary dark:border-border-dark dark:hover:bg-dark-body-bg">
                    Today
                </button>
            </div>
            <div class="grid grid-cols-7 gap-2 text-center text-sm font-medium text-text-secondary dark:text-dark-text-secondary mb-3">
                <div>Sun</div>
                <div>Mon</div>
                <div>Tue</div>
                <div>Wed</div>
                <div>Thu</div>
                <div>Fri</div>
                <div>Sat</div>
            </div>
            <div id="calendarGrid" class="grid grid-cols-7 gap-2 text-sm"></div>
        </div>

        <div>
            <h3 id="eventListTitle" class="text-lg font-semibold text-text-primary dark:text-dark-text-primary mb-4"></h3>
            <div id="eventListContainer" class="space-y-4">
                <div id="noEventPlaceholder" class="text-center py-10 bg-component-bg dark:bg-dark-component-bg rounded-2xl shadow-md">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <p class="mt-2 text-sm text-text-secondary dark:text-dark-text-secondary">Tidak ada jadwal untuk bulan ini.</p>
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

        const calendarTitle = document.getElementById("calendarTitle");
        const calendarGrid = document.getElementById("calendarGrid");
        const eventList = document.getElementById("eventList");
        const eventListTitle = document.getElementById("eventListTitle");
        const noEventPlaceholder = document.getElementById("noEventPlaceholder");

        const today = new Date();
        let currentMonth = today.getMonth();
        let currentYear = today.getFullYear();

        const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

        // Function untuk fetch events dari API
        async function fetchEvents(month, year) {
            try {
                const response = await fetch(`{{ route('calendar.events') }}?month=${month + 1}&year=${year}`, {
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

        function renderCalendar(month, year) {
            calendarGrid.innerHTML = "";
            const firstDay = new Date(year, month).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();

            calendarTitle.textContent = `${monthNames[month]} ${year}`;

            // Empty cells for days before month starts
            for (let i = 0; i < firstDay; i++) {
                calendarGrid.innerHTML += `<div></div>`;
            }

            // Days of the month
            for (let d = 1; d <= daysInMonth; d++) {
                const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
                const isToday = d === today.getDate() && month === today.getMonth() && year === today.getFullYear();
                const hasEvent = events[dateStr] && events[dateStr].length > 0;

                const dayCell = document.createElement('div');
                dayCell.className = 'relative flex items-center justify-center p-2 rounded-full transition-colors aspect-square text-text-primary dark:text-dark-text-primary cursor-pointer hover:bg-body-bg dark:hover:bg-dark-body-bg';

                const dayNumber = document.createElement('span');
                dayNumber.textContent = d;

                if (isToday) {
                    dayNumber.className = 'flex items-center justify-center h-8 w-8 rounded-full bg-primary text-white font-semibold';
                }

                if (hasEvent) {
                    const eventCount = events[dateStr].length;
                    const eventDot = document.createElement('div');
                    eventDot.className = `absolute bottom-1.5 h-1.5 w-1.5 rounded-full ${isToday ? 'bg-white' : 'bg-primary'}`;
                    dayCell.appendChild(eventDot);

                    // Add tooltip
                    dayCell.title = `${eventCount} task${eventCount > 1 ? 's' : ''}`;
                }

                dayCell.appendChild(dayNumber);

                // Click handler untuk scroll ke event
                if (hasEvent) {
                    dayCell.addEventListener('click', () => {
                        document.getElementById('eventListContainer').scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    });
                }

                calendarGrid.appendChild(dayCell);
            }

            renderMonthlyEvents(month, year);
        }

        function renderMonthlyEvents(month, year) {
            eventListTitle.textContent = `Jadwal Bulan ${monthNames[month]}`;
            eventList.innerHTML = "";

            const monthlyEvents = Object.keys(events)
                .filter(date => {
                    const eventDate = new Date(date + 'T00:00:00');
                    return eventDate.getMonth() === month && eventDate.getFullYear() === year;
                })
                .sort((a, b) => new Date(a) - new Date(b))
                .flatMap(date => events[date].map(event => ({ ...event, date })));

            if (monthlyEvents.length > 0) {
                noEventPlaceholder.style.display = 'none';
                eventList.style.display = 'block';

                monthlyEvents.forEach(event => {
                    const eventDate = new Date(event.date + 'T00:00:00');
                    const day = eventDate.getDate();
                    const formattedDeadline = event.deadline.split('-').reverse().join('/');
                    const formattedCreatedDate = event.createdDate.split('-').reverse().join('/');

                    // Cek apakah deadline sudah lewat atau hari ini
                    const deadlineDate = new Date(event.deadline + 'T00:00:00');
                    const todayDate = new Date();
                    todayDate.setHours(0, 0, 0, 0);

                    let deadlineClass = 'text-red-500';
                    if (deadlineDate < todayDate) {
                        deadlineClass = 'text-red-700 font-bold'; // Overdue
                    } else if (deadlineDate.getTime() === todayDate.getTime()) {
                        deadlineClass = 'text-orange-500 font-bold'; // Today
                    }

                    const listItem = document.createElement('li');
                    listItem.className = "flex items-center gap-4 p-4 bg-component-bg dark:bg-dark-component-bg rounded-lg shadow-sm hover:shadow-md transition-shadow";
                    listItem.innerHTML = `
                        <div class="flex-shrink-0 h-12 w-12 flex flex-col items-center justify-center bg-primary/10 dark:bg-primary/20 rounded-lg">
                            <span class="text-xl font-bold text-primary">${String(day).padStart(2, '0')}</span>
                        </div>
                        <div class="flex-grow">
                            <p class="font-semibold text-text-primary dark:text-dark-text-primary">${event.task}</p>
                            <p class="text-sm text-text-secondary dark:text-dark-text-secondary">${event.momTitle}</p>
                            <div class="flex items-center text-xs text-gray-500 dark:text-gray-400 mt-1.5">
                                <i class="fa-solid fa-calendar-plus fa-xs mr-1.5"></i>
                                <span>Dibuat: ${formattedCreatedDate}</span>
                            </div>
                        </div>
                        <div class="text-right text-sm">
                            <p class="font-medium text-text-secondary dark:text-dark-text-secondary">Deadline</p>
                            <p class="${deadlineClass} font-semibold">${formattedDeadline}</p>
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

            // Fetch events untuk bulan baru
            fetchEvents(currentMonth, currentYear);
        }

        document.getElementById("prevMonth").addEventListener("click", () => navigate(-1));
        document.getElementById("nextMonth").addEventListener("click", () => navigate(1));
        document.getElementById("goToday").addEventListener("click", () => {
            currentMonth = today.getMonth();
            currentYear = today.getFullYear();
            fetchEvents(currentMonth, currentYear);
        });

        // Initial render
        renderCalendar(currentMonth, currentYear);
    });
</script>
@endpush
