
@extends('layouts.app')

@section('title', 'Kelola Siswa')

@section('content')

<div class="page-heading">
    <div>
        <p class="eyebrow">DATA PESERTA DIDIK</p>
        <h1>Kelola Siswa</h1>
        <p>Hubungkan akun siswa yang sudah tersedia di database ke mata pelajaran dan kelas.</p>
    </div>
</div>

<section class="panel assign-panel">

    <div class="panel-heading">
        <div>
            <h2>Hubungkan Akun Siswa</h2>
            <p>Form ini tidak membuat akun/login baru. Akun siswa harus sudah tersedia di database.</p>
        </div>

        <span class="status-pill status-completed">Akun existing</span>
    </div>

    <form
        method="POST"
        action="{{ route('teacher.students.assign') }}"
        class="form-grid compact-form"
    >
        @csrf

        <div class="field-group">
            <label>Email akun siswa <b>*</b></label>
            <input
                class="form-control"
                type="email"
                name="email"
                placeholder="siswa@email.com"
                value="{{ old('email') }}"
                required
            >
        </div>

        <div class="field-group">
            <label>Mata Pelajaran <b>*</b></label>
            <select class="form-control" name="subject_id" required>
                <option value="">Pilih mata pelajaran</option>

                @foreach($subjects as $subject)
                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="field-group">
            <label>Kelas (opsional)</label>
            <select class="form-control" name="class_id">
                <option value="">Pilih kelas</option>

                @foreach($classes as $class)
                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="field-group field-actions-align">
            <button class="btn btn-primary" type="submit">
                <x-icon name="plus" size="16" class="button-icon" />
                Hubungkan Siswa
            </button>
        </div>

    </form>

</section>

<section class="panel">

    <form method="GET" class="filter-bar">

        <input
            class="form-control search-control"
            name="q"
            value="{{ request('q') }}"
            placeholder="Cari nama atau email siswa..."
        >

        <select class="form-control" name="subject_id">
            <option value="">Semua Mata Pelajaran</option>

            @foreach($subjects as $subject)
                <option value="{{ $subject->id }}" @selected(request('subject_id') == $subject->id)>
                    {{ $subject->name }}
                </option>
            @endforeach
        </select>

        <button class="btn btn-secondary">
            <x-icon name="filter" size="16" class="button-icon" />
            Filter
        </button>

    </form>

    <div class="panel-heading table-title">
        <div>
            <h2>Daftar Akun Siswa</h2>
            <p>{{ $students->total() }} akun siswa ditemukan</p>
        </div>
    </div>

    <div class="table-wrap">

        <table class="data-table">

            <thead>
                <tr>
                    <th>Nama Siswa</th>
                    <th>Email</th>
                    <th>Kelas</th>
                    <th>Mata Pelajaran</th>
                    <th>Status Akun</th>
                </tr>
            </thead>

            <tbody>

                @forelse($students as $student)

                    <tr>

                        <td>
                            <div class="person-cell">
                                <span class="avatar avatar-small">
                                    {{ strtoupper(substr($student->name, 0, 1)) }}
                                </span>

                                <strong>{{ $student->name }}</strong>
                            </div>
                        </td>

                        <td>{{ $student->email }}</td>

                        <td>
                            {{ $student->classes->pluck('name')->join(', ') ?: '–' }}
                        </td>

                        <td>
                            <div class="tag-list">

                                @forelse($student->subjects as $subject)

                                    <span class="subject-tag">
                                        {{ $subject->name }}

                                        <form
                                            method="POST"
                                            action="{{ route('teacher.students.detach', [$student, $subject]) }}"
                                            onsubmit="return confirm('Lepaskan mata pelajaran ini dari siswa?')"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                title="Lepaskan"
                                                aria-label="Lepaskan mata pelajaran"
                                            >
                                                <x-icon name="x" size="14" />
                                            </button>
                                        </form>

                                    </span>

                                @empty

                                    <span class="muted-text">Belum terhubung</span>

                                @endforelse

                            </div>
                        </td>

                        <td>
                            <span class="status-pill {{ $student->is_active ? 'status-completed' : 'status-cancelled' }}">
                                {{ $student->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="5">
                            <div class="empty-inline">Belum ada akun siswa.</div>
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    <div class="pagination-row">
        {{ $students->links() }}
    </div>

</section>

@endsection
