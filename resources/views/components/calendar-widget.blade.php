
@props([
    'eventDates' => [],
    'calendarDays' => [],
    'calendarStartOffset' => 0,
    'title' => null
])

<div
    class="calendar-widget"
    x-data="{
        eventDates: @js(collect($eventDates)->map(fn($date) => \Illuminate\Support\Carbon::parse($date)->toDateString())->values()->all()),

        currentYear: {{ now()->year }},
        currentMonth: {{ now()->month - 1 }},
        todayKey: @js(today()->toDateString()),

        get monthLabel() {
            return new Intl.DateTimeFormat('id-ID', {
                month: 'long',
                year: 'numeric'
            }).format(new Date(this.currentYear, this.currentMonth, 1));
        },

        get daysInMonth() {
            return new Date(
                this.currentYear,
                this.currentMonth + 1,
                0
            ).getDate();
        },

        get startOffset() {
            return new Date(
                this.currentYear,
                this.currentMonth,
                1
            ).getDay();
        },

        get calendarCells() {
            return [
                ...Array(this.startOffset).fill(null),
                ...Array.from(
                    { length: this.daysInMonth },
                    (_, index) => index + 1
                )
            ];
        },

        dateKey(day) {
            const month = String(this.currentMonth + 1).padStart(2, '0');
            const date = String(day).padStart(2, '0');

            return this.currentYear + '-' + month + '-' + date;
        },

        hasEvent(day) {
            return day !== null &&
                this.eventDates.includes(this.dateKey(day));
        },

        isToday(day) {
            return day !== null &&
                this.dateKey(day) === this.todayKey;
        },

        previousMonth() {
            const date = new Date(
                this.currentYear,
                this.currentMonth - 1,
                1
            );

            this.currentYear = date.getFullYear();
            this.currentMonth = date.getMonth();
        },

        nextMonth() {
            const date = new Date(
                this.currentYear,
                this.currentMonth + 1,
                1
            );

            this.currentYear = date.getFullYear();
            this.currentMonth = date.getMonth();
        }
    }"
>

    {{-- HEADER KALENDER --}}
    <div class="calendar-heading">

        <strong x-text="monthLabel"></strong>

        <span class="calendar-arrows">

            <button
                type="button"
                aria-label="Bulan sebelumnya"
                @click="previousMonth()"
            >
                <x-icon name="chevron-left" size="16" />
            </button>

            <button
                type="button"
                aria-label="Bulan berikutnya"
                @click="nextMonth()"
            >
                <x-icon name="chevron-right" size="16" />
            </button>

        </span>

    </div>

    {{-- NAMA HARI --}}
    <div class="calendar-grid calendar-weekdays">
        <span>Min</span>
        <span>Sen</span>
        <span>Sel</span>
        <span>Rab</span>
        <span>Kam</span>
        <span>Jum</span>
        <span>Sab</span>
    </div>

    {{-- TANGGAL --}}
    <div class="calendar-grid">

        <template
            x-for="(day, index) in calendarCells"
            :key="currentYear + '-' + currentMonth + '-' + index"
        >

            <span
                :class="{
                    'calendar-empty': day === null,
                    'calendar-day': day !== null,
                    'today': isToday(day),
                    'has-event': hasEvent(day)
                }"
            >

                <span
                    x-show="day !== null"
                    x-text="day"
                ></span>

                <i x-show="hasEvent(day)"></i>

            </span>

        </template>

    </div>

    {{-- KETERANGAN --}}
    <div class="calendar-legend">
        <span>
            <i class="dot-orange"></i>
            Ada aktivitas
        </span>

        <span>
            <i class="dot-soft"></i>
            Hari ini
        </span>
    </div>

</div>
