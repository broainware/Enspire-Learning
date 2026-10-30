
@extends('layouts.app')

@section('title', 'Profil Pengajar')

@section('content')

<div class="page-heading">
    <div>
        <p class="eyebrow">PENGATURAN AKUN</p>
        <h1>Profil Pengajar</h1>
        <p>Kelola informasi profil yang digunakan pada Enspire Learning.</p>
    </div>
</div>

<section class="panel profile-panel">

    <div class="profile-hero">

        <div class="profile-avatar">
            @if($user->photo_path)
                <img src="{{ asset('storage/'.$user->photo_path) }}" alt="Foto profil">
            @else
                {{ strtoupper(substr($user->name, 0, 1)) }}
            @endif
        </div>

        <div>
            <h2>{{ $user->name }}</h2>
            <p>{{ $user->email }}</p>
            <span class="status-pill status-completed">Pengajar</span>
        </div>

    </div>

    <form
        method="POST"
        action="{{ route('teacher.profile.update') }}"
        enctype="multipart/form-data"
        class="form-grid profile-form"
    >
        @csrf
        @method('PUT')

        {{-- NAMA --}}
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

        {{-- EMAIL READONLY --}}
        <div class="field-group">
            <label>Email</label>

            <input
                class="form-control"
                type="email"
                value="{{ $user->email }}"
                readonly
                aria-readonly="true"
                style="background:#f8f5f2;color:#a18a7e;cursor:not-allowed;"
            >

            <small class="form-help">
                Email merupakan identitas akun dan tidak dapat diubah melalui profil.
            </small>
        </div>

        {{-- TELEPON --}}
        <div class="field-group">
            <label>Nomor Telepon</label>

            <input
                class="form-control"
                type="text"
                name="phone"
                value="{{ old('phone', $user->phone) }}"
                placeholder="08xxxxxxxxxx"
            >
        </div>

        {{-- FOTO --}}
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

        {{-- ROLE --}}
        <div class="field-group field-span-2">
            <label>Role Akun</label>

            <input
                class="form-control readonly-control"
                value="Pengajar"
                disabled
            >

            <small class="form-help">
                Role ditentukan dari database dan tidak dapat diubah melalui profil.
            </small>
        </div>

        {{-- SIMPAN --}}
        <div class="field-span-2 form-actions">
            <button class="btn btn-primary" type="submit">
                Simpan Perubahan
                <x-icon name="arrow-right" size="16" class="button-trailing-icon" />
            </button>
        </div>

    </form>

</section>

@endsection
