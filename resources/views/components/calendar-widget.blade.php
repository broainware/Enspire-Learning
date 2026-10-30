@props(['eventDates' => [], 'calendarDays' => [], 'calendarStartOffset' => 0, 'title' => null])
<div class="calendar-widget">
    <div class="calendar-heading"><strong>{{ $title ?? now()->translatedFormat('F Y') }}</strong><span class="calendar-arrows">‹ &nbsp; ›</span></div>
    <div class="calendar-grid calendar-weekdays"><span>Min</span><span>Sen</span><span>Sel</span><span>Rab</span><span>Kam</span><span>Jum</span><span>Sab</span></div>
    <div class="calendar-grid">
        @for ($i = 0; $i < $calendarStartOffset; $i++) <span class="calendar-empty"></span> @endfor
        @foreach ($calendarDays as $day)
            @php $dateString = now()->startOfMonth()->day($day)->format('Y-m-d'); $hasEvent = collect($eventDates)->contains($dateString); @endphp
            <span class="calendar-day {{ $dateString === today()->format('Y-m-d') ? 'today' : '' }} {{ $hasEvent ? 'has-event' : '' }}">{{ $day }}@if($hasEvent)<i></i>@endif</span>
        @endforeach
    </div>
    <div class="calendar-legend"><span><i class="dot-orange"></i> Ada aktivitas</span><span><i class="dot-soft"></i> Hari ini</span></div>
</div>
