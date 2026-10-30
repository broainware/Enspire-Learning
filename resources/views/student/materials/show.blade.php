
@extends('layouts.app')

@section('title', $material->title)

@section('content')

<div class="page-heading">

    <div>
        <p class="eyebrow">
            {{ $material->subject?->name }} ·
            {{ $material->education_level ?: 'Materi Belajar' }}
        </p>

        <h1>{{ $material->title }}</h1>

        <p>
            Dipublikasikan {{ $material->created_at->translatedFormat('d F Y') }}
        </p>
    </div>

    <a href="{{ route('student.materials.index') }}" class="btn btn-secondary">
        <x-icon name="arrow-left" size="16" class="button-icon" />
        Kembali ke Materi
    </a>

</div>

<div class="content-column">

    {{-- DESKRIPSI DAN LAMPIRAN --}}
    <section class="panel article-panel">

        <h2>Deskripsi Materi</h2>

        <div class="article-copy">
            {!! nl2br(e($material->description ?: 'Belum ada deskripsi untuk materi ini.')) !!}
        </div>

        <div class="divider"></div>

        <div class="panel-heading">
            <div>
                <h2>Lampiran Pembelajaran</h2>
                <p>Unduh file pendukung untuk dipelajari.</p>
            </div>
        </div>

        <div class="attachment-list">

            @forelse($material->files as $file)

                <a
                    class="attachment-item attachment-link"
                    href="{{ route('student.materials.files.download', $file) }}"
                >

                    <span>
                        <x-icon name="file-text" size="17" />
                    </span>

                    <strong>{{ $file->original_name }}</strong>

                    <small>
                        {{ number_format($file->size_bytes / 1024, 0) }} KB
                    </small>

                    <span class="download-label">
                        Unduh
                        <x-icon name="download" size="14" />
                    </span>

                </a>

            @empty

                <p class="muted-text">Belum ada lampiran.</p>

            @endforelse

        </div>

    </section>

    {{-- PROGRESS --}}
    <section class="panel">

        <div class="panel-heading">

            <div>
                <h2>Progress Materi</h2>
                <p>Tandai status belajarmu untuk materi ini.</p>
            </div>

            <span class="progress-number">
                {{ $progress->progress_percent }}%
            </span>

        </div>

        <div class="progress-track">
            <span style="width: {{ $progress->progress_percent }}%"></span>
        </div>

        <form
            method="POST"
            action="{{ route('student.progress.update', $material) }}"
            class="inline-status-form"
        >
            @csrf
            @method('PUT')

            <select name="status" class="form-control">

                <option value="not_started" @selected($progress->status === 'not_started')>
                    Belum Dimulai
                </option>

                <option value="in_progress" @selected($progress->status === 'in_progress')>
                    Sedang Dipelajari
                </option>

                <option value="completed" @selected($progress->status === 'completed')>
                    Selesai
                </option>

            </select>

            <button class="btn btn-primary" type="submit">
                Perbarui Progress
                <x-icon name="check" size="16" class="button-trailing-icon" />
            </button>

        </form>

    </section>

</div>

@endsection
