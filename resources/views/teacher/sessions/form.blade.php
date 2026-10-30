
@extends('layouts.app')

@section('title', $session->exists ? 'Edit Jadwal' : 'Tambah Jadwal')

@section('content')

<div class="page-heading">
    <div>
        <p class="eyebrow">JADWAL MENGAJAR</p>
        <h1>{{ $session->exists ? 'Edit Sesi Mengajar' : 'Tambah Sesi Mengajar' }}</h1>
        <p>Isi detail pertemuan. Jadwal ini akan muncul pada laporan aktivitas akunmu.</p>
    </div>

    <a href="{{ route('teacher.sessions.index') }}" class="btn btn-secondary">
        <x-icon name="arrow-left" size="16" class="button-icon" />
        Kembali
    </a>
</div>

<section class="panel form-panel">

    <form
        method="POST"
        action="{{ $session->exists ? route('teacher.sessions.update', $session) : route('teacher.sessions.store') }}"
    >
        @csrf

        @if($session->exists)
            @method('PUT')
        @endif

        <div class="form-grid">

            <div class="field-group">
                <label>Mata Pelajaran <b>*</b></label>

                <select class="form-control" name="subject_id" required>
                    <option value="">Pilih mata pelajaran</option>

                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" @selected(old('subject_id', $session->subject_id) == $subject->id)>
                            {{ $subject->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="field-group">
                <label>Kelas</label>

                <select class="form-control" name="class_id">
                    <option value="">Pilih kelas (opsional)</option>

                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" @selected(old('class_id', $session->class_id) == $class->id)>
                            {{ $class->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="field-group field-span-2">
                <label>Materi Pembelajaran</label>

                <select class="form-control" name="material_id">
                    <option value="">Pilih materi (opsional)</option>

                    @foreach($materials as $material)
                        <option value="{{ $material->id }}" @selected(old('material_id', $session->material_id) == $material->id)>
                            {{ $material->title }} · {{ $material->subject?->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="field-group">
                <label>Tanggal <b>*</b></label>
                <input
                    class="form-control"
                    type="date"
                    name="session_date"
                    value="{{ old('session_date', $session->session_date?->format('Y-m-d')) }}"
                    required
                >
            </div>

            <div class="field-group">
                <label>Jumlah Siswa</label>
                <input
                    class="form-control"
                    type="number"
                    name="student_count"
                    min="0"
                    max="500"
                    value="{{ old('student_count', $session->student_count) }}"
                    placeholder="Contoh: 12"
                >
            </div>

            <div class="field-group">
                <label>Jam Mulai <b>*</b></label>
                <input
                    class="form-control"
                    type="time"
                    name="start_time"
                    value="{{ old('start_time', $session->start_time ? substr($session->start_time, 0, 5) : '') }}"
                    required
                >
            </div>

            <div class="field-group">
                <label>Jam Selesai <b>*</b></label>
                <input
                    class="form-control"
                    type="time"
                    name="end_time"
                    value="{{ old('end_time', $session->end_time ? substr($session->end_time, 0, 5) : '') }}"
                    required
                >
            </div>

            <div class="field-group">
                <label>Status <b>*</b></label>

                <select class="form-control" name="status">
                    <option value="scheduled" @selected(old('status', $session->status ?: 'scheduled') === 'scheduled')>
                        Terjadwal / Mendatang
                    </option>

                    <option value="in_progress" @selected(old('status', $session->status) === 'in_progress')>
                        Berlangsung
                    </option>

                    <option value="completed" @selected(old('status', $session->status) === 'completed')>
                        Selesai
                    </option>

                    <option value="cancelled" @selected(old('status', $session->status) === 'cancelled')>
                        Dibatalkan
                    </option>
                </select>
            </div>

            <div class="field-group field-span-2">
                <label>Catatan Aktivitas</label>
                <textarea
                    class="form-control"
                    name="notes"
                    rows="4"
                    placeholder="Catatan singkat tentang sesi mengajar..."
                >{{ old('notes', $session->notes) }}</textarea>
            </div>

        </div>

        <div class="form-actions">

            <a href="{{ route('teacher.sessions.index') }}" class="btn btn-secondary">
                Batal
            </a>

            <button class="btn btn-primary" type="submit">
                {{ $session->exists ? 'Simpan Perubahan' : 'Simpan Jadwal' }}
                <x-icon name="arrow-right" size="16" class="button-trailing-icon" />
            </button>

        </div>

    </form>

</section>

@endsection
