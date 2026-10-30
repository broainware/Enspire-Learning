
@extends('layouts.app')

@section('title', 'Laporan Aktivitas Pengajar')

@section('content')

<div class="page-heading">
    <div>
        <p class="eyebrow">REKAP KEGIATAN</p>
        <h1>Laporan Pembelajaran</h1>
        <p>Pantau dan rekap aktivitas mengajar, sesi pertemuan, serta pelaksanaan kelas milik akunmu.</p>
    </div>

    <div class="heading-actions">

        <a
            class="btn btn-secondary"
            href="{{ route('teacher.reports.export', ['month' => $month, 'year' => $year]) }}"
        >
            <x-icon name="download" size="16" class="button-icon" />
            Unduh CSV
        </a>

        <a class="btn btn-primary" href="{{ route('teacher.sessions.create') }}">
            <x-icon name="plus" size="16" class="button-icon" />
            Tambah Aktivitas
        </a>

    </div>
</div>

<section class="panel report-filter">

    <form method="GET" class="filter-bar">

        <div class="filter-label">Periode laporan</div>

        <select name="month" class="form-control">

            @foreach(range(1, 12) as $m)
                <option value="{{ $m }}" @selected($month === $m)>
                    {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                </option>
            @endforeach

        </select>

        <select name="year" class="form-control">

            @foreach(range(now()->year - 2, now()->year + 1) as $y)
                <option value="{{ $y }}" @selected($year === $y)>
                    {{ $y }}
                </option>
            @endforeach

        </select>

        <button class="btn btn-secondary" type="submit">
            <x-icon name="filter" size="16" class="button-icon" />
            Tampilkan
        </button>

        <span class="muted-text period-caption">{{ $monthName }}</span>

    </form>

</section>

<div class="stat-grid stat-grid-three">

    <div class="stat-card">
        <div class="stat-icon mint">
            <x-icon name="check-circle" size="20" />
        </div>

        <div>
            <span>Pertemuan Selesai</span>
            <strong>{{ $completedCount }}</strong>
            <small>sesi pada periode ini</small>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon peach">
            <x-icon name="clock" size="20" />
        </div>

        <div>
            <span>Pertemuan Mendatang</span>
            <strong>{{ $upcomingCount }}</strong>
            <small>jadwal mulai hari ini dan seterusnya</small>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon lavender">
            <x-icon name="alert-circle" size="20" />
        </div>

        <div>
            <span>Perlu Perhatian</span>
            <strong>{{ $attentionCount }}</strong>
            <small>sesi lewat belum ditandai selesai</small>
        </div>
    </div>

</div>

<div class="dashboard-grid report-grid">

    {{-- GRAFIK --}}
    <section class="panel panel-wide">

        <div class="panel-heading">
            <div>
                <h2>Aktivitas Mengajar Mingguan</h2>
                <p>Jumlah sesi per minggu pada {{ $monthName }}</p>
            </div>
        </div>

        <div class="chart-box chart-report">
            <canvas id="reportChart"></canvas>
        </div>

    </section>

    {{-- CATATAN PERIODE --}}
    <section class="panel panel-side">

        <div class="panel-heading">
            <div>
                <h2>Catatan Periode</h2>
                <p>Ringkasan milik {{ auth()->user()->name }}</p>
            </div>
        </div>

        <div class="report-note">

            <span class="note-icon">
                <x-icon name="sparkles" size="20" />
            </span>

            <p>
                Laporan ini hanya menampilkan aktivitas yang ditugaskan
                kepada akun pengajar yang sedang masuk.
            </p>

            <small>Data diperbarui dari jadwal mengajar.</small>

        </div>

    </section>

    {{-- DETAIL AKTIVITAS --}}
    <section class="panel panel-full">

        <div class="panel-heading">
            <div>
                <h2>Detail Aktivitas Mengajar</h2>
                <p>{{ $sessions->total() }} sesi tercatat pada periode {{ $monthName }}</p>
            </div>
        </div>

        <div class="table-wrap">

            <table class="data-table">

                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Mata Pelajaran</th>
                        <th>Kelas</th>
                        <th>Materi</th>
                        <th>Jumlah Siswa</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($sessions as $session)

                        <tr>

                            <td>
                                <strong>
                                    {{ $session->session_date->translatedFormat('d M Y') }}
                                </strong>

                                <small class="muted-text">
                                    {{ substr($session->start_time, 0, 5) }}–{{ substr($session->end_time, 0, 5) }}
                                </small>
                            </td>

                            <td>{{ $session->subject?->name }}</td>
                            <td>{{ $session->classRoom?->name ?? '–' }}</td>
                            <td>{{ $session->material?->title ?? '–' }}</td>
                            <td>{{ $session->student_count ?? '–' }}</td>

                            <td>
                                <span class="status-pill status-{{ $session->status }}">
                                    {{ $session->display_status }}
                                </span>
                            </td>

                            <td>
                                <div class="table-actions">

                                    <a
                                        class="icon-action"
                                        href="{{ route('teacher.sessions.edit', $session) }}"
                                        title="Edit"
                                        aria-label="Edit aktivitas"
                                    >
                                        <x-icon name="pencil" size="16" />
                                    </a>

                                    <form
                                        method="POST"
                                        action="{{ route('teacher.sessions.destroy', $session) }}"
                                        onsubmit="return confirm('Hapus aktivitas ini?')"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="icon-action danger-action"
                                            title="Hapus aktivitas"
                                            aria-label="Hapus aktivitas"
                                        >
                                            <x-icon name="trash" size="16" />
                                        </button>
                                    </form>

                                </div>
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="7">
                                <div class="empty-inline">
                                    Belum ada aktivitas pada periode ini.
                                </div>
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        <div class="pagination-row">
            {{ $sessions->links() }}
        </div>

    </section>

</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {

    const el = document.getElementById('reportChart');

    if (!el || !window.Chart) return;

    new window.Chart(el, {
        type: 'bar',

        data: {
            labels: @json($labels),

            datasets: [
                {
                    label: 'Selesai',
                    data: @json($finished),
                    backgroundColor: '#f0783d',
                    borderRadius: 8
                },
                {
                    label: 'Belum selesai',
                    data: @json($unfinished),
                    backgroundColor: '#f6d4c0',
                    borderRadius: 8
                }
            ]
        },

        options: {
            responsive: true,
            maintainAspectRatio: false,

            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        boxWidth: 8
                    }
                }
            },

            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0 }
                },

                x: {
                    grid: { display: false }
                }
            }
        }
    });

});
</script>
@endpush
