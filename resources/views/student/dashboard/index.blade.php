
@extends('layouts.app')

@section('title', 'Dashboard Siswa')

@section('content')

<div class="page-heading">

    <div>
        <p class="eyebrow">AREA SISWA</p>
        <h1>Halo, {{ $student->name }}!</h1>
        <p>Selamat datang di Enspire Learning. Yuk, terus semangat belajar dan raih tujuanmu.</p>
    </div>

    <div class="heading-quote student-quote">
        <span><x-icon name="sparkles" size="20" /></span>
        <p>“Proses kecil hari ini, hasil besar esok nanti.”</p>
        <small>
            Program: {{ $student->subjects->pluck('name')->join(', ') ?: 'Belum ditentukan' }}
        </small>
    </div>

</div>

{{-- STATISTIK --}}
<div class="stat-grid stat-grid-three">

    <div class="stat-card">
        <div class="stat-icon peach">
            <x-icon name="book-open" size="20" />
        </div>
        <div>
            <span>Materi Saya</span>
            <strong>{{ $materialCount }}</strong>
            <small>materi tersedia untukmu</small>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon orange">
            <x-icon name="calendar" size="20" />
        </div>
        <div>
            <span>Jadwal Terdekat</span>
            <strong>{{ $sessions->count() }}</strong>
            <small>sesi yang akan datang</small>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon mint">
            <x-icon name="chart-pie" size="20" />
        </div>
        <div>
            <span>Progress Belajar</span>
            <strong>{{ $progressPercent }}%</strong>
            <small>{{ $doneProgress }} materi selesai</small>
        </div>
    </div>

</div>

<div class="dashboard-grid">

    {{-- JADWAL TERDEKAT --}}
    <section class="panel panel-wide">

        <div class="panel-heading">
            <div>
                <h2>Jadwal Terdekat</h2>
                <p>Jadwal kelas berdasarkan mata pelajaran yang kamu ikuti.</p>
            </div>

            <a href="{{ route('student.schedules.index') }}" class="text-link">
                Lihat semua
                <x-icon name="arrow-right" size="15" class="button-trailing-icon" />
            </a>
        </div>

        @if($sessions->isEmpty())

            <div class="empty-state">
                <span><x-icon name="sun" size="28" /></span>
                <strong>Belum ada jadwal mendatang</strong>
                <p>Jadwal akan tampil di sini setelah pengajar menambahkannya.</p>
            </div>

        @else

            <div class="table-wrap">

                <table class="data-table">

                    <thead>
                        <tr>
                            <th>Hari & Tanggal</th>
                            <th>Materi Belajar</th>
                            <th>Tutor</th>
                            <th>Waktu</th>
                            <th>Kelas</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach($sessions as $session)

                            <tr>
                                <td>
                                    <strong>
                                        {{ $session->session_date->translatedFormat('D, d M Y') }}
                                    </strong>
                                </td>

                                <td>
                                    {{ $session->material?->title ?? $session->subject?->name }}
                                </td>

                                <td>{{ $session->teacher?->name }}</td>

                                <td>
                                    {{ substr($session->start_time, 0, 5) }}–{{ substr($session->end_time, 0, 5) }}
                                </td>

                                <td>{{ $session->classRoom?->name ?? '–' }}</td>
                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        @endif

    </section>

    {{-- KALENDER --}}
    <section class="panel panel-side">

        <div class="panel-heading">
            <div>
                <h2>Kalender</h2>
                <p>Jadwal belajar bulan ini</p>
            </div>
        </div>

        <x-calendar-widget
            :event-dates="$eventDates"
            :calendar-days="$calendarDays"
            :calendar-start-offset="$calendarStartOffset"
        />

    </section>

    {{-- MATERI TERBARU --}}
    <section class="panel panel-full">

        <div class="panel-heading">
            <div>
                <h2>Materi Terbaru</h2>
                <p>Materi yang dapat kamu akses sesuai program belajarmu.</p>
            </div>

            <a href="{{ route('student.materials.index') }}" class="text-link">
                Lihat semua
                <x-icon name="arrow-right" size="15" class="button-trailing-icon" />
            </a>
        </div>

        <div class="material-card-grid">

            @forelse($latestMaterials as $material)

                <a href="{{ route('student.materials.show', $material) }}" class="material-card">

                    <div class="material-card-icon">
                        <x-icon name="book-open" size="18" />
                    </div>

                    <div class="material-card-body">

                        <span class="eyebrow">
                            {{ $material->subject?->name }}
                        </span>

                        <h3>{{ $material->title }}</h3>

                        <p>
                            {{ \Illuminate\Support\Str::limit($material->description, 85) }}
                        </p>

                        <small>
                            {{ $material->files->count() }} lampiran ·
                            {{ $material->created_at->translatedFormat('d M Y') }}
                        </small>

                    </div>

                    <span class="material-card-arrow">
                        <x-icon name="arrow-up-right" size="16" />
                    </span>

                </a>

            @empty

                <div class="empty-inline">
                    Belum ada materi yang dipublikasikan untuk mata pelajaranmu.
                </div>

            @endforelse

        </div>

    </section>

</div>

@endsection
