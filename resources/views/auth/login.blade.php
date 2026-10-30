@extends('layouts.auth')

@section('title', 'Masuk ke akun')

@section('content')
<div class="login-shell">

    {{-- BAGIAN KIRI --}}
    <section class="login-visual">

        {{-- BRAND LOGO --}}
        <a href="{{ route('login') }}" class="brand-lockup login-brand">
            <img
                src="{{ asset('images/enspire-icon.png') }}"
                alt="Logo Enspire Learning"
                class="login-brand-image">

            <span class="login-brand-text">
                <strong>Enspire Learning</strong>
                <small>Belajar · Tumbuh · Raih Mimpi</small>
            </span>
        </a>

        {{-- COPYWRITING --}}
        <div class="login-copy">
            <span class="eyebrow">RUANG UNTUK BERTUMBUH</span>

            <h1>
                Langkah kecil hari ini,
                <br>
                <em>masa depan</em> yang lebih besar.
            </h1>

            <p>
                Temukan cara belajar yang menyenangkan,
                terarah, dan sesuai dengan tujuanmu.
            </p>
        </div>

        {{-- ILUSTRASI --}}
        <div class="login-illustration" aria-hidden="true">
            <div class="ill-circle circle-one"></div>
            <div class="ill-circle circle-two"></div>

            <div class="book-stack">
                <span></span>
                <span></span>
                <span></span>
                <b>✦</b>
            </div>

            <div class="plant">
                <i></i>
                <i></i>
                <i></i>
                <span></span>
            </div>

            <div class="floating-note">
                Belajar hari ini
                <br>
                <strong>lebih baik dari kemarin.</strong>
            </div>
        </div>

        <div class="login-visual-footer">
            © {{ date('Y') }} Enspire Learning
        </div>

    </section>


    {{-- BAGIAN KANAN --}}
    <section class="login-panel">
        <div class="login-card">

            {{-- WELCOME --}}
            <div class="login-welcome">

                <div class="login-welcome-icon">
                    <img
                        src="{{ asset('images/enspire-icon.png') }}"
                        alt="Enspire Learning"
                        class="login-welcome-image">
                </div>

                <p class="eyebrow">SELAMAT DATANG</p>

                <h2>Masuk ke akunmu</h2>

                <p>
                    Silakan masuk untuk melanjutkan aktivitas
                    belajar dan mengajar.
                </p>

            </div>


            {{-- FORM LOGIN --}}
            <form
                method="POST"
                action="{{ route('login.store') }}"
                class="form-stack">
                @csrf

                <label class="field-label" for="email">
                    Email
                </label>

                <input
                    class="form-control"
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="nama@email.com"
                    autocomplete="username"
                    required
                    autofocus>

                @error('email')
                <span class="field-error">{{ $message }}</span>
                @enderror


                <label class="field-label" for="password">
                    Password
                </label>


                <div class="password-wrap" x-data="{ show: false }">
                    <input
                        class="form-control"
                        id="password"
                        :type="show ? 'text' : 'password'"
                        name="password"
                        placeholder="Masukkan password"
                        autocomplete="current-password"
                        required>

                    <button
                        type="button"
                        @click="show = !show"
                        class="password-toggle password-icon-toggle"
                        :aria-label="show ? 'Sembunyikan password' : 'Lihat password'"
                        :title="show ? 'Sembunyikan password' : 'Lihat password'">
                        <svg x-show="!show" xmlns="http://www.w3.org/2000/svg"
                            width="20" height="20" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="1.8"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z" />
                            <circle cx="12" cy="12" r="3" />
                        </svg>

                        <svg x-show="show" x-cloak xmlns="http://www.w3.org/2000/svg"
                            width="20" height="20" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="1.8"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="m3 3 18 18" />
                            <path d="M10.6 10.6a2 2 0 0 0 2.8 2.8" />
                            <path d="M9.9 5.2A11 11 0 0 1 12 5c6.4 0 10 7 10 7a16 16 0 0 1-3.1 3.8" />
                            <path d="M6.2 6.2C3.5 8 2 12 2 12s3.6 7 10 7a10 10 0 0 0 3-.5" />
                        </svg>
                    </button>
                </div>


                @error('password')
                <span class="field-error">{{ $message }}</span>
                @enderror


                <label class="checkbox-line">
                    <input
                        type="checkbox"
                        name="remember"
                        value="1">
                    <span>Ingat saya di perangkat ini</span>
                </label>


                <button
                    class="btn btn-primary btn-full"
                    type="submit">
                    Masuk ke Enspire <span>→</span>
                </button>

            </form>


            {{-- INFORMASI --}}
            <div class="login-hint">
                <span>ⓘ</span>
                Akun disediakan oleh pihak Enspire Learning.
                Registrasi mandiri tidak tersedia.
            </div>

        </div>
    </section>

</div>
@endsection