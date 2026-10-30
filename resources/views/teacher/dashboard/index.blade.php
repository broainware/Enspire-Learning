@extends('layouts.app')
@section('title', 'Dashboard Pengajar')
@section('content')
<div class="page-heading"><div><p class="eyebrow">AREA PENGAJAR</p><h1>Halo, {{ str_starts_with($teacher->name, 'Bu ') ? $teacher->name : 'Bu '.$teacher->name }}!</h1><p>Semangat mengajar hari ini! Yuk, cek jadwal, materi, dan aktivitas belajarmu.</p></div><div class="heading-quote"><span>✦</span><p>“Pendidikan bukan hanya tentang mengajar, tapi tentang menyalakan semangat belajar.”</p></div></div>
<div class="stat-grid stat-grid-four">
    <div class="stat-card"><div class="stat-icon peach">♧</div><div><span>Total Siswa</span><strong>{{ number_format($totalStudents) }}</strong><small>siswa aktif terhubung ke mapel</small></div></div>
    <div class="stat-card"><div class="stat-icon orange">▣</div><div><span>Jadwal Hari Ini</span><strong>{{ $todayCount }}</strong><small>sesi mengajar</small></div></div>
    <div class="stat-card"><div class="stat-icon lavender">▤</div><div><span>Total Materi</span><strong>{{ $materialCount }}</strong><small>materi tersedia</small></div></div>
    <div class="stat-card"><div class="stat-icon mint">!</div><div><span>Perlu Perhatian</span><strong>{{ $attentionCount }}</strong><small>sesi belum ditandai selesai</small></div></div>
</div>
<div class="dashboard-grid">
    <section class="panel panel-wide">
        <div class="panel-heading"><div><h2>Jadwal Mengajar Hari Ini</h2><p>{{ now()->translatedFormat('l, d F Y') }}</p></div><a class="text-link" href="{{ route('teacher.sessions.index') }}">Lihat semua →</a></div>
        @if($todaySessions->isEmpty()) <div class="empty-state"><span>☀</span><strong>Belum ada jadwal hari ini</strong><p>Gunakan waktu ini untuk menyiapkan materi atau menambahkan jadwal baru.</p><a href="{{ route('teacher.sessions.create') }}" class="btn btn-primary btn-small">+ Tambah jadwal</a></div>
        @else
        <div class="table-wrap"><table class="data-table"><thead><tr><th>Waktu</th><th>Mata Pelajaran</th><th>Kelas</th><th>Materi</th><th>Status</th></tr></thead><tbody>
            @foreach($todaySessions as $session)<tr><td><strong>{{ substr($session->start_time,0,5) }} – {{ substr($session->end_time,0,5) }}</strong></td><td>{{ $session->subject?->name }}</td><td>{{ $session->classRoom?->name ?? '–' }}</td><td>{{ $session->material?->title ?? 'Belum ditentukan' }}</td><td><span class="status-pill status-{{ $session->status }}">{{ $session->display_status }}</span></td></tr>@endforeach
        </tbody></table></div>
        @endif
    </section>
    <section class="panel panel-side"><div class="panel-heading"><div><h2>Kalender</h2><p>Jadwal aktivitas bulan ini</p></div></div><x-calendar-widget :event-dates="$eventDates" :calendar-days="$calendarDays" :calendar-start-offset="$calendarStartOffset" /></section>
    <section class="panel panel-wide"><div class="panel-heading"><div><h2>Aktivitas Mengajar</h2><p>Perbandingan sesi selesai dan belum selesai selama 7 hari terakhir.</p></div><a class="text-link" href="{{ route('teacher.reports.index') }}">Buka laporan →</a></div><div class="chart-box"><canvas id="teacherWeekChart"></canvas></div></section>
    <section class="panel panel-side"><div class="panel-heading"><div><h2>Materi Terbaru</h2><p>Materi dari semua mata pelajaran</p></div><a class="text-link" href="{{ route('teacher.materials.index') }}">Semua →</a></div>
        <div class="compact-list">@forelse($latestMaterials as $material)<a href="{{ route('teacher.materials.edit', $material) }}" class="compact-item"><span class="compact-file">▤</span><span class="compact-main"><strong>{{ $material->title }}</strong><small>{{ $material->subject?->name }} · {{ $material->createdBy?->name }}</small></span><span class="mini-arrow">↗</span></a>@empty<p class="muted-text">Belum ada materi.</p>@endforelse</div>
    </section>
</div>
@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const el = document.getElementById('teacherWeekChart');
    if (!el || !window.Chart) return;
    new window.Chart(el, {type:'bar', data:{labels:@json($weekLabels), datasets:[{label:'Selesai',data:@json($completedSeries),backgroundColor:'#f0783d',borderRadius:8,barThickness:18},{label:'Belum selesai',data:@json($pendingSeries),backgroundColor:'#f8d9c7',borderRadius:8,barThickness:18}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'bottom',labels:{usePointStyle:true,boxWidth:8,color:'#85766e'}}},scales:{x:{grid:{display:false},ticks:{color:'#988b83'}},y:{beginAtZero:true,ticks:{precision:0,color:'#988b83'},grid:{color:'#f1e9e1'}}}}});
});
</script>
@endpush
