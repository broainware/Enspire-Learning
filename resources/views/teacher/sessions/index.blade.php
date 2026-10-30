
@extends('layouts.app')

@section('title', 'Jadwal Mengajar')

@section('content')

<div class="page-heading">
    <div>
        <p class="eyebrow">PENGELOLAAN KELAS</p>
        <h1>Jadwal Mengajar</h1>
        <p>Atur sesi mengajar, materi, dan waktu pertemuan yang menjadi tanggung jawabmu.</p>
    </div>

    <a href="{{ route('teacher.sessions.create') }}" class="btn btn-primary">
        <x-icon name="plus" size="16" class="button-icon" />
        Tambah Jadwal
    </a>
</div>

<section class="panel">

    <form method="GET" class="filter-bar">

        <select name="month" class="form-control">
            <option value="">Semua Bulan</option>

            @foreach(range(1, 12) as $month)
                <option value="{{ $month }}" @selected(request('month') == $month)>
                    {{ \Carbon\Carbon::create()->month($month)->translatedFormat('F') }}
                </option>
            @endforeach
        </select>

        <select name="status" class="form-control">
            <option value="">Semua Status</option>

            @foreach([
                'scheduled' => 'Terjadwal',
                'in_progress' => 'Berlangsung',
                'completed' => 'Selesai',
                'cancelled' => 'Dibatalkan'
            ] as $key => $label)

                <option value="{{ $key }}" @selected(request('status') === $key)>
                    {{ $label }}
                </option>

            @endforeach
        </select>

        <button class="btn btn-secondary" type="submit">
            <x-icon name="filter" size="16" class="button-icon" />
            Terapkan Filter
        </button>

    </form>

    <div class="panel-heading table-title">
        <div>
            <h2>Daftar Sesi Mengajar</h2>
            <p>Hanya jadwal yang ditugaskan kepada akun pengajar ini.</p>
        </div>
    </div>

    <div class="table-wrap">

        <table class="data-table">

            <thead>
                <tr>
                    <th>Tanggal & Waktu</th>
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
                                    aria-label="Edit jadwal"
                                >
                                    <x-icon name="pencil" size="16" />
                                </a>

                                <form
                                    method="POST"
                                    action="{{ route('teacher.sessions.destroy', $session) }}"
                                    onsubmit="return confirm('Hapus jadwal ini?')"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        class="icon-action danger-action"
                                        type="submit"
                                        title="Hapus"
                                        aria-label="Hapus jadwal"
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
                                Belum ada jadwal mengajar. Tambahkan sesi pertamamu.
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

@endsection
