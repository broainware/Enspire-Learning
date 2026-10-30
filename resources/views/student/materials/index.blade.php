
@extends('layouts.app')

@section('title', 'Materi Belajar')

@section('content')

<div class="page-heading">

    <div>
        <p class="eyebrow">RUANG BELAJAR</p>
        <h1>Materi Belajar</h1>
        <p>Temukan materi yang tersedia untuk mata pelajaran yang kamu ikuti.</p>
    </div>

    <div class="heading-chip">
        {{ $subjects->pluck('name')->join(' · ') ?: 'Belum ada mata pelajaran' }}
    </div>

</div>

<section class="panel">

    {{-- FILTER MATERI --}}
    <form method="GET" class="filter-bar">

        <input
            class="form-control search-control"
            name="q"
            value="{{ request('q') }}"
            placeholder="Cari materi atau topik..."
        >

        <select class="form-control" name="subject_id">

            <option value="">Semua Mata Pelajaran</option>

            @foreach($subjects as $subject)

                <option value="{{ $subject->id }}" @selected(request('subject_id') == $subject->id)>
                    {{ $subject->name }}
                </option>

            @endforeach

        </select>

        <button class="btn btn-secondary" type="submit">
            <x-icon name="search" size="16" class="button-icon" />
            Cari Materi
        </button>

    </form>

    {{-- DAFTAR MATERI --}}
    <div class="material-card-grid student-material-grid">

        @forelse($materials as $material)

            @php
                $progress = $material->progressRecords->first();
            @endphp

            <a
                href="{{ route('student.materials.show', $material) }}"
                class="material-card"
            >

                <div class="material-card-icon">
                    <x-icon name="book-open" size="18" />
                </div>

                <div class="material-card-body">

                    <span class="eyebrow">
                        {{ $material->subject?->name }} ·
                        {{ $material->education_level ?: 'Umum' }}
                    </span>

                    <h3>{{ $material->title }}</h3>

                    <p>
                        {{ \Illuminate\Support\Str::limit($material->description, 100) }}
                    </p>

                    <div class="material-meta">

                        <small>{{ $material->files->count() }} file</small>

                        <span class="status-pill {{ $progress?->status === 'completed' ? 'status-completed' : 'status-scheduled' }}">
                            {{ $progress?->status === 'completed' ? 'Selesai' : ($progress?->status === 'in_progress' ? 'Dipelajari' : 'Belum dimulai') }}
                        </span>

                    </div>

                </div>

                <span class="material-card-arrow">
                    <x-icon name="arrow-up-right" size="16" />
                </span>

            </a>

        @empty

            <div class="empty-state empty-full">
                <span><x-icon name="book-open" size="28" /></span>
                <strong>Belum ada materi yang tersedia</strong>
                <p>Materi yang dipublikasikan untuk mata pelajaranmu akan muncul di sini.</p>
            </div>

        @endforelse

    </div>

    <div class="pagination-row">
        {{ $materials->links() }}
    </div>

</section>

@endsection
