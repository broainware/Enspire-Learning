
@extends('layouts.app')

@section('title', 'Profil Siswa')

@section('content')

<div class="page-heading">
    <div>
        <p class="eyebrow">PENGATURAN AKUN</p>
        <h1>Profil Saya</h1>
        <p>Kelola informasi pribadi yang ditampilkan pada akun Enspire Learning.</p>
    </div>
</div>

<section class="panel profile-panel">

    {{-- INFORMASI PROFIL --}}
    <div class="profile-hero">
        <div class="profile-avatar">
            @if($user->photo_path)
                <img
                    src="{{ asset('storage/'.$user->photo_path) }}"
                    alt="Foto profil"
                >
            @else
                {{ strtoupper(substr($user->name, 0, 1)) }}
            @endif
        </div>

        <div>
            <h2>{{ $user->name }}</h2>
            <p>{{ $user->email }}</p>
            <span class="status-pill status-completed">Siswa</span>
        </div>
    </div>

    {{-- FORM EDIT PROFIL --}}
    <form
        method="POST"
        action="{{ route('student.profile.update') }}"
        enctype="multipart/form-data"
        class="form-grid profile-form"
    >
        @csrf
        @method('PUT')

        {{-- NAMA LENGKAP --}}
        <div class="field-group">
            <label>Nama Lengkap <b>*</b></label>
            <input
                class="form-control"
                type="text"
                name="name"
                value="{{ old('name', $user->name) }}"
                required
            >
        </div>

        {{-- EMAIL (TIDAK DAPAT DIUBAH) --}}
        <div class="field-group">
            <label>Email</label>
            <input
                class="form-control"
                type="email"
                value="{{ $user->email }}"
                readonly
                aria-readonly="true"
                style="background: #f8f5f2; color: #a18a7e; cursor: not-allowed;"
            >
            <small class="form-help">
                Email merupakan identitas akun dan tidak dapat diubah melalui profil.
            </small>
        </div>

        {{-- NOMOR TELEPON --}}
        <div class="field-group">
            <label>Nomor Telepon</label>
            <input
                class="form-control"
                type="text"
                name="phone"
                value="{{ old('phone', $user->phone) }}"
                placeholder="Masukkan nomor telepon"
            >
        </div>

        {{-- FOTO PROFIL --}}
        <div class="field-group">
            <label>Foto Profil</label>
            <input
                class="form-control file-control"
                type="file"
                name="photo"
                accept="image/jpeg,image/png,image/webp"
            >
            <small class="form-help">
                JPG/PNG/WEBP, maksimal 2 MB.
            </small>
        </div>

        {{-- MATA PELAJARAN --}}
        <div class="field-group field-span-2">
            <label>Mata Pelajaran Terdaftar</label>

            <div class="tag-list">
                @forelse($user->subjects as $subject)
                    <span class="subject-tag">
                        {{ $subject->name }}
                    </span>
                @empty
                    <span class="muted-text">
                        Belum ada mata pelajaran terhubung.
                    </span>
                @endforelse
            </div>

            <small class="form-help">
                Mata pelajaran dikelola oleh pengajar, bukan dari profil siswa.
            </small>
        </div>

        {{-- TOMBOL SIMPAN --}}
        <div class="field-span-2 form-actions">
            <button class="btn btn-primary" type="submit">
                Simpan Perubahan →
            </button>
        </div>

    </form>

</section>

@endsection
